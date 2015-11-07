<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';

$city_id = idx($_GET, 'city_id');
$force = idx($_GET, 'force');
//$force = true; // For temp overrides


if (!$city_id) {
  $city_id = Cities::NYC;
}

$apc_key = 'api:combined:response:'.$city_id;
$apc_data = false;
if (!ApiUtils::API_SKIP_APC && !$force) {
  $apc_data = apc_fetch($apc_key);
}
if ($apc_data !== false) {
  $response = unserialize($apc_data);
} else {

  // Get all lists to show on homepage
  $lists = DataReadUtils::getTopListsForCity($city_id, 100);

  // Hide seasonal lists
  foreach ($lists as $key => $list) {
    if (ApiUtils::shouldHideSeasonablList($list)) {
      unset($lists[$key]);
    }
  }

  // Start with the Genres
  $list_response = array();
  foreach (ListGenreTypes::getConstants() as $genre_name => $genre_id) {
    $list_response[$genre_id] =
      array(
        'items' => array(),
        'name'  => $genre_name,
      );
  }

  // Now go through all the lists and put them in genre buckets
  foreach ($lists as $list) {
    $list = ApiUtils::addListDataToList($list);
    $list_response[$list['list_genre']]['items'][$list['name']] = $list;
  }

  // Remove empty categories
  foreach ($list_response as $key => $genre_list) {
    if (!$genre_list['items']) {
      unset($list_response[$key]);
    }
  }

  // Reindex everything, so client considers these as arrays and not objects
  foreach ($list_response as $key => $val) {
    ksort($list_response[$key]['items']);
    $list_response[$key]['items'] =
      array_values($list_response[$key]['items']);
  }


  // Reorder Genres
  $ordered_list_response = array();
  $genre_order = array(
    ListGenreTypes::ACTIVITIES,
    ListGenreTypes::DRINK,
    ListGenreTypes::FOOD,
    ListGenreTypes::CUISINE
  );
  foreach ($genre_order as $genre_id) {
    if (isset($list_response[$genre_id])) {
      $ordered_list_response[] = $list_response[$genre_id];
    }
  }

  $supported_cities = array(Cities::NYC, Cities::SF);
  $cities = array();
  foreach ($supported_cities as $supported_city_id) {
    $cities[] = array(
      'id'    => $supported_city_id,
      'label' => Cities::getName($supported_city_id)
    );
  }

  $headers_and_lists = array();
  foreach (array_filter($ordered_list_response) as $lists) {
    $headers_and_lists[] =
      array('isDivider' => true, 'name' => $lists['name']);
    foreach ($lists['items'] as $item) {
      unset($item['entries']);
      $headers_and_lists[] = $item;
    }
  }

  $response = array(
    'cities'    => $cities,
    'lists_with_headers' => $headers_and_lists,
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
  slog($response);
}
