<?php

// @date 2012-02-21
// @author mahone

class EmcGlobal
{
    private static $benchmark = null;
    private static $config = null;
    private static $input = null;
    private static $loader = null;
    private static $log = null;
    private static $db = null;
    private static $db_key = '';
    private static $output = null;
    private static $uri = null;
    
    // get benchmark
    public static function &get_benchmark()
    {
        if (self::$benchmark == null){
            self::$benchmark = new EmcBenchmark();
        }
        
        return self::$benchmark;
    }
    
    // get config
    public static function &get_config()
    {
        if (self::$config == null){
            self::$config = new EmcConfig(array(
                COREPATH . '/config.php',
                PLUGINPATH . '/config.php',
                APPPATH . '/config/config.php'
            ));
        }
        
        return self::$config;
    }
    
    // get input
    public static function &get_input()
    {
        if (self::$input == null){
            self::$input = new EmcInput();
        }
        
        return self::$input;
    }
    
    public static function &get_loader()
    {
        $cfg =& self::get_config();
        
        if (self::$loader == null){
            self::$loader = new EmcLoader($cfg->get('emcloader'));
        }
        
        return self::$loader;
    }
    
    // get log
    public static function &get_log()
    {
        $cfg =& self::get_config();
        
        if (self::$log == null){
            self::$log = new EmcLog($cfg->get('emclog'));
        }
        
        return self::$log;
    }
    
    // get db
    public static function &get_db($key='db_default')
    {
        $cfg =& self::get_config();
        
        // db instance exists & the connection is just what you want, return.
        if (self::$db!=null && self::$db_key==$key){
            return self::$db;
        }
        
        // db instance exists, disconnect.
        if (self::$db != null){
            self::$db->disconnect();
            self::$db = null;
        }
        
        // new db instance.
        $config = $cfg->get($key);
        switch ($config['driver']){
            case 'mysql':
                self::$db = new EmcMysql($cfg->get($key), self::get_log());
                self::$db_key = $key;
                break;
            case 'mysqli':
                self::$db = new EmcMysqli($cfg->get($key), self::get_log());
                self::$db_key = $key;
                break;
            default:
                self::$db = new EmcMysql($cfg->get($key), self::get_log());
                self::$db_key = $key;
                break;
        }
        
        return self::$db;
    }
    
    // get output
    public static function &get_output()
    {
        if (self::$output == null){
            self::$output = new EmcOutput();
        }
        
        return self::$output;
    }
    
    // get uri
    public static function &get_uri()
    {
        if (self::$uri == null){
            self::$uri = new EmcUri();
        }
        
        return self::$uri;
    }
    
    // clear
    public static function clear($key='')
    {
        if (empty($key)){
            self::$benchmark = null;
            self::$config = null;
            self::$input = null;
            self::$loader = null;
            self::$log = null;
            self::$db = null;
            self::$output = null;
            self::$uri = null;
        } else {
            if (isset(self::$$key)){
                self::$$key = null;
            }
        }
    }
}