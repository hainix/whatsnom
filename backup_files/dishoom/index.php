<?php

include_once 'lib/utils.php';
include_once 'lib/core/page.php';
include_once 'lib/display/news.php';

$box_office_films = get_objects(get_cached_box_office_ids(), 'films');
$coming_soon_films = get_objects(get_cached_coming_soon_ids(), 'films');
$spotlight_articles = get_featured_articles();
$latest_articles = get_latest_articles();

$first_few_spotlight_articles = array_slice($spotlight_articles, 0, 3, true);
foreach ($first_few_spotlight_articles as $article_id => $_) {
  if (isset($latest_articles[$article_id])) {
    unset($latest_articles[$article_id]);
  }
}

if (head_key($spotlight_articles) == head_key($latest_articles)) {
  //unset($latest_articles[head_key($latest_articles)]);
}

// Put highest rated box office film first
$box_office_films = array_sort($box_office_films, 'rating', SORT_DESC);
$box_office_render =
'<div id="big_stories_header" class="header" style="margin-left: 4px;">
   <h1>In Theaters</h1>
 </div>'
 .render_movie_list($box_office_films);

$coming_soon_render =
'<div id="big_stories_header" class="header">
   <h1  style="margin-top: 20px;">Coming Soon</h1>
 </div>'
 .render_movie_list($coming_soon_films);


$spotlight_articles_render =
 '<ul class="big_stories_container" id="spotlight-article-container">'
  .render_spotlight_articles($spotlight_articles)
  .'</ul>'
.'<div id="loadmoreajaxloader-spotlight" style="display:none; padding-top: 10px;"><center><img src="https://raw.github.com/webcreate/infinite-ajax-scroll/master/dist/images/loader.gif" /></div>';

$latest_articles_render =
  get_infinite_scroll_js()
.'<ul class="std-posts" id="latest-article-container" style="margin-bottom: 20px;">'
.render_latest_articles($latest_articles)
.'</ul><div id="loadmoreajaxloader-latest" style="display:none; padding-top: 10px;"><center><img src="https://raw.github.com/webcreate/infinite-ajax-scroll/master/dist/images/loader.gif" /></center></div>';

$html = '
    <div class="five columns">'
    .$spotlight_articles_render
  .'</div>
    <div class="seven columns">'
  .$latest_articles_render
  .'</div>
  <div class="three columns">'
    .$box_office_render
    .$coming_soon_render
  .'</div>';


$page = new Page('home');
$page
  ->setTitle('Dishoom | Bollywood News, Reviews, and Gossip')
  ->setContent($html)
  ->render();

function render_movie_list($films) {
$list =
'<div class="hot_list">
    <ul>';
foreach ($films as $film) {
  $profile_pic = new ProfilePic($film['id'], 'film');
  $profile_pic_render =
    $profile_pic
      ->setLinked(false)
      ->setCropped(true)
      ->setWidth(200)
      ->setWidth(200)
      ->render();

  $rating_text = null;
  if ($film['rating']) {
    $rating_class = $film['rating'] > 50 ? 'trend_num_good' : 'trend_num_bad';
    $rating_text = $film['rating'].'%';
  } else if ($film['release_time']) {
    $rating_class = 'trend_num_neutral';
    $rating_text = date('M d', $film['release_time']);
  }
  $hover_text =
    $film['rating']
    ? $film['name'].'<br/>'.$film['rating'].'%'
    : $film['name'];

  $stars_render = render_stars_for_object($film);

if ($rating_text) {
  $rating_box =
    '<div class="trend c">
       <div class="trend_num_box">
         <div class="trend_num '.$rating_class.'">'.$rating_text.'</div>
       </div>
     </div>';
}

$title_box = '<div class="movie_title_overlay">'.$film['name'].'</div>';

 $caption =
   '<span class="caption full-caption">
      <h3>'.$hover_text.'</h3>
      <p>'.$stars_render.'</p>
    </span>';
 $box =
   '<div class="box">'
   .render_object_link_no_hovercard(
     $film,
     $profile_pic_render
     .$title_box
     .$rating_box
     .$caption
  )
  .'</div>';

  $list .=
    '<li>'.$box.'</li>';
}
$list .= '</ul></div>';
return $list;
}

function get_infinite_scroll_js() {
return
'<script type="text/javascript">
var next_page_latest = 1;
var next_page_spotlight = 1;
$(window).scroll(function() {
  if($(window).scrollTop() == $(document).height() - $(window).height() ) {
    $("div#loadmoreajaxloader-latest").show();
    $.ajax({
      url: "load_more_articles.php?t=latest&p="+next_page_latest,
      success:
        function(html) {
          if (html && $("div#loadmoreajaxloader-latest").is(":visible")) {
            $("#latest-article-container").append(html);
            $("div#loadmoreajaxloader-latest").hide();
            next_page_latest++;
          } else {
            $("div#loadmoreajaxloader-latest").html("<center>loading more posts...</center>");
          }
        }
    });

    $("div#loadmoreajaxloader-spotlight").show();
    $.ajax({
      url: "load_more_articles.php?t=spotlight&p="+next_page_spotlight,
      success:
        function(html2) {
          if (html2 && $("div#loadmoreajaxloader-spotlight").is(":visible")) {
            $("#spotlight-article-container").append(html2);
            $("div#loadmoreajaxloader-spotlight").hide();
            next_page_spotlight++;
          } else {
            $("div#loadmoreajaxloader-spotlight").html("<center>loading more posts...</center>");
          }
        }
    });
}
});
</script>
';

}
