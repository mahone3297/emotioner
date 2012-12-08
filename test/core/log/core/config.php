<?php  if ( ! defined('COREPATH')) exit('No direct script access allowed');

// @date: 2012-01-09
// @author: mahone

// class EmcLog
$config['emlog'] = array(
    'use_buffer' => false,
    'log_threshold' => EmcLog::INFO,
    'log_path' => APPPATH . '/log'
);