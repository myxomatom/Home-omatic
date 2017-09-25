<?php

require_once ("Modele.php");

class DHT22 extends Modele
{
	protected $bdd;

	public function get_data($debut, $fin)
	{
		$requete_sql = 'SELECT date_enregistrement , temperature FROM enreg_sonde_temp_hum WHERE date_enregistrement > :debut AND date_enregistrement < :fin';
		$param = array('debut' => $debut,
						'fin' => $fin);
		$reponse_req = $this->executerRequete($requete_sql,$param);
		$resultat = $reponse_req->fetchAll();

		return $resultat;
	}

	public function create_table_moyenne_h()
	{
		$requete_sql = 'CREATE TABLE IF NOT EXISTS moyenne_temp_h 
							(	dateHour DATETIME NOT NULL, 
								id_capteur INT UNSIGNED NOT NULL, 
								temperature_moyenne SMALLINT NOT NULL,  
								PRIMARY KEY (dateHour))
							ENGINE=INNODB ';
		$this->executerRequete($requete_sql);
	}

	public function calc_moyenne($debut,$type)
	{
		$fin = $debut + 1;
		if ($type == "jour")
		{
			$debut .= "00";
			$fin .= "00";
			$date_d = DateTime::createFromFormat('YmdH', $debut);
			$date_f = DateTime::createFromFormat('YmdH', $fin);
			$str_date_d = $date_d->format('Y/m/d H:00');
			$str_date_f = $date_f->format('Y/m/d H:00');

			$requete_sql = 'SELECT AVG(temperature) AS moyenne , MIN(temperature) AS min, MAX(temperature) AS max  
								FROM enreg_sonde_temp_hum WHERE date_enregistrement > :debut AND date_enregistrement < :fin';
			$param = array('debut' => $str_date_d,
							'fin' => $str_date_f);
			$reponse_req = $this->executerRequete($requete_sql,$param);
			$resultat = $reponse_req->fetch();
			return $resultat;
		}

		elseif ($type == "jour_h")
		{
			$resultat =[];
			for ($i = $debut . "00"; $i < $debut . "24" ; $i++)
			{
				$date_d = DateTime::createFromFormat('YmdH', $i);
				$date_f = DateTime::createFromFormat('YmdH', $i+1);
				$str_date_d = $date_d->format('Y/m/d H:00');
				$str_date_f = $date_f->format('Y/m/d H:00');

				$requete_sql = 'SELECT AVG(temperature) AS moyenne , MIN(temperature) AS min, MAX(temperature) AS max  
									FROM enreg_sonde_temp_hum WHERE date_enregistrement > :debut AND date_enregistrement < :fin';
				$param = array('debut' => $str_date_d,
								'fin' => $str_date_f);
				$reponse_req = $this->executerRequete($requete_sql,$param);
				$reponse = $reponse_req->fetch();
				array_push($resultat, $reponse);
			}
		return $resultat;
		}
	}

	function create_procedure_moyenne_j($date)
	{
		$requete_sql = 'DELIMITER |
						CREATE PROCEDURE calc_moyenne_jour (IN Date_Deb DATE)
						BEGIN
							SELECT AVG(temperature) AS moyenne , MIN(temperature) AS min, MAX(temperature) AS max  
									FROM enreg_sonde_temp_hum WHERE date_enregistrement > Date_Deb AND date_enregistrement < Date_Deb +1;
							INERT 

						END|
						DELIMITER;';
	}
}