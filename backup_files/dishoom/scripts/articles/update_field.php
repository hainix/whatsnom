<?php
include_once '../../lib/utils.php';
include_once '../script_lib.php';
global $link;


set_time_limit(0);
ini_set('memory_limit', '32M');

$i = 0;;
do {
  $sql = sprintf("select * from articles where article_handle "
                 ."is null limit %d, %d",
		 $i++, 1);
  echo 'trying sql '.$sql;
  $o = get_object_from_sql($sql, 'article');
  if (!$o) {
    exit('term');
  }
  //hlog($o);
  if ($i % 100 == 1) {
    echo '.';
  }
  $handle  = substr(
    strtolower(
      preg_replace("/ /", "-",
                   preg_replace(
                     "/[^a-zA-Z0-9 ]+/", "", $o['headline']
                   )
      )
    ),
    0,
    100);
  if ($handle) {
    $sql = sprintf("update articles set article_handle = '".$handle."' where id = %d limit 1",
                   $o['id']);
  echo 'updating to handle '.$handle.' for id '.$o['id'];

  }
  $result = mysql_query($sql);
  if (!$result) {
    hlog('sql error '.mysql_error() );
    exit(1);
  } else if ($i % 1000 == 1) {
    hlog('['.$i.'] updated with: '.$sql);
  }
} while (1);

