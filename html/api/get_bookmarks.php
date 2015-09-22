<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';
$user_id = idx($_GET, 'uid');
$bookmarks = null;
if ($user_id && is_numeric($user_id)) {
  $users_bookmarks_assocs = DataReadUtils::getAllOutgoingAssocs(
    array('id' => $user_id),
    'bookmarks'
  );

  $combined_bookmarks = array();
  foreach ($users_bookmarks_assocs as $bookmark_key => $bookmark) {
    $entry_id = $bookmark['target_id'];
    $entry = get_object($entry_id, 'entries');
    $spot = get_object($entry['spot_id'], 'spots');
    $entry['list_item_thumbnail'] = $spot['profile_pic'];
    $entry['place'] = $spot;
    $combined_bookmarks[$bookmark_key] = $entry;
  }
  $bookmarks = $combined_bookmarks;
}

$response = $bookmarks;

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response).')';
} else {
  slog($response);
}

