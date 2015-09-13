<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';
$data = array();
$start = 3;
define('WORKABLE_CHUNK_SIZE', 1);
set_time_limit(0);
$existing_data = array();
$i = 0;
do {
  echo "---------- [[[ starting film ".$i."\n\n";
  $objects = get_objects_from_sql(
    sprintf("select * from films where fullfilm > '' limit %d, %d",
            $i++, 1));
  if (!$objects) {
    $exits++;
    if ($exits > 10) {
      hlog('[[script complete]]');
      exit(1);
    } else {
      continue;
    }
  }
  $data = array();
  foreach ($objects as $obj) {
    $handle = $obj['fullfilm'];
    $handle = head(explode('&', $handle));
    if (!youtube_film_exists_in_db($handle)) {
      $data[] = array('youtube_handle' => $handle,
                      'title' => str_replace(' ', '-', strtolower($obj['name'])),
                      'price' => 0);
    } else {
      //update_film_id_in_db($handle, $obj['id']);
    }
  }
  if ($data) {
    //db_write_it($data);
  }

} while (1);

function youtube_film_exists_in_db($handle) {
	global $link;
	$sql = sprintf("SELECT title from youtube_films WHERE youtube_handle = '%s'", $handle);
	$r = mysql_query($sql);
	return mysql_fetch_assoc($r);
}

function db_write_it($data) {
	global $link;
	foreach ($data as $r) {
	  $sql = sprintf("INSERT IGNORE INTO youtube_films (title, youtube_handle, price) VALUES ('%s', '%s', %d)",
			 $r['title'], $r['youtube_handle'], $r['price']);
	  $result = mysql_query($sql);
	  //$result = true;
	  if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $sql;
	    //die($message);
	  } else {
	    hlog('saved to db with sql: '.$sql."\n");
	  }
	}
}

function update_film_id_in_db($handle, $id) {
	global $link;
	  $sql = sprintf("update youtube_films set film_id = %d where youtube_handle =  '%s' limit 1",
                   $id, $handle);
	  $result = mysql_query($sql);
	  //$result = true;
	  if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $sql;
      die($mesage);
	    //die($message);
	  } else {
	    hlog('saved to db with sql: '.$sql."\n");
	  }
}
?>

