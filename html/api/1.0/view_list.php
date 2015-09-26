<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';

$list_id = idx($_GET, 'list_id');
if (!$list_id || !is_numeric($list_id)) {
  echo 'ERROR: unsupported list_id '.$list_id;
  die(1);
}

$list = get_object($list_id, 'lists');
$list = ApiUtils::addListDataToList($list);

$response = $list;

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response, JSON_NUMERIC_CHECK).')';
} else {
  slog($response);
}

