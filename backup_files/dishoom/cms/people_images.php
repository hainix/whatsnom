1;2c<?php
include_once '../lib/core/page.php';
include_once '../lib/utils.php';

global $uid;

$tier = idx($_GET, 'tier');
$worker = isset($_GET['worker'])  ? $_GET['worker'] : $uid;
$workers = array(7946279 => array('name' => 'farraz'), 7931125 => array('name' => 'arun'), 7906796 => array('name' => 'nix'));
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$start = isset($_GET['s']) ? $_GET['s'] : 0; // where to start
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// for now, we mark hollywood people as to_delete = 1 and people with no
// manual tier override && no wiki link && less than 5 films as to_delet = 3
/*
mysql> update people set to_delete = 3 where tier = 'C' and film_count < 5 and film_count is not null and wiki_handle = '';
*/

define('WORKABLE_CHUNK_SIZE', 1);
set_time_limit(0);
ini_set('memory_limit', '32M');
if ($id) {
  $person = get_object($id, 'people', array('id', 'name', 'tier'));
} else if ($tier) {
  $person = head(get_objects_from_sql(
    sprintf("select id,name,tier from people where tier='%s' LIMIT %d, %d",
	    $tier, $start, 1)));
} else if ($worker) {
  $person = head(get_objects_from_sql(
    sprintf("select id,name,tier from people where worker=%d LIMIT %d, %d",
	    $worker, $start, 1)));
  } else {
  slog('specify a worker, dummy');
}
$images = null;
if ($person) {
  $images =
    get_objects_from_sql(
      sprintf("select * from images where subject_id = %d",
	      $person['id']));
}

$next_s = (int) $start + 1;
$prev_s = (int) $start -1;

$html = '<div class="main_layout_table" >';
$html .= '<table><tr><td><h4>'.render_image('logos/dishoom_logo_top.png').' Person CMS</h4></td>';
$html .= '</tr></table>';

if (!$id) {
  if ($prev_s >= 0) {
    $html .= '<div style="float: left; font-size: 30px;"><a href="http://dishoomfilms.com/cms/person.php?s=' .$prev_s;
    $html .= ($tier) ? '&tier='.$tier : '&worker='.$worker;
    $html .= '">previous</a></div>';
  }
  $html .= '<div style="float: right; font-size: 30px;"><a href="http://dishoomfilms.com/cms/person.php?s=' .$next_s;
  $html .= ($tier) ? '&tier='.$tier : '&worker='.$worker;
  $html .= '">next</a></div></td></tr>';
}
/*
$html .= '<table ><tr><td>filters: </td>';

foreach ($workers as $worker_id => $worker_arr) {
  $html .= '<td><h2>'.render_link($worker_arr['name'], '?worker='.$worker_id).'</h2></td>';
}
  $html .= '<td><h2>'.render_link('A tier', '?tier=A').'</h2> (~111 total)</td>';
$html .= '<td><h2>'.render_link('B tier', '?tier=B').'</h2> (~312 total)</td>';
$html .= '<td>'.render_link('change tiers', 'https://docs.google.com/spreadsheet/ccc?key=0AsYSn1BAtiGwdG0yc25TQlFZLXRwMUhncHZYX1V0Smc&hl=en_US#gid=0', false).'</td>';


$html .= '</tr></table>';
*/
$person_link = render_link($person['name'],
			   BASE_URL.'person.php?id='.$person['id'], false);
  $delete_link =
     '<small><form id="image_form_'.$person['id'].'" action="" method="post"><input type="hidden" name="id" value="'
    .$person['id'].'"/>'
    .'<div id="person_delete_div_'.$person['id'].'" class="rfloat">'
    .'<a href="#" class="delete_person" proj="'.$person['id'].'">delete person forever</a></div></form></small>';

if (!$images) {
  $html .= 'no images found for '.$person_link . '( tier '.idx($person, 'tier', '?').' ) ';
} else {
  // slice images due to tiers
  if ($person['tier'] == 'C') {
    $images = array_slice($images, 0, C_TIER_IMAGES_TO_SHOW + 10);
  }

  $html .= '<h1>'.render_profile_pic($id, 'person', 100).'Showing fields for of '.$person_link.' to vet, owned by '.
    $workers[$worker]['name'] . '</h1> (tier '.$person['tier'].') ';

	$html .= '<table border="5" cellpadding="8"><tr>';
	$i = 0;
foreach ($images as $image_id => $image) {
  if ($i % 4 == 0) {
    $html .= '</tr><tr>';
  }
  $i++;
  //  $thumb = $image['thumb'];
  $thumb = BASE_URL.'/lib/phpthmb/phpThumb.php?src='.$image['src'].'&w=' . 200;

  $html .= '<td>'.render_link(render_image($thumb, false), $image['src'], false, array('class' => 'fancy', 'rel' => 'gallery')).'<br/>'
    .$image['title'];
  if ($image['to_delete'] == 1) {
    $html .= '<br/><span style="color: red;">[deleted]</span>';
  } else  if ($image['to_delete'] == 2) {
    $html .= '<br/><span style="color: red;">[too small]</span>';
  } else {
$html .=
    '<br/><div style="float: right;"><form id="image_form_'.$image['id'].'" action="" method="post"><input type="hidden" name="id" value="'.$image['id'].'"/>'
    .'<div id="name_project_submit_div_'.$image['id'].'" class="rfloat">'
  .'<a href="#" class="submit_comment" proj="'.$image['id'].'">delete</a></div></form></div>';

  $html .=
    '<div style="float: left;">';
  if ($image['is_profile']) {
    $html .= '<span style="color: green;"> starred</span>';
  } else {
  $html .= '<form id="image_profile_form_'.$image['id'].'" action="" method="post"><input type="hidden" name="id" value="'
    .$image['id'].'"/>'
    .'<div id="profile_pic_promote_div_'.$image['id'].'" class="rfloat">'
    .'<a href="#" class="profile_pic_upgrade" proj="'.$image['id'].'">star for profile pic</a></div></form>';
$html .= '</div>';
  }
  }
$html .= '</td>';
  }
}
$html .= '</tr></table>';


$html .= '</div>';


$head = '
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" src="'.BASE_URL.'js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>'
."<script type='text/javascript'>
    $(document).ready(function() {
      $('a.fancy').attr('rel', 'gallery').fancybox();
    });
</script>"
.'  <script src="jquery-ImageAuditsubmit.js" type="text/javascript" charset="utf-8"></script>
<title>Dishoom Person CMS</title>
</head>';

echo $head;
echo $html;


?>
