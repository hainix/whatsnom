<?php

function render_film_cast($film) {
  // Cast
  $ret = '';
  $fields = array(
    'directors',
    'stars',
    'music_directors',
    'producers',
    'supporting_actors',
  );

  foreach ($fields as $person_field) {
    if ($film[$person_field]) {
      $objects = get_objects(explode(',', $film[$person_field]),
                             'people');
      if ($objects) {
        $type_name =
          ucwords(str_replace('_', ' ', $person_field));
        $ret .= '<h5><span>'.$type_name.'</span></h4>'
          .'<ul>';
        foreach ($objects as $person) {
          $ret .= '<li>'.render_person_link($person).'</li>';
        }
        $ret .= '</ul><br/>';
      }
    }
  }
  return $ret;
}


function film_is_released($film, $now = null) {
  if (!$now) {
    $now = time();
  }
  if ($film['release_time']) {
    return $now > $film['release_time'];
  } else if (date("Y", $now) == $film['year']) {
      return false;
  }
  return true;

}

function render_film_release_time($film, $now = null) {
  $now = $now ?: time();
  if ($film['release_time']
      && date("Y", $film['release_time']) == date("Y", $now)) {
    return date("j M Y", $film['release_time']);
  } else if ($film['year'] || $film['release_time']) {
    return $film['release_time']
      ? date("Y", $film['release_time'])
      : $film['year'];
  }
  return null;
}
