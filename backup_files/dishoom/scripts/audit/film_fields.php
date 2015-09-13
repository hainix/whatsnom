<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';

$start = isset($_GET['s']) ? $_GET['s'] : 0; // where to start
$id = isset($_GET['id']) ? $_GET['id'] : 0; 
$force = isset($_GET['f']) ? $_GET['f'] : false; 
$write = isset($_GET['w']) ? $_GET['w'] : false; 
$year = isset($_GET['y']) ? $_GET['y'] : 2000; 

define('WORKABLE_CHUNK_SIZE',15);
set_time_limit(0);
ini_set('memory_limit', '64M');

$ids = $id ? array($id) : get_ids('films', $start*WORKABLE_CHUNK_SIZE, WORKABLE_CHUNK_SIZE);

// to parse more/less, add fields here
$objects = get_objects($ids, 'films',  array('cast', 'title', 'id'));

$data = array();
$err = array();

if ($ids && $objects) {
	echo '<h2>against film ids: '.implode(', ',$ids).'</h2><br/>';
	foreach ($objects as $film) {
		$cast = unserialize($film['cast']);
		if (!$cast) {
			echo '<h2>no cast for film : '.$film['title'].'</h2><br/>';
			mark_todo($film['id'], 'films', 'cast');	
		} else {
			foreach ($cast as $person) {
				if (!person_exists_in_db($person['id'])) {
					mark_todo($person['id'], 'people', 'add');	
				}
			}
		}
		slog($cast);
	}
} else {
	echo 'bogus man. wevs. die.'; die(1);	
}


echo '<br/>data: <pre>'.print_r($data, true).'</pre>';
echo '<br/>errors: <pre>'.print_r($err, true).'</pre>';


function person_exists_in_db($id) {
	global $link;
	$sql = sprintf("SELECT * from people WHERE id = %d LIMIT 1", $id);
	$r = mysql_query($sql);
	if ($r) {
		return mysql_num_rows($r);
	}
}

function mark_todo($id, $type, $action) {
	global $link;
	$sql = sprintf("INSERT IGNORE INTO to_add (id, type, action) "
			."VALUES (%d, '%s', '%s')",
			$id, $type, $action);
	$result = mysql_query($sql);		
	if (!$result) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	} else {
		echo 'marked in db with sql: '.$sql.'<br/>';
	}
}


?>
