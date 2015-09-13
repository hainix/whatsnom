<?php
  /* run this to update the films in the film db after staging:
update films inner join youtube_films on youtube_films.film_id = films.id set films.fullfilm_handle = youtube_films.youtube_handle, films.fullfilm_price = youtube_films.price, youtube_films.processed = 1 where youtube_films.film_id != 0 and films.fullfilm_handle is null and youtube_films.processed = 0;
  */

$root = '/var/www/html/';

include_once $root.'lib/utils.php';
include_once $root.'scripts/parser.php';
include_once $root.'scripts/script_lib.php';
$data = array();
$start = 1;
define('WORKABLE_CHUNK_SIZE', 1);
set_time_limit(0);
define('NUM_YOUTUBE_PAGES_TO_PARSE', 7);
define('WORKING_TYPE', 'paid');
// START To get free movie urls from youtube index pages
// to update from youtube, uncomment this section, set NUM_YOUTUBE_PAGES_TO_PARSE to an accurate number of pages to parse, then
// run. take the array output and put it in the function get_free_movie_urls, then run piecewise scrapers to populate the db
$url_bases = array();
$url_bases['free'] = 'http://www.youtube.com/movies/indian-cinema?fl=f&l=hi&pt=g&st=d&p=';
$url_bases['paid'] = 'http://www.youtube.com/movies/indian-cinema?fl=p&l=hi&pt=g&st=d&p=';
$existing_data = array();
for ($i = $start ; $i <= NUM_YOUTUBE_PAGES_TO_PARSE; $i++) {
  echo "---------- [[[ starting page ".$i."\n\n";
  $data = $existing_data = null;
  $html = file_get_html($url_bases[WORKING_TYPE] . $i);
  foreach ($html->find('a[class=ux-thumb-wrap contains-addto ]') as $a) {
    $temp = null;
    preg_match('/vi\/.*movieposter.jpg/',
	       $a->find('img', 0)->outertext,
	       $temp);
    $handle = rem(trim(head($temp)), array('vi/', '/movieposter.jpg'));
    if (WORKING_TYPE == 'free') {
      $price = 0;
    } else {
      $price = (int) floor(rem($a->next_sibling()->next_sibling()->plaintext,
                               '$') + 0.01);
    }
    $fields = array('youtube_handle' => $handle,
                    'title' => trim($a->next_sibling()->plaintext),
                    'price' => $price);
    if (!youtube_film_exists_in_db($handle)) {
      $data[] = $fields;
    } else {
      $existing_data[] = $fields;
    }
  }
  hlog('---skipped '.count($existing_data).' films');
  if ($data) {
    hlog($data);
    hlog('writing...');
    db_write_it($data);
    hlog('sleeping...');
    sleep(rand(10, 30));
  } else {
    hlog('existing data...');
    hlog($existing_data);
  }
}

function youtube_film_exists_in_db($handle) {
	global $link;
	$sql = sprintf("SELECT title from youtube_films WHERE youtube_handle = '%s'", $handle);
	$r = mysql_query($sql);
	return mysql_fetch_assoc($r);
}

function db_write_it($data) {
	global $link;

	foreach ($data as $r) {
	  $sql = sprintf("INSERT IGNORE INTO youtube_films (title, youtube_handle, price) VALUES ('%s', '%s', %d)",
			 $r['title'], $r['youtube_handle'], $r['price']);
	  $result = mysql_query($sql);
	  //$result = true;
	  if (!$result) {
	    $message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $sql;
	    //die($message);
	  } else {
	    hlog('saved to db with sql: '.$sql."\n");
	  }
	}
}
?>

