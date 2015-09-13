<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';
set_time_limit(0);
ini_set('memory_limit', '128M');

/*
while (1) {
  $objects = get_objects_from_sql(
    sprintf("select  id, title from films where to_delete = 0 "
	    ."limit %d, %d",
	    $i++, 1));
  if (!$objects) {
    hlog('[[script complete]]');
    exit(1);
  }
  $film = head($objects);
  hlog('['.$i.'] fetching stars for film '.$film['title'].' ('.$film['id'].')');
  add_film_stars($film['id']);
}
*/
add_film_stars(1696191);



?>
