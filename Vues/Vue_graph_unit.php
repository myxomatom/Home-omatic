<div class="col-md-5 col-md-offset-1">
	<h4 class="text-center"><?php echo($reference['titre_graph']);?></h4>
 	<canvas id=<?php echo($reference['id_canvas']);?> width="400" height="400"></canvas>
 	<div class="row">							
 		<div class="col-md-5 col-xs-6 date_pick">
			<div class="input-group">
			<span class="input-group-addon" id="basic-addon3">Du :</span>
			<input type="text" class="form-control bfh-number form-control" id=<?php echo($reference['id_debut']);?>><br></div>
		</div>
		<div class="col-md-5 col-xs-6 date_pick">
			<div class="input-group">
			<span class="input-group-addon" id="basic-addon3">Au :</span>
			<input type="text" class="form-control bfh-number form-control" id=<?php echo($reference['id_fin']);?>><br></div>
		</div>
		<div class="col-xs-12 col-md-2">
			<button type="button" class="btn btn-primary center-block" 	id=<?php echo($reference['id_button']);?> 
																		type_capteur=<?php echo($reference['type_capteur']);?> 
																		measure=<?php echo($reference['measure']);?> 
																		sensor=<?php echo($reference['ref_capteur']);?>  
																		title=<?php echo($reference['titre_graph']);?>
																		dualYaxis=<?php echo($reference['yaxis']);?>>Rafraîchir</button>
		</div>	
	</div>
</div>