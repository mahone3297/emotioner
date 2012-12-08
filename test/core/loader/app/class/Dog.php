<?php

class Dog extends Animal
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function shout()
	{
		return 'dog shout';
	}
}