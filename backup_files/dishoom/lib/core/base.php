<?php
date_default_timezone_set('America/Los_Angeles');
//ob_start("ob_gzhandler");

define('BASE_URL', 'http://www.dishoomfilms.com/');
define('MEDIA_BASE', 'http://media.dishoomfilms.com.s3.amazonaws.com/');

$link = mysql_connect('localhost', 'root', 'Dest1ny') or die("Cannot connect to the local database ".mysql_error());
mysql_select_db("dishoomfilms") or die("Cannot select db dishoomreviews");

function get_film_url($film, $rel = true) {
  $suffix = isset($film['handle']) && $film['handle']
    ? 'film/'.$film['handle']
    : 'f/?id='.$film['id'];
  return $rel ? $suffix : BASE_URL.$suffix;
}

function get_article_url($article) {
  return  isset($article['handle']) && $article['handle']
    ? 'article/'.$article['handle']
    : 'a/?id='.$article['id'];
}



?>