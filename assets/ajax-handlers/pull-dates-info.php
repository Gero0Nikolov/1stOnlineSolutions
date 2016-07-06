<?php 
	$from_date = $_POST[ "from_date" ];
	$to_date = $_POST[ "to_date" ];

	// Include Database variables
	include "../../database.php";
	// Include the radio controller
	include "../radio-controller.php";

	$controller_ = new RADIO_CONTROLLER( $server_name, $db_name, $db_user, $db_pass );

	// Get information for dates
	$info_ = $controller_->get_dates_statistics( $from_date, $to_date );

	// Return info
	echo $info_;
?>