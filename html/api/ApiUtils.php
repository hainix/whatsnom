<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';


final class ApiUtils {

  const BASE_URL =  'http://www.whatsnom.com/';
  const API_APC_TTL = 10000;
  const API_SKIP_APC = false;

  public static function addListConfigToList($list) {
    $config_for_list = ListTypeConfig::$config[$list['type']];

    $list['list_genre'] = $config_for_list[ListTypeConfig::GENRE];

    $list['name'] = $config_for_list[ListTypeConfig::LIST_NAME];
    $list['entry_name'] = $config_for_list[ListTypeConfig::ENTRY_NAME];
    $list['plural_entry_name'] =
      $config_for_list[ListTypeConfig::PLURAL_ENTRY];
    $list['icon'] =
      self::BASE_URL . 'icondir/'
      . $config_for_list[ListTypeConfig::ICON] .'.png';
    $list['city_name'] = Cities::getName($list['city']);
    return $list;
  }

}