<?php
include_once 'facebook_api.php';

function get_film_og_tags($film) {
  $head = '<meta property="og:title" content="'.$film['name'].'"/>
  <meta property="og:url" content="'.BASE_URL.'f/?id='.$film['id'].'"/>
<meta property="og:image" content="'.get_profile_pic_src($film['id'],
                                                         'film', array('width' => 400)).'"/>
<meta property="og:type" content="movie"/> ';
  return $head;
}

function get_person_og_tags($person) {
  $head = '<meta property="og:title" content="'.$person['name'].'" />
  <meta property="og:url" content="'.BASE_URL.'p/?id='.$person['id'].'"/>
<meta property="og:type" content="profile"/>
<meta property="og:image" content="'.get_profile_pic_src($person, 'person', array('width' => 280))
    .'"/>';

  return $head;
}

function get_retroactive_article_url($article) {
 return $article['publish_time'] && $article['publish_time'] < 1386091485
    ? BASE_URL.'a/?id='.$article['id']
    : BASE_URL.get_article_url($article);
}

function get_article_og_tags($article) {
  $pic_src = get_pic_src_from_article($article);

  $head = '<meta property="og:title" content="'.strip_tags($article['headline']).'" />
<meta property="og:image" content="'.$pic_src.'"/>
  <meta property="og:type" content="article"/>
  <meta property="og:url" content="'.get_retroactive_article_url($article).'"/>';
  return $head;
}

define('FB_APP_ID', '167795946645382');

$facebook = new Facebook(array(
  'appId'  => FB_APP_ID,
  'secret' => '1c8e60e56eff7464b0023df9cac3668a',
  'cookie' => true,
));


$uid = $facebook->getUser();
$me = null;
$user = null;
// Session based API call.
if ($uid) {
  try {
    $user = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

function get_fb_user() {
	global $user;
	return $user ? $user : null;
}

function get_fb_login_url() {
	 global $facebook;
	 return $facebook->getLoginUrl();
}

function get_fb_logout_url() {
	 global $facebook;
	 return $facebook->getLogoutUrl();
}

function get_fb_appid() {
	return FB_APP_ID;
}

function get_fb_session() {
	global $session;
	return $session ? json_encode($session) : null;
}

function render_fb_comments($url, $posts = 20, $width = 550) {
    return
        '<div class="fb-comments" data-href="'.$url.'" data-num-posts="'.$posts.'" data-width="'.$width.'"></div>';
}

function render_fb_like($url, $send_button = true) {
  return
    '<div class="fb-like-container">'
    .'<fb:like href="'.$url.'" send="'.$send_button.'" layout="button_count" width="100"'
    .' show_faces="false" font="arial"></fb:like>'
    .'</div>';
}

function render_fb_like_box($url) {
  return
    '<div style="margin-right: 15px;">'
    .'<fb:like href="'.$url.'" send="false" layout="box_count" width="80"'
    .' show_faces="false" font="arial"></fb:like></div>';
}


function render_wide_fb_like($url) {
return
  '<div class="fb-like" data-href="'.$url.'" data-send="true" data-width="600" data-show-faces="false"></div>';
}

function render_twitter_box($url) {
return
'<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$url.'">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
}

function render_fb_send($url) {
  return
  '<div class="fb-send" data-href="'.$url.'" data-font="arial"></div>';
}

function render_share_box($url, $two_rows = false) {
    $ret =
    '<table>
    <colgroup>
        <col width="90px">
</colgroup>
<tr><td>'
    .render_fb_like($url).'</td>';
    if ($two_rows) {
      $ret .= '</tr><tr>';
    }
    $ret .= '<td style="padding-left: 6px;">'
    .render_twitter_box($url).'</td></tr></table>';
    return $ret;
}


function render_share_box_for_object($object, $two_rows = false) {
  $url = null;
  switch ($object['type']) {
    case 'film':
      $url = BASE_URL.'f/?id='.$object['id'];
      break;
    case 'person':
      $url = BASE_URL.'p/?id='.$object['id'];
      break;
    case 'article':
      $url = get_retroactive_article_url($object);
      break;
  default: slog('no type for '.$object['type']);
  }

  return render_share_box($url, $two_rows);
}

?>

