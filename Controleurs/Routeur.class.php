<?php

require "controleur.class.php";

class Routeur extends Controleur {


	public function genererPage()
	{
		require 'Controleurs/ctrl_' . $this->getAction() . '.php';
	}
}

