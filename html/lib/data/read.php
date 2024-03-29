<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/base.php';

final class DataReadUtils {

  public static function isSupportedAssoc($type) {
    switch ($type) {
    case 'bookmarks':
    case 'votes':
      return true;
    }
    return false;
  }

  public static function getAssoc($creator_id, $target_id, $type) {
    $sql =
      sprintf(
        "SELECT * FROM %s WHERE creator_id = %.0f AND target_id = %d"
        ." AND deleted is NULL",
        $type,
        $creator_id,
        $target_id
      );
    $sql .= " LIMIT 1";
    return get_object_from_sql($sql);
  }

  public static function getAllBookmarksForUser($creator, $limit = 500) {
    $sql =
      sprintf(
        "SELECT * FROM bookmarks WHERE creator_id = %.0f "
        ." AND deleted is NULL LIMIT %d",
        $creator['id'],
        $limit
      );
    return get_objects_from_sql($sql);
  }

  public static function getAllOutgoingAssocs($creator, $type, $limit = 500) {
    if (!self::isSupportedAssoc($type)) {
      return false;
    }
    $sql =
      sprintf(
        "SELECT * from %s WHERE creator_id = %.0f "
        ." AND deleted is NULL LIMIT %d",
        $type,
        $creator['id'],
        $limit
      );
    return get_objects_from_sql($sql);
  }

  public static function getGenericSpotsForQuery($query, $limit = 10) {
    $sql =
      sprintf(
        "SELECT * FROM spots WHERE type = %d AND city_id = %d "
        ."AND deleted IS NULL AND rating > %d AND review_count > %d "
        ."ORDER BY last_updated ASC "
        ."LIMIT %d",
        $query->getType(),
        $query->getCity(),
        40,
        50,
        $limit
      );
    return get_objects_from_sql($sql);
  }

  public static function getListsForQuery($query, $limit = 1) {
    $sql =
      sprintf(
        "SELECT * FROM lists WHERE type = %d AND city = %d "
        ."AND deleted IS NULL ORDER BY upvotes DESC LIMIT %d",
        $query->getType(),
        $query->getCity(),
        $limit
      );
    return
      $limit == 1
      ? get_object_from_sql($sql)
      : get_objects_from_sql($sql);
  }

  public static function getAllListsForCreator($user, $limit = 500) {
    $sql =
      sprintf(
        "SELECT * FROM lists WHERE creator_id = %.0f "
        ."AND deleted IS NULL ORDER BY city, upvotes DESC LIMIT %d",
        $user['id'],
        $limit
      );
    return get_objects_from_sql($sql);
  }

  public static function getListForCreator($type_id, $city_id,
                                           $creator_id) {
    $sql =
      sprintf(
        "SELECT * from lists where "
        ."type = %d AND "
        ."city = %d AND "
        ."creator_id = %.0f "
        ."AND deleted is null LIMIT 1",
        (int) $type_id,
        (int) $city_id,
         $creator_id
      );
    return get_object_from_sql($sql);
  }

  public static function getTopListsForCity($city_id, $limit = 1) {
    $sql =
      sprintf(
        "SELECT * FROM lists WHERE city = %d"
        ." AND type != 0"
        ." AND deleted IS NULL ORDER BY created_time DESC LIMIT %d",
        $city_id,
        $limit
      );
    return
      $limit == 1
      ? get_object_from_sql($sql)
      : get_objects_from_sql($sql);
  }

  public static function getRecentListsForCity($city_id, $limit = 1) {
    $sql =
      sprintf(
        "SELECT * FROM lists WHERE city = %d"
        ." AND deleted IS NULL ORDER BY created_time DESC LIMIT %d",
        $city_id,
        $limit
      );
    return
      $limit == 1
      ? get_object_from_sql($sql)
      : get_objects_from_sql($sql);
  }

  public static function getEntriesForList($list) {
    $sql =
      sprintf(
        "SELECT * FROM entries WHERE list_id = %d AND DELETED IS NULL ORDER "
        ."BY position ASC LIMIT ".ListTypeConfig::NUM_PER_LIST,
        $list['id']
      );
    return get_objects_from_sql($sql);
  }

  public static function getEntryForListAndSpot($list, $spot) {
    $sql =
      sprintf(
        "SELECT * FROM entries WHERE list_id = %d AND spot_id = %d AND DELETED IS NULL LIMIT 1",
        $list['id'],
        $spot['id']
      );
    return get_object_from_sql($sql);
  }

  public static function getSpotsFromHandles($handles) {
    $sql = sprintf(
      "SELECT * FROM spots WHERE yelp_id in ('%s') AND DELETED IS NULL "
      ."LIMIT %d",
      implode($handles, "','"),
      count($handles)
    );
    return get_objects_from_sql($sql);

  }

  public static function cln($t) {
    return mysql_real_escape_string(trim(stripslashes($t)));
  }


}


?>