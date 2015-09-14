<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';

$city_id = Cities::NYC;
$base_url = 'http://www.whatsnom.com/';

// DATA FETCH START

// Get all lists to show on homepage
$lists = DataReadUtils::getTopListsForCity($city_id, 10);
$entries_grouped_by_list_id = array();

foreach ($lists as $list) {
  $entries_grouped_by_list_id[$list['id']] =
    DataReadUtils::getEntriesForList($list);
}

// NOTE: Spot data fetching happens in the foreach loop below

// DATA FETCH END

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
$list_fields_to_unset = array('deleted', 'creator_id', 'created_time', 'upvotes');
$entry_fields_to_unset = array('deleted', 'list_id');
foreach ($lists as $list) {
  $list_id = $list['id'];

  // To minimize response size, unset unneeded fields
  foreach ($list_fields_to_unset as $field_name) {
      unset($list[$field_name]);
  }


  // Parse and put the list items on the list
  $entries_for_list = $entries_grouped_by_list_id[$list_id];
  $entry_ids = array();
  foreach ($entries_for_list as $entry_key => $entry) {
    $entry_ids[] = $entry['spot_id'];
    foreach ($entry_fields_to_unset as $field_name) {
      unset($entries_for_list[$entry_key][$field_name]);
    }
  }
  $list['items'] = $entries_for_list;

  // Generate snippet, which for now is the first few place names
  $entry_names = array();
  foreach (array_slice($entry_ids, 0, 4) as $spot_id) {
    $spot = get_object($spot_id, 'spots');
    if ($spot['name']) {
      $entry_names[] = $spot['name'];
    }
  }
  $list['snippet'] = implode($entry_names, ', ');

  // Merge render info about the list type into the response
  $config_for_list = ListTypeConfig::$config[$list['type']];

  $list_genre = $config_for_list[ListTypeConfig::GENRE];

  $list['name'] = $config_for_list[ListTypeConfig::LIST_NAME];
  $list['entry_name'] = $config_for_list[ListTypeConfig::ENTRY_NAME];
  $list['icon'] =
    $base_url . 'icondir/'. $config_for_list[ListTypeConfig::ICON] .'.png';

  $response[$list_genre]['items'][$list['type']] = $list;
}

//Prevent caching.
header('Cache-Control: no-cache, must-revalidate');
//The JSON standard MIME header.
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response).')';
} else {
  slog($response);
}
// JSON
//echo json_encode($response);

