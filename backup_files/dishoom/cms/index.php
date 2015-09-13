<?php
include_once '../lib/core/page.php';
include_once '../lib/utils.php';
include_once 'cms_lib.php';


$filter = idx($_GET, 'filter', 'priority');
$show_posters = idx($_GET, 'show_posters', true);
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$html = '';
$audit = 'posters, oneliners and ratings ';
  //  .render_link('toggle posters',
  //               'cms/?filter='.$filter.'&show_posters='.!$show_posters).')';

switch ($filter) {
case 'all':
  $sql = "SELECT * FROM films WHERE deleted is null ORDER BY tier ASC, year DESC";
  $films = get_objects_from_sql($sql);
  break;
case 'priority':
  $sql = "SELECT * FROM films WHERE tier like 'A%' and deleted is null ORDER BY tier ASC, year DESC limit 20";
  $films = get_objects_from_sql($sql);
  break;
case 'upcoming':
  $sql = sprintf("SELECT * FROM films WHERE deleted is null and year >= %d ORDER BY tier ASC, year DESC", date("Y"));
  $unsorted_films = get_objects_from_sql($sql);
  foreach ($unsorted_films as $key => $film) {
    $unsorted_films[$key]['release_timestamp'] = $film['release_date']
      ? strtotime($film['release_date'])
      : 999999999999;
  }
  $films = array_sort($unsorted_films, 'release_timestamp', SORT_ASC);
  break;
}

if (!$films) {
    slog('no films found');
    exit(1);
}

$film_ids = array_pull($films, 'id');

$sql = sprintf(
         "select film_id as id, count(*) as count from reviews where film_id"
	 ." in (%s) group by film_id;", implode(',', $film_ids));
$review_counts = get_objects_from_sql($sql);

//slog($films);


$data = array('<td>'.implode('</td><td>',array('id', 'cms', 'tier', 'name', 'oneliner', 'stars', 'supporting',  'rating', 'reviews', 'poster', 'year', 'release')).'</td>');
$missing = '<h2>?</h2>';
foreach ($films as $film) {
  $stars = $film['stars'] ? count(explode(',',$film['stars'])) : $missing;
  $supporting = $film['supporting_actors']
    ? count(explode(',',$film['supporting_actors']))
    : $missing;
  $reviews = idx($review_counts, $film['id'])
    ? $review_counts[$film['id']]['count']
    : $missing;
  $rating = $film['rating'] ? : $missing;
  $oneliner = $film['oneliner'] ? : $missing;
  $poster = '-';
  if ($show_posters) {
    $poster =
		render_external_link(render_profile_pic_square($film, 'film'),
			    'http://images.google.com/?q='.$film['name'].'+poster+'.$film['id']
                         .'&dish_id='.$film['id']);
  }

	$data[] = '<td>'.implode('</td><td>',array(
		render_film_link_no_hovercard($film, $film['id']),
    render_link('[edit]', 'cms/edit.php?id='.$film['id'].'&type=film'),
		$film['tier'],
		render_film_link_no_hovercard($film),
		$oneliner,
		$stars,
		$supporting,
		$rating,
		$reviews,
    $poster,
		$film['year'],
    $film['release_date'],
		)).'</td>';
}


$html .=
 '<h3>[TODO] verify '.$audit.' for these '.count($films).' '.$filter
  .' films:</h3>';
switch ($filter) {
  case 'priority':
    $html .= '<small>these are ordered by overall priority, and is the default</small>';
    break;
  case 'all':
    $html .= '<small>these are all films in our database</small>';
    break;
  case 'upcoming':
    $html .= '<small>these are sorted by release date descending (furthest away last)</small>';
    break;
}
$html .=
  '<br/><table border="2"><tr>'.implode('</tr><tr>', $data).'</tr></table>';

$head = '<script src="../js/fileuploader.js" type="text/javascript"></script>';

$title = 'Dishoom | Audit';
$page = new cmsPage();
$page->setContent($html);
$page->addHeadContent($head);
$page->render();


?>
