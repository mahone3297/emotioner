<?php

// @date 2012-01-12
// @author mahone

define('COREPATH', __DIR__ . '/../../src/core/');
define('PLUGINPATH', __DIR__ . '/../../src/plugin');
define('APPPATH', __DIR__ . '/../../src/app');
require_once __DIR__ . '/../../src/core/class/EmcBenchmark.php';
require_once __DIR__ . '/../../src/core/class/EmcConfig.php';
require_once __DIR__ . '/../../src/core/class/EmcGlobal.php';
require_once __DIR__ . '/../../src/core/class/EmcInput.php';
require_once __DIR__ . '/../../src/core/class/EmcLoader.php';
require_once __DIR__ . '/../../src/core/class/EmcLog.php';
require_once __DIR__ . '/../../src/core/class/EmcMysqli.php';
require_once __DIR__ . '/../../src/core/class/EmcOutput.php';
require_once __DIR__ . '/../../src/core/class/EmcUri.php';

class EmcGlobalTest extends PHPUnit_Framework_TestCase
{
    // test all get method
    public function test_get()
    {
        $benchmark =& EmcGlobal::get_benchmark();
        $this->assertEquals(true, $benchmark instanceof EmcBenchmark);
        
        $config =& EmcGlobal::get_config();
        $this->assertEquals(true, $config instanceof EmcConfig);
        
        $input =& EmcGlobal::get_input();
        $this->assertEquals(true, $input instanceof EmcInput);
        
        $loader =& EmcGlobal::get_loader();
        $this->assertEquals(true, $loader instanceof EmcLoader);
        
        $log =& EmcGlobal::get_log();
        $this->assertEquals(true, $log instanceof EmcLog);
        
        $db =& EmcGlobal::get_db();
        $this->assertEquals(true, $db instanceof EmcMysqli);
        $db =& EmcGlobal::get_db('db_another');
        $this->assertEquals(true, $db instanceof EmcMysqli);
        
        $output =& EmcGlobal::get_output();
        $this->assertEquals(true, $output instanceof EmcOutput);
        
        $uri =& EmcGlobal::get_uri();
        $this->assertEquals(true, $uri instanceof EmcUri);
    }
    
    public function test_clear()
    {
        $benchmark =& EmcGlobal::get_benchmark();
        $this->assertEquals(true, $benchmark instanceof EmcBenchmark);
        
        EmcGlobal::clear('benchmark');
        EmcGlobal::clear();
    }
}