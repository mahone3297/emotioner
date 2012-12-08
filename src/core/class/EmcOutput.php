<?php

// @date 2012-01-14
// @author mahone

class EmcOutput
{
    // whether or not to parse variables like {elapsed_time} and {memory_usage}
    protected $parse_exec_vars = true;
    
    private $output = '';
    private $header = array();
    
    public function __construct()
    {
        // todo
    }
    
    // get output
    public function get_output()
    {
        return $this->output;
    }
    
    // set output
    public function set_output($output)
    {
        $this->output = $output;
    }
    
    // append output
    public function append_output($output)
    {
        $this->output .= $output;
    }
    
    // set header
    public function set_header($header, $replace=true)
    {
        $this->header[$header] = (bool)$replace;
    }
    
    // get header
    public function get_header()
    {
        return $this->header;
    }
    
    // display
    public function display()
    {
        if ($this->parse_exec_vars){
            $elapse_time = EmcGlobal::get_benchmark()->elapse('Em_start');
            $memory     = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';

            $this->output = str_replace('{elapsed_time}', $elapse_time, $this->output);
            $this->output = str_replace('{memory_usage}', $memory, $this->output);
        }
        
        foreach ($this->header as $header => $replace){
            header($header, $replace);
        }
        
        echo $this->output;
    }
}