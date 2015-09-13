<?php
include_once '../lib/core/page.php';
include_once '../lib/utils.php';
include_once 'cms_lib.php';

$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$fields = array('id', 'title', 'youtube_handle', 'film_id', 'price', 'processed');
$objects = get_objects_from_sql(
				sprintf("select ".implode(',', $fields)
		    ." from youtube_films where deleted is null ORDER BY processed DESC, film_id ASC, id DESC"));
$html = '<div align="center"><h6> here\'s a list of all youtube films, ordered first by'
  .' if i\'ve processed them, then by if they have associated film_ids, and then by recency (most recent on top) '
  .'<br/>if you see a missing film id, add it! make sure to verify the video + film year. If you see a film that '
  .'is marked as "processed" but has an error, then edit it on the film\'s cms page, under the fullfilm_handle field.'
  .'</h6></div><hr/>';
$html .= '<table border=1 width="900px" id="exportableTable"><tr>';
$html .= '<td><h2>actions</h2></td>';
foreach ($fields as $field) {
  $html .= '<td><h2>'.$field.'</h2></td>';
}
$html .= '</tr>';
$previous_film_id = null;
foreach ($objects as $object) {
  $html .= '<tr>';
  $check_fields = array();
  if ($object['processed']) {
    $check_fields[] = 'processed<br/>'
      .render_link('edit film',
                   'cms/edit.php?id=' . $object['film_id'] . '&type=film');
  } else {
    $check_fields[] =
    render_link('edit',
                'cms/edit.php?id=' . $object['id'] . '&type=youtube_film');
  }


  $film_id = $object['film_id'];
  if ($film_id) {
    $film_id_text = $film_id;
    if ($film_id == $previous_film_id) {
      $film_id_text = '<h4>dup? '.$film_id.'</h4>';
    } else {
      $previous_film_id = $film_id;
    }

    $object['film_id'] = render_film_link(array('id' => $film_id), $film_id_text);
  }

  $handle =   $object['youtube_handle'];
  if ($handle) {
    $object['youtube_handle'] =
      render_external_link($handle,
                           get_youtube_embed_src($handle));
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
