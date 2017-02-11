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
  $lit_lists_raw = LitAppUtils::getLitListResponseForCity($city_id, 100);

  // Get entries for each list
  $lists_with_entries = array();
  $list_response_with_headers = array();
  $list_response_with_headers[] = array('isDivider' => 1, 'name' => 'Lit Right Now');

  foreach ($lit_lists_raw as $raw_list) {
    $temp_list = ApiUtils::addListDataToLitList($raw_list);
    $list_response_with_headers[] = $temp_list;
    $ordered_list_response[]['items'] = array($temp_list);
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
