<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';

// this file updates all films for a certain field
set_time_limit(0);
ini_set('memory_limit', '60M');
global $link;

$vars = parse_args($argv);
hlog($vars);

$start = idx($vars, 's', 0);

$i = $start;
$exits = 0;
do {
  $objects = get_objects_from_sql(
				  sprintf("select id, tags from people where type like '%s' limit %d, %d",
					  "%producer%",
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


  $tags = $object['tags'];
  // skip this if you want to rewrite old tags
  if ($tags) {
    $tags = explode(',', $tags);
  } else {
    $tags = array();
  }
  $tags = array_flip($tags);
  
  // tag with specific tag
  //  $tags[48] = 1; // singer
  //  $tags[46] = 1; // director
  //  $tags[47] = 1; //music director
  $tags[49] = 1; // producer

  hlog('['.$i.'] starting update for id '.$id);

  // reflip
  $tags = array_keys($tags);

  if (!is_array($tags)) {
    hlog('something wrong with tag format');
    hlog($tags);
    continue;
  }

  if (!$tags) {
    hlog('no tags for id '.$id.', maybe check it out?');
    continue;
  }

  $sql = sprintf("update people set tags = '%s' where id = %d LIMIT 1",
		 implode(',',$tags),
		 $id);
  hlog($sql);
  $result = mysql_query($sql);
  //$result = true;
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog('[err]--'.$message);
  } else {
    hlog('--- saved to db for id '.$id.' with new fields');
    hlog($tags);
    hlog('old tags');
    hlog($object['tags']);
  }
  unset($result);
} while (1);


?>