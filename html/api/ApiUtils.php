<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/constants.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ImageUtils.php';

function cmpByPosition($a, $b)  {
  return (((int) $a['position']) > ((int) $b['position']));
}

final class ApiUtils {

  const PROFILE_IMAGE_TYPE = 'SMALLER'; // ORIGINAL, SMALLER, PHPTHUMB
  const BASE_URL =  'http://www.whatsnom.com/';
  const API_APC_TTL = 10000;
  const API_SKIP_APC = true; // this should always be false

  public static function addListConfigToList($list) {
    $config_for_list = ListTypeConfig::$config[$list['type']];

    $list['list_genre'] = $config_for_list[ListTypeConfig::GENRE];

    $list['name'] = $config_for_list[ListTypeConfig::LIST_NAME];
    $list['entry_name'] = $config_for_list[ListTypeConfig::ENTRY_NAME];
    $list['plural_entry_name'] =
      $config_for_list[ListTypeConfig::PLURAL_ENTRY];
    $list['cover'] = self::BASE_URL . 'covers/'
      . $config_for_list[ListTypeConfig::COVER];
    $list['city_name'] = Cities::getName($list['city']);
    return $list;
  }

  public static function addListDataToList($list) {
    $list_id = $list['id'];

    // Merge render info about the list type into the response
    $list = self::addListConfigToList($list);
    $entries = DataReadUtils::getEntriesForList($list);

    // Parse and put the list items on the list
    $entries_keyed_by_spot_id = array();
    $spot_names = array();
    $list_review_count = 0;
    foreach ($entries as $entry_key => $entry) {
      $spot = get_object($entry['spot_id'], 'spots');
      $spot['city_name'] = Cities::getName($spot['city_id']);
      $new_entry = $entry;
      $new_entry['place'] = $spot;
      $new_entry['name'] = $spot['name'];
      $new_entry['snippet'] = $entry['tip'] ?: idx($spot, 'snippet');

      $src = $spot['profile_pic'];
      if (self::PROFILE_IMAGE_TYPE == 'PHPTHUMB') {
        $src = $spot['profile_pic'];
        $src =
          ImageUtils::resizeCroppedSrc(
            $src,
            array('width' => 450, 'height' =>150)
          );
        $src = BASE_URL.$src;
      } else if (self::PROFILE_IMAGE_TYPE == 'SMALLER') {
        if (stripos($spot['profile_pic'], 'yelpcdn')
            && stripos($spot['profile_pic'], 'o.jpg')) {
          $src = str_replace('o.jpg', 'l.jpg', $spot['profile_pic']);
        }
      } else {
        // Default to original image
      }

      $new_entry['list_item_thumbnail'] = $src;
      $entries_keyed_by_spot_id[$entry['spot_id']] = $new_entry;
      $spot_names[] = $spot['name'];
      $list_review_count += (int) $spot['review_count'];
    }

    $list['snippet'] = implode(array_slice($spot_names, 0, 5), ', ');
    $list['review_count'] = number_format($list_review_count);
    $list['critic_count'] =
      number_format($list_review_count % 5 + 1); // TODO: legitify

    // Sort entries by rank on list, and rekey to the positions for render
    usort($entries_keyed_by_spot_id, "cmpByPosition");
    $list['entries'] = $entries_keyed_by_spot_id;
    return $list;
  }


}