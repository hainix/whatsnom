<?php
 include_once $_SERVER["DOCUMENT_ROOT"].'/lib/core/page.php';
 include_once $_SERVER["DOCUMENT_ROOT"].'/lib/utils.php';

class cmsPage extends page {
  protected function getPageType() {
    return 'cms';
  }

  protected function showWrapper() {
    return false;
  }

  protected function getContent() {
    return render_cms_top_bar()
      . '<div class="wrapper">'.$this->content.'</div>';
  }

  protected function canSee() {
    return true;
    return is_admin();
  }

  protected function getAnalyticsCode() {
    return null;
  }

  protected function getLogo() {
    return '<img src="'.BASE_URL.'images/logo/logo_small_cms.png" >';
  }
}

function update_cache_for_object($id, $type) {
  $obj = head(get_objects_from_db($id, $type));
  //slog('updated primary key cache');
  // Update cache for secondary objects
  $secondary_map = get_secondary_id_map();
  $table_name = get_table_name($type);
  if (isset($secondary_map[$table_name]) &&
      $obj[$secondary_map[$table_name]]) {
    // If it's a supported secondary type, then update the cache
    // for the primary's id/type pair. the following fetch is ignored, but does
    // the cache update
    //slog('updated secondary key cache');
    get_objects_against_secondary_id_from_db(
      $obj[$secondary_map[$table_name]],
      $table_name);
  }

}


function render_cms_top_bar() {
  return
    '<div style=" background: #f7f6e0; width: 100%; '.
    'border-bottom: #8D8C8C 1px solid; margin-bottom: 20px; min-height: 50px">'
    .'<div class="rfloat">'.render_cms_add_bar().'</div>'
    .'<div style="padding: 10px;">'.render_cms_all_bar().'</div></div>';

}

function render_cms_add_bar() {
  $fields = array_keys(get_add_fields_lists());
  $html =
  '<div style="margin-top: 4px;">
<table><tr><td>'
    .render_link('<img src="'.BASE_URL.'images/image_upload.png" />', 'cms/upload')
.'</td><td>
     <form action="'.BASE_URL.'cms/add.php" method="get">
       <select name="type">';
  foreach ($fields as $field) {
    $html .=
      '<option value="'.$field.'">'
      .ucwords(str_replace('_', ' ', $field))
      .'</option>';
  }
  $html .=
    '</select>
</td><td>
    <input type="submit" value="Add New" class="small red button" />
</td>
</form></tr></table></div>';
  return $html;
}

function render_cms_all_bar() {
  $key_links = array();
  $film_filters = array('upcoming', 'all');
  foreach ($film_filters as $filter) {
    $key_links[$filter.' films'] = 'cms/?filter='.$filter;
  }
  $key_links['articles'] = 'cms/add.php?type=article';
  //$key_links['slides'] = 'cms/add.php?type=slide';
  $all_render_types =
    array(
      'people',
      //'quotes',
      //'tags',
      //'videos',
      //  'youtube_films'
    );
  foreach ($all_render_types as $render_type) {
    $key_links[str_replace('_', ' ', $render_type)] =
      'cms/'.$render_type.'.php';
  }


  $html = '<table><tr><td><h4>browse:</h4></td>';
  foreach ($key_links as $key => $link) {
    $html .= '<td> | </td>';
    $html .= '<td><h4>'.render_link($key, $link).'</h4></td>';
  }
  $html .= '</tr></table>';

  return $html;
}

function field_supports_mentions($field) {
  $supporting_fields = array('oneliner' => 1,
                             'plot' => 1,
                             'article_text' => 1);
  return isset($supporting_fields[$field]);
}

function get_field_word_count_constraints($field) {
  $restraints = array(
    'oneliner'     => array(15, 25),
    'article_text' => array(50, 1500),
    'subheader'    => array(1, 10),
    'headline'     => array(1, 8),
    'excerpt'      => array(5, 150),
  );
  return idx($restraints, $field, array());
}

function render_input_type($field_type, $name, $default = null) {
  $default = stripslashes($default); // hopefully this isn't terrible..
  switch ($field_type) {
  case 'user':
    $ret = '<input name ="'.$name.'" value="'.get_admin_name().'" />';
    return $ret;
  case 'int':
    $ret = '<input name="'.$name.'" ';
    if ($default !== null) {
      $ret .= 'value="'.$default.'" ';
    }
    $ret .= 'size=8 maxlength=8 />';
    return $ret;
  case 'readonly':
    return $default;
  case 'text':
    $ret = '<textarea name="'.$name.'" ';
    $word_count_restraints = get_field_word_count_constraints($name);
    if ($word_count_restraints) {
      list($wc_min, $wc_max) = $word_count_restraints;
      $ret .= 'class="count['.$wc_min.','.$wc_max.']" ';
    }
    $ret .= 'rows="9" cols="90">'
      .$default
      .'</textarea>';
    if (field_supports_mentions($name)) {
      $ret .= '<div style="float: right; text-align: right;">';
      if ($word_count_restraints) {
        list($wc_min, $wc_max) = $word_count_restraints;
        $ret .= '<i>min <b>'.$wc_min.'</b> / max <b>'.$wc_max.'</b></i><br/>';
      }
      $ret .= 'use {vixen:p:42021} to link people(p)/songs(s)/films(f)/articles(a)'
        .'<br/>,{Jism 2:t:58518} for film trailers';
      if ($name == 'article_text') {
        $ret .=
          '<br/>or {v:Sqz5dbs5zmo} for youtube videos, or {v:Sqz5dbs5zmo:90} to start 90 seconds in';
        $ret .=
          '<br/>or {i:1381733964_0.jpg} for images, or {i:1381733964_0.jpg:cute girl} for captions';
        $ret .=
          '<br/>or {cool site:l:therazoredge.org} for an external link - don\'t add the http:// part';

      }
      $ret .= '</div>';
    }

    return $ret;
  case 'string':
  case 'char':
    $ret = '<input name="'.$name.'" ';
    $ret .= ($field_type == 'char') ? ' size=1 maxlength=1 ' : ' size=20 ';

    if ($default) {
      $ret .= 'value="'.$default.'"';
    }
    $ret .= '/>';
    if ($name == 'relationship_status') {
      $ret .= '<br/><b>options</b>: M = Married, S = Single, D = Dating, E = Engaged, C = It\'s Complicated';
    } else if ($name == 'caption_position') {
      $ret .= '<br/><b>options</b>: L = Left, R = Right (these suck: T = Top, B = Bottom)';
    } else if ($name == 'object_type') {
      $ret .= '<br/><b>options</b>: F = Film, A = Article, P = Person, S = Song, V = Video, T = Trailer';
    } else if ($name == 'related_videos') {
      $ret .= '<br/>enter comma seperated dishoom video ids, in display order, like for video 22, put "21, 23". also do this for videos 21 and 23';
    } else if ($name == 'image_id') {
      $ret .= '<br/>id of what you uploaded to slider folder. should be objectid_date, so for bipasha (id 42021) on dec 17, you would save the file as 42021_171212.jpg and put 42021_171212 here';
    } else if ($name == 'slide_position') {
      $ret .= '<br/>1 is the first position, 2 is the second, etc. if two slides have the same position, then the more recent ones will show first. the default position is 5, so you can keep this blank and use higher numbers to explicitly move up slides when you want to save them';
    } else if ($name == 'is_featured') {
      $ret .= '<br/>set to false to explicitly remove from homepage. defaults to true, and will fall off if 5 more recent slides have been added';
    } else if ($name == 'freeform_link') {
      $ret .= '<br/>any dishoom link can be put here. for example, if you wanted the slide to link to dishoomfilms.com/sunny-on-pbs then you would put "sunny-on-pbs" (no quotes) as the freeform link. this takes precendence over object_id and object_type';
    } else if ($name == 'image_handle') {
      $ret .= '<br/>from '.render_link('Dishoom uploader', 'cms/upload', array('target' => '_blank')).', like 1379879043_10.jpg';
    }


    return $ret;
  case 'date':
    $default_unixtime = $default ? : time();
    $default_time = date('m/d/Y H:i', $default_unixtime);
    $ret =
      '<input id="date-picker-'.$name.'" name="'.$name.'" class="datepicker" value="'.$default_time.'">';
    $ret .=
      '<script>$("#date-picker-'.$name.'").datetimepicker();</script>';
    return $ret;
  case 'youtube':
    $ret = render_video_icon(array('youtube_handle' => $default))
      . '<input name="'.$name.'"  size=20 ';
    if ($default) {
      $ret .= 'value="'.$default.'"';
    }
    $ret .= '/> (remember, nothing after the &)';
    return $ret;
  case 'author':
    $authors = get_author_data();
    $ret = '<select name="'.$name.'">';
    foreach ($authors as $uid => $author_data) {
      $ret .= '<option value="'.$uid.'" ';
      if ($default == $uid) {
        $ret .= ' selected="true" ';
      }
      $ret .= '>'.$author_data['name'].'</option>';
    }
    $ret .= '</select>';
    return $ret;
  case 'peoplelist' :
  case 'filmlist' :
  case 'tags' :
  case 'musicdirectorlist' :
  case 'playbacksingerlist' :
  case 'directorlist' :
  case 'producerlist' :
  case 'sourceshowlist' :
  case 'distributorlist' :
    global $type;
    switch ($field_type) {
    case 'peoplelist':
      global $people;
      $prefill_tags = $people;
      break;
    case 'producerlist' :
      global $producers;
      $prefill_tags = $producers;
      break;
    case 'directorlist' :
      global $directors;
      $prefill_tags = $directors;
      break;
    case 'musicdirectorlist' :
      global $music_directors;
      $prefill_tags = $music_directors;
      break;
    case 'playbacksingerlist' :
      global $playback_singers;
      $prefill_tags = $playback_singers;
      break;
    case 'distributorlist' :
      global $distributors;
      $prefill_tags = $distributors;
      break;
    case 'sourceshowlist' :
      global $source_shows;
      $prefill_tags = $source_shows;
      break;
    case 'filmlist':
      global $films;
      $prefill_tags = $films;
      break;
    case 'tags':
      global $possible_tags;
      $prefill_tags = $possible_tags;
    }
    $ret = '<input class="wide" type="text" name="'.$name.'" ';
    if ($default) {
      $ret .= 'value="'.convert_ids_to_tags($default, $prefill_tags).'"';
    }
    $ret .= ' id ="'.$name.'" /> ';
    $ret .=
      '<script type="text/javascript">$(document).ready(function() {'.
      '$("#' . $name . '").tagit({allowSpaces: true, availableTags: '.json_encode(array_values($prefill_tags)).'});});</script>';
    if ($field_type == 'tags') {
      $ret .= '<b>options</b>: '.implode(', ', array_values($possible_tags));
    }

    return $ret;
  case 'bool':
    $ret = '<input type="radio" name="'.$name.'" value="1" ';
    if ($default) {
      $ret .= ' checked="yes" ';
    }
    $ret .= '/>Yes ';

    $ret .= '<input type="radio" name="'.$name.'" value="0" ';
    if (!$default) {
      $ret .= ' checked="yes" ';
    }
    $ret .= '/>No';
    return $ret;
  default:
    return 'unrecognized field type yo';
  }
}
function ar($t) {
  return $t ? mysql_real_escape_string(serialize($t)) : '';
}

function cln($t) {
  return mysql_real_escape_string(trim(stripslashes($t)));
}
function get_tag_delimiter() {
  return ',';
  //  return '*#*';
}

function convert_tags_to_ids($tags, $mapping) {
  if (!is_array($tags)) {
    $tags = explode(get_tag_delimiter(), $tags);
  }
  $name_to_id_map = array_flip($mapping);
  $ret = array();
  foreach ($tags as $tag) {
    $ret[] = idx($name_to_id_map, stripslashes($tag));
  }
  return implode(get_tag_delimiter(), array_filter($ret));
}

function convert_ids_to_tags($ids, $mapping, $return_names_only = false) {
  if (!is_array($ids)) {
    $ids = explode(get_tag_delimiter(), $ids);
  }
  $ret = array();
  foreach ($ids as $id) {
    if ($return_names_only) {
      $ret[] = idx(idx($mapping, $id), 'name');
    } else {
      $ret[] = idx($mapping, $id);
    }
  }
  return implode(get_tag_delimiter(), array_filter($ret));
}

function process_people_tags($tags) {
  $ret = array();
  foreach ($tags as $tag) {
    if (!$tag['name']) {
      continue;
    }
    $ret[$tag['id']] = str_replace(',', '', stripslashes($tag['name']));
  }
  return $ret;
}

function process_film_tags($tags) {
  $ret = array();
  foreach ($tags as $tag) {
    $ret[$tag['id']] = str_replace(',', '', $tag['name'].' ('.$tag['year'].')');
  }
  return $ret;
}

function get_add_fields_lists($type = null) {
  /*
    'youtube_film' => array(
      'youtube_handle' => 'youtube',
      'price' => 'int',
      'film_id' => 'int',
    ),
  */
  $ret =  array(
    'article' => array(
      'headline' => 'string',
      'image_handle' => 'string',
    ),
    'slide' => array(
      'object_id'      => 'int',
      'object_type'    => 'char',
      'headline'       => 'string',
      'image_id'       => 'string',
      'is_featured'    => 'bool',
      'slide_position' => 'int',
      'freeform_link'  => 'string',
    ),
    'person' => array(
      'name'          => 'string',
      'gender'        => 'char',
      'wiki_handle'   => 'string',
      'tier'          => 'char',
      'rating'        => 'int',
    ),

    'poll' => array(
      'question' => 'text',
    ),
    'poll_option' => array(
      'poll_id' => 'int',
      'value'   => 'string',
    ),
    'song' => array(
      'name'           => 'string',
      'film_id'        => 'int',
      'youtube_handle' => 'youtube',
    ),
    'film' => array(
      'name'        => 'string',
      'year'        => 'int',
      'rating'      => 'int',
      'wiki_handle' => 'string',
      'oneliner'    => 'text',
    ),
    'review' => array(
      'reviewer' => 'string',
      'film_id' => 'int',
      'source_name' => 'string',
      'source_link' => 'string',
      'rating' => 'int',
      'excerpt' => 'text',
    ),
    'tag'    => array(
      'name' => 'string',
      'film' => 'bool',
      'person'=> 'bool',
      'article' => 'bool',
      'song' => 'bool',
      'image' => 'bool',
      'video' => 'bool',
    ),
    'video' => array(
      'name' => 'string',
      'youtube_handle' => 'youtube',
      'film_id' => 'int',
      'published_date' => 'string',
      'related_videos' => 'string',
      'part' => 'int',
    ),
    'quote' => array(
      'film_id' => 'int',
      'quote' => 'string'
    ),
  );

  if ($type) {
    return idx($ret, $type);
  }
  return $ret;
}

function get_fields_lists($type =  null) {
  $ret =  array(
    'person' => array(
      'name'    => 'string',
      'primary_type'    => 'tags',
      'actor_type'    => 'string',
      'gender'  => 'char',
      'tags'    => 'tags',
      'oneliner' => 'text',
      'num_photos' => 'int',
      'bio'     => 'text',
      'short_name' => 'string',
      'nickname' => 'string',
      'relationship_status' => 'char',
      'relationship_partner' => 'peoplelist',
      'linked_to' => 'peoplelist',
      'epithet' => 'string',
      'year_started' => 'int',
      'famous_for' => 'filmlist',
      'interesting_fact' => 'text',
      'related_to' => 'peoplelist',
      'birthday_string' => 'string',
      'birthtown' => 'string',
      'died_string' => 'string',
      'hometown' => 'string',
      'twitter' => 'string',
      'wiki_handle' => 'string',
      'wiki_summary' => 'text',
      'rating' => 'int',
      'tier' => 'char',
      'deleted' => 'bool',
      'duplicate_of' => 'int',
      'alternate_spellings' => 'string',
    ),
    'song' => array(
      'name'   => 'string',
      'tags' => 'tags',
      'film_id' => 'filmlist',
      'youtube_handle' => 'youtube',
      'lyrics' => 'text',
      'stars' => 'peoplelist',
      'playback_singers_text' => 'string',
      'playback_singers' => 'playbacksingerlist',
      'music_directors_text' => 'readonly',
      'music_directors' => 'musicdirectorlist',
      'lyricists' => 'readonly',
      'duration' => 'int',
      'rating' => 'int',
      'manual_pass' => 'bool',
      'lyrics_link' => 'readonly',
      'source' => 'readonly',
      'deleted' => 'bool',
    ),
    'film' => array(
      'name'   => 'string',
      'year'    => 'int',
      'tags' => 'tags',
      'tier' => 'string',
      'stars'   => 'peoplelist',
      'supporting_actors'   => 'peoplelist',
      'cameos'   => 'peoplelist',
      'rating'  => 'int',
      'trailer' => 'youtube',
      'oneliner' => 'text',
      'plot'    => 'text',
      'summary' => 'readonly',
      'storyline' => 'readonly',
      'release_time' => 'date',
      //'release_date' => 'readonly',
      'directors' => 'directorlist',
      'producers' => 'producerlist',
      'distributor' => 'distributorlist',
      'music_directors' => 'musicdirectorlist',
      //'fullfilm_handle' => 'youtube',
      //'fullfilm_price' => 'int',
      //'fullfilm_has_subtitles' => 'bool',
      //'fullfilm_embed_disabled' => 'bool',
      'alternate_spellings' => 'string',
      'wiki_handle' => 'string',
      'wog_handle' => 'string',
      'runtime' => 'string',
      'handle' => 'readonly',
      'deleted' => 'bool',
    ),
    'review' => array(
      'reviewer' => 'string',
      'film_id' => 'filmlist',
      'source_name' => 'string',
      'source_link' => 'string',
      'rating' => 'int',
      'excerpt' => 'text',
      'article' => 'text',
      'dishoom_article_id' => 'int',
      'deleted' => 'bool',
    ),
    'tag'    => array(
      'name' => 'string',
      'film' => 'bool',
      'person' => 'bool',
      'article' => 'bool',
      'song' => 'bool',
      'image' => 'bool',
      'video' => 'bool',
      'deleted' => 'bool',
    ),
    'video' => array(
      'name' => 'string',
      'youtube_handle' => 'youtube',
      'tags' => 'tags',
      'film_id' => 'filmlist',
      'stars' => 'peoplelist',
      'rating' => 'int',
      'published_date' => 'string',
      'related_videos' => 'string',
      'source_show' => 'sourceshowlist',
      'part' => 'int',
      'quote' => 'string',
      'featured' => 'bool',
      'deleted' => 'bool',
    ),
    'youtube_film' => array(
      'title' => 'readonly',
      'youtube_handle' => 'readonly',
      'price' => 'int',
      'film_id' => 'filmlist',
      'deleted' => 'bool',
    ),
    'quote'        => array(
      'film_id' => 'filmlist',
      'quote' => 'text'
    ),
    'slide' => array(
      'object_id' => 'int',
      'object_type' => 'char',
      'headline' => 'string',
      'image_id' => 'string',
      'is_featured' => 'bool',
      'slide_position' => 'int',
      'freeform_link'  => 'string',
      'deleted' => 'bool',
    ),
    'article'      => array(
      'headline' => 'string',
      'subheader' => 'text',
      'handle' => 'readonly',
      'image_handle' => 'string',
      'image_finalized' => 'bool',
      'article_text' => 'text',
      'film_id' =>  'filmlist',
      'stars' => 'peoplelist',
      'featured' => 'bool',
      'publish_time' => 'date',
      'author' => 'author',
      'deleted' => 'bool',
    ),
    'poll' => array(
      'question' => 'text',
    ),
    'poll_option' => array(
      'poll_id' => 'int',
      'value' => 'string',
    )
  );
  if ($type) {
    return idx($ret, $type);
  }
  return $ret;
}

function sanitize_cms_text($text) {
  return $text;
  return addslashes($text);
}

?>
