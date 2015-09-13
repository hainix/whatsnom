<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/utils.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/display/news.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/display/film/film_summary.php';

define('MAX_OTHER_ARTICLES',6);

if (idx($_GET, 'id')) {
  $article = get_object($_GET['id'], 'articles');
} else if ( idx($_GET, 't')) {
  $article = get_object_from_sql(
    "select * from articles where handle='".$_GET['t']."' limit 1",
    'article'
  );
}
if (!$article) {
  go_404();
}


// Data fetching
$now = time();
$unpublished =  $article['publish_time'] && $now < $article['publish_time'];

if (!$article
    || (!is_admin() && $unpublished && !(idx($_GET, 't') == $article['handle']))) {
  go_404();
}

$article_url = BASE_URL.get_article_url($article);

// Set up related content
$people = $film = null;
if ($article['stars']) {
  $star_ids = explode(',', $article['stars']);
  $people = get_objects($star_ids, 'people');
}
if ($article['film_id']) {
  $film = get_object($article['film_id'], 'film');
}
$spotlight_articles = get_featured_articles();
if (isset($spotlight_articles[$article['id']])) {
  unset($spotlight_articles[$article['id']]);
}
$spotlight_articles = array_slice($spotlight_articles, 0, 4, true);


$sidebar = '';

$ad_unit =
'<div style="margin: 10px 0;"><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Dishoom-med-rect -->
<ins class="adsbygoogle"
     style="display:inline-block;width:300px;height:250px"
     data-ad-client="ca-pub-4179758031327017"
     data-ad-slot="5549097480"></ins>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({});
</script><div align="right" style="color: #878686; width: 300px; font-size: 9px; margin-top: -7px;">From Our Sponsors</div></div>';

$responsive_ad_unit =
'<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Dishoom-Responsive -->
<ins class="adsbygoogle"
     style="display:inline-block"
     data-ad-client="ca-pub-4179758031327017"
     data-ad-slot="9979297089"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';

if ($people) {
  $sidebar .=
    '<div style="margin-bottom: 20px;">'
     .'<h3>Related Stars</h3>'
      .render_bubbles($people, 3, 12)
    .'</div>';
}

$sidebar .= $ad_unit;

if ($film) {
  $film_profile_pic = new ProfilePic($film['id'], 'film');
  $film_pic =
    $film_profile_pic
      ->setLinked(true)
      ->setWidth(300)
      ->render();

  $sidebar .=
    '<div style="margin: 10px 0;"><h3>Related Film</h3>'
    .'<div style="margin-top: 8px;">'
    .$film_pic
    .'<h4 style="margin: 0;">'
    .render_object_link_no_hovercard($film)
    .'</h4></div>'
    .'<h5>'.render_film_release_time($film, $now).'</h5>'
    .'</div><br/>';

}
$sidebar .= $ad_unit;

$sidebar .=
     '<h3>Trending Today</h3>'
    .'<ul class="big_stories_container">'
    .render_spotlight_articles(
      $spotlight_articles,
      array('width' => 300, 'height' => 220)
    )
  .'</ul>';


$comments_box =
  $unpublished
  ? null
  : render_wide_fb_like($article_url)
    .'<br/>'
  .'<div class="hide-on-mobile">'
    .render_fb_comments($article_url)
  .'</div>'
  .'<div class="show-on-mobile">'
  .render_fb_comments($article_url, 3 /* posts */, 280 /* width */)
  .'</div>';

$banner_dimensions  = array('width' => 760, 'height' => 320);
$pic_src = get_pic_src_from_article($article, $banner_dimensions);

$article_text = str_replace("\n", '</p><p>', $article['article_text']);

$edit_link = is_admin()
  ? render_button('Edit Article #'.$article['id'],
                  'cms/edit.php?type=article&id='.$article['id'])
  : null;


$published_text = null;
if ($article['publish_time'] || $article['author']) {
  $published_text =
    '<span style="color: #878686; font-size: 12px">posted';
  if ($article['publish_time']) {
    $published_text .= ' on '.date("F j, Y \a\\t g:i a T", $article['publish_time']);
  }
  if ($article['author']) {
    $author_data = get_author_data();
        if ($author_data[$article['author']]['external']) {
      $author_attribution_text =
        $author_data[$article['author']]['source_name'];
      $published_text .= ', by ';
      $published_text .=
        idx($author_data[$article['author']], 'source_link')
        ? render_external_link($author_attribution_text,
                               $author_data[$article['author']]['source_link'])
        : '<span style="color: #4A4A4A;">'.$author_attribution_text.'</span>';
    }
  }
  $published_text .= '</span>';
}
$html =
  '<div class="ten columns">
  <div class="post_content article_content group" style="margin-top: 10px;">'
   .'<h2 style="margin-bottom: 6px;">'.render_mentions_text_no_hovercard($article['headline']).'</h2>'
   .'<h4><span>'.render_mentions_text_no_hovercard($article['subheader']).'</span></h4>'
   .'<div style="padding:10px 0 15px 0; border-bottom: 1px solid #eee;">'
   . ($unpublished ? null : render_share_box_for_object($article, false, true))
   .$published_text
   .'</div>'
   .'<p>'
   .render_mentions_text_for_article_body($article_text)
   .'</p></div>
   <div style="margin-top: 30px;" class="group">'
   .$comments_box
   .'</div>
   </div>
   <div class="five columns">
   <div style="margin-top: 10px; padding-left: 10px;">'
  .'<div align="center"><h2>'.$edit_link.'</h3></div>'
      .$sidebar.'
    </div>
  </div>';


$title = strip_tags($article['headline']);
$page = new Page('news');

if ($article_text) {
  $page->setDescription(
      strip_tags(
        render_mentions_text_no_hovercard(
          $article['subheader']
        )
      ).' | '.
    truncate2(
      strip_tags(
        render_mentions_text_no_hovercard(
          $article_text
        )
      ),
      200,
      ' '
    )
  );
}

$page
->setContent($html)
->addHeadContent(get_article_og_tags($article))
->setTitle($title)
->render();

?>
