<?php
  //include_once '../../lib/utils.php';
//include_once '../parser.php';
include_once '../script_lib.php';

// this file updates all films for a certain field
//set_time_limit(0);
//ini_set('memory_limit', '60M');
global $link;

$vars = parse_args($argv);
hlog($vars);

$start = idx($vars, 's', 0);

$i = $start;
$exits = 0;
do {
  hlog('start fetch');
  $objects = get_objects_from_sql(
				  sprintf("select id, name, release_date, release_time from films limit %d, %d",
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
  if (!$objects) {
    hlog('no objects, moving on');
    continue;
  }
  $object = head($objects);
  $id = $object['id'];
  hlog('processing film id '.$id.', titled '.$object['name']);

  if ($object['release_date'] && !$object['release_time']) {
    $release_time = strtotime($object['release_date']);
    $sql = sprintf(
      "update films set release_time = %d where id = %d LIMIT 1",
      $release_time,
      $id
    );
    hlog($sql);
    $result = mysql_query($sql);
  }
} while (1);
  /*
  $songs = get_objects_from_sql(
				sprintf("select id from songs where film_id = %d",
					$id));
  hlog('num songs = '.count($songs));

  $reviews = get_objects_from_sql(
				sprintf("select id from reviews where film_id = %d",
					$id));
  hlog('num reviews = '.count($reviews));


  $boolean_set = array();
  if (count($songs) < 2) {
    $boolean_set[] = 'needs_music';
  }
  if (count($reviews) < 2) {
    $boolean_set[] = 'needs_critic_reviews';
  }

  foreach ($boolean_set as $boolean_field_name) {

    $sql = sprintf("update films set ".$boolean_field_name." = 1 where id = %d LIMIT 1",
		   $id);
    hlog($sql);
    $result = mysql_query($sql);
    //$result = true;
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      hlog('[err]--'.$message);
    } else {
      hlog('--- saved to db for id '.$id.' with new fields '.$boolean_field_name);
    }
    unset($result);
  }
} while (1);
  */


?>