#!/usr/local/bin/php                                                                           
<?php
include_once '../script_lib.php';
include_once '../imdb_parser_lib.php';
set_time_limit(0);
ini_set('memory_limit', '32M');
global $link;

$i = 0;
/*
while(1) {
  $objects = get_objects_from_sql(
    sprintf('select  id, name, films from people where to_delete = 0 and film_count is null '
	    .'limit %d, %d',
	    $i++, 1));
  if (!$objects) {
    hlog('[[script complete]]');
    exit(1);
  }
  $person = head($objects);
  $id = $person['id'];
  $films = $person['films'];
  if (!$films) {
    hlog('onoes, person '.$id.' has no films... skipping');
    continue;
  }
  try {
    $film_count = count(unserialize($films));
  } catch (Exception $e) {
    hlog('--unserialize error for '.$id.', skipping.');
    continue;
  }
  hlog('['.$i.'] person '.$person['name'].' ('.$id.') = '.$film_count.' films');
*/
$data = get_adhoc_data();
if (!$data) {
  hlog('enter data');
  exit(1);
}
foreach ($data as $id => $field) {
  $sql = sprintf("update people set type = '%s' "
		 ."where id = %d limit 1",
		 $field,
		 $id);
  $result = mysql_query($sql);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog($message);
  } else {
    hlog('--saved field '.$field.' for '.$id);
  }
}

function get_adhoc_data() {

}