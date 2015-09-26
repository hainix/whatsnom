<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';

$user_id = idx($_GET, 'uid');
$bookmarks = null;
if ($user_id && is_numeric($user_id)) {
  $users_bookmarks_assocs = DataReadUtils::getAllOutgoingAssocs(
    array('id' => $user_id),
    'bookmarks'
  );

  $bookmarks_by_list = array();
  foreach ($users_bookmarks_assocs as $bookmark_key => $bookmark) {
    $entry_id = $bookmark['target_id'];
    $entry = get_object($entry_id, 'entries');
    $spot = get_object($entry['spot_id'], 'spots');
    $entry['list_item_thumbnail'] = $spot['profile_pic'];
    $entry['place'] = $spot;
    $bookmarks_by_list[$entry['list_id']][$entry['position']] = $entry;
  }
  $bookmarks = array();
  foreach ($bookmarks_by_list as $bookmark_list_id => $_) {

    $list = get_object($bookmark_list_id, 'lists');
    $list = ApiUtils::addListConfigToList($list);
    ksort($bookmarks_by_list[$bookmark_list_id]);
    $bookmarks[$bookmark_list_id] = $list;
    usort($bookmarks_by_list[$bookmark_list_id], "cmpByPosition");
    $bookmarks[$bookmark_list_id]['entries'] =
      $bookmarks_by_list[$bookmark_list_id];
  }
}

$response = $bookmarks;

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response, JSON_NUMERIC_CHECK).')';
} else {
  slog($response);
}

