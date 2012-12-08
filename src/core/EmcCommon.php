<?php  if ( ! defined('COREPATH')) exit('No direct script access allowed');

// @date: 2012-01-06
// @author: mahone

// em_error_handler
if ( ! function_exists('em_error_handler')){
    function em_error_handler($errno, $errstr, $errfile, $errline)
    {
        // if ( ! (error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            // return false;
        // }
        
        $str = "errno: $errno, errstr: $errstr, errfile: $errfile, errline: $errline\n";
        echo $str;
        
        if ( ! class_exists('EmcGlobal')){
            require_once COREPATH . '/class/EmcGlobal.php';
        }
        EmcGlobal::get_log()->error($str, 'EM_ERROR_HANDLER');
        
        return false;
    }
}

// em_exception_handler
if ( ! function_exists('em_exception_handler')){
    function em_exception_handler($exception)
    {
        $str = sprintf("expcode: %s, expmsg: %s, expfile: %s, expline: %s, exptrace: %s\n",
            $exception->getCode(), $exception->getMessage(), $exception->getFile(),
            $exception->getLine(), $exception->getTraceAsString());
        echo $str;
        
        if ( ! class_exists('EmcGlobal')){
            require_once COREPATH . '/class/EmcGlobal.php';
        }
        EmcGlobal::get_log()->error($str, 'EM_EXCEPTION_HANDLER');
    }
}

// url base_url
if ( ! function_exists('base_url')){
    function base_url($uri='')
    {
        $cfg =& EmcGlobal::get_config();
        
        if ($uri == ''){
            return rtrim($cfg->get('base_url'), '/');
        }
        
        return rtrim($cfg->get('base_url'), '/') . '/' . ltrim($uri, '/');
    }
}

// url site_url
if ( ! function_exists('site_url')){
    function site_url($uri)
    {
        $cfg =& EmcGlobal::get_config();
        return rtrim($cfg->get('base_url'), '/') . '/' . $cfg->get('index_page') . '/' . ltrim($uri, '/');
    }
}

// url redirect
if ( ! function_exists('redirect')){
    function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        if ( ! preg_match('#^https?://#i', $uri)){
            $uri = rtrim(EmcGlobal::get_config()->get('base_url'), '/') . '/' . ltrim($uri, '/');
        }
        
        switch($method)
        {
            case 'refresh':
                header("Refresh:0;url=".$uri);
                break;
            default:
                header("Location: ".$uri, true, $http_response_code);
                break;
        }
        exit;
    }
}