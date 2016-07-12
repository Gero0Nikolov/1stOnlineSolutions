<?php 
class RADIO_CONTROLLER {
	private $server_name = NULL;
	private $db_name = NULL;
	private $db_user = NULL;
	private $db_pass = NULL;
	private $table_  = NULL;

	/*
	* 	__construct function.
	*  	Purpose:
	*  	- Creates the RADIO_CONTROLLER class.
	*  	Arguments:
	*  	- $server_name: The name of the host server. Example: localhost
    * 	- $db_name: The name of the database that we are going to use. Example: dopamine_ra_news
    * 	- $db_user: The name of the database user. Example: root
    * 	- $db_pass: The password of the database. Example: pass_5869
	*  	- $table: The table where we are going to store the songs. By default it is "rc_songs".
	 */
	function __construct(
			$server_name,
			$db_name, 
			$db_user, 
			$db_pass,
			$table = "rc_songs"
		){
		$this->server_name = $server_name;
		$this->db_name = $db_name;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->table_ = $table;
	}

	/*
	* 	__destruct function.
	*  	Purpose:
	*  	- Unsets the $GLOBALS variables.
	 */
	function __destruct() {
    	unset( $this->server_name );
    	unset( $this->db_name );
    	unset( $this->db_user );
    	unset( $this->db_pass );
    	unset( $this->table_ );
    }

    /*
  	* 	Connect to database function.
  	*  	Purpose:
  	*  	- Connects to the database and returns the connection bridge.
  	 */
    function connect_to_database() {
  		// Set database variables
  		$server_name = $this->server_name;
  		$db_user = $this->db_user;
  		$db_pass = $this->db_pass;
  		$db_name = $this->db_name;

  		// Connect to the database
  		$connection_ = mysqli_connect( $server_name, $db_user, $db_pass, $db_name );
		if ( $connection_->connect_error ) { die("Fatal connection pronlem: " . $conn->connect_error); }

		return $connection_;
  	}

  	/*
    *	Initialize Database function.
    * 	Purpose:
    * 	- Tests the connection to the database.
    * 	- Creates the needed tables in the database.
     */
    function initialize_db() {
    	$table_ = $this->table_;
    	$connection_ = $this->connect_to_database();

  		// Build the table
  		$sql_ = "SELECT id FROM $table_ LIMIT 1";
  		$catch_ = $connection_->query( $sql_ );
  		if ( isset( $catch_->num_rows ) ) { /* The table exists! */ }
  		else {
  			$sql_ = "
			CREATE TABLE $table_ (
				id int NOT NULL AUTO_INCREMENT,
				singer LONGTEXT,
				title LONGTEXT,
				album LONGTEXT,
				genre LONGTEXT,
				duration LONGTEXT,
				publish_date INT,
				PRIMARY KEY (id)
			)
       		";
       		if ( $connection_->query( $sql_ ) === FALSE ) {
				echo "<h4>Table: <i>$stories_table</i> wasn't created.<br>Reason: $connection_->error</h4>";
			}
  		}

  		$connection_->close();
    }

    /*
    *	Add Song function
    *	Purpose:
    *	- Add song to the database and return info as an JSON to the front-end.
    *	Variables:
    * 	- $title: Title of the song
    * 	- $album: Album of the song
    * 	- $genre: Genre of the song
    * 	- $duration: Duration of the song
     */
    function add_song( $title, $album, $genre, $duration ) {
    	$table_ = $this->table_;
    	$publish_date = strtotime( date( "Y/m/d" ) );
    	$response_ = true;

    	$singer = trim( explode( "-", $title )[0] );
    	$title = trim( explode( "-", $title )[1] );

    	$connection_ = $this->connect_to_database();
    	$sql_ = "INSERT INTO $table_ ( 
    		singer,
    		title, 
    		album, 
    		genre, 
    		duration, 
    		publish_date 
		) VALUES ( 
			'$singer',
    		'$title',
    		'$album',
    		'$genre',
    		'$duration',
    		'$publish_date' 
		)";
		if ( $connection_->query( $sql_ ) === FALSE) { $response_ = false; }
		$connection_->close();

		return $response_;
    }

    /*
    *	Collect function.
    *	Purpose:
    *	- Collect the newly pushed by the Radio Server info.
    *	Variables:
    *	- $src_: The path to the source file
     */
    function collect_( $src_ ) {
    	$response_ = NULL;
    	if ( file_exists( $src_ ) ) {
    		$xml_ = "";
    		$input_handler = fopen( $src_, "r" );
    		while ( !feof( $input_handler ) ) {
    			$line_ = trim( fgets( $input_handler ) );
    			if ( !empty( $line_ ) ) { $xml_ .= $line_; }
    		}
    		fclose( $input_handler );

    		// Split the XML to an Array
    		$xml_handler = simplexml_load_string( $xml_ );
    		if ( !empty( $xml_handler ) ) {
    			$song_title = $xml_handler->title;
    			$song_album = $xml_handler->album;
    			$song_genre = $xml_handler->genre;
    			$song_duration = $xml_handler->duration;
    			$song_duration_float = floatval( str_replace( ":", ".", $song_duration ) );
    			$next_song_interval = $xml_handler->next;
    			$next_song_interval_float = floatval( str_replace( ":", ".", $next_song_interval ) );

    			// Add song to the Database if it's different song
    			if ( $next_song_interval_float == $song_duration_float ) { $this->add_song( $song_title, $song_album, $song_genre, $song_duration ); }

    			// Convert the XML to JSON
    			$json_handler = json_encode( $xml_handler );
    			
    			// Set response to respond the JSON
    			$response_ = $json_handler;
    		} else { $response_ = 0; }
    	} else { $response_ = 0; }

    	return $response_;
    }

    /*
	*	Get Dates Statistics function.
	*	Purpose:
	*	- Get statistics for custom dates From - To
	*	Variables:
	*	- $from_date: Starting date.
	*	- $to_date: End date.
	 */
	function get_dates_statistics( $from_date, $to_date ) {
		$table_ = $this->table_;
		$from_date = strtotime( $this->sanitize_string( $from_date ) );
		$to_date = strtotime( $this->sanitize_string( $to_date ) );
		$statistics_ = array();

		$connection_ = $this->connect_to_database();

		// Get the most shown singer
		$sql_ = "
		SELECT singer, COUNT( singer ) 
		AS value_occurence 
		FROM $table_
		WHERE publish_date >= $from_date AND publish_date < $to_date
		GROUP BY singer 
		ORDER BY value_occurence 
		DESC LIMIT 1
		";
		$catch_ = $connection_->query( $sql_ );
  		if ( isset( $catch_->num_rows ) > 0 ) {
  			while ( $row_ = $catch_->fetch_assoc() ) {
  				$statistics_[ "vip_author" ] = $row_[ "singer" ];
  			}
  		}

  		// Get the most shown song
		$sql_ = "
		SELECT singer, title, COUNT( title ) 
		AS title_occurence 
		FROM $table_
		WHERE publish_date >= $from_date AND publish_date < $to_date
		GROUP BY title
		ORDER BY title_occurence 
		DESC LIMIT 1
		";
		$catch_ = $connection_->query( $sql_ );
  		if ( isset( $catch_->num_rows ) > 0 ) {
  			while ( $row_ = $catch_->fetch_assoc() ) {
  				$statistics_[ "vip_song_singer" ] = $row_[ "singer" ];
  				$statistics_[ "vip_song" ] = $row_[ "title" ];
  			}
  		}

  		// Get the longest song
  		$sql_ = "
		SELECT singer, title, duration, MAX( duration )
		AS duration
		FROM $table_
		WHERE publish_date >= $from_date AND publish_date < $to_date
		ORDER BY duration
		DESC LIMIT 1
  		";
  		$catch_ = $connection_->query( $sql_ );
  		if ( isset( $catch_->num_rows ) > 0 ) {
  			while ( $row_ = $catch_->fetch_assoc() ) {
  				$statistics_[ "longest_song_singer" ] = $row_[ "singer" ];
  				$statistics_[ "longest_song" ] = $row_[ "title" ];
  				$statistics_[ "longest_song_duration" ] = $row_[ "duration" ];
  			}
  		}

  		// Get the shortest song
  		$sql_ = "
		SELECT singer, title, duration, MIN( duration )
		AS duration
		FROM $table_
		WHERE publish_date >= $from_date AND publish_date < $to_date
		ORDER BY duration
		DESC LIMIT 1
  		";
  		$catch_ = $connection_->query( $sql_ );
  		if ( isset( $catch_->num_rows ) > 0 ) {
  			while ( $row_ = $catch_->fetch_assoc() ) {
  				$statistics_[ "shortest_song_singer" ] = $row_[ "singer" ];
  				$statistics_[ "shortest_song" ] = $row_[ "title" ];
  				$statistics_[ "shortest_song_duration" ] = $row_[ "duration" ];
  			}
  		}

  		// Get the most shown genre
  		$sql_ = "
		SELECT genre, COUNT( genre ) 
		AS genre_occurence 
		FROM $table_
		WHERE publish_date >= $from_date AND publish_date < $to_date
		GROUP BY genre
		ORDER BY genre_occurence 
		DESC LIMIT 1
		";
		$catch_ = $connection_->query( $sql_ );
  		if ( isset( $catch_->num_rows ) > 0 ) {
  			while ( $row_ = $catch_->fetch_assoc() ) {
  				$statistics_[ "vip_genre" ] = $row_[ "genre" ];
  				$statistics_[ "vip_genre_songs" ] = $row_[ "genre_occurence" ];
  			}
  		}

		$connection_->close();

		if ( array_filter( $statistics_ ) ) { $statistics_ = json_encode( (object) $statistics_ ); }
		else { $statistics_ = "-1"; }


		// Return response
		return $statistics_;
	}

	/*
  	* 	Sanitize string function.
  	*  	Purpose:
  	*  	- Sanitizes the user input and returns safe content for the database.
  	*  	Arguments:
  	*  	- $string: This is the string that we are going to sanitize.
  	 */
  	function sanitize_string( $string_ ) {
  		$string_ = str_replace( "'", "&#39", trim( htmlentities( $string_ ) ) );
  		return $string_;
  	}
};
?>