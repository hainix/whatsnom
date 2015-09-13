<?php
$root = '/var/www/html/';
include_once $root.'scripts/script_lib.php';
include_once $root.'scripts/parser.php';
set_time_limit(0);
ini_set('memory_limit', '32M');

$vars = parse_args($argv);
$start = idx($vars, 's', 0);
$film_id = idx($vars, 'f', null);
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// OVERRIDES - todo get from command line args
$id = 58518;


define('WORKABLE_CHUNK_SIZE', 1);

//$urls = get_wogma_urls_from_basepage();
//exit(1);
$i = $start;
do {
  hlog('[[[[[[ '.$i.' ]]]]]]');
  $id_sql = null;
  if ($id) {
    $id_sql = 'and id = '.$id;
  }
  $film = get_objects_from_sql(
    sprintf("select  * from films where year = 2012 ".$id_sql
            ." ORDER BY id DESC limit %d, %d",
            $i++, 1));
  if (!$film) {
    hlog('[[ script complete ]]');
    //continue;
    exit(1);
  }
  $film = head($film);

  // see if it has reviews
  $reviews = get_objects_from_sql("select id from reviews where film_id = ".$film['id']);
  hlog('-- found '.count($reviews).' reviews for film '.$film['id'].' - '.$film['name']);
  if (count($reviews) > 8) {
    hlog('--- enough reviews, skipping');
    continue;
  }

  // if you wanna risk scrape protection
  if (!$film['wog_handle'] || stripos('/', $film['wog_handle']) !== false) {
    $handle = get_wogma_handle_from_guess($film);
    if (!$handle) {
      //$handle = get_wogma_handle_from_google($film);
    }
    if ($handle) {
      hlog('found handle '.$handle.' for film '.$film['name']);
      // uncomment if you really want to update wogs
      if (true || update_wogma_handle($film, $handle)) {
        $film['wog_handle'] = $handle;
      }
    }
  }
  if (!$film['wog_handle']) {
    hlog('still no wog handle, sadness, for film '.$film['id']);
    continue;
  }
  $reviews = get_reviews_for_wog_handle($film);
  hlog('returned from review fetch');
  if ($reviews) {
    write_reviews_to_db($reviews, true);
    //update_needs_reviews_handle($film, 0);
  }
} while (1);

function update_wogma_handle($film, $handle) {

  if ($handle) {
  $sql = sprintf("update films set wog_handle = '%s' where id = %d limit 1",
		 $handle, $film['id']);
  } else {
  $sql = sprintf("update films set wog_handle = null where id = %d limit 1",
		 $film['id']);

  }
  global $link;
  $result = mysql_query($sql);

  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog($message);
    return false;
  } else {
    hlog('--- updated wog handle '.$handle.' for film '.$film['id']);
    return true;
  }

}

function get_wogma_handle_from_google($film) {
  $query = $film['name'].'  external reviews site:wogma.com/movie';
  $url  = "http://www.google.com/search?hl=en&q=" . urlencode($query) ;
  hlog('-searching '.$url.' for goggle info');
  $html = curl_url($url);
  sleep(rand(18, 68));

  if (stripos($html, 'Google automatically detects requests') !== false) {
    die('********************** SCRAPE DETECTION ONOESSSSS! DIEEE! ***********************');
    exit(1);
  }

  if(stripos($html, "302 Moved") !== false) {
    $first_url = match('/HREF="(.*?)"/ms', $html, 1);
    $html = curl_url($first_url);
  }

  if (!$html) {
    hlog('[err]-- error parsing google results');
    return false;
  }

  $dom = str_get_html($html);
  $handle = $dom->find('cite', 0);
  hlog('first cite: '.$handle->plaintext);
  if (!$handle) {
    hlog('fail with this html '.$dom->outertext);
    if (stripos($dom->outertext, 'Google automatically detects requests coming') !== false) {
      die('********************** SCRAPE DETECTION ONOESSSSS! DIEEE! ***********************');
      die(1);
    }
    return false;
  }
  return rem($handle->plaintext, array('wogma.com/movie/', '-teho/', '-review/', '-urating'));
}

function get_wogma_handle_from_guess($film) {
  return strtolower(str_replace(' ', '-', $film['name']));
}

//exit(1);

function get_reviews_for_wog_handle($film) {
  hlog('getting reviews for film '.$film['name']. ' with id '.$film['id']);
  $reviews = array();
  $film_id = $film['id'];
  $url = 'http://wogma.com/movie/'.rem($film['wog_handle'], array('-review', '/')).'-teho/';
  $html = str_get_html(get_url($url));
  if (!$html->find('div[class=movie_page]', 0)) {
    hlog(' -- this is not a movie page, moving on');
    return false;
  }
  $title = trim(rem($html->find('div[class=movie_page]', 0)->find('h1', 0)->plaintext, '- External Reviews'));
  $edit_distance = edit_distance(strtolower($title), strtolower($film['name']));
  if ((strlen($film['name']) < 9 && $edit_distance > 4) ||
      strlen($film['name']) > 14 && $edit_distance > 7){
    hlog('title mismatch btwn film '.$film['name'].' and wog title '.$title.', aborting -- edit distance = '.$edit_distance);
    update_wogma_handle($film, null);
    return false;
  } else {
    hlog('title match btwn film '.$film['name'].' and wog title '.$title.', continue');
  }

  $review_div = $html->find('div.otherreviews', 0);
  if (!$review_div) {
    hlog('no review div found');
    return false;
  }
  foreach ($review_div->find('p') as $p) {
    $r = array();
    $source = $p->find('a', 0);
    $excerpt = $p->find('span.excerpt', 0)->plaintext;

    $r['source_link'] =  $p->find('a', 1)->href;
    $r['source_name'] = trim($source->plaintext);
    $r['excerpt'] = rem($excerpt, array('...','`'));

    $author_container = rem(trim(head(explode('<a',$p->innertext))),',');
    $author_container = explode('by', $author_container);
    if (isset($author_container[1])) {
      $r['reviewer'] = strip_html($author_container[1]);
    }
    switch (trim($author_container[0])) {
    case 'Thumbs up':    $thumbs = 'up'; break;
    case 'So-So':        $thumbs = 'meh'; break;
    case 'Thumbs down':  $thumbs = 'down'; break;
    default: echo 'unrecognized rating: '.$author_container[0];
    }
    if ($thumbs) {
      $r['thumbs'] = $thumbs;
    }

    $r['film_id'] = $film_id;
    $reviews[] = $r;

  }

  $html->clear();
  unset($html);
  return $reviews;
}

function get_film_from_title($title) {
  return get_object_from_sql("select * from films where title like '%s'", $title);
}

function get_wogma_urls_from_basepage() {
  $base_url = 'http://wogma.com/movies/teho/';
  $html = str_get_html(get_url($base_url));
  hlog($html->outertext);
  $urls = array();
  $film_to_add = array();
  foreach ($html->find('li.teho_item') as $item) {
    $a = $item->find('a', 0);
    $title = $a->innertext;
    $film = get_film_from_title($title);
    if (!$film) {
      hlog('missing id for '.$title);
      continue;
    }
    if (true || !$film['wog_handle']) {
      $handle = rem($a->href, array('-teho/', '/movie/'));
      hlog('updating film '.$title.' with handle '.$handle);
      update_wogma_handle($film, $handle);
    }
    hlog('processed '.$title);
  }
}


//write_reviews_to_db($reviews);

?>
