<?php

// @date 2012-02-21
// @author mahone

class EmpHttp
{
    private $logger = null;
    
    private $ch = null;
    private $errno = 0;
    private $errmsg = '';
    private $opt = array();
    
    public function __construct(Array $opt=array(), $logger=null)
    {
        $this->ch = curl_init();
        $this->set_opt_array($opt);
        
        $this->logger = $logger;
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    // close curl
    public function close()
    {
        if ($this->ch == null){
            return ;
        }
        
        curl_close($this->ch);
        $this->ch = null;
    }
    
    // set opt
    public function set_opt($option, $value)
    {
        $this->opt[$option] = $value;
    }
    
    // set opt array
    public function set_opt_array(Array $option_arr)
    {
        foreach ($option_arr as $key => $val){
            $this->opt[$key] = $val;
        }
    }
    
    // set error number
    public function errno()
    {
        if ($this->ch != null){
            $this->errno = curl_errno($this->ch);
        }
        return $this->errno;
    }
    
    // set error message
    public function error()
    {
        if ($this->ch != null){
            $this->errmsg = curl_error($this->ch);
        }
        return $this->errmsg;
    }
    
    // get curl info
    public function get_info($opt=null)
    {
        if ($opt == null){
            return curl_getinfo(self::$ch);
        } else {
            return curl_getinfo(self::$ch, $opt);
        }
    }
    
    // get
    public function get($url, $opt=null)
    {
        return $this->common_http('get', $url, $opt);
    }
    
    // post
    public function post($url, $data, $opt=null)
    {
        return $this->common_http('post', $url, $opt, $data);
    }
    
    // @access: private
    private function common_http($action, $url, $opt=null, $data=null)
    {
        switch ($action){
            case 'get':
                break;
            case 'post':
                $this->set_opt_array(array(
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $data
                ));
                break;
            default:
                break;
        }
        $this->set_opt(CURLOPT_URL, $url);
        if (isset($opt)){
            $this->set_opt_array($opt);
        }
        curl_setopt_array($this->ch, $this->opt);
        
        $rt = curl_exec($this->ch);
        if ($rt === false){
            $this->errno();
            $this->error();
            
            if ($this->logger != null){
                $this->logger->error('curl error: errno: '.$this->errno().', msg: '.$this->error().' url: '.$url, 'EMCHTTP');
            }
        }
        
        return $rt;
    }
}