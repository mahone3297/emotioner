<?php

// @date 2012-02-23
// @author mahone
// @desc:
    // apt-get install memcached
    // /usr/bin/memcached -m 64 -p 11211 -u memcache -l 127.0.0.1
    
    // not implement
    // public array Memcached::fetch ( void )
    // public array Memcached::fetchAll ( void )
    // public bool Memcached::getDelayed ( array $keys [, bool $with_cas [, callback $value_cb ]] )
    // public bool Memcached::getDelayedByKey ( string $server_key , array $keys [, bool $with_cas [, callback $value_cb ]] )

class EmpCacheMemcache
{
    private $logger = null;
    
    // result code
    private $rs_code = 0;
    // result message
    private $rs_msg = '';
    private $opt = array();
    private $inst = null;
    
    public function __construct(Array $servers=array(), Array $options=array(), $logger=null)
    {
        $this->inst = new Memcached();
        
        if ( ! empty($servers)){
            $this->add_servers($servers);
        }
        if ( ! empty($options)){
            $this->set_option_array($options);
        }
        
        $this->logger = $logger;
    }
    
    public function __destruct()
    {
        // $this->disconnect();
    }
    
    // @todo php5-memcached does not implement the close function.
    // disconnect
    public function disconnect()
    {
    }
    
    // get instance. some function not implement, so get_instance() can return the instance for you.
    public function get_instance()
    {
        return $this->inst;
    }
    
    // add server
    public function add_server($host, $port, $weight=0)
    {
        return $this->inst->addServer($host, $port, $weight);
    }
    
    // add servers
    public function add_servers(Array $servers)
    {
        return $this->inst->addServers($servers);
    }
    
    // get option
    public function get_option($option)
    {
        return $this->inst->getOption($option);
    }
    
    // set option array
    public function set_option_array($options)
    {
        foreach ($options as $key => $val){
            $this->inst->setOption($key, $val);
        }
    }
    
    // result
    public function result()
    {
        $this->rs_code = $this->inst->getResultCode();
        $this->rs_msg = $this->inst->getResultMessage();
    }
    
    // get result
    public function get_result()
    {
        return array('rs_code' => $this->rs_code, 'rs_msg' => $this->rs_msg);
    }
    
    // add
    public function add($key, $value, $expiration=86400, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->add($key, $value, $expiration);
        } else {
            $rt = $this->inst->addByKey($server_key, $key, $value, $expiration);
        }
        
        // The Memcached::getResultCode() will return Memcached::RES_NOTSTORED if the key already exists.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // cas
    public function cas($cas_token, $key, $value, $expiration=86400, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->cas($cas_token, $key, $value, $expiration);
        } else {
            $rt = $this->inst->casByKey($cas_token, $server_key, $key, $value, $expiration);
        }
        
        // The Memcached::getResultCode() will return Memcached::RES_DATA_EXISTS if the item you are trying to store has been modified since you last fetched it.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // delete
    public function delete($key, $time=0, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->delete($key, $time);
        } else {
            $rt = $this->inst->deleteByKey($server_key, $key, $time);
        }
        
        // The Memcached::getResultCode() will return Memcached::RES_NOTFOUND if the key does not exist.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // replace
    public function replace($key, $value, $expiration=86400, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->replace($key, $value, $expiration);
        } else {
            $rt = $this->inst->replaceByKey($server_key, $key, $value, $expiration);
        }
        
        // The Memcached::getResultCode() will return Memcached::RES_NOTSTORED if the key does not exist.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // get
    public function get($key, $cache_cb=null, &$cas_token=0, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->get($key, $cache_cb, $cas_token);
        } else {
            $rt = $this->inst->getByKey($server_key, $key, $cache_cb, $cas_token);
        }
        
        // The Memcached::getResultCode() will return Memcached::RES_NOTFOUND if the key does not exist.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // set
    public function set($key, $value, $expiration=86400, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->set($key, $value, $expiration);
        } else {
            $rt = $this->inst->setByKey($server_key, $key, $value, $expiration);
        }
        
        // Use Memcached::getResultCode() if necessary.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // get multi
    public function get_multi(Array $keys, Array &$cas_tokens=array(), $flags=Memcached::GET_PRESERVE_ORDER, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->getMulti($keys, $cas_tokens, $flags);
        } else {
            $rt = $this->inst->getMultiByKey($server_key, $keys, $cas_tokens, $flags);
        }
        
        // Use Memcached::getResultCode() if necessary.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // set multi
    public function set_multi(Array $items, $expiration=86400, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->setMulti($items, $expiration);
        } else {
            $rt = $this->inst->setMultiByKey($server_key, $items, $expiration);
        }
        
        // Use Memcached::getResultCode() if necessary.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // prepend
    public function prepend($key, $value, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->prepend($key, $value);
        } else {
            $rt = $this->inst->prependByKey($server_key, $key, $value);
        }
        
        // The Memcached::getResultCode() will return Memcached::RES_NOTSTORED if the key does not exist.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // append
    public function append($key, $value, $server_key='')
    {
        if (empty($server_key)){
            $rt = $this->inst->append($key, $value);
        } else {
            $rt = $this->inst->appendByKey($server_key, $key, $value);
        }
        
        // The Memcached::getResultCode() will return Memcached::RES_NOTSTORED if the key does not exist.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // increment
    public function increment($key, $offset=1)
    {
        $rt = $this->inst->increment($key, $offset);
        
        // The Memcached::getResultCode() will return Memcached::RES_NOTFOUND if the key does not exist.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // decrement
    public function decrement($key, $offset=1)
    {
        $rt = $this->inst->decrement($key, $offset);
        
        // The Memcached::getResultCode() will return Memcached::RES_NOTFOUND if the key does not exist.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // flush
    public function flush($delay=0)
    {
        $rt = $this->inst->flush($delay);
        
        // Use Memcached::getResultCode() if necessary.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // get server by key
    public function get_server_by_key($server_key)
    {
        $rt = $this->inst->getServerByKey($server_key);
        
        // Use Memcached::getResultCode() if necessary.
        if ($rt === false){
            $this->result();
            $this->log_error();
        }
        
        return $rt;
    }
    
    // get server list
    public function get_server_list()
    {
        return $this->inst->getServerList();
    }
    
    // get stats
    public function get_stats()
    {
        return $this->inst->getStats();
    }
    
    // get version
    public function get_version()
    {
        return $this->inst->getVersion();
    }
    
    // @access private
    private function log_error($str='')
    {
        if ($this->logger != null){
            $this->logger->error('MEMCACHED rs_code: '.$this->rs_code.', rs_msg: '.$this->rs_msg.', info: '.$str, 'EMCCACHEMEMCACHE');
        }
    }
}