<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';

$list_id = idx($_GET, 'list_id');
$city_id = Cities::NYC;
$base_url = 'http://www.whatsnom.com/';

$list = get_object($list_id, 'lists');
$entries = DataReadUtils::getEntriesForList($list);
$list['entries'] = $entries;
$config_for_list = ListTypeConfig::$config[$list['type']];
$list_genre = $config_for_list[ListTypeConfig::GENRE];

$list['name'] = $config_for_list[ListTypeConfig::LIST_NAME];
$list['entry_name'] = $config_for_list[ListTypeConfig::ENTRY_NAME];
$list['icon'] =
  $base_url . 'icondir/'. $config_for_list[ListTypeConfig::ICON] .'.png';


//Prevent caching.
header('Cache-Control: no-cache, must-revalidate');
//The JSON standard MIME header.
header('content-type: application/json; charset=utf-8');

// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($list).')';
} else {
  slog($_GET);
  slog($list);
}

