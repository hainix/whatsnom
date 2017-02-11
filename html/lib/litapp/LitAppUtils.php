<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';

final class LitAppUtils {
  public static function getLitListResponseForCity($city_id, $limit = 1) {
    $lists = array(
      0 => array('isDivider' => 1, 'name' => 'Lit Right Now'),
      1 => array(
        'city' => 4,
        'id' => 1,
        'items' => array(),
        'name' => 'Clubs',
        'type' => 66,
        'cover' => 'http://www.whatsnom.com/covers/club.jpg',
        'spot_count' => 17,
        'snippet' => "Lit AF Clubs atm",
        'review_count' => 1564,
        'critic_count' => 5,
      ),
    );
    return array_slice($lists, 0, $limit, true);
  }
}

?>