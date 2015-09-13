<?php


include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/api/yelp.php';

if (!idx($_GET, 'y')) {
  echo 'no y id';
  die(1);
}
$yelp_id = $_GET['y'];
$info = get_yelp_business_info($yelp_id);
slog($info);



die(1);

// REMOVE to iterate
//$type_override_id = ListTypes::DESSERT;
$city_override_id = Cities::EAST_BAY;
//$city_override_id = Cities::SOUTH_BAY;
//$city_override_id = Cities::SF;

// Iterate
$type_offset = 43;
$cities_offset = 0;

$limit = 20;
$pages_per_type = 6;
$cities_per_pass = 1;

foreach(array_slice(Cities::getConstants(), $cities_offset, $cities_per_pass) as $city_id) {

  $city_id = $city_override_id ?: $city_id;
  $bounds = Cities::getBoundsForCity($city_id);
  echo "<br/>-- fetching city ".Cities::getName($city_id);

  if ($type_override_id) {
    $type_constants = array_flip(ListTypes::getConstants());
    $list_types =
      array($type_constants[$type_override_id] => $type_override_id);
  } else {
    $list_types =
      array_slice(ListTypes::getConstants(), $type_offset, 1, true);
  }

  foreach ($list_types as $type_key => $type_id) {

    $term = strtolower(str_replace('_', '+', $type_key));
    for ($offset = 0; $offset < $pages_per_type; $offset++) {
      $url = 'http://api.yelp.com/v2/search?term='.$term.'&limit='.$limit.'&offset='.$offset*$limit.'&category_filter=restaurants&bounds='.$bounds;
      echo '<br/>---fetching url '.$url;
      $api_response = null;
      $api_response = get_yelp_info($url); // comment out to do nothing
      //slog($api_response);
      if (!$api_response->businesses) {
        echo '<br/>***error!';
        die(1);
      }
      foreach ($api_response->businesses as $business) {
        slog('<br/>----adding spot for yelp id '.$business->id);
        if ($business->id) {
          DataWriteUtils::addNewSpot($business->id, $type_id, $city_id, $debug = true);
        }
      }
    }
  }
}

echo '<br/><br/>[[[ bai ]]]';
