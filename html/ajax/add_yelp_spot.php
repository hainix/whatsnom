<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';
$user = FacebookUtils::getUser();
$type_id = idx($_POST, 'type_id');
$city_id = idx($_POST, 'city_id');
$yelp_id = idx($_POST, 'yelp_id');
if (!$user || !$yelp_id || !$city_id) {
  exit(1);
}


if (DataWriteUtils::addNewSpot($yelp_id, $type_id, $city_id)) {
  echo
    '<img src="'.BASE_URL.'images/checkmark.png" class="small-icon" />';
} else {
  echo '<img src="'.BASE_URL.'images/plus.png" class="small-icon" />';
}

