<?php

abstract class Controleur {

	public function getAction()
	{
		if (isset($_GET['action']))
		{
			$action = htmlspecialchars($_GET['action']);
			return $action;
		}
		else
		{
			return "default";
		}
	}
}