<?php
define('APC_TTL', 5000);
define('SKIP_APC_CACHE', true);

define('SEC_IN_DAY', 86400);

function get_object($id, $type) {
  return head(get_objects($id, $type));
}

function get_objects_from_db($ids, $type) {
  return get_objects_helper($ids, $type, true);
}

function get_objects($ids, $type) {
  return get_objects_helper($ids, $type, false);
}

function get_objects_helper($ids, $type, $skip_cache = false) {
  global $link;
  $ids = is_array($ids) ? $ids : array($ids);
  $ids = array_unique($ids);

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
                   ." where id in (%s)", implode(',', $ids));
    $sql .= ' and deleted is null ';
    $r = mysql_query($sql);
    if (!$r) {
      slog('Invalid query: ' . mysql_error().' for sql '.$sql);
      return false;
    }

    if (mysql_num_rows($r) > 0) {
      while ($row = mysql_fetch_assoc($r)) {
        // Store in APC for next quick fetch
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

function get_objects_from_sql($sql) {
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
        $rows[$row['id']] = $row;
      }
    }
    return $rows;
}

function get_apc_key($type, $id) {
  return $type.':'.$id;
}

?>