<?php
require_once ('script_lib.php');

$start = isset($_GET['s']) ? $_GET['s'] : 0; // where to start
$object_type = isset($_GET['t']) ? $_GET['t'] : 'person'; // type of object, film or person

if (!in_array($object_type, array('film', 'person'))) {
	echo 'unrecognized object type';
	exit(1);
}

define('WORKABLE_CHUNK_SIZE', 5);
set_time_limit(0);
ini_set('memory_limit', '64M');

$objects =  get_objects_with_poster_src($object_type, $start*WORKABLE_CHUNK_SIZE, WORKABLE_CHUNK_SIZE);

if (!$objects) {
	echo 'no objects fool';
	exit(1);
}

foreach ($objects as $id => $object) {

	// see if poster already exists
	$save_path = '../images/'.$object_type.'/'.$id.'.jpg';
	if (file_exists($save_path)) {
		echo 'already have poster for '.$object_type.' '.$id.' at '.$save_path."<br/>";
		continue;
	}

	$url = $object['poster_src'];
	if (!$url) {
		continue;
	}

	$ch = curl_init ("http://www.dishoomreviews.com/lib/display/movieposter.php?url=".$url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	$rawdata=curl_exec ($ch);
	curl_close ($ch);

	$fp = fopen($save_path, 'w');
	fwrite($fp, $rawdata);
	fclose($fp);

	echo 'saved poster for '.$object_type.' id '.$id.' to '.$save_path.' from url '.$url.'<br/>';
}


function get_objects_with_poster_src($type, $offset = 0, $rowcount = 10) {
	global $link;
	$tables = array('film' => 'films', 'person' => 'people');
	$sql = sprintf("SELECT poster_src, id  FROM ".$tables[$type]." WHERE poster_src LIKE '%s' LIMIT %d , %d", 'http%', $offset, $rowcount);
	$r = mysql_query($sql);
	if (!$r) {
		echo 'Invalid query: ' . mysql_error() . "|| ".$sql."\n";
		return false;
	}
	$objects = array();
	if (mysql_num_rows($r)>0) {
		while ($row = mysql_fetch_assoc($r)) {
			$objects[$row['id']] = $row;
		}
	}
	return $objects;
}



?>
