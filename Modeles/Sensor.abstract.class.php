<?php

require_once ("Modele.php");

class Sensor extends Modele
{
	protected $type;
	protected $tables;
	protected $measure;
	protected $description;
	protected $graph_map = null;

	public function get_sensors()
	{
		$requete_sql = 'SELECT * FROM Sensors';
		$reponse_req = $this->executerRequete($requete_sql);
		return $reponse_req->fetchAll();
	}

	public function get_measure()
	{
		return $this->measure;
	}

	public function get_graph_map()
	{
		if ($this->graph_map != null)
		return $this->graph_map;
	}

	public function get_data($debut, $fin, $id_capteur, $data_type)
	{
		$select_part = "SELECT date_enregistrement";
		$select_part .= ", " . $data_type;
		$from_part = ' FROM '.$this->type;
		$where_part = ' WHERE date_enregistrement > :debut AND date_enregistrement < :fin AND id_capteur = :id_capteur';
		$requete_sql = $select_part . $from_part . $where_part;
		$param = array('debut' => $debut,
						'fin' => $fin,
						'id_capteur' => $id_capteur);
		$reponse_req = $this->executerRequete($requete_sql,$param);
		$resultat = $reponse_req->fetchAll();
		return $resultat;
	}

	public function data_to_json($data, $type_donnee, $measure_names)
	{
		$releve_json = array();
		foreach ($data as list($date, ${$type_donnee}))
				{if (${$type_donnee}!= null){
					array_push($releve_json,['x' => $date , 'y' => ${$type_donnee}]);}}
		return $releve_json;
	}

	public function get_graph_config($capteur_ID){
		$requete_sql = "SELECT couleurs FROM graph_config WHERE ID_capteur = :capteur_ID";
		$param = array('capteur_ID' => $capteur_ID);
		return $this->executerRequete($requete_sql,$param)->fetch();
	}

	public function register_sensor($serial)
	{
		/*
		****************************************************************************
		****************CREATE TABLES IF NOT EXISTS ********************************
		****************************************************************************

		****************CREATE GENERAL SENSORS TABLE IF NOT EXISTS *********************
		 */
		$create_part = 'CREATE TABLE IF NOT EXISTS Sensors
							(id INT UNSIGNED NOT NULL AUTO_INCREMENT,
							 serial_number VARCHAR(50),
							 type VARCHAR(50),
							 record_date DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)) ENGINE=INNODB';
		$requete_sql = $create_part;
		$this->executerRequete($requete_sql);
		/*
		****************CREATE SENSOR TYPE TABLE IF NOT EXISTS *********************
		 */
		$create_part = 'CREATE TABLE IF NOT EXISTS ' . $this->type;
		$values_part = ' (	id INT UNSIGNED NOT NULL AUTO_INCREMENT, ';
		foreach ($this->measure as $value)
			{$values_part .= $value . ' FLOAT NOT NULL, ';}
		$end_part = 'record_date DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)) ENGINE=INNODB';
		$requete_sql = $create_part . $values_part . $end_part;
		$this->executerRequete($requete_sql);

		/*
		****************CREATE HOURLY AVERAGE FOR SENSOR TYPE TABLE IF NOT EXISTS *********************
		 */
		$create_part = 'CREATE TABLE IF NOT EXISTS ' . $this->type .'_H_AVG'  ;
		$values_part =		'(	dateHour DATETIME NOT NULL,
								sensor_id INT UNSIGNED NOT NULL,';
		foreach ($this->measure as $value)
			{$values_part .= $value . '_moyenne FLOAT NOT NULL, '. $value . '_min FLOAT NOT NULL, '.$value.'_max FLOAT NOT NULL, ' ;}

		$end_part =	'PRIMARY KEY (dateHour,sensor_id))
							ENGINE=INNODB ';
		$requete_sql = $create_part . $values_part . $end_part;
		$this->executerRequete($requete_sql);

		/*
		****************CREATE DAILY AVERAGE FOR SENSOR TYPE TABLE IF NOT EXISTS *********************
		*/
		$create_part = 'CREATE TABLE IF NOT EXISTS ' . $this->type .'_DAY_AVG'  ;
		$values_part =		'(	date DATE NOT NULL,
								sensor_id INT UNSIGNED NOT NULL,';
		foreach ($this->measure as $value)
			{$values_part .= $value . '_moyenne FLOAT NOT NULL, ' . $value . '_min FLOAT NOT NULL, '.$value.'_max FLOAT NOT NULL, ';}

		$end_part =	'PRIMARY KEY (date,sensor_id))
							ENGINE=INNODB ';
		$requete_sql = $create_part . $values_part . $end_part;
		$this->executerRequete($requete_sql);
		/*
		****************************************************************************
		****************REGISTER SENSOR IN GENERAL SENSORS TABLE *******************
		****************************************************************************
		*/
		$insert_req = 'INSERT INTO Sensors (serial_number, type)
							VALUES (:serial_number, :type)';
		$param = array('serial_number' => $serial,
						'type' => $this->type);
		$this->executerRequete($insert_req, $param);
	}

public function calc_moyenne($debut,$type,$sensor_id)
	{
		/*
			@param $debut 	    -> Integer 	  : date pour laquelle les moyennes sont calculées au format "mySQL" (par exemple pour le 28 octobre 2016 : 20161028)
			@param $type 		    -> String 		: type de moyenne à calculer: journalière ou horraire (jour ou jour_h)
			@param $sensor_id 	-> Integer 		: reference du capteur pour le calcul de la moyenne
		*/
		$fin = $debut + 1;
		if ($type == "jour")
		{
			// Mise en forme des dates au format adapté
			$debut .= "00";
			$fin .= "00";
			$date_d = DateTime::createFromFormat('YmdH', $debut);
			$date_f = DateTime::createFromFormat('YmdH', $fin);
			$str_date_d = $date_d->format('Y/m/d H:00');
			$str_date_f = $date_f->format('Y/m/d H:00');

			//Generation du tableau de parametres pour la requete SQL
			$param = array(	'debut' => $str_date_d,
							'fin' => $str_date_f,
							'id_capteur' => $sensor_id);

			//Verification de l'existance de données avant le calcul de la moyenne
			$requete_sql = 'SELECT * FROM '.$this->type.' WHERE date_enregistrement > :debut AND date_enregistrement < :fin AND id_capteur = :id_capteur';
			if ($this->executerRequete($requete_sql,$param)->fetch())
			{	//**************************************************************************
				//Si le resultat n'est pas vide, calcul de la moyenne pour la periode testée
				// Conception de la requete
				$requete_sql = $this->forge_SELECT_AVG();
				$reponse_req = $this->executerRequete($requete_sql,$param);
				$resultat = $reponse_req->fetch();

				//**************************************************
				//Insertion du resultat dans la table correspondante
				//Conception de la requete
				$table = "_DAY_AVG";
				$date_record = $date_d->format('Ymd');
				$requete_sql = $this->forge_INSERT_AVG($table,$date_record,$sensor_id,$resultat);
				//Execution de la requete
				$this->executerRequete($requete_sql);
				//******************************************************
			}
		}

		elseif ($type == "jour_h")
		{

			$resultat =[];
			for ($i = $debut . "00"; $i < $debut . "24" ; $i++)
			{
				// Mise en forme des dates au format adapté
				$date_d = DateTime::createFromFormat('YmdH', $i);
				$date_f = DateTime::createFromFormat('YmdH', $i+1);
				$str_date_d = $date_d->format('Y/m/d H:00');
				$str_date_f = $date_f->format('Y/m/d H:00');

				//Generation du tableau de parametres pour la requete SQL
				$param = array('debut' => $str_date_d,
								'fin' => $str_date_f,
								'id_capteur' => $sensor_id);
				//Verification de l'existance de données avant le calcul de la moyenne
				$requete_sql = 'SELECT * FROM '.$this->type.' WHERE date_enregistrement > :debut AND date_enregistrement < :fin AND id_capteur = :id_capteur';
				if ($this->executerRequete($requete_sql,$param)->fetch())
				{
				/*
				$requete_sql = 'SELECT AVG(temperature) AS moyenne , MIN(temperature) AS min, MAX(temperature) AS max
									FROM enreg_sonde_temp_hum WHERE date_enregistrement > :debut AND date_enregistrement < :fin';
				*/
				$requete_sql = $this->forge_SELECT_AVG();
				$reponse_req = $this->executerRequete($requete_sql,$param);
				$reponse = $reponse_req->fetch();

				//**************************************************
				//Insertion du resultat dans la table correspondante
				//Conception de la requete
				$table = "_H_AVG";
				$date_record = $date_d->format('YmdH0000');
				$requete_sql = $this->forge_INSERT_AVG($table,$date_record,$sensor_id,$reponse);
				//Execution de la requete
				$this->executerRequete($requete_sql);
				//******************************************************
				}
			}
		}
	}

	protected function forge_SELECT_AVG()
	{
		$requete_sql = 'SELECT ';
			foreach ($this->measure as $value)
			{
				$requete_sql .=	' AVG('.$value.') AS '.$value.'_moyenne , MIN('.$value.') AS '.$value.'_min, MAX('.$value.') AS '.$value.'_max ,';
			}
			$requete_sql = trim($requete_sql, ",");
			$requete_sql .= 'FROM '.$this->type.' WHERE date_enregistrement > :debut AND date_enregistrement < :fin AND id_capteur = :id_capteur';
			return $requete_sql;
	}

	protected function forge_INSERT_AVG($table,$date_record,$sensor_id,$data)
	{
		/*
			@param $table 			  -> suffixe de la table (ex: _H_AVG pour la table DHT22_H_AVG)
			@param $date_record 	-> date pour l'enregistrement dans mySQL au format YmdHis par exemple pour le 28 octobre 2016 à 12h00 : 20161028120000
			@param $sensor_id 		-> entier (ref capteur)
			@param $data 			    -> tableau de données correspondant au données à enregistrer
		 */
		$requete_sql = 'INSERT INTO '.$this->type.$table.' VALUES ('.$date_record.', '.$sensor_id. ', ';
		$index = "0";
		foreach ($data as $key => $value) {
			$index != $key ?  $requete_sql .= $value.',': $index++;
		}
		$requete_sql = trim($requete_sql, ",");
		$requete_sql .= ") ";
		$requete_sql .= "ON DUPLICATE KEY UPDATE ";
		$index = "0";
		foreach ($data as $key => $value) {
			$index!= $key ?  $requete_sql .= $key . '='.$value.',': $index++;
		}
		$requete_sql = trim($requete_sql, ",");
		return $requete_sql;
	}

	public function calc_daily_cumulation($date,$sensor_id,$plages = NULL)
	{
		/*
			@param $date 	      -> Integer 	  : date pour laquelle les moyennes sont calculées au format "mySQL" (par exemple pour le 28 octobre 2016 : 20161028)
			@param $sensor_id 	-> Integer 		: reference du capteur pour le calcul de la moyenne
		*/
		$create_part = 'CREATE TABLE IF NOT EXISTS wattmeter1ch_DAILY_CUMUL'  ;
		$values_part =		'(	date DATE NOT NULL,
		            sensor_id INT UNSIGNED NOT NULL,
		            kWh_total FLOAT,
		            kWh_jour FLOAT,
		            kWh_nuit FLOAT ';

		$end_part =	'PRIMARY KEY (date,sensor_id))
		          ENGINE=INNODB ';

		$requete_sql = $create_part . $values_part . $end_part;
				echo $requete_sql;
		$this->executerRequete($requete_sql);
/*
		$debut = $date;
  	$fin = $debut + 1;

		// Mise en forme des dates au format adapté
		$debut .= "00";
		$fin .= "00";
		$date_d = DateTime::createFromFormat('YmdH', $debut);
		$date_f = DateTime::createFromFormat('YmdH', $fin);
		$str_date_d = $date_d->format('Y/m/d H:00');
		$str_date_f = $date_f->format('Y/m/d H:00');

		//Generation du tableau de parametres pour la requete SQL
		$param = array(	'debut' => $str_date_d,
						'fin' => $str_date_f,
						'id_capteur' => $sensor_id);

		//Verification de l'existance de données avant le calcul de la moyenne
		$requete_sql = 'SELECT * FROM '.$this->type.' WHERE date_enregistrement > :debut AND date_enregistrement < :fin AND id_capteur = :id_capteur';
		if ($this->executerRequete($requete_sql,$param)->fetch())
		{	//**************************************************************************
			//Si le resultat n'est pas vide, calcul de la moyenne pour la periode testée
			// Conception de la requete
			$requete_sql = $this->forge_SELECT_AVG();
			$reponse_req = $this->executerRequete($requete_sql,$param);
			$resultat = $reponse_req->fetch();

			//**************************************************
			//Insertion du resultat dans la table correspondante
			//Conception de la requete
			$table = "_DAY_CUMUL";
			$date_record = $date_d->format('Ymd');
			$requete_sql = $this->forge_INSERT_AVG($table,$date_record,$sensor_id,$resultat);
			//Execution de la requete
			$this->executerRequete($requete_sql);
			//******************************************************
		}
		*/
	}
}
