<?php
include_once '../lib/core/page.php';
include_once '../lib/utils.php';
include_once 'cms_lib.php';

$secret = isset($_GET['hot'])  ? $_GET['hot'] : null;
if (!$secret || $secret !== 'bipasha') {
  go_404();
}

$type = isset($_GET['type'])  ? $_GET['type'] : null;
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];


$html = '<div class="main_layout_table" >';
$html .= '<table><tr><td><h1>'.render_local_image('logos/dishoom_logo_top.png').' Live Data DUMP - CMS</h1></td>';
$html .= '<td width="400px"> </td>';
$html .= '<td><h1><a href="http://dishoomfilms.com/cms/data.php?type=films&hot=bipasha">show films</a></h1></td>';
$html .= '<td><h1><a href="http://dishoomfilms.com/cms/data.php?type=people&hot=bipasha">show people</a></h1></td>';
$html .= '<td><h1><a href="http://dishoomfilms.com/cms/data.php?type=reviews&hot=bipasha">show reviews</a></h1></td>';
$html .= '<td><h1><a href="http://dishoomfilms.com/cms/data.php?type=songs&hot=bipasha">show songs</a></h1></td>';
$html .= '<td><h1><a href="http://dishoomfilms.com/cms/data.php?type=videos&hot=bipasha">show videos</a></h1></td>';

$html .= '</tr></table>';

if ($type == 'people') {
  $fields = array('id', 'name', 'tier', 'type', 'gender', 'tags', 'bio', 'birthday_string', 'twitter', 'wiki_handle', 'wiki_summary', 'film_count');
  $objects = get_objects_from_sql(
				  sprintf("select ".implode(',', $fields)
		    ." from people where deleted is null and (tier = 'A' or tier = 'B') ORDER BY tier, film_count DESC"));
} else if ($type == 'songs') {
  $fields = array('id', 'name', 'film_id', 'tags', 'playback_singers_text', 'playback_singers', 'youtube_handle',  'lyrics_link', 'stars', 'rating', 'lyricists', 'music_directors_text', 'music_directors', 'source', 'duration', 'player_link', 'manual_pass');
  $objects = get_objects_from_sql(
				  sprintf("select ".implode(',', $fields)
					  ." from songs where deleted is null order by film_id desc"));
  $html .= '<h3>note: manual_pass being 0 means nobody\'s reviewed it yet, and 1 means someone checked "review complete" for this song. also song sources refer to these mappings: 1 = hindilyrix.com, 2 = hindilyrics.net, 3 = hindisongs.net, 4 = raaga, 5 = smashhits</h3>';
}  else if ($type == 'reviews') {
  $fields = array('id', 'reviewer', 'film_id', 'source_name', 'source_link', 'rating', 'excerpt', 'thumbs');
  $objects = get_objects_from_sql(
				  sprintf("select ".implode(',', $fields)
					  ." from reviews order by film_id desc"));
} else if ($type == 'films') {

  $fields = array('id', 'name', 'year', 'tags', 'votes', 'rating', 'trailer',
		  'distributor', 'fullfilm_handle', 'oneliner', 'tier', 'plot',
		  'comments');
  $objects = get_objects_from_sql(
				  sprintf("select ".implode(',', $fields)
					  ." from films where deleted is null ORDER BY votes DESC"));
}  else if ($type == 'videos') {

  $fields = array('id', 'name', 'youtube_handle', 'tags', 'film_id', 'rating', 'stars');
  $objects = get_objects_from_sql(
				  sprintf("select ".implode(',', $fields)
					  ." from videos where deleted is null ORDER BY rating DESC"));
} else {
  $html .= '<h2> yo homie, choose a filter from above to export data. remember, this is costly, and no promises that the whole server won\'t go down if u hammer it too much. so something something spiderman\'s uncle.</h2>';
  echo $html;
  exit(1);
}

$html .= '<table border=1 id="exportableTable"><tr>';

// Special parsing of tags
$mapping = null;
if (in_array('tags', $fields)) {
  $fields[] = 'rendered_tags';
  $mapping = get_tags(get_table_name($type));
}
foreach ($fields as $field) {
  $html .= '<td><h2>'.$field.'</h2></td>';
}
$html .= '</tr>';
foreach ($objects as $object) {
  $html .= '<tr>';
  if ($mapping) {
    $object['rendered_tags'] = convert_ids_to_tags($object['tags'], $mapping, true);
  }
  foreach ($fields as $field) {
    $html.= '<td>'.idx($object, $field).'</td>';
  }
  $html .= '</tr>';
}


$html .= '</div>';
$html .= "<input value=\"Export as CSV, nbd.\" type=\"button\" onclick=\"$('#exportableTable').table2CSV()\"><br/><small>if you use this, copy the resulting text to a text document, save it as data.csv and double click on the resulting file (open in excel), or just import straight to googledocs. boom.</small><br/><br/>";

$head = '<head>
<script type="text/javascript" src="http://jqueryjs.googlecode.com/files/jquery-1.3.1.min.js" > </script>
<script type="text/javascript" src="http://www.kunalbabre.com/projects/table2CSV.js" > </script>

<title>Dishoom Data Dump CMS</title>
</head>';

$page = new cmsPage();
$page->addHeadContent($head);
$page->setContent($html);
$page->render();

?>
