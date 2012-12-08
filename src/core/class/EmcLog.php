<?php

// @date 2012-02-14
// @author mahone

class EmcLog
{
    private $use_buffer = false;
    private $buffer_data = '';
    private $log_threshold = self::WARNING;
    private $log_path = '';
    private $enable = false;
    
    const INFO = 1;
    const ERROR = 2;
    const WARNING = 3;
    const DEBUG = 4;
    
    const BUFFER_SIZE = 4096;
    
    public function __construct(Array $config)
    {
        $this->buffer_data = '';
        $this->use_buffer = $config['use_buffer'];
        $this->log_threshold = $config['log_threshold'];
        
        $this->log_path = $config['log_path'];
        if (is_dir($this->log_path) && is_writable($this->log_path)===true){
            $this->enable = true;
        }
    }
    
    // info
    public function info($msg, $token='NULL')
    {
        if ($this->enable===true && $this->log_threshold>=self::INFO){
            $this->log($token, 'INFO', $msg);
        }
    }
    
    // error
    public function error($msg, $token='NULL')
    {
        if ($this->enable===true && $this->log_threshold>=self::ERROR){
            $this->log($token, 'ERROR', $msg);
        }
    }
    
    // warning
    public function warning($msg, $token='NULL')
    {
        if ($this->enable===true && $this->log_threshold>=self::WARNING){
            $this->log($token, 'WARNING', $msg);
        }
    }
    
    // debug
    public function debug($msg, $token='NULL')
    {
        if ($this->enable===true && $this->log_threshold>=self::DEBUG){
            $this->log($token, 'DEBUG', $msg);
        }
    }
    
    // log
    private function log($token, $level, $msg)
    {
        if ( ! is_scalar($msg)){
            $msg = json_encode($msg);
        }
        $this->buffer_data .= sprintf("%s - %s - %s --> %s\n", date('Y-m-d H:i:s'), strtoupper($token), $level, $msg);
        if ($this->use_buffer===false || strlen($this->buffer_data)>=self::BUFFER_SIZE){
            $this->flush();
        }
    }
    
    // 写入数据到文件
    private function flush()
    {
        if (strlen($this->buffer_data) > 0){
            $filepath = sprintf("%s/emlog-%s.php", $this->log_path, date('Y-m-d'));
            if ( ! file_exists($filepath)){
                $this->buffer_data = "<?php  if ( ! defined('COREPATH')) exit('No direct script access allowed'); ?>\n\n"
                    . $this->buffer_data;
            }
            @file_put_contents($filepath, $this->buffer_data, FILE_APPEND | LOCK_EX);
            $this->buffer_data = '';
        }
    }
    
    // 对象析构时，如果使用 buffer, 将 buffer 数据 flush
    public function __destruct()
    {
        $this->flush();
    }
}