#!/usr/local/bin/php
<?php
include_once '../script_lib.php';
set_time_limit(0);
ini_set('memory_limit', '32M');
$vars = parse_args($argv);
$start = idx($vars, 's', 0);


$i = $start;
do {
  $film = get_object_from_sql(
    sprintf("select  id, title from films where deleted is null "
	    ."limit %d, %d",
	    $i++, 1));
  if (!$film) {
    hlog('[[script complete]]');
    continue;
    //exit(1);
  }

  hlog("\n\n".'['.$i.'] processing film '.$film['title'].' ('.$film['id'].')');

  // first, make sure we have a wiki handle.
  $sql = sprintf("select * from songs where film_id = %d and deleted is null", $film['id']);
  $songs = get_objects_from_sql($sql);
  if (!$songs) {
    hlog('no songs. sniff');
    continue;
  }

  $to_delete = array();
  // Cleanup
  foreach ($songs as $song_id => $song) {
    if ($song['youtube_handle']) {
      hlog('at least one song in this set has a youtube handle, so skipping');
      continue(2);
    }
    if (stripos($song['name'], 'dialogue') !== false
	|| stripos($song['name'], 'dialouge') !== false) {
      $to_delete[] = $song;
      hlog('deleting '.$song['name']);
      unset($songs[$song_id]);
    } else {
      hlog('*'.$song['source'].': '.$song['name']);
    }
  }

  // Iterate over every possible source, in order of goodness
  $source_to_use = null;
  //    1 = hindilyrix.com, 2 = hindilyrics.net, 3 = hindisongs.net, 4 = raaga, 5 = smashhits
  foreach (array(4, 5, 3, 1, 2) as $source) {
    $num_songs_of_source = 0;
    foreach ($songs as $song) {
      if ($source == $song['source']) {
	$num_songs_of_source++;
      }
    }
    if ($num_songs_of_source > 2) {
      $source_to_use = $source;
      hlog('** decided on source '.$source.' with '
	   .$num_songs_of_source.' songs');
      break;
    }
  }

  // If decided on a song, delete the rest
  if ($source_to_use) {
    foreach ($songs as $song) {
      if ($source_to_use != $song['source']) {
	hlog('deleting '.$song['name'].' as its source, '
	     .$song['source'].' != '.$source_to_use);
	$to_delete[] = $song;
      }
    }
  } else {
    hlog('***** no source?!?!');
  }

  if ($to_delete) {
    hlog('********** DELETING *********');
    foreach ($to_delete as $delete_song) {
      delete_song($delete_song);
    }
   hlog(count($to_delete).' songs');
  }

} while (1);

function delete_song($song) {
  if (!$song || !$song['id']) {
    hlog('deleting a null song');
    return false;
  }
  global $link;
  $sql = sprintf("update songs set deleted = 1 where id = %d limit 1",
		 $song['id']);
  hlog('running sql '.$sql);
  $result = mysql_query($sql);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      hlog($message);
    } else {
      hlog('deleted song '.$song['id']);
    }
}


?>