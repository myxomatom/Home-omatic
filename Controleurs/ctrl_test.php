<?php
include_once('Modeles/DHT22.php');
include_once('Modeles/wattmeter1ch.php');
include_once('Modeles/Test_sensor.php');

$capteur = new wattmeter1ch();

$capteur->calc_daily_cumulation(null,null);
