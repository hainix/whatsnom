<?php
include_once 'cms_lib.php';

$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$type = isset($_GET['type']) ? $_GET['type'] : null;
$show_all = (bool) idx($_GET, 'all');
global $uid;

set_time_limit(0);
ini_set('memory_limit', '32M');

$title = 'Dishoom | Add New Object';

$html = '<div class="main_layout_table" >';

if (!$type) {
  $page = new cmsPage();
  $page
    ->setContent($html)
    ->setTitle('Dishoom CMS | Add Object')
    ->render();
  exit(1);
}

$fields = idx(get_add_fields_lists(), $type);

if ($_POST) {
  // saving
  $data = array_filter($_POST);
  $html .= '<div align="center"><h2>';
    $fields_changed_names = implode(', ', array_keys($data));
    if ($new_id = add_cms_object($type, $data, $fields)) {
      update_cache_for_object($new_id, $type);
      $html .= 'successfully saved values for new '.$type.' obj with fields:</h2><br/>';
      $html .= print_r($data, true).'<br/>';
      $html .= ' <br/><h2>with object id = '.$new_id.'<br/>'.render_link('edit it now', 'cms/edit.php?id='.$new_id.'&type='.$type);
    } else {
      $html .= 'error saving fields: '.$fields_changed_names;
    }
    $html .= '</h2></div><hr/><br/>';
}

$html .= '<div align="center"><h2>adding a new <span>'.$type.'</span> object</h2><small>fill out as much as you can, then edit the object after creating it</small><br/>';
if ($type == 'person') {
  $html .= 'if adding a distributor or production house, then just set the name (and wiki handle if it exists),<br/> and then tag it with the'
    .' appropriate tag on the edit page, after you create the person<br/><br/>';
} else if ($type == 'tag') {
  $html .= 'for tags, you need to specify what kinds of objects can be tagged with this tag. mark "Yes" for those that apply<br/><br/>';
} else if ($type == 'poll' || $type == 'poll_option') {
  $html .= '<hr/>';
  $sql = "SELECT * FROM poll_questions where deleted is null limit 10";
  $poll_objects = get_objects_from_sql($sql);
  $poll_ids = array_pull($poll_objects, 'id');

  $poll_fields = array('', 'poll id', 'question', 'options', 'created');
  $poll_table = '<table class="striped"><tr><td>'.implode('</td><td>', $poll_fields).'</td></tr>';
  foreach ($poll_objects as $poll) {
    $sql = "SELECT * from poll_options where deleted is null and poll_id =".$poll['id'];
    $poll_option_objects = get_objects_from_sql($sql);
    $poll_edit_link = 'cms/edit.php?type=poll&id='.$poll['id'];
    $row = array();
    $row[] = render_link('edit', $poll_edit_link);
    $row[] = $poll['id'];
    $row[] = render_link($poll['question'], $poll_edit_link);
    $options = array();
    foreach ($poll_option_objects as $option) {
      $options[] = render_link($option['value'], 'cms/edit.php?type=poll_option&id='.$option['id']);
    }
    if ($options) {
      $row[] = '<ul><li>'.implode('</li><li>', $options).'</li></ul>';
    } else {
      $row[] = 'none yet. '.render_link('add some options now!', 'cms/add.php?type=poll_option');
    }
    $row[] = $poll['created_time'];
    $poll_table .= '<tr><td>'.implode('</td><td>', $row).'</td></tr>';
  }
  $poll_table .= '</table>';
  $html .=
    '<h2>Recent <span>Polls</span></h2>'
    .$poll_table;

} else if ($type == 'article') {
  $html .= '<hr/>';

  $sql = "SELECT * FROM articles where deleted is null and publish_time is not null order by publish_time DESC";
  if (!$show_all) {
    $sql .= ' limit 10';
  }

  $articles = get_objects_from_sql($sql);
  $article_fields =
    array(
      '',
      'headline',
      'publish_time',
      'image_handle'
    );
  $show_all_link = $show_all
    ? null
    : render_link(' show all articles', 'cms/add.php?type=article&all=1');
  $article_table = $show_all_link.'<table class="striped"><tr><td>'.implode('</td><td>', $article_fields).'</td></tr>';

  foreach ($articles as $article) {
    $row = array();
    $row[] =
      render_link(
        'edit',
        'cms/edit.php?type=article&id='.$article['id']
      );
    $row[] = '<b>'.$article['headline'].'<b>';
    $row[] = $article['publish_time']
      ? date('m/d/y h:i', $article['publish_time'])
      : '-';
    $row[] = $article['image_handle']
      ? render_link($article['image_handle'],
                   'images/media/'.$article['image_handle'])
      : null;
    $article_table .= '<tr><td>'.implode('</td><td>', $row).'</td></tr>';
  }

  $article_table .= '</table>';
  $html .=
    '<h2>Recent <span>Articles</span></h2>watch out for duplicates!'
    .$article_table
    .'<h2>Suggested <span>Articles</span></h2><br/>'
    .render_buzz_box('bollywood');

} else if ($type == 'slide') {
  $html .= '<hr/>';

  $sql = "SELECT * FROM slides where deleted is null order by id DESC";
  if (!$show_all) {
    $sql .= ' limit 10';
  }

  $slides = get_objects_from_sql($sql, 'slide');
  $slide_fields =
    array(
      '',
      'headline',
      'object_id',
      'object_type',
      'image_id',
      'is_featured',
      'freeform_link',
      'slide_position'
    );
  $show_all_link = $show_all
    ? null
    : render_link(' show all slides', 'cms/add.php?type=slide&all=1');
  $slide_table = '<table class="striped"><tr><td>'.implode('</td><td>', $slide_fields).'</td></tr>';
  foreach ($slides as $slide) {
    $row = array();
    $row[] = render_link('edit', 'cms/edit.php?type=slide&id='.$slide['id']);
    $row[] = '<b>'.$slide['headline'].'<b>';
    $row[] = $slide['object_id'];
    $row[] = $slide['object_type'];
    $row[] = $slide['image_id'];
    $row[] = $slide['is_featured'] ? 'Y' : 'N';
    $row[] = $slide['freeform_link'] ? 'Y' : 'N';
    $row[] = $slide['slide_position'];
    $slide_table .= '<tr><td>'.implode('</td><td>', $row).'</td></tr>';
  }
  $slide_table .= '</table>';
  $html .=
    '<h2>Recent <span>Slides</span></h2><br/>'
    .$show_all_link
    .$slide_table;
}
$html .= '<table><form id="obj_create_form" action="" method="post" action="'.$url.'">';
foreach ($fields as $field_name => $field_type) {
  $html .= '<tr valign="top"><td><b>'.$field_name.'</b> <small>('.$field_type.')</small></td><td>'.
    render_input_type($field_type, $field_name)
    .'</td></tr>';
}
$html .= '<tr><td> </td><td align="right">'
  .'<input type="submit" value="Add '.ucwords(str_replace('_', ' ',$type)).'!" class="button large red" /> </td></tr>';
$html .= '</table></div>';

$html = roundbox($html, 'Dishoom Content Management System');


$page = new cmsPage();
$page
->setContent($html)
->requireModule(array('news'))
->setTitle('Dishoom CMS | Add '.ucwords($type))
->render();

function add_cms_object($type, $data, $field_info) {
  global $link;
  $type = get_table_name($type);
  if (!$type) {
    slog('db update missing type');
    return false;
  }
  $update_fields = array();
  $field_names = array();
  $field_values = array();
  foreach ($data as $field_name => $field_val) {
    $field_names[] = $field_name;
    switch($field_info[$field_name]) {
    case 'int':
      $field_values[] = $data[$field_name];
      break;
    case 'bool':
      $field_values[] = $data[$field_name] ? 1 : 'NULL';
      break;
    default:
      $field_values[] = "'" . sanitize_cms_text($data[$field_name]) . "'";
    }
  }

  $q = "insert ignore into ".$type." (".implode(',', $field_names). ") VALUES (";
  $q .= implode(', ', $field_values) . ")";
  //slog('query = '.$q);
  // $r = true;
  $r = mysql_query($q);
  if (!$r) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $q;
    die($message);
  } else {
    // save edit to revisions db
    global $uid;
    $id = mysql_insert_id();
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
    return $id;
  }
  return null;
}


?>
