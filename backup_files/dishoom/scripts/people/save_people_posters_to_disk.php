<?php
require_once ('../script_lib.php');

$start = 17; //isset($_GET['s']) ? $_GET['s'] : 0; // where to start
$object_type = isset($_GET['t']) ? $_GET['t'] : 'person'; // type of object, film or person

if (!in_array($object_type, array('film', 'person'))) {
  echo 'unrecognized object type';
  exit(1);	
}

define('WORKABLE_CHUNK_SIZE', 5);
set_time_limit(0);
ini_set('memory_limit', '64M');
$i = $start;
$exits = 0;
do {
  hlog("  [[[ ".$i." ]]] processing");

  $person =  get_object_from_sql(sprintf("select * from people where tier is not null limit %d, %d", $i++, 1));
  if (!$person) {
    hlog('out of people');
    exit(1);
  }
  $images = get_objects_from_sql(sprintf("select * from images where subject_id = %d and is_profile = 1", $person['id']));

  $image_num = 0;
  foreach ($images as $image) {
    $url = $image['src'];
    if (!$url) {
      continue;
    }
    
    hlog('curling url '.$url);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_TIMEOUT, 20); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $rawdata=curl_exec ($ch);
    if(curl_errno($ch)) {
      hlog('omg error!', curl_error($ch));
      continue;
    }
    curl_close ($ch);

    hlog('saving ...');
    $save_path = '../../images/people/'.$person['id'].'_'.$image_num.'.jpg';
    $image_num++;

    $fp = fopen($save_path, 'w');
    fwrite($fp, $rawdata); 
    fclose($fp);
    
    hlog('saved poster for person '.$person['id'].' for index '.$image_num - 1);
  }
} while (1);


?>
