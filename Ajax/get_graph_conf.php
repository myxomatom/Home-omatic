<?php
session_start();
require_once("../Modeles/DHT22.php");
require_once("../Modeles/wattmeter1ch.php");

if (isset($_GET['capteur']) && isset($_GET['type'])){
	$id_capteur = $_GET['capteur'];
	$type =  $_GET['type'];
	$capteur = new $type;
	$configuration = $capteur->get_graph_config($id_capteur);
	$configuration = explode(';', $configuration['couleurs']);
	$color_json =[];
	foreach ($configuration as $color)
				{array_push($color_json,$color);}
	

	header('Content-Type: application/json');
	echo json_encode($color_json);
}