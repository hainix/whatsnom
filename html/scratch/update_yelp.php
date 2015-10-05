<?php

include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/api/yelp.php';

die(1);


$spot_ids = array(
7274, 7276
);
$offset = $_GET['o'];
if (!$offset) {
  echo 'no offset';
  die(1);
}
//$spot_ids = array_slice($spot_ids, $offset, 20);


foreach ($spot_ids as $spot_id) {
  $spot = get_object($spot_id, 'spots');
  if (true || !$spot['categories']) {
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
