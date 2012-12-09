<?php  if ( ! defined('COREPATH')) exit('No direct script access allowed');

// @date: 2012-01-15
// @author: mahone

$config['base_url']	= 'http://myem.com/';
$config['index_page'] = 'index.php';

// database config
$config['db_default'] = array(
    'master' => array(
        'host' => 'master',
        'username' => 'username',
        'password' => 'password',
        'database' => 'test',
        'port' => 3306,
        'charset' => 'utf8',
        'timeout' => 5
    ),
    // 'slave' = array(
            // array(
            // 'host' => 'slave',
            // 'username' => 'username',
            // 'password' => 'password',
            // 'database' => 'test',
            // 'port' => 3306,
            // 'charset' => 'utf8',
            // 'timeout' => 5
        // ), array(
            // 'host' => 'slave',
            // 'username' => 'username',
            // 'password' => 'password',
            // 'database' => 'test',
            // 'port' => 3306,
            // 'charset' => 'utf8',
            // 'timeout' => 5
        // )
    // )
);

$config['db_another'] = array(
    'master' => array(
        'host' => 'master',
        'username' => 'username',
        'password' => 'password',
        'database' => 'test',
        'port' => 3306,
        'charset' => 'utf8',
        'timeout' => 5
    )
);