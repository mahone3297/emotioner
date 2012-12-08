<?php

// @date 2012-01-09
// @author mahone

require_once __DIR__ . '/../../src/core/class/EmcConfig.php';

class EmcConfigTest extends PHPUnit_Framework_TestCase
{
    private $path_config = null;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->path_config = array(
            __DIR__ . '/config/core/config.php',
            __DIR__ . '/config/plugin/config.php',
            __DIR__ . '/config/app/config/config.php'
        );
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        $this->path_config = null;
    }
    
    public function test_get()
    {
        $cfg = new EmcConfig($this->path_config);
        $this->assertEquals(array(
            'coreconfig' => 'coreconfig',
            'pluginoverrideconfig' => 'pluginhere',
            'appoverrideconfig' => 'apphere',
            'pluginconfig' => 'pluginconfig',
            'appconfig' => 'appconfig'
        ), $cfg->get());
        
        $this->assertEquals('coreconfig', $cfg->get('coreconfig'));
        $this->assertEquals(null, $cfg->get('null'));
    }
    
    public function test_set()
    {
        $cfg = new EmcConfig($this->path_config);
        $this->assertEquals(null, $cfg->get('settest'));
        $cfg->set('settest', 'test');
        $this->assertEquals('test', $cfg->get('settest'));
        
        $cfg->set(array(
            'settesta' => 'a',
            'settestb' => 'b',
            'settestc' => 'c',
        ));
        $this->assertEquals('a', $cfg->get('settesta'));
        $this->assertEquals('b', $cfg->get('settestb'));
        $this->assertEquals('c', $cfg->get('settestc'));
    }
}