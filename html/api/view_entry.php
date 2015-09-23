<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';

$city_id = Cities::NYC;
$base_url = 'http://www.whatsnom.com/';

$entry_id = idx($_GET, 'entry_id');
$uid = idx($_GET, 'uid');
if (!$entry_id || !is_numeric($entry_id)) {
  echo 'unsupported entry id '.$entry_id;
  die(1);
}

$entry = get_object($entry_id, 'entries');
if (!$entry) {
  echo 'no entry found for id '.$entry_id;
  die(1);
}

$bookmark =
  $uid
  ? DataReadUtils::getAssoc($uid, $entry_id, 'bookmarks')
  : null;
$place = get_object($entry['spot_id'], 'spots');
$list = get_object($entry['list_id'], 'lists');
$response = array(
  'bookmark' => $bookmark,
  'entry'    => $entry,
  'place'    => $place,
  'list'     => $list
);

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response).')';
} else {
  slog($response);
}

