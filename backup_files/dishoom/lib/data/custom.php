<?php

define('NEWS_TTL', 600);
define('LONG_TTL', 46400);
define('SKIP_APC_CACHE', false);
define('LATEST_ARTICLES_PER_PAGE', 20);
define('SPOTLIGHT_ARTICLES_PER_PAGE', 11);

function get_cached_box_office_ids() {
  $apc_key = 'boxofficeids';
  $apc_data = apc_fetch($apc_key);
  if (!SKIP_APC_CACHE && $apc_data !== false && !is_admin()) {
    return unserialize($apc_data);
  }
  $sql = 'SELECT id FROM films WHERE rating IS NOT NULL AND rating > 0 AND release_time IS NOT NULL AND release_time < '.time().' ORDER BY release_time DESC LIMIT 6';
  $data = get_objects_from_sql($sql, 'film');
  $data = array_pull($data, 'id');

  if (!is_admin()) {
    apc_store($apc_key, serialize($data), LONG_TTL);
  }
  return $data;
}

function get_cached_coming_soon_ids() {
  $apc_key = 'comingsoonids';
  $apc_data = apc_fetch($apc_key);
  if (!SKIP_APC_CACHE && $apc_data !== false && !is_admin()) {
    return unserialize($apc_data);
  }
  $sql = 'SELECT id FROM films WHERE release_time is not null AND release_time > '.time().' ORDER BY release_time ASC LIMIT 6';
  $data = get_objects_from_sql($sql, 'film');
  $data = array_pull($data, 'id');
  if (!is_admin()) {
    apc_store($apc_key, serialize($data), LONG_TTL);
  }
  return $data;
}

function get_featured_articles($start = 0, $limit = SPOTLIGHT_ARTICLES_PER_PAGE) {
  $apc_key = 'featuredarticles:'.$start.':'.$limit;
  $apc_data = apc_fetch($apc_key);
  if (!SKIP_APC_CACHE && $apc_data !== false && !is_admin()) {
    return unserialize($apc_data);
  }
  $sql = 'select * from articles where deleted is null and featured = 1 and publish_time is not null';
  if (!is_admin()) {
    $sql .= ' AND publish_time < '.time();
  }
  $sql .= ' ORDER BY publish_time DESC, id DESC limit '.$start.', '.$limit;

  $data = get_objects_from_sql($sql, 'article');
  if (!is_admin()) {
    apc_store($apc_key, serialize($data), NEWS_TTL);
  }
  return $data;
}

function get_latest_articles($start = 0, $limit = LATEST_ARTICLES_PER_PAGE) {
  $apc_key = 'latestarticles:'.$start.':'.$limit;
  $apc_data = apc_fetch($apc_key);
  if (!SKIP_APC_CACHE && $apc_data !== false && !is_admin()) {
    return unserialize($apc_data);
  }
  $sql = 'select * from articles where deleted is null and publish_time is not null';
  if (!is_admin()) {
    $sql .= ' AND publish_time < '.time();
  }
  $sql .= ' ORDER BY publish_time DESC, id DESC limit '.$start.', '.$limit;
  $data = get_objects_from_sql($sql, 'article');
  if (!is_admin()) {
    apc_store($apc_key, serialize($data), NEWS_TTL);
  }
  return $data;
}

function get_random_quote() {
  global $link;
  $sql = 'select * from quotes order by RAND() limit 1';
  return head(get_objects_from_sql($sql, 'quotes'));
}

function get_tags_from_db($type) {
  if ($type) {
    $q = sprintf("SELECT id, name FROM tags where ".$type." IS NOT NULL "
                 ."and deleted is null");
  } else {
    $q = sprintf("SELECT id, name FROM tags where deleted is null");
  }
  $unsimplified_tags = get_objects_from_sql($q, 'tags');
  $tags = array();
  foreach ($unsimplified_tags as $tag) {
    $tags[$tag['id']] = array('id' => $tag['id'],
                              'name' => $tag['name']);
  }
  return $tags;
}

function get_untaggable_types() {
  return array('poll_option', 'poll', 'review', 'youtube_film', 'tag');
}

function get_tags($type = null, $from_db = false) {
  $type = get_object_name($type);
  if ($type
      && in_array($type, get_untaggable_types())) {
    // Not supported
    return array();
  }

  if ($type) {
    $apc_key = 'tagslist:'.$type;
  } else {
    $apc_key = 'alltags';
  }

  $apc_data = apc_fetch($apc_key);
  if ($apc_data !== false) {
    return unserialize($apc_data);
  }

  $tags = get_tags_from_db($type);
  apc_store($apc_key, serialize($tags), APC_TTL);

  return $tags;
}


function get_films_for_person_field($person_id, $field) {
  $supported_fields =
    array('stars' => 1,
          'directors' => 1,
          'producers' => 1,
          'music_directors' => 1,
          'supporting_actors' => 1);
  if (!$person_id || !isset($supported_fields[$field])) {
    slog('trying to get '.$field.' for id '.$person_id.' which is not supported');
    return null;
  }
  $apc_key = get_apc_key($field.'_films', $person_id);
  $apc_data = apc_fetch($apc_key);
  if ($apc_data !== false) {
    return unserialize($apc_data);
  }

  $sql =
    sprintf(
      "SELECT * from films where deleted is null and "
      ."(".$field." = '%s' or ".$field." like '%s' or ".$field." like '%s' or ".$field." like '%s')"
      ." order by year desc",
      $person_id,
      $person_id.',%',
      '%,'.$person_id.',%',
      '%,'.$person_id);
  $data = get_objects_from_sql($sql, 'films');
  apc_store($apc_key, serialize($data), APC_TTL);
  return $data;
}

function get_tagged_films($tag_id_or_ids, $limit = null, $random = false,
                          $extra = null) {
  $sql = "SELECT * from films where deleted is null";
  if (!is_array($tag_id_or_ids)) {
    $tag_id_or_ids = array($tag_id_or_ids);
  }
  foreach ($tag_id_or_ids as $tag_id) {
    $sql .=
      sprintf(
        " and (tags = '%s' or tags like '%s' or tags like '%s' or tags like '%s')",
        $tag_id,
        $tag_id.',%',
        '%,'.$tag_id.',%',
        '%,'.$tag_id);
  }
  if ($extra) {
    $sql .= ' '.$extra;
  }
  if ($random) {
    $sql .= ' ORDER BY RAND()';
  } else {
    $sql .= ' ORDER BY rating desc';
  }
  if ($limit && is_numeric($limit)) {
    $sql .= ' LIMIT '.$limit;
  }

  return get_objects_from_sql($sql, 'films');
}

function get_similar_films($film, $limit = 5) {
  if (!$film || !$film['id'] || !$film['tags'] || !$film['rating']) {
    return null;
  }
  $apc_key = $film['id'].':similarfilms';
  $apc_data = apc_fetch($apc_key);
  if ($apc_data !== false) {
    return unserialize($apc_data);
  }

  $tags = explode(',',$film['tags']);
  $num_found = 0;
  $ret = array();
  $extra = sprintf("AND year > %d and year < %d and oneliner > '' "
                   ."and rating > %d and rating < %d",
                   $film['year'] - 6,
                   $film['year'] + 8,
                   $film['rating'] - 10,
                   $film['rating'] + 16);
  foreach ($tags as $tag) {
    $similar_films = get_tagged_films($tag, $limit, false, $extra);
    foreach ($similar_films as $similar_film) {
      $film_id = $similar_film['id'];
      if ($film['id'] == $film_id) {
        continue;
      }
      if (!isset($ret[$film_id])) {
        $ret[$film_id] = $similar_film;
        $ret[$film_id]['similar_tag'] = $tag;
        $num_found++;
        if ($num_found == $limit) {
          break 2;
        }
      }
    }
  }

  apc_store($apc_key, serialize($ret), APC_TTL);
  return $ret;
}

?>