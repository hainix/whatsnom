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
  $type_id = idx($_GET, 't', ListTypes::BOTTOMLESS_BRUNCH);
  $city_id = idx($_GET, 'c', Cities::NYC);
}

// Data Fetch START
$type_name = ListTypes::getName($type_id);
$city_name = $city_id ? Cities::getName($city_id) : null;
$query = new ListQuery($type_id, $city_id);

$same_lists = DataReadUtils::getListsForQuery($query, 5);
$city_lists = DataReadUtils::getTopListsForCity($city_id, 50);
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
$star_icon =  '<img class="list-profile" src="'.BASE_URL.'/images/star.png" />';
$edit_icon =  '<img width="26px" height="26px" '
  .'src="'.BASE_URL.'/images/pencil.png" />';

$my_list_edit = null;


// Primary List START
$big_add_list_message = null;
if (!$primary_list || !$entries || !$spots) {
  $yelp_list_render = '';
  $yelp_list_render .= '<ul class="list">';
  $spots = DataReadUtils::getGenericSpotsForQuery($query);
  if ($spots) {
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
    $list_render =
    '<div class="error-box"><h4>Oops! We don\'t have a curator for this list yet.</h4><p>Can we interest you in one of our other popular lists?</p></div>';
  }
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
  $list_render =  '<ul class="list">';

/*
// Used for baseline ranking
$spots = array();
foreach ($entries as $entry) {
  $spots[$entry['spot_id']] = get_object($entry['spot_id'], 'spots');
}
$ordered = array();
foreach ($spots as $spot_id => $spot) {
  $ordering = (1000 * $spot['rating']) + $spot['review_count'];
  $ordered[$ordering] = $spot['name'];
}
krsort($ordered);
slog($ordered);
// End baseline ranking
*/


  $total_review_count = 0;
  foreach ($entries as $entry) {
    $spot = $spots[$entry['spot_id']];
   $total_review_count += (int) $spot['review_count'];
    $list_render .=
      '<li>'.Modules::listItem($entry, $spot).'</li>';
  }
  $list_render .= '</ul>';
}
$share_url = BASE_URL;
if ($primary_list) {
   $share_url = BASE_URL.'?l='.$primary_list['id'];
}
// Primary List END

// City Lists START
$city_lists_render =
  Modules::renderCoverList($city_lists);
// City Lists END

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
  '<ul class="profile-list" style="margin-top: 0px"><h3><span>What Is WhatsNom?</span></h3>'
  .'<li>'.$star_icon.'Curated lists of food and bars</br> by '
  .'resident experts. Nom!'
  .'</li>';
$about_us .= '</ul>';

$ios_icon =
  RenderUtils::renderExternalLink(
    '<img width="110px" class="list-profile" src="'.BASE_URL.'/images/appstore.svg" />',
    'https://itunes.apple.com/us/app/whatsnom-curated-top-lists/id1044133059'
  );
$googleplay_icon =
  RenderUtils::renderExternalLink(
    '<img width="110px" class="list-profile" src="'.BASE_URL.'/images/playstore.svg" />',
    'https://play.google.com/store/apps/details?id=com.whatsnom.inlist&hl=en'
  );
$app_buttons = '<table style="margin-top: 20px;"><tr>'
.'<td>'.$ios_icon.'</td>'
.'<td>'.$googleplay_icon.'</td>'
.'</tr></table>';


$add_link_render = '<ul class="profile-list"><li>'.$add_link_profile_item.'</li></ul>';


$query->setCount(count($spots));

$filter_render = Modules::renderFilter($query);

$critic_attribution = null;

if ($total_review_count) {
  $critic_count = number_format($total_review_count % 5 + 2); // TODO: legitify
   $critic_attribution =
   '<p class="list-subheader">Based on <b>'.$critic_count.' critics</b> and <b>'.number_format($total_review_count).' Reviews</b></p>';
}
$share_box =
  '<div align="left" style="margin: 10px 0 0 0; width: 100%;">'
    .FacebookUtils::render_share_box()
    .$critic_attribution
  .'</div>';
$list_title =
  '<div class="hide-on-mobile">
     <h4 class="list-title">'.$query->getTitle().'</h4>'
  .$share_box
  .'</div>';

$map = null;
if ($primary_list && $entries) {
  $map = Modules::renderMapForList($entries, $spots);
}

$content =
'<div class="twelve columns" style="margin-top: 20px;">'
.$list_title
.$map
.$list_render
.'</div>
		<div class="four columns sidebar">'
    .$app_buttons
    .$about_us
    .$city_lists_render
    .$my_list_edit
    .$filter_render
    .RenderUtils::renderContactForm()
    .$add_link_render
    .'<div class="hide-on-mobile">'.$yelp_attribution.'</div>'
		.'</div>
	</div><!-- container -->
';

$page = new Page();
$page
  ->setType(PageTypes::BROWSE)
  ->setQuery($query)
  ->setContent($content)
  ->render();
