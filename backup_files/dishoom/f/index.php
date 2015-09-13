<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/utils.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/display/film/film_reviews.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/display/film/film_summary.php';

define('NUM_REVIEWS_TO_SHOW', 10);
define('MIN_REVIEWS_TO_SHOW', 2);

$handle = idx($_GET, 'h');
$id = idx($_GET, 'id');


// START DATA FETCH
if ($id) {
  $film = get_object($id, 'film');
} else if ($handle) {
  $film = get_object_from_sql(
    "select * from films where handle='".$handle."' and deleted is null limit 1",
    'film'
  );
}

if (!$film) {
  go_404();
}

$stars =
    idx($film, 'stars')
    ? get_objects(
      explode(
        ',',
        $film['stars']
      ),
      'people'
    )
    : null;

if (!$film || !isset($film['id']) || $film['deleted']) {
  go_404();
}

$id = $film['id'];
$reviews = get_objects_against_secondary_id($id, 'reviews');
$now = time();

$recommended_films = get_similar_films($film);

// END DATA FETCH

list($review_class,$rating) = get_rating_info($film);

$header_subtitle = render_film_release_time($film, $now);
$header_subtitle = $header_subtitle
  ? ' <span style="font-size: 18px;">'.$header_subtitle.'</span>'
  : null;

$header_style = $film['rating'] ? ' style="padding-left: 80px;"' : null;
$header = '<div '.$header_style.'><h2>'.$film['name']
  .$header_subtitle
  .'</h2>';


// oneliner and header
if ($oneliner = render_oneliner($film)) {
  $header .= '<p style="margin-top: 7px; font-size: 18px;">'.$oneliner.'</p>';
}
$header .= '</div>';

$profile_pic = new ProfilePic($film['id'], 'film');
$profile_pic->setLinked(false)->setIsThumb(true);

$thumb = $profile_pic->setWidth(280)->setCropped(true)->render();
$buttons = $below_poster_actions = array();

if ($film['trailer']) {
  $film_trailer = get_youtube_link($film['trailer']);
  $poster_section = ' <a href="'.$film_trailer.'" class="video_link">'
    .$thumb.'</a>';
  $buttons[] =
    render_forced_external_link('Watch Trailer',
                                $film_trailer,
                                array('class' => 'large red button video_link'));
} else {
  $poster_section = $thumb;
}
$poster_section = '<div align="left">'.$poster_section.'</div>';


// Show showtime link for films which release in the next 10 days or have been out for
// 2 months or less
if ($film['release_time'] && $now > $film['release_time']
    && $film['release_time'] > ($now - (SECONDS_PER_DAY * 10))) {
    $ticket_text = $film['release_time'] < $now ? 'Find Showtimes' : 'Reserve Tickets';
    $buttons[] =
      render_forced_external_link(
        $ticket_text,
        'http://www.google.com/movies?q='.urlencode($film['name'])
        .'&btnG=Search+Movies&hl=en&ct=title&cd=1',
        array('class' => 'large orange button',
              'target' => '_blank'));
}

if (is_admin()) {
  $below_poster_actions[] =
    render_link('Edit Film #'.$film['id'],
                'cms/edit.php?id='.$film['id'].'&type=film&hsh='.mt_rand(1, 99999),
                array('class' => 'large red button'));
}
$buttons_render =
  '<ul class="button-list" align="right"><li>'
  .implode('</li><li>', $buttons).'</li></ul>';


$below_poster_actions_render =
   '<ul align="center"><li>'
    .implode('</li><li>', $below_poster_actions).'</li>
   <li><div style="padding: 20px 0;">'
  .render_share_box_for_object($film, $two_rows = false)
  . '</div></li><ul>';


$stars_render =
  $stars
  ? '<div style="margin-bottom: 20px;">
       <h4>Stars</h4>'.render_bubbles($stars, 3, 3)
   .'</div>'
  : '';

$plot_render = $film['plot']
  ? '<div style="margin-bottom: 20px;"><h4>Plot</h4>'
    .$film['plot'].'</div>'
  : '';

$dish_render = '<h4>The Latest</h4>'
  .render_buzz_box($film['name'].' movie', 'No Film News Found');

$cast_render =
  '<h4>Cast and Crew</h4>'
  .render_film_cast($film);

$summary_panel =
  $poster_section
  . unit($below_poster_actions_render)
  . $plot_render
  . $cast_render;

$stars_panel =
  unit($stars_render)
  .$dish_render;


$reviews_panel = null;
if ($reviews && count($reviews) > MIN_REVIEWS_TO_SHOW) {
  shuffle($reviews);
  $review_list = get_review_list($reviews, $film);
  if ($review_list) {
    $reviews_to_show = array_slice($review_list, 0, NUM_REVIEWS_TO_SHOW);
    $reviews_panel =
      '<h4>Reviews</h4>'
      .'<ul>'.implode(' ', $reviews_to_show).'</ul>';
  }
}


$recommended_films_slider = null;
if ($recommended_films) {
  $quote = get_random_quote();
  $quote_render = render_film_link_no_hovercard(
    array('id' => $quote['film_id']),
    nl2br($quote['quote'])
  );
  if (is_admin()) {
    $quote_render .= '<br/>'
      .render_link('edit quote',
                   'cms/edit.php?id='.$quote['id'].'&type=quote');
  }
  //  shuffle($recommended_films);
  if ($recommended_films && count($recommended_films) > 2) {
  $recommended_films_slider =
    '<div class="hide-on-mobile">
       <div class="recommend-carousel-container">
         <h3>If You Like <span>'.$film['name'].'</span>...</h3>'
         .render_recommended_films_slider($recommended_films)
       .'<div class="special-font footer-quote">'.$quote_render.'</div>
       </div>
     </div>';
  }
}

if ($reviews_panel) {
  $main_sections =
    '<div class="five columns">'
      .$summary_panel
   .'</div>
     <div class="five columns">'
       .$stars_panel
   .'</div>
     <div class="five columns">'
     .$reviews_panel
    .'</div>';
} else {
  $main_sections =
    '<div class="five columns">'
      .$summary_panel
   .'</div>
     <div class="seven columns">'
       .$stars_panel
    .'</div>';
}

$html =
  '<div class="eleven columns" style="padding:20px 0 0 0; margin-bottom: 10px;">'
    .render_rating($film)
    .$header
  .'</div>
   <div class="three columns" style="min-height: 40px; padding: 20px;" >'
     .$buttons_render
   .'</div></div><div class="container" style="border-top: 1px solid #eee; padding-top: 20px;">'
   .$main_sections
   .'</div><div class="container" style="margin-top: 10px;padding-top: 20px;border-top: 1px solid #eee;">
     <div class="fourteen columns">'
       .$recommended_films_slider
   .'</div>';

$page = new Page('movies');

if ($oneliner) {
  $page->setDescription(strip_tags(render_mentions_text_no_hovercard($film['oneliner'])));
}


$page
->setTitle($film['name'].' ('.$film['year'].')')
->setContent($html)
->addHeadContent(get_film_og_tags($film))
->requireModule(
  array('fancybox',
        'tabs',
        'news',
        'expander',
        'accordian-slider')
)
->render();
