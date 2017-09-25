<?php

include_once("Sensor.abstract.class.php");

class DHT22 extends Sensor
{
	function __construct()
	{
		$this->measure = ["temperature","humidite"];
		$this->type = get_class($this); 
		/*$this->graph_map = [["titre" => "Suivi de la température",
							"mesures" => "temperature"],
							["titre" => "Suivi de l'humidité",
							"mesures" => "humidite"]];*/
		$this->graph_map = [["titre" => "Suivi de la température",
							"mesures" => ["temperature","humidite"],
							"dualYaxis" => false,
							"type" => ["mesure","mesure"]]];
	}
}