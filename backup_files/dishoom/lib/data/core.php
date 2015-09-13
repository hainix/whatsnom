<?php
define('APC_TTL', 5000);
define('SKIP_APC_CACHE', false);

function get_object($id, $type) {
  return head(get_objects($id, $type));
}

function get_table_map() {
  return
    array('film' => 'films',
          'person' => 'people',
          'image' => 'images',
          'media' => 'media',
          'song' => 'songs',
          'review' => 'reviews',
          'tag' => 'tags',
          'news' => 'news',
          'quote' => 'quotes',
          'article' => 'articles',
          'video' => 'videos',
          'slide' => 'slides',
    );
}

function get_object_name($type) {
  $map = array_flip(get_table_map());
  return idx($map, $type, $type);
}

function get_table_name($type) {
  $map = get_table_map();
  return idx($map, $type, $type);
}

function get_objects_from_db($ids, $type) {
  return get_objects_helper($ids, $type, true);
}

function get_objects($ids, $type) {
  return get_objects_helper($ids, $type, false);
}

function get_object_against_secondary_id($key, $type) {
  $objects = get_objects_against_secondary_id($key, $type);
  if (!$objects || !is_array($objects)) {
    return null;
  }
  return head($objects);
}

function get_objects_against_secondary_id_from_db($key, $type, $secondary_key = null) {
  if (!$secondary_key) {
    $secondary_key_map = get_secondary_id_map();
    $secondary_key = $secondary_key_map[$type];
  }

  $sql = sprintf("SELECT * FROM ".$type." where "
                 .$secondary_key." = %d  and deleted is null",
                 $key);

  $r = mysql_query($sql);
  if (!$r) {
    throw new Exception('Invalid query: ' . mysql_error().' for sql '.$sql);
  }

  $data = array();
  if (mysql_num_rows($r) > 0) {
    while ($row = mysql_fetch_assoc($r)) {
      $row['type'] = get_object_name($type);
      $data[$row['id']] = $row;
    }
  }
  mysql_free_result($r);
  unset($r);

  $apc_key = get_apc_key($type, $key, $secondary_key);
  apc_store($apc_key, serialize($data), APC_TTL);
  return $data;
}

function get_secondary_id_map() {
  return array('songs' => 'film_id',
               'videos' => 'film_id',
               'youtube_films' => 'film_id',
               'images' => 'subject_id',
               'articles' => 'film_id',
               'reviews' => 'film_id');
}

function get_objects_against_secondary_id($key, $type, $secondary_key = null) {
  $type = get_table_name($type);
  $secondary_key_map = get_secondary_id_map();
  if (!isset($secondary_key_map[$type])) {
    throw new Exception('unsupported secondary key type');
  }
  if (!$secondary_key) {
    $secondary_key = $secondary_key_map[$type];
  }
  $apc_key = get_apc_key($type, $key, $secondary_key);
  $apc_data = apc_fetch($apc_key);
  if (!is_admin() && $apc_data !== false) {
    return unserialize($apc_data);
  }
  return get_objects_against_secondary_id_from_db($key, $type, $secondary_key);
}

function get_apc_key($type, $primary_key = null, $secondary_key = null) {
  if (!$primary_key && !$secondary_key) {
    throw new Exception('must define primary or secondary key for apc key');
  }
  $key = $type;
  if ($primary_key) {
    $key = $primary_key.':'.$key;
  }
  if ($secondary_key) {
    $key = $key.':'.$secondary_key;
  }
  return $key;
}

function get_objects_helper($ids, $type, $skip_cache = false) {
  global $link;
  $ids = is_array($ids) ? $ids : array($ids);
  $ids = array_unique($ids);
  $type = get_table_name($type);

  $data = array();
  // Check if we have data in apc. if we do, unset it from the mysql fetch
  if (!$skip_cache && !SKIP_APC_CACHE) {
    foreach ($ids as $key => $id) {
      $apc_data = apc_fetch(get_apc_key($type, $id));
      if ($apc_data === false) {
        continue;
      }
      // We have an APC hit
      $data[$id] = unserialize($apc_data);
      unset($ids[$key]);
    }
  }

  // We still have some ids left
  if ($ids) {
    $sql = sprintf("SELECT * FROM ".$type
                   ." where id in (%s)", implode(',',$ids));
    $sql .= ' and deleted is null ';

    $r = mysql_query($sql);
    if (!$r) {
      slog('Invalid query: ' . mysql_error().' for sql '.$sql);
      return false;
    }

    if (mysql_num_rows($r) > 0) {
      while ($row = mysql_fetch_assoc($r)) {
        // Store in APC for next quick fetch
        $row['type'] = get_object_name($type);
        apc_store(get_apc_key($type, $row['id']), serialize($row), APC_TTL);
        $data[$row['id']] = $row;
      }
    }
    mysql_free_result($r);
    unset($r);
  }
  return $data;
}

function get_object_from_sql($sql, $type = null) {
  return head(get_objects_from_sql($sql, $type));
}

function get_objects_from_sql($sql, $type = null) {
    global $link;
    if (!$sql) {
      return null;
    }
    $r = mysql_query($sql);
    if (!$r) {
      slog(mysql_error());
      return null;
    }

    $rows = array();
    if (mysql_num_rows($r) > 0) {
      while ($row = mysql_fetch_assoc($r)) {
        if ($type) {
          $row['type'] = get_object_name($type);
        }
        $rows[$row['id']] = $row;
      }
    }
    return $rows;
}

function get_ids_from_objects($objects) {
  if (!$objects) {
    return null;
  }
  $ids = array();
  foreach ($objects as $object) {
    $ids[] = $object['id'];
  }
  return $ids;
}

function is_video($object) {
  return ($object['type'] == 'video');
}

function is_person($object) {
  return ($object['type'] == 'person');
}


?>