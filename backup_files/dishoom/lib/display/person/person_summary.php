<?php

function get_filmography_data_from_person($person) {
  $person_id = $person['id'];

  // Director
  $module_title = null;
  $skip_rated_films = false;
  $supporting_films = array();
  if ($person['primary_type'] == 46) {
    $films = get_films_for_person_field($person_id, 'directors');
    $module_title = 'Directed';
  } else if ($person['primary_type'] == 47) {
    $films = get_films_for_person_field($person_id, 'music_directors');
    $module_title = 'Music';
    $skip_rated_films = true;
  } else if ($person['primary_type'] == 49
             || $person['primary_type'] == 53) {
    $films = get_films_for_person_field($person_id, 'producers');
    $module_title = 'Produced';
  } else {
    $films = get_films_for_person_field($person_id, 'stars');
    $supporting_films = get_films_for_person_field($person_id,
                                                   'supporting_actors');

    // For the object table
    foreach ($supporting_films as $supporting_film_id => $supporting_film) {
      if (isset($films[$supporting_film_id])) {
        // Don't allow people to be stars and supporting
        // actors of the same film
        unset($supporting_films[$supporting_film_id]);
      } else {
        $supporting_films[$supporting_film_id]['subtitle'] =
          '<i>Supporting Role</i>';
      }
    }
  }

  $best_film_render = $worst_film_render = null;
  $all_films_render = $costars_render = null;
  if ($films) {
    $high_rating = 0;
    $low_rating = 100;
    $first_film = $highest_film = $lowest_film = null;

    // For a star actor with enough star films, use only starred films.
    // Otherwise, use all films
    if ($films && count($films) > 5) {
      $films_to_rank_between = $films;
    } else {
      $films_to_rank_between = array_merge($films, $supporting_films);
    }

    if ($films_to_rank_between && count($films_to_rank_between) > 5) {
      foreach ($films_to_rank_between as $ranked_film) {
        $rating = $ranked_film['rating'];
        if (!$rating) {
          continue;
        }

        if ($rating > $high_rating) {
          $high_rating = $rating;
          $highest_film = $ranked_film;
        } else if ($rating < $low_rating) {
          $low_rating = $rating;
          $lowest_film = $ranked_film;
        }
      }
    }

    if (!$skip_rated_films) {
      if ($highest_film) {
        $best_film_render =
          '<h4>Best '.$module_title.'<span> Movie</span></h4>'
          .render_objects_table(array($highest_film));
      }
      if ($lowest_film && ($highest_film &&
                           $highest_film['id'] != $lowest_film['id'])) {
        $worst_film_render =
          '<h4>Worst '.$module_title.'<span> Movie</span></h4>'
          .render_objects_table(array($lowest_film));
      }
    }

    $all_films = array_merge($supporting_films, $films);
    // Sort combined films by year, descending
    $all_films = array_sort($all_films, 'year', SORT_DESC);

    $top_stars = get_top_stars($all_films);
    // Don't mark yourself as top collaborator
    if (idx($top_stars, $person['id'])) {
      unset($top_stars[$person['id']]);
    }

    if ($top_stars && count($top_stars) >= 2) {
      $collaborator_objects =
        get_objects(
          array_slice(array_keys($top_stars), 0, 3, true),
          'person');
      foreach ($collaborator_objects as $collab_person_id => $val) {
        $collaborator_objects[$collab_person_id]['subtitle']
          = $top_stars[$collab_person_id].' films';
      }
      $costars_render =
        '<h4>Top <span>Co-Stars</span></h4>'
        .render_bubbles($collaborator_objects);
    }

    $all_films_render =
      '<h4>'.$module_title.' <span>Filmography <small>('
      .count($all_films).')</small></span></h4>'
      .render_objects_table($all_films);
  }
  return array($best_film_render, $worst_film_render, $all_films_render,
               $costars_render);
}

function get_top_stars($films, $count_min = 2) {
  $star_counter = array();
  foreach ($films as $film) {
    if (!$film['stars']) {
      continue;
    }
    $stars = explode(',', $film['stars']);
    foreach ($stars as $star) {
      $star_counter[$star] = idx($star_counter, $star, 0) + 1;
    }
  }
  $star_counter =
    array_filter($star_counter,
                 function ($element) use ($count_min) {
                   return ($element >= $count_min); });
  arsort($star_counter);

  return $star_counter;
}

function get_person_detail_fields($person) {
  $fields = array();

  /*
  if ($person['tags']) {
    $tags = get_objects(explode(',', $person['tags']), 'tags');
    if ($tags) {
      $tags = array_pull($tags, 'name');
      $fields['Tags'] = implode($tags, ', ');
    }
  }
  */

  if ($person['nickname'] || $person['short_name']) {
    $nicknames = array();
    foreach (array('nickname', 'short_name') as $nick_type) {
      if ($person[$nick_type]) {
        $nicknames = array_merge($nicknames,
                                 explode(',',$person[$nick_type]));
      }
    }
    $fields['Nicknames'] = implode(', ', array_unique($nicknames));
  }

  $standard_fields =
    array(
      'birthday_string' => 'Birthday',
      'birthtown' => 'Birthtown',
      'hometown' => 'Hometown',
	  );


  foreach ($standard_fields as $field_name => $field_render) {
    if ($person[$field_name]) {
      $fields[$field_render] = $person[$field_name];
    }
  }

  $relationship_fields = array();
  $people_fields = array('relationship_partner', 'linked_to', 'related_to');
  foreach ($people_fields as $person_field) {
    if ($person[$person_field]) {
      if ($person_field == 'relationship_partner') {
        switch ($person['relationship_status']) {
        case 'M': $person_render_header = 'Married to'; break;
        case 'D': $person_render_header = 'Currently Dating'; break;
        case 'E': $person_render_header = 'Engaged to'; break;
        case 'C': $person_render_header = "It's Complicated With"; break;
        default: $person_render_header = 'Relationship Partner';
        }
      } else if ($person_field == 'linked_to') {
        $person_render_header = 'Rumored Linkups';
      } else {
      $person_render_header =
        ucwords(str_replace('_', ' ', $person_field));
      }

      $fields[$person_render_header] =
        render_people_list(explode(',', $person[$person_field]),
                           null, false);
    }
  }

  if ($person['interesting_fact']) {
    $fields['Did you know'] =
      render_mentions_text($person['interesting_fact']);
  }


  return $fields;
}

?>