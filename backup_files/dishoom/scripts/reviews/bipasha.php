<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
<div align="left">
<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once 'specific_review_parsers.php';
include_once '../script_lib.php';

$start = isset($_GET['s']) ? $_GET['s'] : 0; // where to start
$id = isset($_GET['id']) ? $_GET['id'] : 0; 
$force = isset($_GET['f']) ? $_GET['f'] : false; 
$write = isset($_GET['w']) ? $_GET['w'] : false; 

define('WORKABLE_CHUNK_SIZE', 1);
set_time_limit(0);
//ini_set('memory_limit', '32M');

$film_ids = $id ? array($id) : get_ids('films', $start*WORKABLE_CHUNK_SIZE, WORKABLE_CHUNK_SIZE);
$films = get_objects($film_ids, 'films',  array('id', 'title', 'year', 'votes', 'rating'));
echo 'against films: '.print_r($films, true).'<br/>';

$data = array();
$err = array();

foreach ($films as $film) {

	//get_rediff_rating($film, $data, $err);
	//get_oneindia_rating($film, $data, $err);
	get_timesofindia_rating($film['id'], $data, $err);
	//get_ndtv_rating($film, $data, $err);
	//get_masand_rating($film, $data, $err);
	//get_planetbw_rating($film, $data, $err, $force, $write);	
}
echo 'data: <pre>'.print_r($data, true).'</pre>';
echo 'errors: <pre>'.print_r($err, true).'</pre>';


write_reviews_to_db($data, $write);






?>
</div>