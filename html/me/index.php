<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';

$uid = idx($_GET, 'view');
$just_logged_out = idx($_GET, 'logout');
$user = null;
if ($uid && is_numeric($uid)) {
  $user = get_object($uid, 'users');
}
$me = FacebookUtils::getUser();
$user = $user ?: $me;

if (!$user) {
  $page = new Page();
  $page
    ->setType('add')
    ->setContent(
      RenderUtils::renderMessage(
        'Sign in to view your bookmarks and lists.'
      )
    )->render();
  exit(1);
}

// Data START
$user_is_me = $user['id'] == $me['id'];
$my_lists = DataReadUtils::getAllListsForCreator($user);
$my_bookmarks_assocs = DataReadUtils::getAllOutgoingAssocs($user, 'bookmarks');
$my_spots = null;
if ($my_bookmarks_assocs) {
  $my_spots = get_objects(
    array_pull($my_bookmarks_assocs, 'target_id'),
    'spots'
  );
}
$my_saved_lists_assocs = DataReadUtils::getAllOutgoingAssocs($user, 'votes');
$my_saved_lists = null;
if ($my_saved_lists_assocs) {
  $my_saved_lists = get_objects(
    array_pull($my_saved_lists_assocs, 'target_id'),
    'lists'
  );
}
// Data END

// Header Message START
$header_message = null;
if ($just_logged_out) {
  $header_message .=
    '<div class="bubble-container" style="padding: 20px 0; margin-top: 10px;">'
    .RenderUtils::renderMessage('<h4>Successfully Logged out.</h4> Viewing your public profile.')
    .'</div>';
}


// Header Message END


// Bookmarks START
if (!$my_spots) {
  $saved_render =
    '<div style="margin-top: 70px;">'
    .RenderUtils::renderMessage('<h4>No Favorites Yet!</h4>')
    .'</div>';
} else {
  $saved_render =
    '<h4>'
    . ($user_is_me ? 'My' : $user['first_name'].'\'s')
    .' <span style="color: #444;">Favorites</span></h4>';
  $saved_render .= '<ul class="list">';
  foreach ($my_spots as $spot) {
    $fake_entry = array('position' => null);
    $saved_render .=
      '<li>'.Modules::listItem($fake_entry, $spot).'</li>';
  }
  $saved_render .= '</ul>';
}
// Bookmarks END


// My Saved Lists START
$my_saved_lists_render = '';
if ($my_saved_lists) {
  $my_saved_lists_render =
    '<h3><span>'
    . ($user_is_me ? 'My' : $user['first_name'].'\'s')
    .'</span> Saved Lists</h3> '
    .Modules::renderProfileList($my_saved_lists, $show_city = true);
}
// My Saved Lists END

// My Lists START
$my_lists_render = '';
foreach (array_group_by_key($my_lists, 'city') as $city_id => $my_city_lists) {
  $my_lists_render .=
  '<h3><span>'
  . ($user_is_me ? 'My' : $user['first_name'].'\'s')
  .' Authored</span> '
    .Cities::getName($city_id)
  .' Lists</h3>'
    .Modules::renderProfileList($my_city_lists);
}
// My Lists END

$log_out_render = null;
if ($user_is_me) {
  $log_out_render =
      '<h3>Account</h3>'
    .'<ul class="profile-list"><li>'
        .RenderUtils::renderExternalLink(
          '<img class="list-profile" src="'.BASE_URL.'/images/gear.png" />'
        .'Log Out<span class="user-meta">See you soon!</span>',
          FacebookUtils::getLogoutURL(
            array(
              'next' =>
                BASE_URL.'me/?view='.$user['id'].'&logout=1'
            )
          ),
          array('target' => '_self')
        )
        .'</li></ul>';

}


$content =
'<div class="twelve columns">'
  .$header_message
  .$saved_render
.'</div>
	<div class="four columns sidebar">'
    .$my_saved_lists_render
    .$my_lists_render
    .RenderUtils::renderContactForm()
    .$log_out_render
.'</div>
</div><!-- container -->';

$page = new Page();
$page
  ->setContent($content)
  ->render();
