<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';

$user_id = idx($_GET, 'uid');
$bookmarks = null;
$bookmark_count = null;

if ($user_id && is_numeric($user_id)) {
  $users_bookmarks_assocs = DataReadUtils::getAllOutgoingAssocs(
    array('id' => $user_id),
    'bookmarks'
  );

  $bookmarks_by_list = array();
  foreach ($users_bookmarks_assocs as $bookmark_key => $bookmark) {
    $entry_id = $bookmark['target_id'];
    $entry = get_object($entry_id, 'entries');
    $entry = ApiUtils::addDataToEntry($entry, $full_entry = true);
    $bookmarks_by_list[$entry['list_id']][$entry['position']] = $entry;
  }

  $bookmark_count = 0;
  $bookmarks = array();
  foreach ($bookmarks_by_list as $bookmark_list_id => $_) {
    $list = get_object($bookmark_list_id, 'lists');
    $list = ApiUtils::addListConfigToList($list);
    ksort($bookmarks_by_list[$bookmark_list_id]);
    $bookmarks[$bookmark_list_id] = $list;
    usort($bookmarks_by_list[$bookmark_list_id], "cmpByPosition");
    $bookmarks[$bookmark_list_id]['entries'] =
      $bookmarks_by_list[$bookmark_list_id];
      $bookmark_count += count($bookmarks[$bookmark_list_id]['entries']);
  }
}

$response =
  array(
    'bookmarks' => $bookmarks,
    'count'     => $bookmark_count
  );

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response, JSON_NUMERIC_CHECK).')';
} else {
  slog($response);
}
