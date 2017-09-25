<?php
$data['titre'] = 'Graphiques des relevés';
ob_start(); ?>

<div class="container">
	<div class="row">
 <?php
 	$index = 0;
 	foreach ($entry['capteurs'] as $key => $value) {
 		$reference['type_capteur'] =   $value[1];
 		$reference['id_capteur'] =   $value[1] . "_" . $key; 
 		$reference['ref_capteur'] = $value[0];	
 			
 		foreach ($value[2] as $val) {
 				$reference['id_canvas'] = '"' ."myChart" . $index .'"';
 				$reference['id_debut'] = '"'."debut_myChart" . $index .'"';
 				$reference['id_fin'] = '"'."fin_myChart" . $index.'"';
 				$reference['id_button'] = '"'."refresh_myChart" . $index.'"';
 				(isset($value[3])) ? $reference['titre_graph']= '"'.$value[3].'"' :  $reference['titre_graph'] = '"'."Suivi de ".$val.'"';
 				(isset($value[4])) ? $reference['yaxis'] = "true" : $reference['yaxis'] = "false";
 				$reference['measure'] = '"'.$val.'"';
 				include("Vues/Vue_graph_unit.php");
 				$index++;
 		}
 	}
 ?> 		
	</div>
</div>

<?php $data['contenu'] = ob_get_clean(); ?>