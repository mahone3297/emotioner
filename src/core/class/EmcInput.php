<?php

// @date 2012-01-14
// @author mahone

class EmcInput
{
    public function __construct()
    {
        // todo
    }
    
    // get
    public function get($key='')
    {
        return $this->fetch_from_array($_GET, $key);
    }
    
    // post
    public function post($key='')
    {
        return $this->fetch_from_array($_POST, $key);
    }
    
    // cookie
    public function cookie($key='')
    {
        return $this->fetch_from_array($_COOKIE, $key);
    }
    
    // session
    public function session($key='')
    {
        return $this->fetch_from_array($_SESSION, $key);
    }
    
    // server
    public function server($key='')
    {
        return $this->fetch_from_array($_SERVER, $key);
    }
    
    // @access: private
    // if $key equals '', return whole array
    private function fetch_from_array($array, $key='')
    {
        if (strlen($key) == 0){
            return $array;
        }
        
        if (isset($array[$key])){
            return $array[$key];
        }
        
        return null;
    }
    
    // get client ip
    public function client_ip()
    {
        $ip = '0.0.0.0';
        if ($this->server('REMOTE_ADDR') != null){
            $ip = $this->server('REMOTE_ADDR');
        } else if ($this->server('HTTP_CLIENT_IP') != null){
            $ip = $this->server('HTTP_CLIENT_IP');
        } else if ($this->server('HTTP_X_FORWARDED_FOR') != null){
            $ip = $this->server('HTTP_X_FORWARDED_FOR');
        }
        
        return $ip;
    }
    
    // get user agent
    public function user_agent()
    {
        return $this->server('HTTP_USER_AGENT');
    }
    
    // is ajax request
    public function is_ajax_request()
    {
        return ($this->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest');
    }
    
    // is cli request
    public function is_cli_request()
    {
        return (php_sapi_name() == 'cli') || defined('STDIN');
    }
}