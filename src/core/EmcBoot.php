<?php  if ( ! defined('COREPATH')) exit('No direct script access allowed');

// @date: 2012-01-11
// @author: mahone

// define version
define('EMCVERSION', '0.0.1');

require(COREPATH . '/EmcCommon.php');

// set error & exception handler
set_error_handler('em_error_handler');
set_exception_handler('em_exception_handler');

// add loader
require_once(COREPATH . '/class/EmcLoader.php');
EmcLoader::init(array(COREPATH.'/class', PLUGINPATH.'/class', APPPATH.'/class', APPPATH.'/controller', APPPATH.'/model'));

EmcGlobal::get_benchmark()->start('Em_start');

$seg =  EmcGlobal::get_uri()->get_segment();
$class = ucfirst(isset($seg[0]) ? $seg[0] : EmcGlobal::get_config()->get('default_controller'));
$method = isset($seg[1]) ? $seg[1] : EmcGlobal::get_config()->get('default_method');

try {
    $inst = new $class();
} catch (Exception $e){
    echo 'omg...you got the error controler...';
    return ;
}

if ( ! method_exists($inst, $method)){
    echo 'omg...you got the error method...';
    return ;
}
call_user_func_array(array($inst, $method), array_slice($seg, 2));

EmcGlobal::get_output()->display();