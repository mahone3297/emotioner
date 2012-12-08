<?php

// @date 2012-02-14
// @author mahone

require_once __DIR__ . '/../../src/core/class/EmcConfig.php';
require_once __DIR__ . '/../../src/core/class/EmcGlobal.php';
require_once __DIR__ . '/../../src/core/class/EmcLog.php';

class EmcLogTest extends PHPUnit_Framework_TestCase
{
    private $log_file = '';
    private $log_config = null;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->log_config = array(
            'use_buffer' => false,
            'log_threshold' => EmcLog::WARNING,
            'log_path' => __DIR__ . '/log/app/log'
        );
        $this->log_file = sprintf("%s/emlog-%s.php", $this->log_config['log_path'], date('Y-m-d'));
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        foreach (glob($this->log_config['log_path'] . '/emlog-*.php') as $val){
            @unlink($val);
        }
        $this->log_config = null;
    }
    
    public function test_log_without_buffer()
    {
        $log = new EmcLog($this->log_config);
        $log->info('info log test');
        $this->assertEquals(true, file_exists($this->log_file));
        
        $log->info('info log test');
        $this->assertEquals(true, file_exists($this->log_file));
        @unlink($this->log_file);
        
        $log->error('error log test');
        $this->assertEquals(true, file_exists($this->log_file));
        @unlink($this->log_file);
        
        $log->warning('warning log test');
        $this->assertEquals(true, file_exists($this->log_file));
        @unlink($this->log_file);
        
        $log->debug('debug log test');
        $this->assertEquals(false, file_exists($this->log_file));
    }
    
    public function test_log_with_buffer()
    {
        $log = new EmcLog(array(
            'use_buffer' => true,
            'log_threshold' => EmcLog::ERROR,
            'log_path' => __DIR__ . '/log/app/log'
        ));
        
        $log->error(str_repeat('*', EmcLog::BUFFER_SIZE - 100));
        $this->assertEquals(false, file_exists($this->log_file));
        
        $log->error(str_repeat('*', 100));
        $this->assertEquals(true, file_exists($this->log_file));
        @unlink($this->log_file);
    }
    
    public function test_log_destruct()
    {
        $log = new EmcLog(array(
            'use_buffer' => true,
            'log_threshold' => EmcLog::ERROR,
            'log_path' => __DIR__ . '/log/app/log'
        ));
        
        $log->error('error log destruct test');
        $this->assertEquals(false, file_exists($this->log_file));
        
        $log = null;
        $this->assertEquals(true, file_exists($this->log_file));
        @unlink($this->log_file);
    }
}