<?php

include_once("Sensor.abstract.class.php");

class wattmeter1ch extends Sensor
{
	function __construct()
	{
		$this->measure = ["kWh","W","Wmin","Wmax"];
		$this->type = get_class($this); 
		$this->graph_map = [["titre" => "Suivi du nombre mini et maxi de watts consommés",
							"mesures" => ["Wmin","Wmax"],
							"type" => ["mesure","mesure"]],
							["titre" => "Nombre de kWh consommés pendant la période",
							"mesures" => "kWh",
							"type" => "compteur"],
                            ["titre" => "Nombre de watts consommés",
                            "mesures" => "W",
                            "type" => "mesure"]];
	}
}