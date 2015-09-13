<?php

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
   *  pulls all values from a 2d array matching key
   */
function array_pull($arr, $key) {
  $ret = array();
  foreach ($arr as $k => $v) {
    $ret[] = $v[$key];

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

