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

// Initially attempted to do this entirely recursively,
// rapidly determined that it was only getting to folders 8 levels deep 
// (just the way my music collection is organized), so settled on a bunch of foreach loops, and then recursion.
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

function directoryList($songs)
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
										$songList += directoryList($s, $dir);
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