#!/usr/local/bin/php
<?php
include_once '../script_lib.php';
set_time_limit(0);
ini_set('memory_limit', '32M');
$vars = parse_args($argv);
$start = idx($vars, 's', 0);


$i = $start;
do {
  $song = get_object_from_sql(
    sprintf("select  id, youtube_handle, name from songs where youtube_handle "
	    ."like '%s' limit %d, %d",
	    '%&%',
	    $i++, 1));
  if (!$song) {
    hlog('[[script complete]]');
    continue;
    //exit(1);
  }

  hlog("\n\n".'['.$i.'] processing '.$song['name'].' ('.$song['id'].')');
  $new_handle = head(explode('&', $song['youtube_handle']));
  update_song_handle($song, $new_handle);
} while (1);

function update_song_handle($song, $handle) {
  if (!$song || !$song['id'] || !$handle) {
    hlog('null song');
    return false;
  }
  global $link;
  $sql = sprintf("update songs set youtube_handle = '%s' where id = %d limit 1",
		 $handle,
		 $song['id']);
  hlog('running sql '.$sql);

  $result = mysql_query($sql);
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      hlog($message);
    } else {
      hlog('updated song '.$song['id']);
    }
}


?>