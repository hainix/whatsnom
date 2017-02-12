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
        'name' => 'Clubs',
        'type' => 66,
        'cover' => 'http://www.whatsnom.com/covers/club.jpg',
      ),
    );
    return array_slice($lists, 0, $limit, true);
  }
}

?>