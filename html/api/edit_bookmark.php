<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';

$user_id = idx($_GET, 'uid');
$entry_id = idx($_GET, 'entry_id');
$force_state = idx($_GET, 'force_state');

$assoc_type = 'bookmarks';

if (!$user_id || !$entry_id
    || !DataReadUtils::isSupportedAssoc($assoc_type)) {
  echo 'exiting';
  exit(1);
}

$existing_assoc =
  DataReadUtils::getAssoc(
    $user_id,
    $entry_id,
    $assoc_type
  );


$response = false;
if ($force_state == 'removed'
    || ($force_state != 'added' && $existing_assoc
        && !$existing_assoc['deleted'])) {
  DataWriteUtils::removeAssoc($user_id, $entry_id, $assoc_type);
  $response = 'removed';
} else {
  DataWriteUtils::addAssoc($user_id, $entry_id, $assoc_type);
  $response = 'added';
}

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response).')';
} else {
  print_r($response);
}

