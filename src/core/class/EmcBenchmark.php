<?php

// @date 2012-01-14
// @author mahone

class EmcBenchmark
{
    private static $timer = array();
    
    // start
    public static function start($key)
    {
        self::$timer[$key] = microtime(true);
    }
    
    // elapse
    public static function elapse($key)
    {
        if ( ! isset(self::$timer[$key])){
            return null;
        }
        
        $begin = self::$timer[$key];
        $end = microtime(true);
        
        return number_format($end - $begin, 6);
    }
    
    // clear
    public static function clear($key='')
    {
        if (empty($key)){
            self::$timer = array();
            return null;
        }
        
        unset(self::$timer[$key]);
    }
}