<?php

// @date: 2012-01-05
// @author: mahone

define('ENV', 'dev');
if (defined('ENV')){
    switch (ENV){
        case 'dev':
            error_reporting(E_ALL | E_STRICT);
            break;
        case 'pro':
            error_reporting(E_ALL & ~E_DEPRECATED);
            break;
        default:
            error_reporting(E_ALL & ~E_NOTICE);
            break;
    }
}

$core_path = '../../core';
$plugin_path = '../../plugin';
$app_path = '../../app';

// Set the current directory correctly for CLI requests
if (defined('STDIN')){
    chdir(dirname(__FILE__));
}

foreach (array(
    'core_path' => $core_path,
    'plugin_path' => $plugin_path,
    'app_path' => $app_path) as $key => $val){
    
    $rt = realpath($val);
    if ($rt !== false){
        $$key = $rt;
    }
    
    $$key = rtrim($$key, '/');
    if ( ! is_dir($$key)){
        exit("wrong $key: " . $$key . "\n");
    }
}

define('COREPATH', $core_path);
define('PLUGINPATH', $plugin_path);
define('APPPATH', $app_path);

// define path constant done, let's go...
require_once(COREPATH . '/EmcBoot.php');