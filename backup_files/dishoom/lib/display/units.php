<?php
  /*
function render_object_widget($object, $dimensions = null) {
  if (!$dimensions) {
    $dimensions = array('width' => 150, 'height' => 220);
  }
  if ($object['type'] == 'song' || $object['type'] == 'video') {
    $poster_src = get_youtube_large_thumb_src($object['youtube_handle']);
    $dimensions_attributes = null;
  } else {
    $poster_src = get_cropped_profile_pic_src($object,
                                              $dimensions);
    $dimensions_attributes = 'width="'.$dimensions['width']
      .'" height="'.$dimensions['height'].'"';
  }
  $img = '<img '.$dimensions_attributes.' src="'.$poster_src.'"'
  .'alt="'.$object['name'].'" title="'.$object['name'].'" class="thumb_image">';
$subtitle = '';
if ($object['type'] == 'film') {
  $subtitle = '<div class="film-subtitle">'.$object['year'];
  if (idx($object, 'rating')) {
    $subtitle .= ' | <b>'.$object['rating'].'%</b>';
  }
  $subtitle .= '</div>';
}
$ret =
  render_object_link_no_hovercard($object, $img)
  .'<div class="film-title">'
  .render_object_link($object)
  .$subtitle
  .'</div>';
return $ret;
}
  */


// for recommendation films
function render_recommended_films_slider($films) {
  $tag_mapping = get_tags('film');
  $rec_rows = array();
  foreach ($films as $film) {
    $poster_src = get_cropped_profile_pic_src($film, array('width' => 130,
                                                           'height' => 205));
    $button = render_small_button('More Info', get_film_url($film));
    $highlight_tag = idx($film, 'similar_tag');
    if (!$highlight_tag && $film['tags']) {
      $highlight_tag = head(explode(',', $film['tags']));
    }
    $tag_render = '';
    if ($film['rating']) {
      $tag_render .= '<b>'.$film['rating'].'%</b> ';
    }

    // For now, don't show the tag bc it's all the same
    if (false && $highlight_tag) {
      $tag_render .= ucwords($tag_mapping[$highlight_tag]['name'])
        .', '.$film['year'];
    } else {
      $tag_render .= $film['year'];
    }

    $rec_rows[] =
      '<div class="handle" rel="">
         <img width="130" height="205" src="'.$poster_src.'" class="attachment-img-accordion-slider wp-post-image" '
          .'alt="'.$film['name'].'" title="'.$film['name'].'" />
         <h4 class="no-cufon">'.render_film_link_no_hovercard($film).'</h4>
         <p class="profile">'.$tag_render.'</p>
      </div>
      <p>'.render_oneliner($film).'</p>
      <br/>
      <div align="center">'
       .$button
    .'</div>';


  }

  $script =
    '<script type="text/javascript">jQuery(document).ready(function($) {
       $(".accordion-slider").hrzAccordion({
         openOnLoad   : 1,
         handlePosition     : "left"
       });
    });
    </script>';

  return
    $script
    . '<ul class="accordion-slider">'
    .'<li>'.implode('</li><li>', $rec_rows).'</li>'
    .'</ul>';
  return $ret;
}

function render_buzz_box($term, $loading_term = 'Loading news...', $only_recent = false, $short_version = false) {
  if ($short_version) {
    $news_func = 'initializeHomeNewsSearch';
  } else {
    $news_func = $only_recent ? 'initializeNewsSearch' : 'initializeDishSearch';
  }
  $box = '
  <script src="'.BASE_URL.'js/news_query.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    var search_term = "'.$term.'";
    google.setOnLoadCallback('.$news_func.');
  </script>
  <div id="dish_container"></div>';
return
  $box
  .'<div id="news_loader">'.$loading_term.'</div>';
}

function render_bubbles($objects, $per_row = 3, $max = null) {
  if (!$objects) {
    slog('no obj');
    return null;
  }
  $bubbles_list_start =  '<ul class="bubbles-list group">';
  $ret = $bubbles_list_start;
  $i = 0;
  foreach ($objects as $object) {
    if ($max && $i == $max) {
      break;
    }
    if ($i && $per_row && ($i % $per_row) == 0) {
      $ret .= '</ul>'.$bubbles_list_start;
    }
    $ret .= render_single_bubble($object);
    $i++;
  }
  $ret .= '</ul>';
  return $ret;
}

function render_single_bubble($object) {
  $type = get_object_name($object['type']);
  $size = 78;
  $bubble_title = explode(' ', $object['name'], 2);
    $tag =
      '<img
         src="'.get_cropped_profile_pic_src($object,
                                            array('width' => $size,
                                                  'height' => $size)).'" '
        .' class="bubbles-list-img" '

      .'alt="'.$object['name'].'" title="'.$object['name'].'">';
    //<div class="shadow-thumb"></div>'
    $ret  = '<div class="sphere">'.$tag.'</div>'
      .' <h4>'.idx($bubble_title, 0).'<span> '
      .idx($bubble_title, 1).'</span>';
    if (idx($object, 'subtitle')) {
      $ret .= '<br/><small>'.$object['subtitle'].'</small>';
    }
    $ret .= '</h4>';
    return '<li>'.render_object_link($object, $ret, false /* hovercard */).'</li>';
}


?>