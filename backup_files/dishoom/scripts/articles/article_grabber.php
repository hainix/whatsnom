<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
<div align="left">
<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';
include_once '../reviews/specific_review_parsers.php';
set_time_limit(0);
ini_set('memory_limit', '32M');

$start = isset($_GET['s']) ? $_GET['s'] : 0; // where to start
$id = isset($_GET['id']) ? $_GET['id'] : 0; 
define('WORKABLE_CHUNK_SIZE', 1);
$reviews = array_slice(get_reviews_from_run('http://timesofindia.indiatimes.com%', 'asin2'), $start, 1);
$err = array();
foreach ($reviews as $review) {
	$data = array();
	if ($review['article']) {
		echo 'skipping '.$review['id'];
		continue;
	}
	get_timesofindia_rating($review['film_id'], $data, $err, $review['source_link']);
	$data = head($data);
	
	if ($data) {
		global $link;
		$rating = 0;
		if (isset($data['rating'])) {
			$rating = $data['rating'];	
		}
		if (isset($data['article']) && $data['article']) {
			$sql = sprintf("UPDATE reviews SET article =  '%s', img_src = '%s', rating = %d WHERE  id = %d LIMIT 1", 
				mysql_real_escape_string($data['article']), 
				mysql_real_escape_string($data['img_src']), 
				$rating,
				$review['id']);
			$result = mysql_query($sql);		
			echo 'SAVED TO DB: '.$sql;
		}
	}
}

echo '<pre>'.print_r($review, true).'</pre>';
echo '<pre>'.print_r($data, true).'</pre>';


?>
</div>