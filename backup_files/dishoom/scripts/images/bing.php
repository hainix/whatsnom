#!/usr/local/bin/php                                                                  
<?php

include_once '../script_lib.php';
set_time_limit(0);
ini_set('memory_limit', '64M');

$i = 0;
while (1) {
  $objects = get_objects_from_sql(
    sprintf("select  id, name from people where id = 65202 limit %d, %d",
	    $i++, 1));
  if (!$objects) {
    hlog('[[script complete]]');
    exit(1);
  }
  $person = head($objects);
  hlog('['.$i.'] querying for '.$person['name'].' ('.$person['id'].')');

  $images = get_objects_from_sql(sprintf(
      "select id from images where subject_id = %d limit 1",
      $person['id']));
  if ($images) {
    hlog('--have images for '.$person['name'].', skipping');
    continue;
  }

  $data = array();

  $offsets = array(0, 50, 100, 150, 200, 250, 300);
  foreach ($offsets as $offset) {
    $m = array();
    $m['id'] = $person['id'];
    $m['name'] = $person['name'];
    
    $apiNumber = 'B57B353A3C8F403031FB7FB9FAB6CA780ED47B96';
    $countImage = '49';
    
    $q = $person['name'];
    $url = 'http://api.bing.net/json.aspx?AppId='.$apiNumber.'&Query='.urlencode($q).'&Sources=Image&Version=2.2&Market=en-US&adlt=strict&Adult=Off&Image.Count='.$countImage.'&Image.Offset='.$offset.'&JsonType=raw';
    $file = file_get_contents($url, 1000000);
    hlog('----bing querying for '.$q);
    $image_result = json_decode($file, true);
    $m['data'] = $image_result;
    $m['query'] = $q;
    $data[] = $m;
    }

  if ($data) {
    update_in_db($data);
    hlog('--images saved');
  }
}


function update_in_db($data) {
  global $link;
  hlog('-----saving images:');
  foreach ($data as $row) {
    if (!$row['data']) {
      hlog('no data found for id '.$row['id']);
      continue;
    }
    $id = $row['id'];
    $query = $row['query'];
    if (!isset($row['data']['SearchResponse']['Image']['Results'])) {
      hlog('no images for '.$id);
      continue;
    }
    
    foreach ($row['data']['SearchResponse']['Image']['Results'] as $image_response) {
      $sql = sprintf("insert ignore into images (subject_id, title, display_url, src, width, height, filesize, thumb)
                            values (%d, '%s', '%s', '%s', %d, %d, %d, '%s')",
		     $id,
		     tr($image_response['Title']),
		     tr($image_response['Url']),
		     tr($image_response['MediaUrl']),
		     $image_response['Width'],
		     $image_response['Height'],
		     $image_response['FileSize'],
		     tr($image_response['Thumbnail']['Url'])
		     );
      $result = mysql_query($sql);
      if (!$result) {
	$message  = 'Invalid query: ' . mysql_error() . "\n";
	$message .= 'Whole query: ' . $sql;
	hlog($message);
      } else {
	//	hlog('saved img to db with title: '.$image_response['Title']);
	echo '.';
      }
    }
  }
  return true;
}

?>