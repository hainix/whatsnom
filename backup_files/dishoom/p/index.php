<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/utils.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/core/bing.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/display/person/person_summary.php';

$id = $_GET['id'];
if (!$id) {
  go_404();
}

$person = get_object($id, 'people');
if (!$person || !$person['id'] || $person['deleted']
    || !$person['name']) {
        go_404();
}
$duplicate = null;
if ($person['duplicate_of'] && is_numeric($person['duplicate_of'])) {
  $duplicate = $id;
  $id = $person['duplicate_of'];
  $person = get_object($id, 'people');
}
$id = $person['id'];

/*
TODO - use vetted images
$images = get_objects_from_secondary_id($id, 'images');
*/

$primary_type_render =
  $person['primary_type']
  ? render_tag($person['primary_type'])
  : null;

$rating_render = null;
if ($person['rating']) {
  $rating_render = render_rating($person);
}

// START HEADER
$header =
  '<div class="section gradient s-1 group">'
  .$rating_render
  .'<div class="section-content-title-container">'
  .'<h3>';

$header_subtitle = $primary_type_render
  ? ' <span class="header-subtitle">'.$primary_type_render.'</span></h3>'
  : null;

// TITLE
$title_parts = explode(' ', $person['name'], 2);
$header .= $title_parts[0].' <span>'.idx($title_parts, 1).'</span>';
if (is_admin() && $duplicate) {
  $header .= '( ID '.$duplicate.' is a duplicate)';
}
$header .= $header_subtitle.'</h3>';

// ONELINER
if ($oneliner = render_oneliner($person)) {
  $header .= '<h5>'.$oneliner.'</h5>';
}

$header .= '</div></div></div>';
// END HEADER

// START POSTER
$poster = '';
$thumb = '<img width="280" title="'.$person['name'].'" '
  .'alt="'.$person['name'].'" class="wp-post-image" src="'
  .get_profile_pic_src($person, 'person', array('width' => 280)).'" />';

$mobile_thumb = '<img width="280" height="280" title="'.$person['name'].'" '
  .'alt="'.$person['name'].'" class="wp-post-image" src="'
  .get_cropped_profile_pic_src($person, array('width' => 280,
                                              'height' => 280)).'" />';
$buttons = array();
$poster .= '<span class="for-not-mobile thumb video">'.$thumb.'</span>'
    .'<span class="for-mobile image-border">'.$mobile_thumb.'</span>';

if (is_admin()) {
  $buttons[] =
    render_button('Edit Person',
		  'cms/edit.php?id='.$id.'&type=person');
}

// TODO maybe bring back buttons
if (in_array($person['tier'], array('A', 'B'))) {
  //$buttons[] = render_button('Watch Videos', 'tv/?id='.$id.'&type=person');
}

if ($buttons) {
  $poster .= '<div align="center">'.implode('', $buttons).'</div>';
}


// END POSTER

// DETAILS
$person_fields = get_person_detail_fields($person);
$details_render = null;
if ($person_fields) {
  $details_render =
    '<br/><h4>Bio-<span>Data</span></h4><table class="striped">';
  foreach ($person_fields as $key => $field) {
    $details_render .= '<tr><td style="font-variant:small-caps;">'.$key.'</td><td> '.$field.'</td></tr>';
  }
  $details_render .= '</table>';
}


// FAMOUS FOR
$famous_films_render = '';
if ($person['famous_for']) {
  $famous_films = get_objects(explode(',', $person['famous_for']),
                              'film');
  $famous_films_render =
    '<h4>Famous<span> For:</span></h4>'
    . render_objects_table($famous_films, 'film')
    .'<br/>';
}

// NEWS
$buzz_term = $person['tier'] == 'A' ? $person['name'] : $person['name'] . 'Bollywood';
$dish_render = '<h4>The <span>Latest</span></h4>'
  .render_buzz_box($buzz_term, 'Loading Celeb News...');

// FILMOGRAPHY
list($best_film, $worst_film, $all_films, $costars) =
  get_filmography_data_from_person($person);

// IMAGES
//$images = get_current_images($person['name'] . ' bollywood ');
$images_render = '';
// TODO bring this back
if (false && $images) {
  $images_render = render_thumbnails_slider($images, 280, 300, null);
}

// INTERVIEWS
$videos = get_related_person_content($person['id'], 'videos', 10);
$video_render = null;
if ($videos) {
  $video_render =
    '<h4><span>Watch</span></h4>'
    .render_video_playlist($videos, 3);
}

// SONGS
$songs = get_related_person_content($person['id'], 'songs', 20);
$song_render = null;
if ($songs) {
  $song_render =
    '<h4>Hit <span>Songs</span></h4>'
    .render_video_playlist($songs, 5);
}

// TWEETS
$tweets_render = null;
if (idx($person, 'twitter')) {
  $tweets = get_recent_tweets($person['id']);
  if ($tweets) {
    $tweets_render =
      '<h4><b>'
      .render_external_link('@'.$person['twitter'],
                            'http://twitter.com/'.$person['twitter'],
                            array('target' => '_blank'),
                            /* no banner */ true).'</b> says:</h4><br/>'
      .render_tweets($tweets, /* limit */ 5, /* show name */ false)
      .'<br/>';
  }
}


$html =
'<div class="home-sections" itemscope itemtype="http://data-vocabulary.org/Person">'
.$header
.'<div id="summary" class="group" >
<ul id="portfolio">
<li class="first">'
  .$poster
  .unit(render_share_box_for_object($person, $two_rows = false, $show_header = true))
  .$details_render
  .$best_film
  .$worst_film
  .'<br/>'
  .$all_films
.'</li>
<li class="portfolio">'
  .unit($video_render)
  .$costars
  .unit($famous_films_render)
  .$song_render
.'</li>
<li class="portfolio last group">'
  .unit($tweets_render)
  .$dish_render
  .$images_render
.'</li>
</ul>
</div>';



$html .= '</div><!-- END FOOTER -->';

$page = new page('stars');
if ($oneliner) {
  $page->setDescription(strip_tags(render_mentions_text_no_hovercard(
                                     $person['oneliner']
                                   )));
}
$page_title = $person['name'];

if ($primary_type_render) {
  $page_title .= ' - '.$primary_type_render;
}
//$page_title .= ' | Dishoom';
$page
  ->setTitle($page_title)
  ->requireModule(
    array(
      'news',
      'fancybox',
      'vertical-ticker',
      'thumbnails-slider',
    )
  )
  ->addHeadContent(get_person_og_tags($person))
  ->setContent($html)
  ->render();
