<?php
require_once '../lib/core/base.php';
/* Get the query string "q" variable -- this is what the user typed in. */
$input = strtolower($_GET['q']);
$len = strlen($input);
$limit = 12;
$now = time();
define('STARS_TO_SHOW', 3);

$data = apc_fetch('search_index');
if (!$data) {
  $data = refresh_search_apc();
}
$data = unserialize($data);
$films = $data['films'];
$people = $data['people'];
$songs = $data['songs'];


if ($len) {
  $results = array('people' => array(),
                   'films' => array(),
                   'songs' => array());
  foreach ($films as $film) {
    if (stripos($film['name'], $input) !== false) {
      //if (strtolower(substr($film['title'],0,$len)) == $input) {
      $count++;
      $results['films'][$film['id']] = $film;
    }
    if ($limit && $count==$limit)
      break;
  }
  /*
  foreach ($people as $person) {
    if (stripos($person['name'], $input) !== false) {
      //if (strtolower(substr($person['name'],0,$len)) == $input) {
      $count++;
      $results['people'][$person['id']] = $person;
    }
    if ($limit && $count==$limit)
      break;
  }

  foreach ($songs as $song) {
    if (stripos($song['name'], $input) !== false) {
      //if (strtolower(substr($song['name'],0,$len)) == $input) {
      $count++;
      $results['songs'][$song['id']] = $song;
    }
    if ($limit && $count==$limit)
      break;
  }
  */
}

// TODO sort by tier / rating

$final_people = array('header' => array(), 'data' => array());
$final_people['header'] = array(
				'title' => 'People',
				'num' => count($results['people']),
				'limit' => $limit
				);
foreach ($results['people'] as $id => $data) {
  $people_data_entry =
	  array(
      'primary' => $data['name'],
      'image' => get_profile_pic_src($id, 'person'),
      'url' => BASE_URL.'p/?id='.$id,
      'fill_text' => $data['name'],
      'timeout' => 100
		);
  if ($data['primary_type']) {
    $people_data_entry['secondary'] = render_tag_simple($data['primary_type']);
  }
  $final_people['data'][] = $people_data_entry;
}


$final_songs = array('header' => array(), 'data' => array());
$final_songs['header'] = array(
				'title' => 'Songs',
				'num' => count($results['songs']),
				'limit' => $limit
				);
foreach ($results['songs'] as $data) {
  if ($data['film_id']) {
    $song_film_ids[$data['film_id']] = 1;
  }
}
if ($song_film_ids) {
  $sql = sprintf("select id, name, year from films where id in (%s)",
		 implode(',',array_keys($song_film_ids)));
  $song_films = get_objects_from_sql($sql);
}
foreach ($results['songs'] as $id => $data) {
  $song_data_row =
    array('primary' => $data['name'],
	  'url' => BASE_URL.'tv/?id='.$id.'&type=song',
	  'fill_text' => $data['name'],
	  'timeout' => 100
	  );

  if ($song_films[$data['film_id']]) {
    $song_data_row['secondary'] = 'from '.$song_films[$data['film_id']]['name']
      .' ('.$song_films[$data['film_id']]['year'].')';
    $song_data_row['image'] = get_profile_pic_src($data['film_id'], 'film');
  }
  $final_songs['data'][] = $song_data_row;
}


$final_films = array('header' => array(), 'data' => array());
$final_films['header'] = array(
			       'title' => 'Films',
			       'num' => count($results['films']),
			       'limit' => $limit
			       );

$people_ids = array();
foreach ($results['films'] as $id => $data) {
  if ($data['stars']) {
    $star_ids = array_slice(explode(',',$data['stars']), 0, STARS_TO_SHOW);
    $people_ids = array_merge($people_ids, $star_ids);
  }
}
if ($people_ids) {
  $sql = sprintf("select id, name from people where id in (%s)",
		 implode(',',array_unique($people_ids)));
  $film_people = get_objects_from_sql($sql);
}
foreach ($results['films'] as $id => $data) {
  $secondary = '';
  if ($data['rating']) {
    $secondary .= $data['rating'] . '%';
  } else if ($data['release_date']
             && !is_numeric($data['release_date'])
             && $now > strtotime($data['release_date'])) {
    $secondary .= 'Coming Soon';
  }
  if ($data['stars']) {
    $stars = array();
    foreach (array_slice(explode(',', $data['stars']), 0, STARS_TO_SHOW)
			 as $star_id) {
      $name_array = preg_split("/[\s]+/",
			       $film_people[$star_id]['name']);
      if (isset($name_array[0]) && $name_array[0]) {
	$stars[] = $name_array[0];
      }
    }
    if ($secondary && $stars) {
      $secondary .= ' · ';
    }
    $secondary .= '<span>'. implode(', ', $stars).'</span>';
  }

  $final_films['data'][] =
    array(
	  'primary' => $data['name'].' ('.$data['year'].')',
	  'secondary' => $secondary,
	  'image' => get_profile_pic_src($id, 'film'),
	  'url' => get_film_url($data, false),
	  'fill_text' => $data['name'],
	  'timeout' => 100,
	  );
}

/* Output JSON */
$final = array($final_films, $final_people, $final_songs);
header('Content-type: application/json');
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header ("Pragma: no-cache"); // HTTP/1.0

echo json_encode($final);

function get_profile_pic_src($id, $type, $scale = 40) {
  $id = (int) $id;
  if ($type == 'person') {
    $id .= '_0';
  }
  $src = MEDIA_BASE . $type . '/' . $id . '.jpg';
  return BASE_URL.'phpthumb/phpThumb.php?src='.$src.'&w='.$scale.'&h='.$scale.'&zc=1';
}


function refresh_search_apc() {
  $films = get_objects_from_sql('select id, name, handle, year, rating, stars, release_date from films where deleted is null order by year desc');
  $people = $songs = array();
  //  $people = get_objects_from_sql('select id, name, primary_type from people where deleted is null and tier is not null order by tier asc');
  //  $songs = get_objects_from_sql("select id, name, rating, film_id from songs where deleted is null and youtube_handle > '' order by rating desc");

  $data = array('films' => $films,
                'people' => $people,
                'songs' => $songs);
  $serialized_data = serialize($data);
  apc_store('search_index', $serialized_data, 1000);
  return $serialized_data;
}

function get_objects_from_sql($sql) {
  global $link;

  if (!$sql) {
    return null;
  }
  $r = mysql_query($sql);
  if (!$r) {
    return null;
  }

  $rows = array();
  if (mysql_num_rows($r)>0) {
    while ($row = mysql_fetch_assoc($r)) {
      $rows[$row['id']] = $row;
    }
  }
  return $rows;
}

function render_tag_simple($tag_id) {
  $map = array(91 => 'Model', 79 => 'Superstar', 49 => 'Producer',
               46 => 'Director', 48 => 'Playback Singer', 45 => 'Actress',
               44 => 'Actor');
  return isset($map[$tag_id]) ? $map[$tag_id] : null;
}

?>