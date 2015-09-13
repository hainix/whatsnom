<?php
include_once '../lib/core/page.php';
include_once '../lib/utils.php';
include_once 'cms_lib.php';

$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$fields = array('id', 'name', 'youtube_handle', 'film_id', 'stars', 'rating', 'featured', 'published_date', 'related_videos');
$objects = get_objects_from_sql(
				sprintf("select ".implode(',', $fields)
		    ." from videos where deleted is null ORDER BY id DESC"));
$html = '<div align="center"><h2> here\'s a list of all '
  .count($objects).' videos'
  .'</h2></div><hr/>';
$html .= '<table border=1 width="900px" id="exportableTable"><tr>';
$html .= '<td><b>actions</b></td>';
foreach ($fields as $field) {
  $html .= '<td><b>'.$field.'</b></td>';
}
$html .= '</tr>';
$previous_film_id = null;
foreach ($objects as $object) {
  $html .= '<tr>';
  $check_fields = array();
  $check_fields[] =
    render_link('edit',
                'cms/edit.php?id=' . $object['id'] . '&type=video');

  $film_id = $object['film_id'];
  if ($film_id) {
    $object['film_id'] = render_film_link(array('id' => $film_id), $film_id);
  }
  if ($object['stars']) {
    $object['stars'] = count(explode(',', $object['stars']));
  }

  $handle =   $object['youtube_handle'];
  if ($handle) {
    $object['youtube_handle'] =
      render_video_icon(array('youtube_handle' => $handle))
      .$handle;
  }
  foreach ($fields as $field) {
    $check_fields[] = $object[$field];
  }

  foreach ($check_fields as $field) {
    $html.= '<td>'.$field.'</td>';
  }
  $html .= '</tr>';
}
$html .= '</table>';

$page = new cmsPage();
$page->setContent($html);
$page->render();

?>
