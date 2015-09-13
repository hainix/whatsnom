#!/usr/local/bin/php
<?php
include_once '../script_lib.php';
set_time_limit(0);
ini_set('memory_limit', '32M');
$vars = parse_args($argv);
$start = idx($vars, 's', 0);


$i = $start;
do {
  $obj = get_objects_from_sql(
    sprintf("select  * from videos where name "
	    ."like '%s' limit %d, %d",
	    '%Part%',
	    $i++, 1));
  if (!$obj) {
    hlog('[[script complete]]');
    exit(1);
  }
  $obj = head($obj);

  hlog("\n\n".'['.$i.'] processing '.$obj['name'].' ('.$obj['id'].')');
  update_and_remove_part_from_video($obj);
} while (1);

function update_and_remove_part_from_video($obj) {
  if (!$obj || !$obj['id']) {
    hlog('null obj');
    return false;
  }
  global $link;
  $title_part  = match_one('/\(.*\)/', $obj['name']);
  $part = rem($obj['name'], array($title_part, '(Part ', ')'));

  if (!$part || !is_numeric($part)) {
    hlog('failed part '.$part);
    return false;
  }
  $new_name = trim($title_part);
  $sql = sprintf("update videos set name = '%s', part = %d where id = %d limit 1",
                 $new_name,
                 $part,
                 $obj['id']);

  $result = true;
  //$result = mysql_query($sql);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog($message);
  } else {
    hlog('updated obj '.$obj['id'].' with '.$sql);
  }
}


?>