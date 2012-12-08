<?php

// @date: 2011-11-03
// @author: mahone

require_once __DIR__.'/../class/LiveBox.php';

class LiveBoxTestTest extends PHPUnit_Framework_TestCase{
	
	public function test_register(){
		$inst = new LiveBox();
		$hosts = array(
			array(
				'host'=>'192.168.1.1',
				'port'=>3306,
				'user'=>'root',
				'pass'=>'mysql'
			),
			array(
				'host'=>'192.168.1.2',
				'port'=>3306,
				'user'=>'root',
				'pass'=>'mysql'
			),
			array(
				'host'=>'192.168.1.3',
				'port'=>3306,
				'user'=>'root',
				'pass'=>'mysql'
			),
			array(
				'host'=>'192.168.1.4',
				'port'=>3306,
				'user'=>'root',
				'pass'=>'mysql'
			),
			array(
				'host'=>'192.168.1.5',
				'port'=>3306,
				'user'=>'root',
				'pass'=>'mysql'
			)
		);
		foreach ($hosts as $val){
			$inst->register($val);
		}
		
		// test register
		foreach ($hosts as $val){
			$this->assertEquals(true, in_array($val, $inst->get_host()));
		}
		
		// test unregister
		$inst->unregister($hosts[1]);
		$this->assertEquals(false, in_array($hosts[1], $inst->get_host()) && in_array($hosts[1], $inst->get_off()));
		$inst->unregister($hosts[0]);
		$this->assertEquals(false, in_array($hosts[0], $inst->get_host()) && in_array($hosts[0], $inst->get_off()));
		
		// test set_off
		$inst->set_off($hosts[3]);
		$this->assertEquals(false, in_array($hosts[3], $inst->get_host()));
		$this->assertEquals(true, in_array($hosts[3], $inst->get_off()));
		
		// test clear
		$inst->clear();
		$this->assertEquals(array(), $inst->get_host());
		$this->assertEquals(array(), $inst->get_off());
	}
}