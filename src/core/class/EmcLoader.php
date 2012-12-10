<?php

// @date 2012-04-19
// @author mahone

class EmcLoader
{
    private static $class_path = array();
    private static $loaded_class = array();
    
    private $ob_level = 0;
    private $view_path = array();
    
    // spl callback
    // @access private
    private static function spl_callback($cls_name)
    {
        $cls_name = ucfirst($cls_name);
        
        foreach (self::$class_path as $dir){
            $file_path = rtrim($dir, '/') . '/' . $cls_name . '.php';
            
            if (file_exists($file_path)){
                require_once($file_path);
                
                if ( ! class_exists($cls_name)){
                    throw new Exception('class not found: ' . $cls_name);
                }
                self::$loaded_class[$cls_name] = true;
                
                return ;
            }
        }
        
        throw new Exception('file not found: ' . $cls_name . '.php');
    }
    
    // init
    public static function init($path_config)
    {
        spl_autoload_register(array(__CLASS__, 'spl_callback'));
        
        self::register($path_config);
    }
    
    // register class path
    public static function register($path)
    {
        if (is_array($path)){
            self::$class_path = array_merge(self::$class_path, $path);
        } else {
            self::$class_path[] = $path;
        }
    }
    
    // clear class path
    public static function clear()
    {
        self::$class_path = array();
        self::$loaded_class = array();
    }
    
    public function __construct(Array $path_config)
    {
        $this->ob_level = ob_get_level();
        $this->view_path = $path_config['view'];
    }
    
    // apppend path
    public function append_path($path, $type)
    {
        switch ($type){
            case 'view':
                if (is_array($path)){
                    $this->view_path = array_merge($this->view_path, $path);
                } else {
                    $this->view_path[] = $path;
                }
                break;
            default:
                break;
        }
        
    }
    
    // view
    public function view($view, $var=array(), $return=false)
    {
        $file_exists = false;
        
        foreach ($this->view_path as $dir){
            $file_path = rtrim($dir, '/') . '/' . $view;
            if (file_exists($file_path)){
                $file_exists = true;
                break;
            }
        }
        
        if ( ! $file_exists){
            return '';
        }
        
        extract($var);
        
        ob_start();
        include($file_path);
        
        if ($return){
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
        
        // note: nested view. '+1' use for let content into output class.
        if (ob_get_level() > $this->ob_level + 1){
            ob_end_flush();
        } else {
            $content = ob_get_contents();
            EmcGlobal::get_output()->append_output($content);
            ob_end_clean();
        }
    }
}