<?php

// @date 2012-01-14
// @author mahone

class EmcConfig
{
    private $config = array();
    
    public function __construct(Array $path_config)
    {
        foreach ($path_config as $filepath){
            if (file_exists($filepath)){
                require($filepath);
                if (!isset($config) || !is_array($config)){
                    continue;
                }
                $this->set($config);
                unset($config);
            }
        }
    }
    
    // get
    // 如果 $key 为空，则返回整个数组
    public function get($key='')
    {
        if ($key == ''){
            return $this->config;
        }
        
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
    
    // set
    // $key 如果为 array, 则忽略 $val, 直接 array 赋值
    public function set($key, $val='')
    {
        if (is_array($key)){
            foreach ($key as $in_key => $in_val){
                $this->config[$in_key] = $in_val;
            }
            return ;
        }
        
        $this->config[$key] = $val;
    }
}