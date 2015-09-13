<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/display/film/film_summary.php';

function render_hovercard_body($object, $show_name = null, $mentions_text = true) {

$subtitle_parts = array();
if ($show_name) {
  $subtitle_parts[] = $object['name'];
}
if ($object['rating']) {
  $subtitle_parts[] = '<b>'.$object['rating'].'%</b>';
}
if (idx($object, 'primary_type')) {
  $subtitle_parts[] = render_tag($object['primary_type']);
}

if (idx($object, 'year')) {

  $subtitle_parts[] = !film_is_released($object)
    ? idx($object, 'release_date', $object['year'])
    : $object['year'];
}

$footer_parts = array();
$stars_render = render_stars_for_object($object);
if ($stars_render) {
  $footer_parts[] = '<b>Starring</b>: '.$stars_render;
}

$hover_html = null;
if ($subtitle_parts) {
  $hover_html .=
    '<p class=\'hc-subtitle\'>'.implode(' · ', $subtitle_parts).'</p>';
}




if (idx($object, 'oneliner')) {
  if ($mentions_text) {
    $oneliner = render_mentions_text($object['oneliner']);
  } else {
    $oneliner = strip_tags(render_mentions_text_no_hovercard($object['oneliner']));
  }
  $hover_html .=
    '<p class=\'hc-oneliner\'>'.$oneliner.'</p>';
}


if ($footer_parts) {
  $hover_html .=
    '<p class=\'hc-subtitle\'>'.implode('<br/>', $footer_parts).'</p>';
}
return $hover_html;
  }

?>