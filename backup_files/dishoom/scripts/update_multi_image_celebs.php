<?php
require_once ('script_lib.php');
define('MANY_PHOTOS_FLOOR', 5);

$start = isset($_GET['s']) ? $_GET['s'] : 0; // where to start
$object_type = 'person';
$tier = 'B';

if (!in_array($object_type, array('film', 'person'))) {
	echo 'unrecognized object type';
	exit(1);
}

define('WORKABLE_CHUNK_SIZE', 5);
set_time_limit(0);
ini_set('memory_limit', '64M');

$i = 0;
$no_photos = $many_photos = array();
$exits = 0;

do {
  hlog('['.$i.'] getting object');
  $sql =
    sprintf("select id, tier from people where deleted is null "
            ."and tier = '%s' and num_photos is null order by id DESC limit %d, %d",
            $tier, $i++, 1);

  $objects = get_objects_from_sql($sql);
  if (!$objects) {
    $exits++;
    if ($exits > 2) {
      hlog('[[script complete at i = '.$i.']]');
      hlog('--- people with no photo: '.implode(', ', array_keys($no_photos)));
      hlog('--- people with many photos: '.implode(', ', array_keys($many_photos)));
      exit(1);
    } else {
      continue;
    }
  }

  $object = head($objects);
  $id = $object['id'];
  $path = 'http://media.dishoomfilms.com.s3.amazonaws.com/person/';

  $num_photos = 0;
  while (url_exists($path . $id . '_' . $num_photos.'.jpg') && $num_photos < 50) {
    hlog('-- checked and found photo '.$num_photos);
    $num_photos++;
  }
  update_people_photo_count($object, $num_photos);
  $many_photos[$id] = true;
} while (1);

function update_people_photo_count($person, $count) {
  if (!is_numeric($count)) {
    hlog('count of null, exiting to be safe');
    return false;
  }
  $sql = sprintf("update people set num_photos = %d where id = %d LIMIT 1",
                 $count,
                 $person['id']);
  hlog($sql);
  $result = mysql_query($sql);
  //$result = true;
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog('[err]--'.$message);
  } else {
    hlog('--- saved to db for id '.$person['id'].' with new photo count '.$count);
  }
  hlog('...');
  sleep(1);
}
?>
