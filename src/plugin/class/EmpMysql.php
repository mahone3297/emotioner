<?php

// @date 2012-02-21
// @author mahone

class EmpMysql
{
    private $logger = null;
    
    private $link = null;
    private $master = null;
    private $slave = null;
    private $is_master = true;
    
    private $bind_marker = '?';
    private $errno = 0;
    private $error = '';
    private $insert_id = 0;
    private $affected_rows = -1;
    
    public function __construct(Array $config, $logger=null)
    {
        $this->master = $config['master'];
        if ( ! empty($config['slave'])){
            $this->slave = $config['slave'];
        }
        
        $this->logger = $logger;
    }
    
    public function __destruct()
    {
        $this->disconnect();
    }
    
    // get gird
    public function get_grid($sql, Array $bind=array())
    {
        $rs = $this->query($sql, $bind);
        return is_resource($rs) ? $this->fetch_from_result($rs) : null;
    }
    
    // get row
    public function get_row($sql, Array $bind=array())
    {
        $rs = $this->query($sql, $bind);
        if (is_resource($rs)){
            $rt = $this->fetch_from_result($rs, 1);
            return empty($rt) ? null : reset($rt);
        }
        
        return  null;
    }
    
    // get col
    public function get_col($sql, Array $bind=array())
    {
        $rt = $this->get_row($sql, $bind);
        return empty($rt) ? null : reset($rt);
    }
    
    // @access private
    private function fetch_from_result($rs, $cnt=-1)
    {
        if (mysql_num_rows($rs) <= 0){
            return null;
        }
        
        $rt = array();
        mysql_data_seek($rs, 0);
        while (($cnt<0 || $cnt-->0) && $row=mysql_fetch_assoc($rs)){
            $rt[] = $row;
        }
        mysql_free_result($rs);
        return empty($rt) ? null : $rt;
    }
    
    // query
    public function query($sql, Array $bind=array())
    {
        $sql = trim($sql);
        if ( ! empty($bind)){
            $sql = $this->autobind($sql, $bind);
        }
        
        $rt = false;
        if ($this->is_write($sql)){
            $rt = $this->connect(true);
        } else {
            $rt = $this->connect(false);
        }
        
        if ($rt === false){
            return false;
        }
        
        return mysql_query($sql, $this->link);
    }
    
    // sql autobind
    // @access private
    private function autobind($sql, Array $bind_array)
    {
        if (strpos($sql, $this->bind_marker) === false){
            return $sql;
        }
        
        $segments = explode($this->bind_marker, $sql);
        if (count($bind_array) >= count($segments)){
            $bind_array = array_slice($bind_array, 0, count($segments)-1);
        }
        
        $rt = $segments[0];
        $i = 1;
        foreach ($bind_array as $bind){
            $rt .= $this->escape($bind);
            $rt .= $segments[$i++];
        }
        return $rt;
    }
    
    // connect
    public function connect($is_master)
    {
        if ( ! empty($this->link)){
            if ($is_master && !$this->is_master){
                $this->disconnect();
            } else {
                if ($this->ping()){
                    return true;
                } else {
                    $this->disconnect();
                    $this->log_error();
                }
            }
        }
        
        $is_master = $is_master ? true : ($this->slave==null ? true : false);
        $db_conf = $is_master ? $this->master : array_rand($this->slave, 1);
        
        ini_set('mysql.connect_timeout', $db_conf['timeout']);
        $rt = false;
        $retry_times = 2;
        while ($retry_times > 0){
            // note: @param new_link
            $rt = mysql_connect(isset($db_conf['port']) ? $db_conf['host'].':'.$db_conf['port'] : $db_conf['host'], $db_conf['username'], $db_conf['password'], true);
            if ($rt !== false){
                break;
            }
            $retry_times--;
            usleep(10000);
            $this->log_error(json_encode($db_conf));
        }
        
        if ($rt === false){
            return false;
        }
        
        $this->link = $rt;
        $this->is_master = $is_master;
        mysql_set_charset($db_conf['charset'], $this->link) or $this->log_error();
        mysql_select_db($db_conf['database'], $this->link) or $this->log_error();
        return true;
    }
    
    // disconnect
    public function disconnect()
    {
        if ( ! empty($this->link)){
            mysql_close($this->link) or $this->log_error();
            $this->link = null;
        }
        
        $this->is_master = true;
    }
    
    // ping
    public function ping()
    {
        return mysql_ping($this->link);
    }
    
    // escape
    public function escape($str)
    {
        if (is_string($str)){
            $str = "'" . $this->escape_str($str) . "'";
        } else if (is_bool($str)){
            $str = ($str === false) ? 0 : 1;
        } else if (is_null($str)){
            $str = 'NULL';
        }
        
        return $str;
    }
    
    // escape str
    public function escape_str($str)
    {
        if (empty($this->link)){
            $str = addslashes($str);
        } else {
            $str = mysql_real_escape_string($str, $this->link);
        }
        
        return $str;
    }
    
    // @access private
    private static function is_write($sql)
    {
        if (preg_match('/^(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)/i', $sql)){
            return true;
        }

        return false;
    }
    
    // set & return insert_id
    public function insert_id()
    {
        $this->insert_id = mysql_insert_id($this->link);
        return $this->insert_id;
    }
    
    // set & return affected_rows
    public function affected_rows()
    {
        $this->affected_rows = mysql_affected_rows($this->link);
        return $this->affected_rows;
    }
    
    // set & return errno
    public function errno()
    {
        $this->errno = mysql_errno($this->link);
        return $this->errno;
    }
    
    // set & return error
    public function error()
    {
        $this->error = mysql_error($this->link);
        return $this->error;
    }
    
    // @access private
    private function log_error($str='')
    {
        $this->errno();
        $this->error();
        
        if ($this->logger != null){
            $this->logger->error('MYSQL errno: '.$this->errno.', error: '.$this->error.', info: '.$str, 'EMCMYSQL');
        }
    }
}