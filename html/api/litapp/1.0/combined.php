<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/litapp/LitAppUtils.php';


$city_id = idx($_GET, 'city_id');
$force = idx($_GET, 'force');
$force = true; // For temp overrides


if (!$city_id) {
  $city_id = Cities::NYC;
}

$apc_key = 'api:litapp:combined:response:'.$city_id;
$apc_data = false;
if (!ApiUtils::API_SKIP_APC && !$force) {
  $apc_data = apc_fetch($apc_key);
}
if ($apc_data !== false) {
  $response = unserialize($apc_data);
} else {

  // Get all lists to show on homepage, including header layout
  $list_response_with_headers = LitAppUtils::getLitListResponseForCity($city_id, 100);

  // Get entries for each list
  $lists_with_entries = array();
  foreach ($list_response_with_headers as $list_response) {
    if (idx($list_response, 'isDivider')) {
      continue;
    }
    $ordered_list_response[]['items']
      = array(ApiUtils::addListDataToLitList($list_response));
  }

  $supported_cities = array(Cities::NYC);
  $cities = array();
  foreach ($supported_cities as $supported_city_id) {
    $cities[] = array(
      'id'    => $supported_city_id,
      'label' => Cities::getName($supported_city_id)
    );
  }

  $response = array(
    'cities'    => $cities,
    'lists_with_headers' => $list_response_with_headers,
    'lists'     => $ordered_list_response
  );

  apc_store($apc_key, serialize($response), ApiUtils::API_APC_TTL);
}

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response, JSON_NUMERIC_CHECK).')';
} else {
  print_r($response);
}
