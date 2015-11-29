<html>
	<body>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<script src="id3-minimized.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		<style>
			div#player
			{
				width: 50%;
				position: fixed;
				left: 25%;

				padding-left: 30px;
				padding-right: 30px;
				padding-top: 10px;
				padding-bottom: 5px;

				border: 1px solid #303030;
				border-radius: 5px;

				background-color: #333333;

				font-family: Verdana, sans-serif;
				color: #cccccc;
			}

			div#player h1
			{
				font-size: 12px;
				margin: 0;
				padding: 0;
			}

			div#player #duration, div#player #time
			{
				display: inline-block;
				margin-top: 5px;
				margin-bottom: 15px;
			 	font-size: 10px;
			}

			div#player h2
			{
				font-size: 10px;
				margin: 0;
				padding: 0;
			}

			div#controls
			{
				float: right;
				margin-left: auto;
				margin-right: auto;
				margin-left: 5%;
			}

			div#slider
			{
				margin-top: 13px;
				width: 96%;
				margin-left: auto;
				margin-right: auto;
			}

			div#volume-slider 
			{
				float: right;
				width: 10%;
				margin-top: 14px;
				margin-bottom: 10px;
				margin-right: 2%;
			}

			div.clear
			{
				clear: all;
			}

			div#controls > button
			{
				border: none;
				background: none;
			}

			.ui-slider
			{
				height: 5px;
			}

			.ui-slider-handle
			{
				height: 15px !important;
				width: 15px !important;
			}

			.ui-slider-handle, .ui-state-default, .ui-corner-all
			{
				border-radius: 100px;
				top: -5px !important;
				background: #cccccc !important;
				border: #cccccc !important;
			}

			.ui-slider-range-min
			{
				background: #aa0000 !important;
				top: 0 !important;
				border-color: #ccc;
			}

			.fa
			{
				color: #cccccc;
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {

				$.ajax({
					dataType: 'json',
					url: 'tracklisting.php'
				})
				.done(function(data) {
					tracks = data
				})
				.fail(function(data) {
					alert('Failure to load JSON response.')
				});

				track = new Audio();

				repeat = false;
				shuffle = 'on';
				active = false;
				trackId = 0;

				function setTrackDuration(param) 
				{ 
					trackduration = param 
				};

				track.ondurationchange = function () 
				{
				    minutes = Math.floor(track.duration / 60);
				    seconds = Math.floor(((track.duration / 60) - minutes) * 60);
				    $("div#duration").html((minutes + ':' + (seconds < 10 ? '0' + seconds : seconds)));
				}

				track.ontimeupdate = function () 
				{
					minutes = Math.floor(track.currentTime / 60);
					seconds = Math.floor(((track.currentTime / 60) - minutes) * 60);
					$("div#time").html((minutes + ':' + (seconds < 10 ? '0' + seconds : seconds)) + '&nbsp;/&nbsp;');
					//$("#slider").slider('value', (100/track.duration*track.currentTime));
					//$("#slider-modal").slider('value', (100/track.duration*track.currentTime));
					//$('[data-src="' + track.src + '"] .radial-progress').attr('data-progress', Math.floor(100/track.duration*track.currentTime));
				};

				track.onvolumechange = function () 
				{
					$("#volume-slider").slider('value', track.volume*100);
					$("#volume-slider-modal").slider('value', track.volume*100);
				}

				track.onended = function () 
				{
					if (repeat == 'on') {
						track.currentTime = 0;
						track.play();
					}
					if (shuffle == 'on') 
					{
						playTrack(Math.floor((Math.random() * (numTracks - 1) + 1)));
					}
					if (shuffle != 'on' && repeat != 'on') 
					{
						trackId = $('[data-src="' + track.src + '"]').attr('data-trackid');
						newId = (parseInt(trackId) + 1);
						if (typeof $('[data-trackId="' + newId + '"]').attr('data-title') !== 'undefined')
						{
							playTrack(newId);
						}
					}
				}

				$( "#slider" ).slider(
				{
					range: "min",
					min: 1,
					max: 100,
					slide: function (event, ui) {
						track.currentTime = track.duration/100*ui.value;
					}
				});

				$( "#volume-slider" ).slider(
				{
					range: "min",
					value: 100,
					min: 1,
					max: 100,
					slide: function (event, ui) {
						track.volume = ui.value/100;
					}
				});

				$("#seek").bind("change", function() 
				{

					track.currentTime = $(this).val();

					$("#seek").attr("max", track.duration);

				});

				$(".next-track").click(function() 
				{
					newId = (parseInt(trackId) + 1);

					if (tracks[newId] !== undefined)
						playTrack(newId);
					else
						playTrack(0);

					trackId++;
				});

				$(".last-track").click(function() 
				{
					newId = (parseInt(trackId) - 1);

					if (tracks[newId] !== undefined)
						playTrack(newId);
					else
						playTrack(0);

					trackId--;
				});

				$("button.play").click(function() 
				{
					track.play();
				});

				$("button.pause").click(function() 
				{
					track.pause();
				});
			});

			function playTrack(trackId)
			{
				newTrack = tracks[trackId];
				track.src = newTrack;
				track.play();

				ID3.loadTags(tracks[trackId], function() {
					var tags = ID3.getAllTags(tracks[trackId]);
					$("div#track-artist").html('<h1>Artist: ' + tags.artist + '</h1>');
					$("div#track-title").html('<h2>Track: ' + tags.title + '</h2>');
				});
			}
		</script>
		<div id="player">
			<div id="track-artist"><h1>Artist: </h1></div>
			<div id="track-title"><h2>Track: </h2></div>
			<div id="slider"></div>
			<div id="volume-slider"></div>
			<div id="controls">
				<button class="last-track"><i class="fa fa-step-backward"></i></button>
				<button class="play" onclick='playTrack(0)'><i class="fa fa-play"></i></button>
				<button class="pause"><i class="fa fa-pause"></i></button>
				<button class="next-track"><i class="fa fa-step-forward"></i></button>
			</div>
			<div class="clear"></div>
			<div id="time">0:00&nbsp;/&nbsp;</div><div id="duration">0:00</div>
		</div>
	</body>
</html>