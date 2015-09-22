<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';

$city_id = Cities::NYC;
$base_url = 'http://www.whatsnom.com/';


function cmpByPosition($a, $b)  {
  return strcmp($a["position"], $b["position"]);
}

/* DATA FETCH START */
// Get all lists to show on homepage
$lists = DataReadUtils::getTopListsForCity($city_id, 10);

/* DATA FETCH END */

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
  $config_for_list = ListTypeConfig::$config[$list['type']];

  $list_genre = $config_for_list[ListTypeConfig::GENRE];

  $list['name'] = $config_for_list[ListTypeConfig::LIST_NAME];
  $list['entry_name'] = $config_for_list[ListTypeConfig::ENTRY_NAME];
  $list['plural_entry_name'] =
    $config_for_list[ListTypeConfig::PLURAL_ENTRY];
  $list['icon'] =
    $base_url . 'icondir/'. $config_for_list[ListTypeConfig::ICON] .'.png';
  $list['city_name'] = Cities::getName($list['city']);

  $entries = DataReadUtils::getEntriesForList($list);


  // Parse and put the list items on the list
  $entries_keyed_by_spot_id = array();
  $spot_names = array();
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
  }

  $list['snippet'] = implode(array_slice($spot_names, 0, 5), ', ');


  // Sort entries by rank on list, and rekey to the positions for render
  usort($entries_keyed_by_spot_id, "cmpByPosition");

  $list['entries'] = $entries_keyed_by_spot_id;

  $list_response[$list_genre]['items'][$list['id']] = $list;
}


$user_id = idx($_GET, 'uid');
$bookmarks = null;
if ($user_id && is_numeric($user_id)) {
  $users_bookmarks_assocs = DataReadUtils::getAllOutgoingAssocs(
    array('id' => $user_id),
    'bookmarks'
  );

  $combined_bookmarks = array();
  foreach ($users_bookmarks_assocs as $bookmark_key => $bookmark) {
    if ($bookmark['type'] == 'S') {
      $spot = get_object($bookmark['target_id'], 'spots');
      $combined_bookmarks[$bookmark_key] = $bookmark;
      $combined_bookmarks[$bookmark_key]['list_item_thumbnail']
        = $spot['profile_pic'];
      $combined_bookmarks[$bookmark_key]['place'] = $spot;
    }
  }
  $bookmarks = $combined_bookmarks;
}

$response = array(
  'bookmarks' => $bookmarks,
  'lists'     => $list_response,
);

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response).')';
} else {
  slog($response);
}

