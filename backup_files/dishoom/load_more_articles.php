<?php
include_once 'lib/utils.php';
include_once 'lib/display/news.php';
$page = idx($_GET, 'p');
$type = idx($_GET, 't');
if ($page) {
  switch ($type) {
    case 'latest':
      $articles = get_latest_articles($page * LATEST_ARTICLES_PER_PAGE, LATEST_ARTICLES_PER_PAGE);
      echo render_latest_articles($articles);
      break;
  case 'spotlight':
    $articles = get_featured_articles($page * SPOTLIGHT_ARTICLES_PER_PAGE, SPOTLIGHT_ARTICLES_PER_PAGE);
    echo render_spotlight_articles($articles);
    break;
  }
}