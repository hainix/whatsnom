<?php
include_once 'cms_lib.php';

$type = isset($_GET['type']) ? $_GET['type'] : null;
$supported_types = array('film', 'person', 'article', 'song', 'image', 'video');
$ret = '';
if (!$type || !in_array($type, $supported_types)) {
  $ret .= '<h2>no type specified, showing all</h2>';
  $tags = get_tags_from_db(null);
} else {
  $ret .= '<h2>list of all current tags for type '.$type.'</h2>';
  $tags = get_tags_from_db($type);
}

$ret .= '<table class="striped"><tr><td>id</id><td>name</td></tr>';
foreach ($tags as $tag) {
  $id = $tag['id'];
  $ret .= '<tr><td>'.$id.'</td><td>'
    .render_link($tag['name'], 'cms/edit.php?type=tag&id='.$id).'</td></tr>';
}
$ret .= '</table>';

$page = new cmsPage();
$page->setContent($ret);
$page->render();


?>