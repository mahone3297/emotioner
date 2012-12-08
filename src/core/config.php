<?php if ( ! defined('COREPATH')) exit('No direct script access allowed');

// @date: 2012-01-15
// @author: mahone

// default controller & method
$config['default_controller'] = 'index';
$config['default_method'] = 'index';

// class EmcLoader
$config['emcloader'] = array(
    'view' => array(APPPATH . '/view')
);

// class EmcLog
$config['emclog'] = array(
    'use_buffer' => false,
    'log_threshold' => EmcLog::ERROR,
    'log_path' => APPPATH . '/log'
);