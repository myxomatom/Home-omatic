<?php
include_once('Modeles/DHT22.php');
include_once('Modeles/wattmeter1ch.php');
include_once('Modeles/Test_sensor.php');

$capteur = new Sensor();
$liste = $capteur->get_sensors();
$capteurs = [];
$entry['capteurs'] = [];

foreach ($liste as $capteur => $value) {
	$capteur = new $value['type'];
	$graph_map = $capteur->get_graph_map();
	if (is_null($graph_map))
	{
		$mesures = $capteur->get_measure();
		$capteurs = [$value['id'],$value['type'],$mesures];
		array_push($entry['capteurs'], $capteurs);
	}
	else
	{
		foreach ($graph_map as $graph) {
			$mesure = null;			
			if (is_array($graph['mesures']))
			{
				foreach ($graph['mesures'] as $nom_mesure) {
					(!isset($mesure))?$mesure = $nom_mesure : $mesure .= " ".$nom_mesure;
				}
				$mesure = [$mesure];
				$capteurs = [$value['id'],$value['type'],$mesure, $graph['titre']];
				(isset($graph['dualYaxis']) && $graph['dualYaxis']==true ) ? array_push($capteurs ,'dualYaxis' ) : null;
				$reference['titre_graph'] = $graph['titre'];
				array_push($entry['capteurs'], $capteurs);
			}
			else
			{
				$mesure = [$graph['mesures']];
				$capteurs = [$value['id'],$value['type'],$mesure, $graph['titre']];
				$reference['titre_graph'] = $graph['titre'];
				array_push($entry['capteurs'], $capteurs);
			}
		}
	}
}

require_once("Vues/Vue_graphique.php");
require_once("Vues/Gabarit.php");