<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';

$city_id = Cities::NYC;
$base_url = 'http://www.whatsnom.com/';

/* DATA FETCH START */
// Get all lists to show on homepage
$lists = DataReadUtils::getTopListsForCity($city_id, 10);

/* DATA FETCH END */

// Start with the Genres
$response = array();
foreach (ListGenreTypes::getConstants() as $genre_name => $genre_id) {
  $response[$genre_id] =
    array(
      'items' => array(),
      'name'  => $genre_name,
    );
}

// Now go through all the lists and put them in genre buckets

foreach ($lists as $list) {
  $list_id = $list['id'];

  // Merge render info about the list type into the response
  $config_for_list = ListTypeConfig::$config[$list['type']];

  $list_genre = $config_for_list[ListTypeConfig::GENRE];

  $list['name'] = $config_for_list[ListTypeConfig::LIST_NAME];
  $list['entry_name'] = $config_for_list[ListTypeConfig::ENTRY_NAME];
  $list['icon'] =
    $base_url . 'icondir/'. $config_for_list[ListTypeConfig::ICON] .'.png';
  $list['snippet'] = 'snippet here';

  $entries = DataReadUtils::getEntriesForList($list);

  // Parse and put the list items on the list
  $entries_keyed_by_spot_id = array();
  foreach ($entries as $entry_key => $entry) {
    $spot = get_object($entry['spot_id'], 'spots');
    $new_entry = $entry;
    $new_entry['place'] = $spot;
    $new_entry['name'] = $spot['name'];
    $new_entry['snippet'] = $entry['tip'] ?: idx($spot, 'snippet');
    $new_entry['thumbnail'] = $spot['profile_pic'];
    $entries_keyed_by_spot_id[$entry['spot_id']] = $new_entry;
  }
  $list['entries'] = $entries_keyed_by_spot_id;

  $response[$list_genre]['items'][$list['id']] = $list;
}

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response).')';
} else {
  slog($response);
}

