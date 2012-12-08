<?php

// @date 2012-01-15
// @author mahone

require_once __DIR__ . '/../../src/core/class/EmcBenchmark.php';

class EmcBenchmarkTest extends PHPUnit_Framework_TestCase
{
    public function test_timer()
    {
        $mark = 'mark';
        
        EmcBenchmark::start($mark);
        usleep(300);
        $end1 = (float)EmcBenchmark::elapse($mark);
        $end2 = (float)EmcBenchmark::elapse($mark);
        $end3 = (float)EmcBenchmark::elapse($mark);
        
        $this->assertEquals(null, EmcBenchmark::elapse('anothermark'));
        $this->assertEquals(true, $end2 > $end1);
        $this->assertEquals(true, $end3 > $end2);
        
        EmcBenchmark::clear();
        $this->assertEquals(null, EmcBenchmark::elapse($mark));
    }
}