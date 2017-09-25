<?php
session_start();
require_once("../Modeles/DHT22.php");
require_once("../Modeles/wattmeter1ch.php");

if (isset($_GET['debut']) && isset($_GET['fin']) && isset($_GET['donnee']) && isset($_GET['capteur'])  && isset($_GET['type']))
{
	$id_capteur = $_GET['capteur'];
	$type =  $_GET['type'];
	$type_donnee = $_GET['donnee'];
	$capteur = new $type;
	$releve = $capteur->get_data($_GET['debut'],$_GET['fin'],$id_capteur,$type_donnee);
	$measure_names = $capteur->get_measure();
	$releve_json = $capteur->data_to_json($releve, $type_donnee, $measure_names);
	header('Content-Type: application/json; charset=utf-8');
  header('Access-Control-Allow-Origin: *');
	echo json_encode($releve_json);
}
