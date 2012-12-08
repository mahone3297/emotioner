<?php

// @date 2011-11-12
// @author mahone

require_once __DIR__ . '/../../src/core/class/EmcGlobal.php';
require_once __DIR__ . '/../../src/core/class/EmcUri.php';

class EmcUriTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }
    
    public function test_cli_one_param()
    {
        // class
        $_SERVER['argc'] = 2;
        $_SERVER['argv'] = array(
            'EmcUriTest.php',
            'class'
        );
        $uri =& EmcGlobal::get_uri();
        $this->assertEquals('/class', $uri->get_uri_string());
        $this->assertEquals(array('class'), $uri->get_segment());
        
        // class method
        $_SERVER['argc'] = 2;
        $_SERVER['argv'] = array(
            'EmcUriTest.php',
            'class/method'
        );
        $uri->parse();
        $this->assertEquals('/class/method', $uri->get_uri_string());
        $this->assertEquals(array('class', 'method'), $uri->get_segment());
    }
    
    public function test_cli_two_param()
    {
        $_SERVER['argc'] = 3;
        $_SERVER['argv'] = array(
            'EmcUriTest.php',
            'class/method',
            'two'
        );
        $uri =& EmcGlobal::get_uri();
        $uri->parse();
        $this->assertEquals('/class/method', $uri->get_uri_string());
        $this->assertEquals(array('class', 'method'), $uri->get_segment());
    }
}