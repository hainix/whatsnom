<?php
include_once '../lib/core/page.php';
include_once '../lib/utils.php';
include_once 'cms_lib.php';

if (!is_admin()) {
  go_404();
}

$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$fields = array('id', 'film_id', 'quote');
$objects = get_objects_from_sql(
				sprintf("select ".implode(',', $fields)
					." from quotes ORDER BY film_id asc"));
$html = '<div align="center"><h2> here\'s the list of all quotes, sorted by film id</h2></div>';
$html .= '<table border=1 width="900px" id="exportableTable"><tr>';
$html .= '<td><h2>actions</h2></td>';
foreach ($fields as $field) {
  $html .= '<td><h2>'.$field.'</h2></td>';
}
$html .= '</tr>';
foreach ($objects as $object) {
  $html .= '<tr>';
  $check_fields = array();
  $check_fields[] = render_link('edit', 'cms/edit.php?id=' . $object['id'] . '&type=quote');
  $check_fields[] = $object['id'];
  $check_fields[] = render_film_link(array('id' => $object['film_id']),
                                     $object['film_id']);
  $check_fields[] = nl2br(idx($object, 'quote', '-'));
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
