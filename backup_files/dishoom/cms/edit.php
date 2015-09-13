<?php
include_once 'cms_lib.php';

$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$type = idx($_GET, 'type', idx($_GET, 't'));
global $uid;

set_time_limit(0);
ini_set('memory_limit', '32M');

if (!$id || !$type) {
  slog('invalid object id or type');
  exit(1);
}
// Force to object name
$type = get_object_name($type);

$obj = head(get_objects_from_db($id, $type));
$untaggable_types = get_untaggable_types();
$possible_tags = (!in_array($type, $untaggable_types))
  ? get_tags_from_db($type)
  : array();

// Simplify the tags
$simple_tags = array();
foreach ($possible_tags as $possible_tag) {
  if (isset($possible_tag['id'])) {
    $simple_tags[$possible_tag['id']] = $possible_tag['name'];
  }
}
$possible_tags = $simple_tags;

$people = process_people_tags(get_objects_from_sql('select id, name from people where deleted is null'));
if ($type == 'film') {
  $distributors = process_people_tags(
    get_objects_from_sql(
      "select id, name from people where tags like '%54%' and deleted is null"));
  $producers = process_people_tags(
    get_objects_from_sql(
      "select id, name from people where (tags like '%53%' or tags like '%49%') and deleted is null"));
  $directors = process_people_tags(
    get_objects_from_sql(
      "select id, name from people where tags like '%46%' and deleted is null"));
} else if ($type == 'video') {
  $source_shows = process_people_tags(
  get_objects_from_sql(
    "select id, name from people where tags like '%134%' and deleted is null"));
}
if ($type == 'film' || $type == 'song') {
  $music_directors = process_people_tags(
    get_objects_from_sql(
      "select id, name from people where tags like '%47%' and deleted is null"));
  $playback_singers = process_people_tags(
    get_objects_from_sql(
      "select id, name from people where tags like '%48%' and deleted is null"));
}

$films = process_film_tags(get_objects_from_sql('select id, name, year from films where deleted is null'));

if (!$obj) {
  slog('no object found');
  exit(1);
}
$fields = idx(get_fields_lists(), $type);

$title = 'Dishoom | Edit';

$obj['type'] = $type;
$html = '<div class="main_layout_table" >'
  .'<br/><div align="center"><h3>Currently editing '
  .' '.render_object_link($obj).' ('.ucwords($type).')'
  .'</h3><br/><hr/>'
  .'</div>';


if ($_POST && idx($_POST, 'id')) {
  // saving;
  $errors = array();
  $changes = array();
  $types_that_need_conversion =
    array('tags',
          'peoplelist',
          'filmlist',
          'directorlist',
          'musicdirectorlist',
          'playbacksingerlist',
          'sourceshowlist',
          'producerlist',
          'distributorlist',
    );
  foreach ($_POST as $saved_name => $saved_val) {
    if (in_array($fields[$saved_name], $types_that_need_conversion)) {
      switch ($fields[$saved_name]) {
      case 'tags':
        $taglist = $possible_tags;
        break;
      case 'peoplelist':
      case 'directorlist':
      case 'musicdirectorlist':
      case 'playbacksingerlist':
      case 'sourceshowlist':
      case 'producerlist':
      case 'distributorlist':
        $taglist = $people;
        break;
      case 'filmlist':
        $taglist = $films;
        break;
      }
      if ($saved_val) {
        $saved_val = convert_tags_to_ids($saved_val, $taglist);
      }
    }
    if (cln($obj[$saved_name]) != cln($saved_val)) {
      if ((int)$obj[$saved_name] ==
          (int) $saved_val && $saved_name == 'deleted') {
        continue;
      }
      if ($fields[$saved_name] == 'date') {
        $saved_val = strtotime($saved_val);
      }
      if ($fields[$saved_name] == 'youtube'
          && stripos($saved_val, '&') !== false) {
        $saved_val = head(explode('&', $saved_val));
        $errors[] =
          'fool, i told you not to put &\'s in your youtube handles! '
          .' i overrode your lame entry and set it to '.$saved_val;
      }
      $wc_restraints = get_field_word_count_constraints($saved_name);
      if ($wc_restraints) {
        list($wc_min, $wc_max) = $wc_restraints;
        $word_count = str_word_count($saved_val);
        if ($word_count < $wc_min) {
          $errors[] = 'field '.$saved_name.' is only '
            .$word_count.' words long - the min is '.$wc_min.' lazy!';
          continue;
        } else if ($word_count > $wc_max + 10) {
          $errors[] = 'field '.$saved_name.' is '
            .$word_count.' words - the max is '.$wc_max.'. relax!';
          continue;
        }
      }

      $changes[$saved_name] = cln($saved_val);
    }
  }
  $html .= '<div align="center"><h2>';

  if (!$changes) {
    $errors[] = ' no fields changed, so nothing was saved';
  } else {
    $fields_changed_names = implode(', ', array_keys($changes));
    if (update_cms_object($id, $type, $changes, $fields)) {
      $html .= ' successfully saved values for updated fields: '.$fields_changed_names;
    } else {
      $errors[] = ' error saving fields: '.$fields_changed_names;
    }
  }
  if ($errors) {
    $html .= '<div class="error">'.implode('<br/>', $errors).'</div>';
  }

  $html .= '</h2></div><br/>';
  update_cache_for_object($id, $type);
  $obj = head(get_objects_from_db($id, $type));
}

$poster = null;
if ($type == 'film' || $type == 'person') {
  $poster =  render_profile_pic_square($obj, $type, 250);
}
$html .= '<table><form id="name_project_form_'.$id.'" action="" method="post" action="'.$url.'">'
  .'<input type="hidden" name="id" value="'.$id.'"/>';
if ($poster) {
  $html .= '<tr><td><b>poster</b></td><td>'.$poster.'</td></tr>';
}
foreach ($fields as $field_name => $field_type) {
  $html .= '<tr valign="top"><td><b>'.$field_name.'</b> <small>('.$field_type.')</small></td><td>';

  // Special case finalized images
  if ($field_name == 'image_handle' && idx($obj, 'image_finalized')) {
    $field_type = 'readonly';
  }
  $html .= render_input_type($field_type, $field_name, idx($obj, $field_name));
  $html .= '</td></tr>';
}
$html .= '<tr><td> </td><td align="right">'.
  '<input type="submit" value="Save '.ucwords(str_replace('_', ' ',$type)).'" class="large red button"/> </td></tr>';
$html .= '</table>';


//<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>

$head = '<link rel="stylesheet" href="'.BASE_URL.'css/tagit.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/blitzer/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
<script src="'.BASE_URL.'js/jquery.tagsuggest.js" type="text/javascript" charset="utf-8"></script>
<script src="'.BASE_URL.'js/jquery.wordcount.js" type="text/javascript" charset="utf-8"></script>
<script src="'.BASE_URL.'cms/datetimepicker.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="'.BASE_URL.'cms/datetimepicker.css" />

<style>
div.wordCount { font-size: 30px; float: right;}
textarea {
border: 1px solid #ddd;
float: left;
height: 120px;
margin-right: 5px;
overflow-y: auto;
padding: 5px;
width: 500px;
}
.error { color: #f00; }

</style>
';

$page = new cmsPage();
$page->setContent($html);
$page->setTitle('Dishoom CMS');
$page->addHeadContent($head);
$page->render();


function sanitize_object_handle($handle) {
    return
      substr(
        strtolower(
          preg_replace("/ /",
                       "-",
                       preg_replace("/[^a-zA-Z0-9 ]+/",
                                    "",
                                    $handle))), 0, 100);
}

function update_cms_object($id, $type, $data, $field_info) {
  global $link;
  $type = get_table_name($type);
  if (!$id || !$type) {
    slog('db update missing id or type');
    return false;
  }

  // Add a handle
  if ($type == 'articles') {
    $article = get_object($id, 'articles');
    $unpublished =  $article['publish_time'] && time() < $article['publish_time'];
    if ((isset($data['headline']) && $unpublished)
        || (!$article['handle'] && (idx($data, 'headline') || $article['headline']))) {
      $data['handle'] = sanitize_object_handle(
        idx($data, 'headline', $article['headline'])
      );
    }
  } else if ($type == 'films') {
    $film = get_object($id, 'films');
    if (!$film['film_handle']) {
      $data['handle'] = sanitize_object_handle($film['name'].'-'.$film['year']);
    }
  }

  $q = "UPDATE ".$type." SET ";
  $update_fields = array();
  foreach ($data as $field_name => $field_val) {

    switch($field_info[$field_name]) {
    case 'int':
      $update_fields[] = $data[$field_name] ?  $field_name . " = " . $data[$field_name] : $field_name . ' = NULL';
      break;
    case 'bool':
      $update_fields[] = $data[$field_name] ? $field_name . " = 1" : $field_name . ' = NULL';
      break;
    default:
      $update_fields[] =  $field_name . " = '"
        . sanitize_cms_text($data[$field_name]) . "'";
    }
  }
  $q .= implode(', ', $update_fields) ." where id = ". $id . " LIMIT 1";
  //slog('query = '.$q);
  $r = mysql_query($q);
  if (!$r) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $q;
    die($message);
  } else {
    // save edit to revisions db
    global $uid;
    $q2 = sprintf("INSERT INTO revisions (changes, actor, target, type) VALUES ('%s', %d, %d, '%s')",
		  implode(',', array_keys($data)),
		  $uid,
		  $id,
		  $type);
    $r = mysql_query($q2);
    if (!$r) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $q2;
      slog($message);
    }
  }
  return $r;
}


?>
