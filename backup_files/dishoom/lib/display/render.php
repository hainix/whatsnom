<?php
define('HIGH_RATING_MIN', 60);
define('MID_RATING_MIN', 40);

define('RENDER_VIDEO_LINKS', false);

function render_mentions_text($text, $show_media = true, $links_offsite = false, $hovercard = true) {
  $matches = null;
  preg_match_all('/{.*?}/', $text.' ', $matches);
  $extras = null;
  if ($links_offsite) {
    $extras = array('target' => '_blank');
  }
  if ($matches) {
    foreach (head($matches) as $match) {
      $sub_match = substr($match, 1, strlen($match)-2);
      $fields = explode(':', $sub_match);
      if (count($fields) == 3 && $fields[0] !== 'v' && $fields[0] !== 'i') {
        switch ($fields[1]) {
        case 'l':
          $render =
            render_external_link($fields[0], 'http://'.$fields[2]);
          break;
        case 'p':
          if ($hovercard) {
            $render = render_person_link(array('id' => $fields[2]), $fields[0]);
          } else {
            $render = render_person_link_no_hovercard(array('id' => $fields[2]), $fields[0]);
          }
          break;
        case 'f':
          $render = render_film_link(array('id' => $fields[2]), $fields[0], $hovercard);
          break;
        case 't':
          $film_id = $fields[2];
          if ($film_id) {
            $render = render_link($fields[0], 'tv/?channel=trailers&type=film&id='.$film_id, $extras);
          }
          break;
        case 's':
          $render = render_song_link(array('id' => $fields[2]), $fields[0], $extras);
          break;
        case 'a':
          $render = render_article_link(array('id' => $fields[2]), $fields[0]);
          break;
        case 'w':
          $render = render_video_link($fields[2], 'video',
                                      $fields[0], $extras);
          break;
        default:
          $render = $fields[0];
        }
        $text = str_replace($match, $render, $text);
      } else if (($fields[0] == 'v' || $fields[0] == 'i') && $show_media) {
        $sub_match = substr($match, 1, strlen($match)-2);
        $fields = explode(':', $sub_match);
        $image_object = null;
        if ($fields[0] == 'i') {

          // Handle old images with underscores
          if (strpos($fields[1], '_') !== false) {
            $url = MEDIA_BASE.'media/'.$fields[1];
          } else {
            $url = BASE_URL.'images/media/'.$fields[1];
          }

          $source_caption = null;
          $image_handle = idx(explode('.', $fields[1], 2), 0);
          // Try to get image object data if available
          if (is_numeric($image_handle)) {
            $image_object = get_object($image_handle, 'media');
            if ($image_object && $image_object['source_link'] ) {
              $source_url_array = parse_url($image_object['source_link']);
              if (idx($source_url_array, 'host')) {
                $source_caption = $source_url_array['host'];
              }
            }
          }

          $text_caption = isset($fields[2])
            ? $fields[2]
            : null;

          $caption = null;

          if ($source_caption) {
            $caption .=
              '<div class="image_caption_source">'
              .render_external_link($source_caption, $image_object['source_link'])
              .'</div>';
          }
          if ($text_caption) {
            $caption .=
              '<div style="text-align: left; font-size:13px; margin-bottom: 9px;">'
              .$text_caption.'</div>';
          }

          $text = str_replace(
            $match,
            '<div align="center" style="margin-bottom: 14px;">'.render_image($url).$caption.'</div>',
            $text);
        } else if ($fields[0] == 'v') {
          $start_time = isset($fields[2]) && is_numeric($fields[2])
            ? $fields[2]
            : null;
          $embed = render_youtube_embed($fields[1], $start_time, 0,
                                        array('width' => 800,
                                              'height' => 480));
          $text = str_replace($match,
                              '<div align="center" style="margin-bottom: 24px;">'
                                .$embed
                             .'</div>',
                              $text);
        }
      } else if (!$show_media) {
        $text = str_replace($match, '', $text);
      } else {
        slog('malformed mentions syntax. n00b. '
             .'u think this is ok? {'.$match.'} you should be ashamed.');
      }
    }
  }
  // TODO - fix this hack properly with magic quotes
  //  $text = stripslashes($text);
  return $text;
}


function render_header($header_text, $subheader_text = null) {
  $header_text = '<h3>'.$header_text.'</h3>';
  if (!$subheader_text) {
    return $header_text;
  }
  return
    '<div class="header-with-subtitle">'
    .$header_text
    .'<div class="header-subtitle">'
    .$subheader_text
    .'</div></div>';
  }

// more info http://plugins.learningjquery.com/expander/index.html#getting-started
function expander_text($text, $chars = 200, $expand_text = 'read more') {
  if (!$text) {
    return null;
  }
  $div_id = 'exp_text_'.mt_rand(0, 9999);
  $activator =
    '<script>
       $(function() {
         $("#'.$div_id.'").expander({
           slicePoint:       '.$chars.',
           expandPrefix:     " ",
           expandText:       "'.$expand_text.'",
           collapseTimer:   0,
           userCollapseText: ""
         });
       });
     </script>';
  return '<div id="'.$div_id.'">'.$text.'</div>'.$activator;

}

function unit($html) {
  if (!$html) {
    return null;
  }
  return '<div class="unit group">'.$html.'</div>';
}

function mobile_unit($html) {
  return '<div class="for-mobile unit group">'.$html.'</div>';
}

function roundbox($foo) {
  return $foo;
  }

function truncate2($string, $limit, $break=" ", $pad="...") {
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }

  return $string;
}

function render_forced_external_link($text, $link, $params = array()) {
  return render_link_helper($text, $link, false, $params);
}

function render_external_link($text, $link, $params = array(), $no_banner = true,
                              $alt_src = null) {
  // Mobile devices links don't play nice
  if (is_mobile() || $no_banner) {
    $params = array_merge($params, array('target' => '_blank'));
  } else {
    $link = BASE_URL.'ex/?s='.urlencode($link);
    if ($alt_src) {
      $link .= '&a='.urldecode($alt_src);
    }
  }
  return render_link_helper($text, $link, false, $params);
}

function render_link($text, $link, $extras = null) {
  $extras = $extras ?: array();
  return render_link_helper($text, $link, true, $extras);
}

function render_link_helper($text, $link, $rel, $extras = null) {
  if ($rel) {
    $link = BASE_URL. $link;
  }
  $extra = '';
  if ($extras) {
    foreach ($extras as $key => $val) {
      $extra .= ' '.$key.'="'.$val.'" ';
    }
  }
  return '<a href="'.$link.'" '.$extra.'>'.$text.'</a>';
}

function render_article_link($article, $text = null, $extras = null) {
  if (!$text) {
    $text = $article['headline'];
    if (!$text) {
      slog('trying to render article without text or headline');
      slog($article);
    }
  }

  return render_link($text, get_article_url($article), $extras);
}

function render_film_link_no_hovercard($film, $text = null) {
  return render_film_link($film, $text, false);
}

function render_film_link($film, $text = null, $hovercard = true, $extras = array()) {
  if (!isset($film['name'])) {
    $film = get_object($film['id'], 'film');
  }

  if (!idx($film, 'id')) {
    slog('cannot get film link for object linked with text "'.$text.'". find it on this page and fix it RIGHT NOW!!! it\'s likely in a oneliner that you linked the wrong object type on. tstk tsk. kthxbai');
    return $text;
  }

  if (!$text) {
    $text = $film['name'];
  }
  $film['type'] = 'film';
  return $hovercard
    ? render_object_hovercard($film, $text)
    : render_link($text, get_film_url($film), $extras);
}

function should_link_to_person($person) {
  return
    $person
    && $person['name']
    && idx($person, 'tier')
    && ($person['tier'] == 'A' || $person['tier'] == 'B');
}

function render_person_link_no_hovercard($person, $text = null) {
  return render_person_link($person, $text, false);
}

function render_person_link($person,
                            $text = null,
                            $hovercard = true) {
  if (!isset($person['name'])) {
    $person = get_object($person['id'], 'person');
  }

  $text = $text ? $text : $person['name'];
  if (should_link_to_person($person)) {
    // Only show titles for non-hovercard objects
    $extras = array();
    if ($hovercard && !idx($person, 'oneliner')) {
      $hovercard = false;
    }
    return '<b>'.$text.'</b>';
    $person['type'] = 'person';
    return $hovercard
      ? render_object_hovercard($person, $text)
      : render_link($text, get_person_url($person));
  }
  return $text;
}

function get_person_url($person) {
  return 'p/?id='.$person['id'];
}

function render_object_hovercard($object, $text = null) {
  $h_id = 'hc-o-'.mt_rand(1,99999);
  $holder_id = 'hc-holder-'.$h_id;
  $object_link = '<label id="'.$h_id.'">'.render_object_link_no_hovercard($object, $text).'</label>';

  // If the full name isn't the subject of the text, show the full name
  $show_name = strip_tags($text) == $object['name'] ? 0 : 1;

  $hovercard_width = idx($object, 'oneliner') ? 350 : 200;

  $script =
  '<script type="text/javascript">
  $(document).ready(function () {

    var hoverHTMLtmp = "<div id=\"'.$holder_id.'\"></div>";

    $("#'.$h_id.'").hovercard({
    detailsHTML: hoverHTMLtmp,
    delay: 900,
    width: '.$hovercard_width.',
    cardImgSrc: "'.get_cropped_profile_pic_src($object, array('width' => 90)).'",
    onHoverIn: function () {
    $.ajax({
      url: "'.BASE_URL.'ajax/hovercard.php?id='.$object['id'].'&type='.$object['type'].'&name='.$show_name.'",
      type: "GET",
      beforeSend: function () {
        $(\'#'.$holder_id.'\').html("Loading...");
      },
      success: function (data) {
        $(\'#'.$holder_id.'\').html(data);
      },
    });
  }
  });
});
</script>';
  return $object_link . $script;
}


function render_video_link($id, $type, $text, $extras = null) {
  if (!RENDER_VIDEO_LINKS) {
    return $text;
  }

  $type = get_table_name($type);
  $channel_info = null;
  if ($type == 'song' || $type == 'songs') {
    $channel_info = '&channel=songs';
  }
  $object = array('id' => $id, 'type' => $type);
  return render_link($text,
                     get_video_url($object),
                     $extras);
}

function get_video_url($object) {
  $channel_info = null;

  if ($object['type'] == 'song' || $object['type'] == 'songs') {
    $channel_info = '&channel=songs';
  }
  return 'tv/?id='.$object['id'].'&type='.$object['type'] . $channel_info;

}


function render_song_link($song, $text = null, $extras = null) {
  $text = $text ? $text : $song['name'];

  if (!RENDER_VIDEO_LINKS) {
    return $text;
  }

  if ($song['id'] && !isset($song['youtube_handle'])) {
    $song = get_object($song['id'], 'song');
  }
  if ($song['youtube_handle']) {
    return render_link($text,
                       get_song_url($song),
                       $extras);
  }
  return $text;
}

function get_song_url($song) {
  return 'tv/?id='.$song['id'].'&type=songs&channel=songs';
}

function render_object_link_no_hovercard($object, $text = null) {
  return render_object_link($object, $text, /* hovercard */ false);
}

function get_object_url($obj) {
  switch ($obj['type']) {
  case 'film':  return get_film_url($obj);
  case 'person': return get_person_url($obj);
  case 'article': return get_article_url($obj);
  case 'video': case 'song': return get_video_url($obj);
  }
  slog('object url get fail');
  slog($obj);
  return false;
}

function render_object_link($obj, $text = null, $hovercard = true) {
  $type = idx($obj, 'type');
  if (!$type) {
    slog('no type for object id '.$obj['id'].' in render object link');
  }
  switch ($type) {
    case 'film':  return render_film_link($obj, $text, $hovercard);
    case 'person': return render_person_link($obj, $text, $hovercard);
    case 'song': return render_song_link($obj, $text);
    case 'article': return render_article_link($obj, $text);
    case 'video': return render_video_link($obj['id'], $type,
                                           $text ? : idx($obj, 'name'),
                                           array('title' => strip_tags($obj['name'])));
    case 'review': return render_film_link(get_object($obj['film_id'], 'film'), $text);
    default:
      if ($type != 'slide') {
        slog('unrecognized type '.$type.' for id '.$obj['id']);
      }
      return $text ? $text : idx($obj, 'name', $obj['id']);
  }
}

function render_video_icon($obj, $force_to_tv_page = false) {
  if (!$obj['youtube_handle']) {
    return null;
  }

  // No music icon for now
  if (false && $obj['type'] == 'song') {
    $video_icon = render_local_image('music_play.png');
  } else {
    $video_icon = render_local_image('play3.png');
  }

  if ($force_to_tv_page) {
    $web_link = render_object_link($obj, $video_icon);
  } else {
    $web_link =
    render_forced_external_link(
      $video_icon,
      get_youtube_link($obj['youtube_handle']),
      array('class' => 'video_link'));

  }
  return
    '<div class="for-not-mobile">'
    .$web_link
    .'</div>'
    .'<div class="for-mobile">'.
    render_forced_external_link(
      $video_icon,
      get_youtube_link($obj['youtube_handle']))
    .'</div>';
}


function convert_smart_quotes($string)  {
    $search = array(chr(145), chr(146), chr(147), chr(148), chr(151));
    $replace = array("'", "'",   '"', '"', '-');
    return str_replace($search, $replace, $string);
}

function render_button($text, $link, $extra_class = '', $rel = true) {
  return render_link_helper($text, $link, $rel, array('class' => 'large red button '.$extra_class));
}

function render_small_button($text, $link, $extra_class = '', $rel = true) {
  return render_link_helper($text, $link, $rel,  array('class' => 'small red button '.$extra_class));
}

function render_oneliner($object) {
  $oneliner = idx($object, 'oneliner');
  return $oneliner ? render_mentions_text($oneliner) : null;
}

function render_mentions_text_no_hovercard($text) {
  return render_mentions_text($text, false, false, false);
}

function render_mentions_text_without_media($text) {
  return render_mentions_text($text, false);
}

function render_mentions_text_for_article_body($text) {
  return render_mentions_text($text, true, true, true);
}


function render_rating($obj, $small = false) {
  list($review_class, $rating) = get_rating_info($obj);
  if (!$rating) {
    return null;
  }
  $container_class = 'rating-container';
  $score_total = null;
  if (!$small) {
    $container_class .= ' highlight-rating-container';
    $score_total = '<div class="score-total">out of 100</div>';
    $score_total .=
      '<div class="score-question">'
      .render_link(render_local_image('question.gif'), 'help/reviews.php')
      .'</div>';
  }

return '<div class="'.$review_class.'">'
  .'<div class="'.$container_class.'">'
  .'<div class="score-number">'
  .render_link($rating, 'help/reviews.php')
  .'</div>'
  .render_link($score_total, 'help/reviews.php')
.'</div>'
  .'</div>';


}

function get_hotness($obj) {
  if (isset($obj['thumbs'])) {
    switch($obj['thumbs']) {
    case 'up'  :    return 'good';
    case 'down':    return 'bad';
    case 'meh':     return 'meh';
    }
  }
  $rating = $obj['rating'];
  if (!$rating) {
    return null;
  }

  if ($rating > HIGH_RATING_MIN ) {
    return 'good';
  } else if ($rating > MID_RATING_MIN) {
    return 'meh';
  } else {
    return 'bad';
  }
}

function get_rating_info($object) {
  $rating = null;
  switch(get_hotness($object)) {
  case 'good': $rating = '+'; $review_class = 'good-review'; break;
  case 'bad':  $rating = '-'; $review_class = 'bad-review'; break;
  case 'meh':  $rating = '~'; $review_class = 'meh-review'; break;
  default:     $review_class = 'no-rating';
  }

  if ($object['rating'] && is_numeric($object['rating'])) {
    $rating = $object['rating'];
  }

  return array($review_class, $rating);
}


function render_badge($img_src, $badge_name = null, $dimensions = null) {
  $ret =
    '<div class="badge-container">'
    .'<img src="'.$img_src.'"';
  if ($dimensions && idx($dimensions, 'width') && idx($dimensions,'height')) {
    $ret .= ' width="'.$dimensions['width'].'px" height="'.$dimensions['height'].'px" ';
  }
  $ret .= '>';
  if ($badge_name) {
    $ret .= '<div class="badge-title">'
      .$badge_name
      .'</div>';
  }
  $ret .= '</div>';
  return $ret;
}

function render_tag($tag_id) {
  $tag = get_object($tag_id, 'tag');
  return ucwords($tag['name']);
}

function render_stars_for_object($object, $max_stars = 3) {
  if (!idx($object, 'stars')) {
    return null;
  }
  $star_ids = explode(',', $object['stars']);
  $star_ids = array_slice($star_ids, 0, 3, true);
  // No links, keep it simple
  $stars = array();
  foreach ($star_ids as $star_id) {
    $star = get_object($star_id, 'person');
    if ($star && $star['name']) {
      $stars[] = $star['name'];
    }
  }
  if ($stars) {
    return implode('<br/>', $stars);
  }
  return null;
}

function get_song_details_from_song($song) {
  $possible_song_people_fields =
    array('playback_singers', 'music_directors', 'lyricists', 'stars');
  $people_ids = array();
  foreach ($possible_song_people_fields as $field) {
    $people_ids = array_unique(array_merge($people_ids,
                                           get_parsed_ids($song, $field)));
  }
  $people = array();
  if (!$people_ids) {
    return array();
  }

  $people = get_objects($people_ids, 'people');

  $details = array();
  foreach ($possible_song_people_fields as $field) {
    if ($song[$field]) {
      $rendered_array = array();
      foreach (explode(',', $song[$field]) as $person_entry) {
        if (is_numeric($person_entry)) {
          if (!idx($people, $person_entry)) {
            continue;
          }
          $rendered_array[] =
            render_person_link($people[$person_entry]);
        } else {
          $rendered_array[] = $person_entry;
        }
      }
      $rendered_field = implode(', ', $rendered_array);
      $details[ucwords(str_replace('_', ' ', $field))] = $rendered_field;
    }
  }
  return $details;
}

?>