<!DOCTYPE html>
<?php 

function listAllDirectories($Directory, $main = false)
{
	$structure = array();
	$count = 0;
	
	foreach($Directory as $entry)
	{
		$subPath = $Directory->getSubPathname();
		if(substr($subPath, -1) != '.')
		{
			$structure[$subPath] = array();
			
			if($Directory->hasChildren())
			{
				$children = $Directory->getChildren();
				$structure[$subPath] = listAllDirectories($children);
			} else {
				unset($structure[$subPath]);
				$filename = $subPath;
				while(strpos($filename, '\\')){
					$filename = substr($filename, strpos($filename, '\\')+1);
				}
				if(substr($filename, -3) == 'mp3')
					$structure[] = $filename.':'.$subPath;
			}
		}
		$count++;
	}
	return $structure;
}

function shuffleSongs($songs)
{
	$songList = array();
	if(is_array($songs))
	{
		foreach($songs as $song)
		{
			if(is_array($song))
			{
				foreach($song as $son)
				{
					if(is_array($son))
					{
						foreach($son as $so)
						{
							if(is_array($so))
							{
								foreach($so as $s)
								{
									if(is_array($s))
									{
										$songList += shuffleSongs($s);
									} else {
										$songList[] = $s;
									}
								}
							} else {
								$songList[] = $so;
							}
						}
					} else {
						$songList[] = $son;
					}
				}
			} else {
				$songList[] = $song;
			}
		}
	} else {
		return false;
	}
	shuffle($songList);
	
	return $songList;
}

$Directory = new RecursiveDirectoryIterator('.\Music', true);
$directories = listAllDirectories($Directory);
//echo '<pre>';
//var_dump($directories);
//echo '</pre>';
$songList = shuffleSongs($directories);
//shuffle($songList);
?>
<html>
<head>
<link href="music.css" rel="stylesheet" type="text/css" />
<link href="vendor/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="vendor/js.bootstrap.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var supportsAudio = !!document.createElement('audio').canPlayType;
	var tracks = [<?php  $count = 0;
						$length = count($songList);
						foreach($songList as $song){
							if($count == 0)
								echo "\t";
							list($name, $location) = explode(':', addslashes($song)); 
							echo '{\'track\':'.$count.', \'name\':\''.$name.'\', \'location\':\''.$location.'\'}, '."\n\t\t\t\t\t";
							$count++;
						} ?>
					];
					
	if(supportsAudio) {
		var index = 0;
		
		var playing = false;
		
		var mediaPath = 'music\\';
		
		var trackCount = tracks.length;
		
		var npAction = $('#npAction');
		
		var npTitle = $('#npTitle');
		
		var audio = $('#audio1');
		
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
			var id = parseInt($(this).index());
			if(id !== index) {
				playTrack(id);
			}
		});
		
		var loadTrack = function(id) {
			
			$('.plSel').removeClass('plSel');
			$('#plUL li:eq(' + (id+1) + ')').addClass('plSel');
			console.log($('#plUL li:eq('+id+')'));
			var locations = tracks[id].location.split('\\');
			var end = locations.length-1;
			npTitle.html(locations[0] + ' <span class=\'glyphicon glyphicon-chevron-right\'></span> ' + locations[end]);
			index = id;
			source.prop('src', mediaPath + tracks[id].location).prop('type', 'audio/mpeg');
			audio[0].load();
		};
		
		var playTrack = function(id) {
			loadTrack(id);
			audio[0].play();
		};

		loadTrack(index);
	}
});
</script>
</head>
<body>
<div id="cwrap">
	<div id="nowPlay">
		<h3 id="npAction">Paused:</h3>
		<div id="npTitle"></div>
	</div>
	<div id="audiowrap">
		<div id="audio0">
			<audio id="audio1" controls="controls" width="300">
				<source src type />
				Your Browser does not support HTML5 Audio Tag.
			</audio>
		</div>
		<div id="extraControls">
			<button id="btnPrev" class="ctrlbtn">|&lt;&lt; Prev Track</button> <button id="btnNext" class="ctrlbtn">Next Track &gt;&gt;|</button>
		</div>
		<div id="plwrap">
			<div id="plHead">
				<div class="plHeadNum">Track</div>
				<div class="plHeadTitle">Title</div>
				<!--<div class="plLength">Length</div>-->
			</div>
			<ul id="plUL">
			<?php
				$count = 0;
				foreach($songList as $song)
				{
					list($name, $location) = explode(':', $song);
					echo "<li>\n\t<div class=\"plItem\">\n\t\t<div class=\"plNum\">".(($count < 9) ? ('0'.($count+1)) : ($count+1))."</div>\n\t\t<div class=\"plTitle\">$name</div>\n\t</div>\n</li>";
					$count++;
				}
			?>
			</ul>
		</div>
	</div>
</div>
<!--<audio controls preload="auto">
<?php
/*	foreach($songList as $song)
	{
		list($name, $location) = explode(':', $song);
		echo "\t".'<source src=\'music\\'.$location.'\' type=\'audio/mpeg\'>'."\n";
	}
	echo 'Get an html5 compatible browser!';*/
?>
</audio>-->
</body>
</html>