<!DOCTYPE html>
<?php 

require_once('List.php');

$Directory = new RecursiveDirectoryIterator('./Music', true);
$directories = listAllDirectories($Directory);
//echo '<pre>';
//var_dump($directories);
//echo '</pre>';
$songList = shuffleSongs($directories);
//shuffle($songList);

$count = 0;
$length = count($songList);
$list = '';
foreach($songList as $song){
	if($count == 0)
		$list = "\t";
	list($name, $location) = explode(':', addslashes(str_replace('\\', '/', $song))); 
	$list .= '{\'track\':'.$count.', \'name\':\''.$name.'\', \'location\':\''.$location.'\'}, '."\n\t\t\t\t\t";
	$count++;
}
?>
<html>
<head>
<link href="vendor/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="music.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="vendor/js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/Pagination.js"></script>
<script type="text/javascript">

var tracks = Array();
var index = 0;
var playTrack;

$(document).ready(function(){
	var supportsAudio = !!document.createElement('audio').canPlayType;
	var perPage = 20;
	tracks = [<?= $list ?>];
					
	Paginate(tracks, perPage, 1, index);
	
	if(supportsAudio) {
		var contextClass = (window.AudioContext ||
			window.webkitAudioContext ||
			window.mozAudioContext ||
			window.oAudioContext ||
			window.msAudioContext);
		
		var playing = false;
		
		var mediaPath = 'music/';
		
		var trackCount = tracks.length;
		
		var npAction = $('#npAction');
		
		var npTitle = $('#npTitle');
		
		var audio = $('#audio1');
		
		if (contextClass) {
			// Web Audio API is available.
			var context = new contextClass();
			
			var source = context.createMediaElementSource(audio[0]); //Use the audio element on the page as the audio source.
			
//			var bufferloader = new BufferLoader(context, [audio.prop('src')], function(bufferList){
//				var source = context.createBufferSource();
				
//				source.buffer = bufferList[0];
				
//				source.connect(context.destination);
//				source.start(0);
//			});
			
			source.connect(context.destination); //Now connected to speakers through Web Audio API
			
		} else {
			// Web Audio API is not available. Ask the user to use a supported browser.
			print("Your Browser does not support the Web Audio API.");
		}
		
		audio.on('play', function() {
			playing = true;
			npAction.text('Now Playing:');
		}).on('pause', function() {
			playing = false;
			npAction.text('Paused:');
		}).on('ended', function() {
			npAction.text('Paused:');
			if((index + 1) < trackCount) {
				index++;
				loadTrack(index);
				audio[0].play();
			} else {
				audio[0].pause();
				index = 0;
				loadTrack(index);
			}
			
			if(index % perPage == 0)
			{
				Paginate(tracks, perPage, parseInt(index / perPage)+1, index);
			}
		});
		
		var source = $('source');

		var btnPrev = $('#btnPrev').click(function() {
			if((index - 1) > -1) {
				index--;
				loadTrack(index);
				if(playing) {
					audio[0].play();
				}
			} else {
				audio[0].pause();
				index = trackCount-1;
				loadTrack(index);
			}
		});
		
		var btnNext = $('#btnNext').on('click', function() {
			if((index + 1) < trackCount) {
				index++;
				loadTrack(index);
				if(playing){
					audio[0].play();
				}
			} else {
				audio[0].pause();
				index = 0;
				loadTrack(index);
			}
		});
		
		var li = $('#plUL li').on('click', function() {
			var id = parseInt($(this).find('.plNum').attr('id'));
			if(id !== index) {
				playTrack(id);
			}
		});
		
		var loadTrack = function(id) {
			
			console.log(id);
			
			$('.plSel').removeClass('plSel');
			$('#'+ id).parents('li').addClass('plSel');
			
			var locations = tracks[id].location.split('\\');
			var end = locations.length-1;
			
			npTitle.html(locations[0] + ' <span class=\'glyphicon glyphicon-chevron-right\'></span> ' + locations[end]);
			var title = 'HallsOfChain Music';
			$('title').text(title + ' > ' + locations[0] + ' > ' + locations[end]);
			index = id;
			source.prop('src', mediaPath + tracks[id].location).prop('type', 'audio/mpeg');
			audio[0].load();
		};
		
		playTrack = function(id) {
			loadTrack(id);
			audio[0].play();
		};

		loadTrack(index);
	}
});
</script>
<title>HallsOfChain Music</title>
</head>
<body>
<div id="cwrap">
	<div id="nowPlay" class="col-sm-offset-2 col-sm-8">
		<h3 id="npAction">Paused:</h3>
		<div id="npTitle"></div>
	</div>
	<div id="audiowrap" class="col-sm-offset-2 col-sm-8">
		<div id="player">
			<div id="audio0" class="col-sm-12">
				<audio id="audio1" controls="controls" width="300">
					<source src type />
					Your Browser does not support HTML5 Audio Tag.
				</audio>
			</div>
			<div id="extraControls">
				<button id="btnPrev" class="ctrlbtn btn btn-primary">|&lt;&lt;<!--Prev Track--></button> <button id="btnNext" class="ctrlbtn btn btn-primary"><!--Next Track-->&gt;&gt;|</button>
			</div>
		</div>
		<div id="plwrap" class="col-sm-12">
			<span id="spanWrapTop" class="col-sm-12"></span>
			<div id="plHead">
				<div class="plHeadNum">Track</div>
				<div class="plHeadTitle">Title</div>
			</div>
			<ul id="plUL" col-sm-12>
			<?php
				$count = 0;
				foreach($songList as $song)
				{
					list($name, $location) = explode(':', $song);
					echo "<li>\n\t<div class=\"plItem\">\n\t\t<div class=\"plNum\" id=\"".$count."\">".(($count < 9) ? ('0'.($count+1)) : ($count+1))."</div>\n\t\t<div class=\"plTitle\">$name</div>\n\t</div>\n</li>";
					$count++;
				}
			?>
			</ul>
			<span id="spanWrapBottom" class="col-sm-12"></span>
		</div>
	</div>
</div>
</body>
</html>