<?php

function render_spotlight_articles($articles, $big_thumb_dimensions = array('width' => 350, 'height' => 180)) {
  if (!$articles) {
    return null;
  }
  $articles_list = '';
  $now = time();
  foreach ($articles as $article) {
    $pic_src = get_pic_src_from_article($article, $big_thumb_dimensions);

    $articles_list .=
    '<li class="small-posts pinned">
       <div class="big_stories_header">'
         .render_article_link(
           $article,
          render_image(
             $pic_src,
             $big_thumb_dimensions
           )
         ).
        '<h4>'
      .render_published_article_link($article)
       .'</h4>
       </div>
     </li>';
  }
  return $articles_list;
}

function render_published_article_link($article) {
  $unpublished = $article['publish_time'] > time();
  return ($unpublished ? '[ [<i> ' : null)
    . render_article_link($article)
    . ($unpublished ? '</i>] ] ' : null);
}

function render_latest_articles($articles) {
if (!$articles) {
  return null;
}
$first_article = true;
$articles_render = '';
$thumb_dimensions = array('width' => 125, 'height' => 83);
$now = time();
foreach ($articles as $article) {
  $pic_src = get_pic_src_from_article($article, $thumb_dimensions);
  $edit_link = '';
  if (is_admin()) {
    $edit_link =
      '<span class="generic-image"></span>'
      .render_link(' (Edit Article)',
                   'cms/edit.php?type=article&id='.$article['id']);
  }

  $pic =
    '<div style="position:relative">'
    .render_article_link($article,
                         render_thumb_image($pic_src, $thumb_dimensions))
    .'</div>';

  $article_more_link = render_article_link($article, 'Read More',
                                           array('class' => 'read_post_link'));
  $article_time = $article['publish_time']
    ? d_date($article['publish_time'])
    : null;

  $blurb =
    render_mentions_text_without_media(
      truncate2(strip_tags($article['article_text']), 100, ' ')
    );


  $related_object_render = null;
  if ($article['film_id']) {
    $related_object_render =
      render_film_link(get_object($article['film_id'], 'film')).' ';
  } else if ($article['stars']) {
    $related_object_render =
      render_person_link(get_object(head(explode(',', $article['stars'])),
                                    'person')).' ';
  }

  $first_class = $first_article ? ' first' : '';
  $first_article = false;
  $articles_render .=
'<li class="post'.$first_class.'">
   <article>
       <div class="thumb-unit">'
         .$pic.
      '</div>
       <div style="margin-left: 140px;">
         <h4>'
    .render_published_article_link($article).
        '</h4>
     <p class="description">'
       .$article['subheader'].
    '</p>
     <p class="small-meta">
       <span class="generic-image icon"></span>'
       .$related_object_render.
       '<span class="time-image icon"></span>
        <span class="time-ago">'
          .$article_time.
       '</span>'
       .$edit_link.
     '</p>
</div>
    </article>
  </li>';
}
return $articles_render;
}


function get_pic_src_from_article($article, $dimensions = null) {
  if (idx($article, 'image_handle')) {
    $handle = $article['image_handle'];
    $prefix =
      $dimensions
      ? $_SERVER["DOCUMENT_ROOT"].'/images/media/'
      : BASE_URL.'images/media/';

    if (strpos($handle, '_') !== false) {
      $prefix = MEDIA_BASE.'media/';
    } else if (is_numeric($handle)) {
      $image_object = get_object($image_handle, 'media');
      if ($image_object && $image_object['finalized']) {
        $prefix = MEDIA_BASE.'media/';
      }
    }

    $src = $prefix . $handle;
    return $dimensions
      ? ImageUtils::resizeCroppedSrc($src, $dimensions)
      : $src;
  }

  if ($article['stars']) {
    $star = get_object(head(explode(',', $article['stars'])), 'person');
    return get_cropped_profile_pic_src($star, $dimensions);
  } else if ($article['film_id']) {
    $film = get_object($article['film_id'], 'film');
    return get_cropped_profile_pic_src($film,
                                       $dimensions);
  }

  return null;
}