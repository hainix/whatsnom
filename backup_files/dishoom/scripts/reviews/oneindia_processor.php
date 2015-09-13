<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
<div align="left">
<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';
include_once 'specific_review_parsers.php';

$write = false;

set_time_limit(0);
define('WORKABLE_CHUNK_SIZE', 1);
$start = isset($_GET['s']) ? $_GET['s'] : 0; // where to start

$full_map = get_map();
$map = head(array_slice($full_map, $start * WORKABLE_CHUNK_SIZE, WORKABLE_CHUNK_SIZE, true));
echo '<h1>index '.$start.' of '.count($full_map).'<br/>';
echo 'processing films: '.print_r($map, true).'</h1>';

$film = get_object($map['film_id'], 'films');

$html = str_get_html(get_url($map['source_link']));
$html = array('content' => $html);
$data = array(); $err = array();
get_oneindia_rating($film, $data, $err, $html, $map['source_link']);
//write_reviews_to_db($data, $write);
echo '<pre>'.print_r($data, true).'</pre>';

function get_map() {
	global $link;
	$sql = "SELECT * FROM  `reviews` WHERE  `source_link` LIKE  'http://movies.indiatimes.com%'";
	$r = mysql_query($sql);

	$objs = array();
	if (mysql_num_rows($r)>0) {
		while ($row = mysql_fetch_assoc($r)) {
			$objs[] = $row;
		}
	}
	return $objs;
}

?>
</div>