<?php
include_once '../lib/core/page.php';
include_once '../lib/utils.php';
include_once 'cms_lib.php';

if (!is_admin()) {
  go_404();
}

$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$show_all = idx($_GET, 'all');

$fields = array('id', 'name', 'tier', 'oneliner', 'rating', 'twitter', 'num_photos');

if ($show_all) {
  $sql =
    sprintf("select ".implode(',', $fields)
            ." from people where deleted is null ORDER BY tier ASC, rating DESC");
  $desc = 'all people, sorted by tier, then name';
} else {
  $sql =
    sprintf("select ".implode(',', $fields)
            ." from people where deleted is null and tier is not null ORDER BY tier ASC, rating DESC");
  $desc = 'tiered people, sorted by tier, then oneliner, then name';
}
$objects = get_objects_from_sql($sql);


$all_link = $show_all
  ? render_link('show tiered people', 'cms/people.php?all=0')
  : render_link('show all people', 'cms/people.php?all=1');

$html = '<div align="center"><h2>'.$desc.'</h2>'.$all_link.'</div>';
$html .= '<table border=1 width="900px" id="exportableTable"><tr>';
$html .= '<td><h4>actions</h4></td>';
foreach ($fields as $field) {
  $html .= '<td><h4>'.$field.'</h4></td>';
}
$html .= '</tr>';
foreach ($objects as $object) {
  $html .= '<tr>';
  $check_fields = array();
  $check_fields[] = render_link('edit', 'cms/edit.php?id=' . $object['id'] . '&type=person');
  $check_fields[] = $object['id'];
  $check_fields[] = render_person_link($object, $object['name']);
  $check_fields[] = idx($object, 'tier', '-');
  $check_fields[] = '<small>'.render_mentions_text_no_hovercard(idx($object, 'oneliner', '-')).'</small>';
  $check_fields[] = idx($object, 'rating', '-');
  $check_fields[] = idx($object, 'twitter', '-');
  $check_fields[] = idx($object, 'num_photos', '-');

  // hiding these for simplicity
  //$check_fields[] = idx($object, 'actor_type', '-');
  //$check_fields[] = idx($object, 'tags', '-');
  //$check_fields[] = idx($object, 'wiki_handle', '-');


  foreach ($check_fields as $field) {
    $html.= '<td>'.$field.'</td>';
  }
  $html .= '</tr>';
}
$html .= '</table>';

$page = new cmsPage('cms');
$page->setContent($html);
$page->render();

?>
