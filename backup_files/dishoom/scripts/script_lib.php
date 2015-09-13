<?php
$root ='/var/www/html/';
//include_once '../parser.php';
include_once $root.'lib/utils.php';
gc_enable();

// For Scripts, mostly
function get_ids($table = 'films', $offset = 0, $rowcount = 10, $year = null) {
  global $link;
  if ($year) {
    $sql = sprintf("SELECT id FROM ".$table." WHERE year > %d LIMIT %d , %d", $year,
                   $offset, $rowcount);
  } else {
    $sql = sprintf("SELECT id FROM ".$table." LIMIT %d , %d", $offset, $rowcount);
  }

  $r = mysql_query($sql);
  if (!$r) {
    echo 'Invalid query '.$sql.': ' . mysql_error() . "\n";
    return false;
  }

  $ids = array();
  if (mysql_num_rows($r) > 0) {
    while ($row = mysql_fetch_assoc($r)) {
      $ids[] = $row['id'];
    }
  }
  return $ids;
}

function edit_distance($str1, $str2) {
  $str1 = strtolower(rem($str1, array(' ', '_')));
  $str2 = strtolower(rem($str2, array(' ', '_')));
  return strlen($str2) - similar_text($str1, $str2);
}

function remove_annotations($str) {
  return
    rem(preg_replace("/\[[0-9]*\]/", "", $str), '[citation needed]');
}

function parse_args($argv){
  array_shift($argv); $o = array();
  foreach ($argv as $a){
    if (substr($a,0,2) == '--'){ $eq = strpos($a,'=');
      if ($eq !== false){ $o[substr($a,2,$eq-2)] = substr($a,$eq+1); }
      else { $k = substr($a,2); if (!isset($o[$k])){ $o[$k] = true; } } }
    else if (substr($a,0,1) == '-'){
      if (substr($a,2,1) == '='){ $o[substr($a,1,1)] = substr($a,3); }
      else { foreach (str_split(substr($a,1)) as $k){ if (!isset($o[$k])){ $o[$k] = true; } } } }
    else { $o[] = $a; } }
  return $o;
}

function simple_compare($str1, $str2) {
	return (bool) stristr(alpha_only($str1), alpha_only($str2));
}

function alpha_only($str) {
	return ereg_replace("[^A-Za-z0-9]", "", $str);
}

function match_one($regex, $str) {
	return head(preg_split($regex, $str));
}

function film_href_to_imdb_id($url) {
	return (int) rem($url, array('/title/tt', '/'));
}

function object_exists_in_db($id, $table) {
	global $link;
	$sql = sprintf("SELECT * from ".$table." WHERE id = %d LIMIT 1", $id);
	$r = mysql_query($sql);
	if ($r) {
		return mysql_num_rows($r);
	}
}

function get_url($url, $follow = false){
	$proxy = null; //get_proxy();
	echo 'following url: '.$url.' with proxy = '.$proxy.'<br/>';
	$page = get_page(
		//'[proxy IP]:[port]', // use valid proxy
	//	'li56-17.members.linode.com:3128',
		$proxy,
		$url,
		'http://www.google.com/',
		'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8',
		//'Googlebot/2.1 (http://www.googlebot.com/bot.html)',
		1,
		5,
		$follow);
	if (empty($page['ERR'])) {
		return $page['EXE'];
	} else {
		echo $page['ERR'];
		return;
	}
}

function get_page($proxy, $url, $referer, $agent, $header, $timeout, $follow) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	if ($follow) {
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	} else {
	    curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
	}


    $result['EXE'] = curl_exec($ch);
    $result['INF'] = curl_getinfo($ch);
    $result['ERR'] = curl_error($ch);

    curl_close($ch);
    return $result;
}

function curl_url($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $html = curl_exec($ch);
  curl_close($ch);
  return $html;
}


function write_reviews_to_db($reviews, $write = false) {
	global $link;
  hlog('starting review write for '.count($reviews).' reviews');
	foreach ($reviews as $r) {
		if (!$r['film_id']) {
			continue;
		}
		$reviewer = isset($r['reviewer']) ? $r['reviewer'] : '';
		$rating = isset($r['rating']) ? $r['rating'] : 0;
		$excerpt = isset($r['excerpt']) ? $r['excerpt'] : '';
		$sql = sprintf("INSERT INTO reviews (reviewer, film_id, source_name, source_link, rating, thumbs, excerpt, article) VALUES ('%s', %d, '%s', '%s', %d, '%s', '%s','%s')",
			       mysql_real_escape_string($reviewer),
			       $r['film_id'],
			       mysql_real_escape_string($r['source_name']),
			       mysql_real_escape_string($r['source_link']),
			       $rating,
             idx($r, 'thumbs'),
             mysql_real_escape_string($excerpt),
			       mysql_real_escape_string(idx($r, 'article'))
		);

		if (!$write) {
			echo 'FAKE writing to db: '.$sql.'<br/>';
		} else {
			$result = mysql_query($sql);
			if (!$result) {
				$message  = 'Invalid query: ' . mysql_error() . "\n";
				$message .= 'Whole query: ' . $sql;
        slog($message);
				//die($message);
			} else {
				echo 'SAVED DB: '.$sql.'<br/>';
			}
		}
	}
	return true;
}



function get_proxy() {
	$proxies =
array('203.178.133.002:3128',
'212.45.5.172:3128',
'95.56.229.118:3128',
'129.242.19.197:3128',
'64.146.167.26:3128',
'190.208.46.98:3128',
'203.139.145.2:3128',
'202.112.49.244:3128',
'205.138.115.85:3128',
'200.84.203.19:3128',
'41.217.215.203:3128',
'146.57.249.98:3128',
'190.202.87.131:3128',
'147.102.224.227:3128',
'137.165.1.111:3128',
'138.246.99.249:3128',
'122.155.13.135:3128',
'64.76.25.227:3128',
'81.177.144.176:3128',
'143.215.131.198:3128',
'203.30.254.186:3128',
'201.88.254.242:3128',
'133.11.240.56:3128',
'129.24.211.29:3128',
'190.225.164.12:3128',
'92.66.115.169:3128',
'140.114.79.233:3128',
'194.42.17.124:3128',
'202.147.249.55:3128',
'200.19.159.35:3128',
'130.37.193.141:3128',
'204.85.191.10:3128',
'156.17.10.52:3128',
'41.222.192.218:3128',
'174.142.24.202:3128',
'222.89.92.106:3128',
'196.205.160.3:3128',
'95.69.252.90:3128',
'150.165.29.252:3128',
'195.228.212.158:3128',
'217.109.99.37:3128',
'41.190.38.166:3128',
'187.105.220.24:3128',
'91.203.36.25:3128',
'190.203.69.69:3128',
'72.51.41.235:3128',
'131.247.2.247:3128',
'202.23.159.51:3128',
'202.189.126.85:3128',
'201.245.64.98:3128');
return idx($proxies, rand(0, count($proxies)-1));

}


function sevenchar($id) {
	$id = (string) $id;
	switch (strlen($id)) {
		case 7: return $id;
		case 6: return '0'.$id;
		case 5: return '00'.$id;
		case 4: return '000'.$id;
		case 3: return '0000'.$id;
		case 2: return '00000'.$id;
		default: echo 'could not parse id '.$id.'to seven chars';
	}
}

function strip($t) {
	return trim(preg_replace('/\s\s+/', ' ', $t));
}


function ar($t) {
	return $t ? mysql_real_escape_string(serialize($t)) : '';
}

function tr($t) {
    return $t ? mysql_real_escape_string($t) : null;
}

function hlog($t) {
  if (is_array($t)) {
    print_r($t);
  } else {
    echo $t;
  }
  echo "\n";
}

function remove_newlines($string) {
  return (string)str_replace(array("\r", "\r\n", "\n"), '', $string);
}

function get_new_id($old_id) {
  global $link;
  $r = mysql_query(sprintf("SELECT id FROM films WHERE DELETE_oldid"
			   ." = %d LIMIT 1", $old_id));
  return head(mysql_fetch_row($r));
}

?>