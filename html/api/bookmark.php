<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';
$user_id = idx($_GET, 'uid');
$type = idx($_GET, 'type');
$target_id = idx($_GET, 'target_id');
$list_id = idx($_GET, 'list_id');
if ($type != 'spot') {
  echo 'unsupported type: '.$type;
  die(1);
}

// Only support spot bookmarks for now
$bookmark_type = ($type == 'spot') ? 'S' : null;
$assoc_type = 'bookmarks';
if (!$user_id || !$target_id || !$type
    || !DataReadUtils::isSupportedAssoc($assoc_type)) {
  echo 'exiting';
  exit(1);
}

$existing_assoc =
  DataReadUtils::getAssoc(
    $user_id,
    $list_id,
    $assoc_type,
  );
$response = false;
if ($existing_assoc && !$existing_assoc['deleted']) {
  DataWriteUtils::removeAssoc($user_id, $list_id, $assoc_type);
  $response = 'removed';
} else {
  DataWriteUtils::addAssoc($user_id, $list_id, $assoc_type);
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

