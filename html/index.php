<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';

$list_id = idx($_GET, 'l');
$primary_list = null;
if ($list_id) {
  $primary_list = get_object($list_id, 'lists');
}
if ($primary_list) {
  $type_id = $primary_list['type'];
  $city_id = $primary_list['city'];
} else {
  $type_id = idx($_GET, 't', null);
  $city_id = idx($_GET, 'c', Cities::SF);
}

// Data Fetch START
$type_name = ListTypes::getName($type_id);
$city_name = $city_id ? Cities::getName($city_id) : null;
$query = new ListQuery($type_id, $city_id);

$same_lists = DataReadUtils::getListsForQuery($query, 5);
$city_lists = DataReadUtils::getTopListsForCity($city_id, 5);
$recent_city_lists = DataReadUtils::getRecentListsForCity($city_id, 5);
$primary_list = $primary_list ?: head((array) $same_lists);

$entries =
  $primary_list
  ? DataReadUtils::getEntriesForList($primary_list)
  : null;
$spots =
  $entries
  ? get_objects(array_pull($entries, 'spot_id'), 'spots')
  : null;
$user = FacebookUtils::getUser();

// Data Fetch END

$heart_icon =  '<img class="list-profile" src="'.BASE_URL.'/images/add-favorite.png" />';
$edit_icon =  '<img width="26px" height="26px" '
  .'src="'.BASE_URL.'/images/pencil.png" />';

$my_list_edit = null;


// Primary List START
$big_add_list_message = null;
if (!$primary_list || !$entries || !$spots) {
  $yelp_list_render = '';
  if (is_admin()) {
    $yelp_list_render .= '<h3 align="center">FALLBACK QUERY</h3><br/><hr/>';
  }
  $yelp_list_render .= '<ul class="list">';
  $spots = DataReadUtils::getGenericSpotsForQuery($query);
  $position = 1;
  foreach ($spots as $spot) {
    $fake_entry = array('position' => $position);
    $yelp_list_render .=
      '<li>'.Modules::listItem($fake_entry, $spot).'</li>';
    $position++;
  }
  $yelp_list_render .= '</ul>';
  $list_render = $yelp_list_render;

} else {
  if ($user && $primary_list['creator_id'] == $user['id']) {
    $my_list_edit =
      '<div style="margin-bottom: 30px;">'
      .RenderUtils::renderMessage(
        RenderUtils::renderLink(
          $edit_icon.' Edit Your List',
          'add/?l='.$primary_list['id']
        )
      )
      .'</div>';
  }
  $list_render = $my_list_edit . '<ul class="list">';
  foreach ($entries as $entry) {
    $list_render .=
      '<li>'.Modules::listItem($entry, $spots[$entry['spot_id']]).'</li>';
  }
  $list_render .= '</ul>';
}

/*
if ($user) {
  $add_url = 'add/?c='.$city_id.'&t='.$type_id;
  $add_link = RenderUtils::renderLink(
    'Add Your Own List',
    $add_url
  );
} else {
  $add_link = RenderUtils::renderExternalLink(
    'Log In and Add Your Own List',
    FacebookUtils::getLoginURL()
  );
}
$list_render .=
  RenderUtils::renderMessage(
    $add_link,
    'add-favorite.png',
    $header = true
  );
*/

// Primary List END

// City Lists START
$city_lists_render =
'<h3><span>Popular </span>'.$city_name.' Lists</h3>'
  .Modules::renderProfileList($city_lists);
// City Lists END

// Recent City Lists START
$recent_city_lists_render =
'<h3><span>New </span>'.$city_name.' Lists</h3>'
  .Modules::renderProfileList($recent_city_lists);
// Recent City Lists END

// Same Lists START
$add_text = $my_list_edit ? 'Edit Your List' : 'Add Your Own List';
if ($user) {
  $add_link_profile_item = RenderUtils::renderLink(
    $heart_icon
    .$add_text.'<span class="user-meta">Share The Nom!</span>',
    'add/?c='.$city_id.'&t='.$type_id
  );
} else {
  $add_link_profile_item = RenderUtils::renderExternalLink(
    $heart_icon
    .$add_text.'<span class="user-meta">Log In To Share!</span>',
    FacebookUtils::getLoginURL()
  );
}
$same_lists_render =
  '<h3><span>Top </span>'.$city_name
  .' '.$type_name
  .' Lists</h3>'
  .Modules::renderProfileList($same_lists, $show_city = false, $primary_list['id'], array($add_link_profile_item));
// Same Lists END


$yelp_attribution =
  '<div style="margin-left: 30px;" >
  <table class="attribution-container">
  <tr>
  <td class="attribution-text">powered by</td>
  <td><img src="images/from-yelp.png" /></td>
  </tr>
  </table>'
  .'</div>';

$about_us =
  '<h3 style="margin-top: 0;"><span>What Is This?</span></h3>'
  .'<div class="about-us-container">Here, you\'ll find curated lists by '
  .'local experts.'
    .'<div align="left" style="margin: 10px 0 0 0; width: 100%;">'
      .FacebookUtils::render_share_box()
    .'</div>'
  .'</div>';


$query->setCount(count($spots));
$content =
'<div class="twelve columns">'
.$list_render
.'</div>
		<div class="four columns sidebar">'
  .$yelp_attribution
    .$about_us
    .$same_lists_render
    .$city_lists_render
    .$recent_city_lists_render
    .RenderUtils::renderContactForm()
		.'</div>
	</div><!-- container -->
';

$page = new Page();
$page
  ->setType(PageTypes::BROWSE)
  ->setQuery($query)
  ->setContent($content)
  ->render();
