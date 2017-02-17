<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/litapp/LitAppUtils.php';

$list_id = idx($_GET, 'list_id');
if (!$list_id || !is_numeric($list_id)) {
  echo 'ERROR: unsupported list_id '.$list_id;
  die(1);
}

$list = idx(LitAppUtils::getLitListResponseForCity($city_id = null, 100), $list_id);
if (!$list) {
  $list = idx(LitAppUtils::getGeneralListResponseForCity($city_id = null, 100), $list_id);
}
if (!$list) {
   echo 'could not find list';
  die(1);
}

$list = ApiUtils::addListDataToLitList($list);

$response = $list;

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response, JSON_NUMERIC_CHECK).')';
} else {
  slog($response);
}
