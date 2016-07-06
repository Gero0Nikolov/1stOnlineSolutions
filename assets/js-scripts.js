$( document ).ready(function(){
	$( "#from_date" ).datepicker();
	$( "#to_date" ).datepicker().change(function(){ pullInformationAboutDates(); });
	pullSong();
});

function pullSong() {
	args = {};
	pull_handler = make_ajax( "assets/ajax-handlers/pull-song.php", "POST", "text", args );
	pull_handler.onreadystatechange = function() {
		if ( pull_handler.readyState == 4 && pull_handler.status == 200 ) {
			response_ = JSON.parse( pull_handler.responseText );
			build_ = "\
			<div id='song-container'>\
				<h2>"+ response_.title +"</h2>\
				<h3>"+ response_.album +"</h3>\
				<h4>"+ response_.duration +"</h4>\
				<h5>"+ response_.next +"</h5>\
			</div>\
			";
			$( "#song-container" ).replaceWith( build_ );
			setTimeout(function(){ pullSong(); }, 1000);
		}
	}
}

function pullInformationAboutDates() {
	from_date = $( "#from_date" ).val().trim();
	to_date = $( "#to_date" ).val().trim();

	flag = 0;

	if ( ( from_date === undefined || from_date == "" ) || ( to_date === undefined || to_date == "" ) ) {
		flag = 1;
		alert( "Choose dates!" );
	}

	if ( flag == 0 ) {
		args = {
			from_date: from_date,
			to_date: to_date
		};
		dates_handler = make_ajax( "assets/ajax-handlers/pull-dates-info.php", "POST", "text", args );
		dates_handler.onreadystatechange = function() {
			if ( dates_handler.readyState == 4 && dates_handler.status == 200 ) {
				if ( dates_handler.responseText != "-1" ) {
					statistics_ = JSON.parse( dates_handler.responseText );
					vip_author = statistics_.vip_author;
					vip_song = statistics_.vip_song;
					longest_song_singer = statistics_.longest_song_singer;
					longest_song = statistics_.longest_song;
					longest_song_duration = statistics_.longest_song_duration;
					shortest_song_singer = statistics_.shortest_song_singer;
					shortest_song = statistics_.shortest_song;
					shortest_song_duration = statistics_.shortest_song_duration;
					vip_genre = statistics_.vip_genre;
					vip_genre_songs = statistics_.vip_genre_songs;

					build_ = "\
					<div id='dates-info'>\
						<p id='vip-author'>Most played singer: "+ vip_author +"</p>\
						<p id='vip-song'>Most played song: "+ vip_song +"</p>\
						<p id='longest-song'>Longest song: "+ longest_song_singer +" - "+ longest_song +", "+ longest_song_duration +"</p>\
						<p id='longest-song'>Shortest song:"+ shortest_song_singer +" - "+ shortest_song +", "+ shortest_song_duration +"</p>\
						<p id='vip-genre'>Most played genre: "+ vip_genre +"</p>\
						<p id='biggest-genre'>Genre with most songs: "+ vip_genre +" - "+ vip_genre_songs +"</p>\
					</div>\
					";
					if ( $( "#dates-info" ).length ) { $( "#dates-info" ).replaceWith( build_ ); }
					else { $( "#date-picker" ).append( build_ ); }
				} else {
					$( "#dates-info" ).remove();
				}
			}
		}
	}
}

// AJAX Caller
function make_ajax( path, type, resultType, variables ) {
	var requestType;	
	//Send request
	if (window.XMLHttpRequest) {
		requestType = new XMLHttpRequest();
	} else {
		requestType = new ActiveXObject("Microsoft.XMLHTTP");
	}

	requestType.open(type, path, true);

	varCount = 0;

	buildVarStructure = "";
	for ( var key in variables ) {
		if ( variables.hasOwnProperty( key ) ) {
			varName = key;
			varValue = variables[key];

			buildVarStructure += varName +"="+ varValue +"&";
		}
	}

	requestType.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	requestType.send( buildVarStructure );

	return requestType;
}