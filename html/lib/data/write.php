<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/api/yelp.php';

final class DataWriteUtils {

  public static function addAssoc($creator_id, $target_id, $type) {
    if (!DataReadUtils::isSupportedAssoc($type)) {
      return false;
    }
    $sql =
      sprintf(
        "INSERT IGNORE INTO %s "
        ."(creator_id, target_id, created_time) "
        ."VALUES (%.0f, %d, %d)"
        ." ON DUPLICATE KEY UPDATE deleted = NULL",
        $type,
        $creator_id,
        $target_id,
        time()
      );
    global $link;
    $r = mysql_query($sql);
    if (!$r) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      die($message);
    }
    $bookmark_id = mysql_insert_id();
    return $bookmark_id;
  }
  public static function removeAssoc($creator_id, $target_id, $type) {
    if (!DataReadUtils::isSupportedAssoc($type)) {
      return false;
    }
    $sql =
      sprintf(
        "UPDATE %s SET deleted = 1 WHERE "
        ."creator_id = %.0f AND target_id = %d limit 1",
        $type,
        $creator_id,
        $target_id
      );
    global $link;
    $r = mysql_query($sql);
    if (!$r) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      die($message);
    }
    return true;
  }

  public static function alterListVotes($list, $delta) {
    if (!$list) {
      slog($list);
      slog('return false isset');
return false;
    }
    if (!$delta || !is_numeric($delta)) {
      slog('alter list vote error');
      return false;
    }
    $new_votes = $list['upvotes'] + $delta;
    $sql =
      sprintf(
        "UPDATE lists SET upvotes = %d WHERE "
        ."id = %d limit 1",
        $new_votes,
        $list['id']
      );

    global $link;
    $r = mysql_query($sql);
    if (!$r) {
      $message  = '[Alter List] Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      die($message);
    }
    return $new_votes;
  }

  public static function createList($type_id, $city_id, $creator_id) {
    $sql =
      sprintf(
        "INSERT IGNORE INTO lists "
        ."(type, city, creator_id, created_time) "
        ."VALUES (%d, %d, %.0f, %d)",
        $type_id,
        $city_id,
        $creator_id,
        time()
      );

    global $link;
    $r = mysql_query($sql);
    if (!$r) {
      $message  = '[Create List] Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $q;
      die($message);
    }
    $list_id = mysql_insert_id();
    return $list_id;
  }

  function addEntryToList($list_id, $position, $spot_id, $tip) {
    if (!$list_id || !$position || !$spot_id) {
      slog('trying to add entry with null params');
      return null;
    }
    $sql =
      sprintf(
        "INSERT INTO entries (list_id, position, spot_id, tip) VALUES "
        ."(%d, %d, %d, '%s') ON DUPLICATE KEY UPDATE spot_id = %d, tip = '%s',"
        ." deleted = NULL",
        $list_id,
        $position,
        $spot_id,
        DataReadUtils::cln($tip),
        $spot_id,
        DataReadUtils::cln($tip)
      );
    global $link;
    $r = mysql_query($sql);
    if (!$r) {
      $message  = '[Add Entry to List] Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $q;
      slog($message);
      return false;
    }
    return true;
  }

  function addNewSpot($yelp_id, $type_id = null, $city_id = null, $debug = false) {
    $existing_spot = get_object_from_sql(
      sprintf(
        "SELECT * from spots where yelp_id = '%s' AND deleted is null LIMIT 1",
        $yelp_id
      )
    );
    if ($existing_spot) {
      if ($debug) {
        slog('----DB SKIP: spot '.$yelp_id.' exists, skipping add');
      }
      return true;
    }
    $info = get_yelp_business_info($yelp_id);
    if (array_filter($info)) {
      global $link;
      if ($debug) {
        slog('----DB WRITE: '.$yelp_id.' of type '.$type_id.' to city id '.$city_id);
      }
      $sql =
        sprintf(
          "INSERT IGNORE into spots (name, rating, address, phone, profile_pic, "
          ."yelp_id, type, review_count, last_updated, city_id, snippet, latitude, longitude, neighborhoods, cross_streets) "
          ."values ('%s', %d, '%s', '%s', '%s', '%s', %d, %d, %d, %d, '%s', '%s', '%s', '%s', '%s')",
          DataReadUtils::cln($info['name']),
          $info['rating'],
          DataReadUtils::cln($info['address']),
          $info['phone'],
          $info['profile_pic'],
          $info['yelp_id'],
          $type_id,
          $info['review_count'],
          time(),
          $city_id,
          DataReadUtils::cln($info['snippet']),
          $info['lat'],
          $info['long'],
          DataReadUtils::cln($info['neighborhoods']),
          DataReadUtils::cln($info['cross_streets'])
        );
      $r = mysql_query($sql);
      if (!$r) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $sql;
        slog($message);
        return false;
      }
    }
    return true;
  }

  function updateSpot($spot_id) {
    $spot = get_object($spot_id, 'spots');
    $yelp_id = $spot['yelp_id'];
    $info = get_yelp_business_info($yelp_id);
    if (array_filter($info)) {
      global $link;
      $sql =
        sprintf(
          "UPDATE spots set name = '%s', rating = %d, address = '%s', "
          ."phone = '%s', profile_pic = '%s', review_count = %d, "
          ."last_updated = %d "
          ."where id = %d  LIMIT 1",
          DataReadUtils::cln($info['name']),
          $info['rating'],
          DataReadUtils::cln($info['address']),
          $info['phone'],
          $info['profile_pic'],
          $info['review_count'],
          time(),
          $spot_id
        );
      $r = mysql_query($sql);
      if (!$r) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $sql;
        slog($message);
      }
    }
  }
}
?>