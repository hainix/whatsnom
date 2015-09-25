<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';

$apc_key = 'api:combined:response';
$apc_data = false;
if (!ApiUtils::API_SKIP_APC) {
  $apc_data = apc_fetch($apc_key);
}
if ($apc_data !== false) {
  $response = unserialize($apc_data);
} else {

  // TODO - switch in the UI
  $city_id = Cities::NYC;

  // Get all lists to show on homepage
  $lists = DataReadUtils::getTopListsForCity($city_id, 10);

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
    $list_id = $list['id'];

    // Merge render info about the list type into the response
    $list = ApiUtils::addListConfigToList($list);
    $entries = DataReadUtils::getEntriesForList($list);

    // Parse and put the list items on the list
    $entries_keyed_by_spot_id = array();
    $spot_names = array();
    $list_review_count = 0;
    foreach ($entries as $entry_key => $entry) {
      $spot = get_object($entry['spot_id'], 'spots');
      $spot['city_name'] = Cities::getName($spot['city_id']);
      $new_entry = $entry;
      $new_entry['place'] = $spot;
      $new_entry['name'] = $spot['name'];
      $new_entry['snippet'] = $entry['tip'] ?: idx($spot, 'snippet');
      $new_entry['list_item_thumbnail'] = $spot['profile_pic'];
      $entries_keyed_by_spot_id[$entry['spot_id']] = $new_entry;
      $spot_names[] = $spot['name'];
      $list_review_count += (int) $spot['review_count'];
    }

    $list['snippet'] = implode(array_slice($spot_names, 0, 5), ', ');
    $list['review_count'] = number_format($list_review_count);
    $list['critic_count'] =
      number_format($list_review_count % 5 + 1); // TODO: legitify

    // Sort entries by rank on list, and rekey to the positions for render
    usort($entries_keyed_by_spot_id, "cmpByPosition");

    $list['entries'] = $entries_keyed_by_spot_id;

    $list_response[$list['list_genre']]['items'][$list['id']] = $list;
  }

  $response = array(
    'lists'     => $list_response,
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

