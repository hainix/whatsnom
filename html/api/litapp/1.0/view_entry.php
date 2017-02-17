<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';

$entry_id = idx($_GET, 'entry_id');
if (!$entry_id || !is_numeric($entry_id)) {
  echo 'unsupported entry id '.$entry_id;
  die(1);
}

$entry = get_object($entry_id, 'lit_entries');
if (!$entry) {
  echo 'no entry found for id '.$entry_id;
  die(1);
}
$entry = ApiUtils::addLitDataToEntry($entry);

$response = array(
  'entry'    => $entry,
  'place'    => $entry['place'],
);

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response, JSON_NUMERIC_CHECK).')';
} else {
  slog($response);
}
