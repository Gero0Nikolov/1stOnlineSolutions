<?php
	// Include Database variables
	include "../../database.php";
	// Include the radio controller
	include "../radio-controller.php";

	$controller_ = new RADIO_CONTROLLER( $server_name, $db_name, $db_user, $db_pass );

	// Get song
	$song_ = $controller_->collect_( "../radio-input/input.xml" );

	// Retun song info
	echo $song_;
?>