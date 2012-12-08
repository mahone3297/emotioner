<?php

// @date 2012-01-10
// @author mahone

// 测试异常时，需要开启这些常量
// define('COREPATH', __DIR__ . '/../');
// define('PLUGINPATH', __DIR__ . '/http/plugin');
// define('APPPATH', __DIR__ . '/http/app');
// require_once __DIR__.'/../class/EmcGlobal.php';
require_once __DIR__ . '/../../src/core/class/EmcConfig.php';
require_once __DIR__ . '/../../src/core/class/EmcLog.php';
require_once __DIR__ . '/../../src/plugin/class/EmpHttp.php';

class EmpHttpTest extends PHPUnit_Framework_TestCase
{
    private $config = array(
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 10
    );
    
    public function test_get_normal()
    {
        $http = new EmpHttp($this->config);
        $this->assertEquals('http get test success',
            $http->get('http://devsite.com/http_test.php?action=get_normal'));
        
        $this->assertEquals('http get test success',
            $http->get('http://devsite.com/http_test.php?action=get_normal'));
    }
    
    // 比较耗时，暂时注释掉该 case
    public function _test_get_timeout()
    {
        $http = new EmpHttp($this->config);
        $http->get('http://devsite.com/http_test.php?action=get_timeout', array(
            CURLOPT_TIMEOUT => 1
        ));
        
        $this->assertEquals(28, $http->errno());
        $this->assertStringStartsWith('Operation timed out', $http->error());
        @unlink(APPPATH . '/log/emlog-'.date('Y-m-d').'.php');
    }
    
    public function test_post()
    {
        $http = new EmpHttp($this->config);
        
        $this->assertEquals('http post test success',
            $http->post('http://devsite.com/http_test.php?action=post_normal', array(
                'key1' => 'val1',
                'key2' => 'val2'
        )));
    }
}