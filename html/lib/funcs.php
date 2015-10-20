<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/FacebookUtils.php';


function shuffle_assoc($list) {
  if (!is_array($list)) return $list;

  $keys = array_keys($list);
  shuffle($keys);
  $random = array();
  foreach ($keys as $key) {
    $random[$key] = $list[$key];
  }
  return $random;
}

function array_sort($array, $on, $order=SORT_ASC) {
  $new_array = array();
  $sortable_array = array();
  if (count($array) > 0) {
    foreach ($array as $k => $v) {
      if (is_array($v)) {
        foreach ($v as $k2 => $v2) {
          if ($k2 == $on) {
            $sortable_array[$k] = $v2;
          }
        }
      } else {
        $sortable_array[$k] = $v;
      }
    }

    switch ($order) {
    case SORT_ASC:
      asort($sortable_array);
      break;
    case SORT_DESC:
      arsort($sortable_array);
      break;
    }

    foreach ($sortable_array as $k => $v) {
      $new_array[$k] = $array[$k];
    }
  }
  return $new_array;
}

/**
 * Pulls items from object array $objects that have keys in $ids
 */
function array_select_keys($objects, $ids) {
  $ret = array();
  foreach ($ids as $id) {
    $ret[$id] = $objects[$id];
  }
  return $ret;
}

function array_merge_unique(array $arrays) {
  $output = array();
  foreach ($arrays as &$array) {
    foreach ($array as $k => &$v) {
      $output[$v] = 1;
    }
  }
  return array_keys($output);
}



/*
 * Pulls all values from a 2d array matching key
 */
function array_pull($arr, $key) {
  $ret = array();
  foreach ($arr as $k => $v) {
    $ret[$k] = $v[$key];

  }
  return array_filter($ret);
  }

function array_pull_unique($arr, $key) {
  $ret = array();
  foreach ($arr as $k => $v) {
    if ($key == $key) {
      $ret[$v] = true;
    }
  }
  return array_flip($ret);
}

/**
 * $arr = array(
 *   array('id' => 5, 'city' => 2),
 *   array('id' => 8, 'city' => 1),
 *   array('id' => 9, 'city' => 2),
 * )
 * array_group_by_key($arr, 'city') returns:
 * array(
 *   2 =>
 *     array(
 *       array('id' => 5, 'city' => 2),
 *       array('id' => 9, 'city' => 2),
 *     ),
 *   1 =>
 *     array(
 *       array('id' => 8, 'city' => 1),
 *     ),
 * )
 */
function array_group_by_key($arr, $key) {
  if (!$key || !$arr) {
    return null;
  }
  $ret = array();
  foreach ($arr as $elem) {
    if (!isset($ret[$elem[$key]])) {
      $ret[$elem[$key]] = array();
    }
    $ret[$elem[$key]][] = $elem;
  }
  return $ret;
}


function idx($arr, $key, $default = null) {
  if (!isset($arr[$key])) {
    return $default;
  }
  return $arr[$key];
}

function head_key(array $arr) {
  reset($arr);
  return key($arr);
}


function head($a) {
  foreach ($a as $v) {
    return $v;
  }
}

function rem($text, $removals) {
  if (!is_array($removals)) {
    $removals = array($removals);
  }
  foreach ($removals as $removal) {
    $text = str_replace($removal, '', $text);
  }
  return trim($text);
}

function hasHTML($text) {
  return strlen($text) == strlen(strip_tags($text));
}

function strip_html($t) {
  return trim(str_replace("\r\n", "", strip_tags($t, '<p><br><rm><b><br/>')));
}

function clean_address($address) {
  return preg_replace( '/\s*\d+$/', '', $address);
}

function is_admin() {
  $user = FacebookUtils::getUser();
  return ($user && $user['id'] == get_admin());
}
function get_admin() {
  return 10104624213101750;
}

function slog($var) {
  if (is_admin()) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
  }
}
