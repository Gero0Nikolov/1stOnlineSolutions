<?php 
	// Include Database variables
	include "database.php";
	// Include the radio controller
	include "assets/radio-controller.php";

	$controller_ = new RADIO_CONTROLLER( $server_name, $db_name, $db_user, $db_pass );

	// Initialize DB
	$controller_->initialize_db();

	// Collect radio input & update the DB
	$controller_->collect_( "assets/radio-input/input.xml" );
?>

<!DOCTYPE html>
<html>
<head>
	<title>Online Radio - Demo</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
	  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script type="text/javascript" src="assets/js-scripts.js"></script>
	<link href='assets/styles.css' rel='stylesheet' type='text/css' media='screen' />
</head>
<body>
	<h1>Online Radio - Demo</h1>
	<div id="song-container">
		<h2>Song title</h2>
		<h3>Song album</h3>
		<h4>Song duration</h4>
		<h5>Song left</h5>
	</div>
	<div id="date-picker">
		<div id="dates">
			<label for="from">From:</label>
			<input type="text" name="from_date" id="from_date">
			<label from="to">To:</label>
			<input type="text" name="to_date" id="to_date">
		</div>
	</div>
</body>
</html>