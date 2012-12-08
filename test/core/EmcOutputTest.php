<?php

// @date 2011-11-14
// @author mahone

require_once __DIR__ . '/../../src/core/class/EmcOutput.php';

class EmcOutputTest extends PHPUnit_Framework_TestCase
{
    public function test_header()
    {
        $output = new EmcOutput();
        $output->set_header('key1', false);
        $this->assertEquals(array(
            'key1' => false
        ), $output->get_header());
        
        $output->set_header('key1', true);
        $output->set_header('key2', true);
        $output->set_header('key3', 'val3');
        $output->set_header('key4', '');
        $this->assertEquals(array(
            'key1' => true,
            'key2' => true,
            'key3' => true,
            'key4' => false
        ), $output->get_header());
    }
    
    public function test_output()
    {
        $output = new EmcOutput();
        $output->append_output('hello');
        $output->append_output(' world');
        $this->assertEquals('hello world', $output->get_output());
        
        $output->set_output('reset again');
        $this->assertEquals('reset again', $output->get_output());
        
        $output->append_output(', test display');
        ob_start();
        $output->display();
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('reset again, test display', $content);
    }
}