<?php

// @date 2012-01-15
// @author mahone

require_once __DIR__ . '/../../src/core/class/EmcLoader.php';

class EmcLoaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        EmcLoader::init(array(
            __DIR__ . '/loader/core/class',
            __DIR__ . '/loader/plugin/class',
            __DIR__ . '/loader/app/class'
        ));
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        EmcLoader::clear();
    }
    
    public function test_file_not_found()
    {
        try {
            $ani = new Fnf();
            $this->assertEquals(true, false);
        } catch (Exception $e){
            $this->assertEquals('file not found: Fnf.php', $e->getMessage());
        }
    }
    
    public function test_class_not_found()
    {
        try {
            $ani = new Cnf();
            $this->assertEquals(true, false);
        } catch (Exception $e){
            $this->assertEquals('class not found: Cnf', $e->getMessage());
        }
    }
    
    public function test_autoload_app()
    {
        $ani = new Animal();
        $this->assertEquals('animal shout', $ani->shout());
        
        $ani = new Dog();
        $this->assertEquals('dog shout', $ani->shout());
    }
    
    public function test_autoload_core()
    {
        $test = new EmcUnittest();
        $this->assertEquals('Emc unittest', $test->unittest());
    }
    
    public function test_autoload_plugin()
    {
        $test = new EmpUnittest();
        $this->assertEquals('Emp unittest', $test->unittest());
    }
    
    public function test_view()
    {
        $loader = new EmcLoader(array(
            'view' => array(__DIR__ . '/loader/app/view')
        ));
        $msg = 'index_msg';
        $this->assertEquals('|index_start||a_start||b||a_end||index_end|index_msg', $loader->view('index.php', array(
            'msg' => $msg
        ), true));
    }
}