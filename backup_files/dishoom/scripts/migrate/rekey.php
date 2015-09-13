#!/usr/local/bin/php                                                                                                   
<?php
include_once '../script_lib.php';
set_time_limit(0);
ini_set('memory_limit', '32M');

$i = 0;;
do {
  $sql = sprintf("select images.id, people.uniqid "
		 ."from images, people where people.id = "
		 ."images.subject_id and images.uniqid is null limit %d, %d",
		 $i++, 1);
  $o = get_object_from_sql($sql);
  if (!$o) {
    exit('term');
  }  
  //hlog($o);
  if ($i % 100 == 1) {
    echo '.';
  }
  $sql = sprintf("update images set uniqid = %d where id = %d limit 1",
		 $o['uniqid'], $o['id']);

  
  $result = mysql_query($sql);
  if (!$result) {
    hlog('sql error '.mysql_error() );
    exit(1);
  } else if ($i % 1000 == 1) { 
    hlog('['.$i.'] updated with: '.$sql);
  }
} while (1);

