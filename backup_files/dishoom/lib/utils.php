<?php
ob_start();

define('SECONDS_PER_DAY', 86400);
include_once 'core/base.php';
include_once 'core/funcs.php';
include_once 'core/facebook.php';
include_once 'display/render.php';
include_once 'display/units.php';
include_once 'display/search.php';
include_once 'core/youtube.php';
include_once 'core/image.php';
include_once 'core/page.php';
include_once 'data/core.php';
include_once 'data/custom.php';
include_once 'constants.php';

if (is_admin()) {
  ini_set('display_errors', 'On');
  error_reporting(E_ALL);
  set_error_handler("customError");
}

function is_mobile() {
  $user_agent = idx($_SERVER, 'HTTP_USER_AGENT');
  return (strpos($user_agent, 'iP') !== FALSE);
}

function customError($errno, $errstr) {
  echo "<b>Error:</b> [$errno] $errstr<br />";
}

function starts_with_capital_letter($str) {
    return $str && preg_match("/[A-Z]/", idx(trim($str), 0));
}

function ensure_ends_with_period($str) {
    $str = ucfirst(trim($str));
    return ends_with_period($str) ? $str : $str.'.';
}

function ends_with_period($str) {
    $last_char = substr($str, strlen($str)-1, 1);
    return $str && ($last_char === '.' || $last_char === '!');
}

function first_sentence($content) {
  $content = html_entity_decode(strip_tags($content));
  $pos = strpos($content, '. ');

  if($pos === false) {
    return $content;
  }
  return substr($content, 0, $pos+1);

}

function d_date($timestamp){
	$difference = time() - $timestamp;
	$periods = array("sec", "min", "hour", "day", "week",
	"month", "years", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");

	if ($difference > 0) { // this was in the past
		$ending = "ago";
	} else { // this was in the future
		$difference = -$difference;
		$ending = "to go";
	}
	for ($j = 0; $difference >= $lengths[$j]; $j++)
	$difference /= $lengths[$j];
	$difference = round($difference);
	if($difference != 1) $periods[$j].= "s";
	$text = "$difference $periods[$j] $ending";
	return $text;
}

function pretty_num($n) {
	if($n>1000000000000) return round(($n/1000000000000),1).'T';
	else if($n>1000000000) return round(($n/1000000000),1).'B';
	else if($n>1000000) return round(($n/1000000),1).'M';
	else if($n>1000) return round(($n/1000),1).'K';
	return number_format($n);
}


function slog($a) {
  if (!is_admin()) {
    return;
  }
  echo '<br/><br/><br/>';
  if (is_array($a)) {
    echo '<pre>'.print_r($a, true).'</pre>';
  } else {
    echo $a;
  }
}

function go_404() {
  header("HTTP/1.0 404 Not Found");
  include_once $_SERVER['DOCUMENT_ROOT'].'/err/404.php';
  exit(1);
}

function match_all($regex, $str, $i = 0) {
  if (preg_match_all($regex, $str, $matches) === false) {
    return false;
  } else {
    return $matches[$i];
  }
}

function match($regex, $str, $i = 0) {
  if (preg_match($regex, $str, $match) == 1) {
    return $match[$i];
  } else {
    return false;
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

function strip_html($t) {
  return trim(str_replace("\r\n", "", strip_tags($t, '<p><br><rm><b><br/>')));
}

function get_parsed_ids($arr, $key_g) {
  return get_parsed_ids_from_array(array($arr), $key_g);
}

function get_parsed_ids_from_array($arr, $key_g) {
  $ids = array();
  foreach ($arr as $key => $obj) {
    if ($obj[$key_g]) {
      if (!is_array($obj[$key_g])) {
	//$arr[$key][$key_g] = unserialize($obj[$key_g]);
	$arr[$key][$key_g] = explode(',', $obj[$key_g]);
      }
      foreach ($arr[$key][$key_g] as $id) {
	if (!is_numeric($id)) {
	  continue;
	}
	$ids[$id] = true;
      }
    }
  }
  return array_keys($ids);
}

function get_alpha($string) {
  return preg_replace('/[^A-Za-z0-9]/', '' ,$string);
}


?>
