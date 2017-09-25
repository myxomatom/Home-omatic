<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" />
		<!-- Latest compiled and minified CSS -->
		<link 	rel="stylesheet" 
				href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
				integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" 
				crossorigin="anonymous">
		<!-- Optional theme -->
		<link 	rel="stylesheet" 
				href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" 
				integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" 
				crossorigin="anonymous">
		<link 	rel="stylesheet"
				href="Vendor/bootstrap-datepicker-1.6.4-dist/css/bootstrap-datepicker3.css">

        <title> <?php echo($data['titre']) ?> </title>		
    </head>
    <body>	
    	<header>
			
    	</header>
		<section>
			<?php echo($data['contenu']) ?>
		</section>
		
	</body>
	<script
			  src="https://code.jquery.com/jquery-3.1.1.min.js"
			  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
			  crossorigin="anonymous"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script 
				src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" 
				integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" 
				crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.bundle.min.js"></script>
	<script src="Vendor/bootstrap-datepicker-1.6.4-dist/js/bootstrap-datepicker.min.js"></script>
	<script src="Vendor/bootstrap-datepicker-1.6.4-dist/locales/bootstrap-datepicker.fr.min.js"></script>
	<script src="JS/chart_manager.js"></script>
	<script src="JS/myscript.js"></script>

</html>