<?php

// @date 2012-02-21
// @author mahone

class EmcMysqli
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
    
    // get grid
    public function get_grid($sql, Array $bind=array())
    {
        $rs = $this->query($sql, $bind);
        return $rs instanceof MySQLi_Result ? $this->fetch_from_result($rs) : null;
    }
    
    // get row
    public function get_row($sql, Array $bind=array())
    {
        $rs = $this->query($sql, $bind);
        if ($rs instanceof MySQLi_Result){
            $rt = $this->fetch_from_result($rs, 1);
            return empty($rt) ? null : reset($rt);
        }
        
        return null;
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
        if ($rs->num_rows <= 0){
            return null;
        }
        
        $rt = array();
        $rs->data_seek(0);
        while (($cnt<0 || $cnt-->0) && $row=$rs->fetch_assoc()){
            $rt[] = $row;
        }
        $rs->free();
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
        
        return $this->link->query($sql);
    }
    
    // sql autoload
    // @access: private
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
        
        $rt = false;
        $retry_times = 2;
        while ($retry_times > 0){
            $rt = mysqli_init();
            $rt->options(MYSQLI_OPT_CONNECT_TIMEOUT, $db_conf['timeout']);
            $rt->real_connect($db_conf['host'], $db_conf['username'], $db_conf['password'], $db_conf['database'], $db_conf['port']);
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
        $this->link->set_charset($db_conf['charset']) or $this->log_error();
        return true;
    }
    
    // disconnect
    public function disconnect()
    {
        if ( ! empty($this->link)){
            $this->link->close() or $this->log_error();
            $this->link = null;
        }
        
        $this->is_master = true;
    }
    
    // ping
    public function ping()
    {
        return $this->link->ping();
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
            $str = $this->link->real_escape_string($str);
        }
        
        return $str;
    }
    
    // @access: private
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
        $this->insert_id = $this->link->insert_id;
        return $this->insert_id;
    }
    
    // set & return affected_rows
    public function affected_rows()
    {
        $this->affected_rows = $this->link->affected_rows;
        return $this->affected_rows;
    }
    
    // set & return errno
    public function errno()
    {
        $this->errno = $this->link->errno;
        return $this->errno;
    }
    
    // set & return error
    public function error()
    {
        $this->error = $this->link->error;
        return $this->error;
    }
    
    // @access private
    private function log_error($str='')
    {
        $this->errno();
        $htis->error();
        
        if ($this->logger != null){
            $this->logger->error('MYSQL errno: '.$this->errno.', error: '.$this->error.', info: '.$str, 'EMCMYSQLI');
        }
    }
}