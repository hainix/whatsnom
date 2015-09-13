<?php

include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';
//include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/api/yelp.php';

$city_id = Cities::NYC;
$lists = DataReadUtils::getTopListsForCity($city_id, 5);
slog($lists);
slog(ListTypeConfig::$config);