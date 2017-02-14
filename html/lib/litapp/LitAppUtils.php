<?php
//include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';

final class LitAppUtils {

  // TODO - fetch from db
  public static function getLitListResponseForCity($city_id, $limit = 1) {
    $lists = array(
      1 => array(
        'city' => 4,
        'id' => 1,
        'items' => array(),
        'name' => "let's dance",
        'type' => 66,
        'cover' => 'club.jpg',
      ),
      112 => array(
        'city' => 4,
        'id' => 112,
        'items' => array(),
        'name' => 'buy me a drink',
        'type' => 59,
        'cover' => 'pickup.jpg',
      ),
      124 => array(
        'city' => 4,
        'id' => 124,
        'items' => array(),
        'name' => 'bring the crew',
        'type' => 6,
        'cover' => 'group.jpg',
      ),
    );
    return array_slice($lists, 0, $limit, true);
  }
}

?>