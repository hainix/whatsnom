<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';

// this file updates all films for a certain field
set_time_limit(0);
ini_set('memory_limit', '60M');
global $link;

$vars = parse_args($argv);
hlog($vars);

$start = idx($vars, 's', 0);

$i = $start;
$exits = 0;
do {
  $objects = get_objects_from_sql(
				  sprintf("select id,badges, tags, category, editors_pick, good_music from films limit %d, %d",
					  $i++, 1));

  if (!$objects) {
    $exits++;
    if ($exits > 10) {
      hlog('[[script complete]]');
      exit(1);
    } else {
      continue;
    }
  }
  if (!$objects) {
    hlog('no objects, moving on');
    continue;
  }
  $object = head($objects);
  $id = $object['id'];


  $tags = $object['tags'];
  // skip this if you want to rewrite old tags
  if ($tags) {
    $tags = explode(',', $tags);
  } else {
    $tags = array();
  }
  $tags = array_flip($tags);

  $badge_mapping =
    array(13 => 'classic', 14 => 'drama', 15 => 'just do it drama', 16 => 'family drama', 17 => 'political drama', 18 => 'period piece/historical drama', 19 => 'relationship trouble', 20 => 'soul searching drama', 21 => 'satire-black comedy', 22 => 'situational comedy', 23 => 'observational comedy', 24 => 'man on a mission', 25 => 'battle action', 26 => 'superhero', 27 => 'stylized action', 28 => 'underworld', 29 => 'police procedurals', 30 => 'star crossed lovers', 31 => 'love triangle', 32 => 'falling in love', 33 => 'thriller', 34 => 'horror', 35 => 'supernatural', 36 => 'masala', 37 => 'commercial entertain', 38 => 'mainstream middle ci', 39 => 'parallel cinema', 40 => 'great music', 41 => 'editors pick');
  $badge_mapping = array_flip($badge_mapping);
  $badge_mapping['historical drama'] = 18;
  $badge_mapping['satire'] = 21;
  $badge_mapping[strtolower('Police Procedural')] = 29;
  $badge_mapping[strtolower('Political/Social Drama')] = 17;
  $badge_mapping['triangle'] = 31;
  $badge_mapping[strtolower('Star-Crossed')] = 30;
  $badge_mapping[strtolower('Star-Crossed Lovers')] = 30;
  $badge_mapping[strtolower('Goondas & Naughty Boys')] = 28;
  $badge_mapping[strtolower('Courtship Romance')] = 32;
  if ($object['editors_pick']) {
    $tags[41] = 1;
  }
  if ($object['good_music']) {
    $tags[40] = 1;
  }


  $badges = $object['badges'];
  if ($badges) {
    hlog('mapping existing badges '.$badges);
    foreach (array_filter(explode(',', $badges)) as $badge) {
      $badge = trim($badge);
      if (!$badge || $badge == '' || !trim($badge)) {
	hlog('empty badge, skipping');
	continue;
      }
      hlog('trying to match badge '.$badge);
      $new_badge_id = idx($badge_mapping, strtolower($badge));
      if ($new_badge_id) {
	hlog('mapping to new badge id '.$new_badge_id);
	$tags[$new_badge_id] = 1;
      } else {
	hlog('[[[[[[[ERRRR]]]]]]] unknown badge '.$badge.' for film id '.$id);
	exit(1);
	continue;
      }
    }
  }

  if ($object['category']) {
    switch($object['category']) {
    case 1: $tags[37] = 1; break;
    case 2: $tags[38] = 1; break;
    case 3: $tags[39] = 1; break;
    case 4: $tags[51] = 1; break;
    }
  }
  $tags = array_keys($tags);

  hlog('['.$i.'] starting update for id '.$id);

  if (!is_array($tags)) {
    hlog('something wrong with tag format');
    hlog($tags);
    continue;
  }

  if (!$tags) {
    hlog('no tags for id '.$id.', maybe check it out?');
    continue;
  }

  $sql = sprintf("update films set tags = '%s' where id = %d LIMIT 1",
		 implode(',',$tags),
		 $id);
  hlog($sql);
  $result = mysql_query($sql);
  //$result = true;
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog('[err]--'.$message);
  } else {
    hlog('--- saved to db for id '.$id.' with new fields');
    hlog($tags);
    hlog('old badges');
    hlog($object['badges']);
  }
  unset($result);
} while (1);


?>