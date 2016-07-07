#Radio Contrоller
<p>The <strong>index.php</strong> in the root flider presents the Demo.</p>
<p>In it you'll be able to see how the controller works.</p>
<p></p>
<p>The <strong>database.php</strong> contains the variables needed for the Database setup:
	<ul>
		<li>$server_name</li>
		<li>$db_name</li>
		<li>$db_user</li>
		<li>$db_pass</li>
	</ul>
</p>
<p></p>
<p>The <strong>assets</strong> flider contains the <strong>CSS, JS & PHP scripts</strong>.</p>
<p>The <strong>radio-controller.php</strong> represents the controller as a Class structure with different methods.</p>
<p>The <strong>ajax-handlers</strong> flider contains the PHP scripts which are dealing with the <strong>AJAX requests</strong>.</p>

#How to setup?
<ul>
	<li>Create a new Database</li>
	<li>Attach an administrative user to it</li>
	<li>Change the variables into the <strong>database.php</strong></li>
	<li>Include the <strong>database.php</strong> on each page you want to use the controller!</li>
</ul>
<p>Once you are ready, now you can create a new controller. Example: <strong>$controller_ = new RADIO_controller( $server_name, $db_name, $db_user, $db_pass );</strong></p>
<p>If you are dealing with a new database it would be good if you call the Database initialize method before you start to use the main functionallities. Example: <strong>$controller_->initialize_db();</strong></p>
<p>To clilect information about the current song and add it to the DB the controller uses a collect() method. Example: <strong>$controller_->collect_( $xml_input_src );</strong></p>
<p>It takes the path to the Radio input as an argument.</p>
<p>The clilect method will check if the song is different from the previous one and if that is true it will add it to the database.</p>
<p>And it will return JSON formated object which contains the information about the current song as a result.</p>
<p></p>
<p>To pull the information for the current song the controller is using <strong>pullSong();</strong> JS function which is called on page load.</p>
