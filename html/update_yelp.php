<?php

include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/api/yelp.php';

die(1);

/*
set_time_limit(0);
ini_set('memory_limit', '64M');
*/

$spot_ids = array(
7275, 7274, 7276, 7277, 7278, 7279, 7280, 7281, 7282, 7283, 7475, 7284, 7285, 7286, 7287, 7288, 7289, 7290, 7291, 7292, 7293, 7294, 7295, 7296, 7297, 7298, 7299, 7300, 7301, 7302
);
$offset = $_GET['o'];
if (!$offset) {
  echo 'no offset';
  die(1);
}
//$spot_ids = array_slice($spot_ids, $offset, 20);


foreach ($spot_ids as $spot_id) {
  $spot = get_object($spot_id, 'spots');
  if (!$spot['categories']) {
    DataWriteUtils::updateSpot($spot_id);
    sleep(rand(2,4));
    echo '[c'.$spot_id.']';
  } else {
    echo '[x'.$spot_id.']';
  }
}

/*
$yelp_id = $spot['yelp_id'];
$info = get_yelp_business_info($yelp_id);
slog($info);
*/

echo '<br/><br/>[[[ bai ]]]';
