<?php

include_once("Sensor.abstract.class.php");

class Test_Sensor extends Sensor
{
	function __construct()
	{
		$this->measure = ["kWh"];
		$this->type = "test_sensor"; 
	}
}