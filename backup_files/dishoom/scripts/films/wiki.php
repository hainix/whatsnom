<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';

// this file takes the name of a movie and guesses its wikipedia link
set_time_limit(0);
ini_set('memory_limit', '60M');
$vars = parse_args($argv);

// usage foo.php i=1234
$id = idx($vars, 'i');
// google scrape block came up at 1587, nov 26
$start = idx($vars, 's', 1587);
$break_condition = $id ? false : true;

$i = $start;
$film = null;
do {
  unset($film);
  if (!$id) {
    $objects = get_objects_from_sql(
      sprintf("select id, title, year, wiki_handle from films where wiki_handle = '' order by votes".
	      " desc limit %d, %d",
	      $i++, 1));
    if (!$objects) {
      hlog('[[script complete]]');
      exit(1);
    }
  $film = head($objects);
  } else {
    $film = get_object($id, 'films', array('id', 'title', 'year', 'wiki_handle'));
  }
  hlog('['.$i.'] starting wiki film lookup for '.$film['id'].' - '.$film['title']);

  // this fetches info
  $info = get_wiki_info($film);
  if ($info) {
    update_wiki_info_in_db($info);
  }

} while ($break_condition);


function get_url_contents($url) {
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: dishoom (+http://dishooms.com/tools/)');
	$res = curl_exec($ch);
	curl_close($ch);
	return json_decode($res, true);
}

function update_wiki_info_in_db($r) {
	global $link;
	$sql = sprintf("update films set
			wiki = '%s',
			wiki_handle = '%s',
			wiki_summary = '%s',
			wiki_plot = '%s'
			where id = %d LIMIT 1",
		       tr(idx($r, 'wiki')),
		       tr(idx($r, 'wiki_handle')),
		       tr(idx($r, 'wiki_summary')),
		       tr(idx($r, 'wiki_plot')),
		       $r['id']);
	$result = mysql_query($sql);
	if (!$result) {
	  $message  = 'Invalid query: ' . mysql_error() . "\n";
	  $message .= 'Whole query: ' . $sql;
	  hlog('[err]--'.$message);
	} else {
	  hlog('--- saved to db');
	}
	unset($result);
	hlog('**mem used = '.memory_get_usage());
	return true;
}


?>