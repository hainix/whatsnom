<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/api/facebook.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/base.php';
define('FB_APP_ID', '299948876841231');


$facebook = new Facebook(array(
  'appId'  => FB_APP_ID,
  'secret' => 'a801a5c6aa83043990b8f3a44331f04e',
  'cookie' => true,
));

// Handle logout first
if ($_GET && idx($_GET, 'logout')) {
  $facebook->destroySession();
}

$uid = $facebook->getUser();
$me = null;
$fb_user = null;
// Session based API call.
if ($uid) {
  try {
    $fb_user = $facebook->api('/me?fields=id,name,first_name,picture');
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

final class FacebookUtils {
  public static function getUser() {
    global $fb_user;
    if ($fb_user && idx($fb_user, 'id')) {
      return self::addOrFetchUserFromFBUser($fb_user);
    }
    return null;
  }

  public static function addOrFetchUserFromFBUser($fb_user) {
    $db_user = get_object($fb_user['id'], 'users');
    $now = time();
    if (!$db_user) {
      $sql =
        sprintf(
          "INSERT INTO users (id, name, first_name, profile_pic_url, "
          ."last_updated) VALUES "
          ."(%.0f, '%s', '%s', '%s', %d)",
          $fb_user['id'],
          DataReadUtils::cln($fb_user['name']),
          DataReadUtils::cln($fb_user['first_name']),
          $fb_user['picture']['data']['url'],
          $now
        );
      global $link;
      $r = mysql_query($sql);
      if (!$r) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $q;
        die($message);
      }
      $entry_id = mysql_insert_id();
      $db_user =  $db_user ?: get_object($entry_id, 'users');
    }

    if ($db_user && $db_user['last_updated'] + (SEC_IN_DAY * 3) < $now) {
      $sql =
        sprintf(
          "UPDATE users SET profile_pic_url = '%s', last_updated = %d"
          ." WHERE id = %d LIMIT 1",
          $fb_user['picture']['data']['url'],
          $now,
          $fb_user['id']
        );
      global $link;
      $r = mysql_query($sql);
      if (!$r) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $q;
        die($message);
      }
    }

    // Replace admin pic for what's nom admin
    if ($db_user['id'] == 10104624213101750) {
      $db_user['profile_pic_url'] = BASE_URL.'images/no-image.png';
    }
    return $db_user;
  }

  public static function getLoginURL() {
    global $facebook;
    return $facebook->getLoginUrl(
    );
  }

  public static function getLogoutURL($params = array()) {
    global $facebook;
    return $facebook->getLogoutUrl($params);
  }

  public static function getAppID() {
    return FB_APP_ID;
  }

  public static function renderLikeButton() {
    return
      '<div class="fb-like" data-href="https://www.whatsnom.com" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>';
  }

  public static function get_fb_session() {
    global $session;
    return $session ? json_encode($session) : null;
  }

  public static function render_fb_comments($url, $posts = 20, $width = 550) {
    return
      '<div class="fb-comments" data-href="'.$url.'" data-num-posts="'.$posts.'" data-width="'.$width.'"></div>';
  }

  public static function render_fb_like($url, $send_button = true) {
    return
      '<div class="fb-like-container">'
      .'<fb:like href="'.$url.'" send="'.$send_button.'" layout="button_count" width="100"'
      .' show_faces="false" font="arial"></fb:like>'
      .'</div>';
  }

  public static function render_fb_like_box($url) {
    return
      '<div style="margin-right: 15px;">'
      .'<fb:like href="'.$url.'" send="false" layout="box_count" width="80"'
      .' show_faces="false" font="arial"></fb:like></div>';
  }


  public static function render_wide_fb_like($url) {
    return
      '<div class="fb-like" data-href="'.$url.'" data-send="true" data-width="600" data-show-faces="false"></div>';
  }

  public static function renderTwitterBox($url) {
    return
      '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$url.'">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
  }

  public static function render_fb_send($url) {
    return
      '<div class="fb-send" data-href="'.$url.'" data-font="arial"></div>';
  }

  public static function render_share_box($url = null, $two_rows = false) {
    if (!$url) {
      $url = BASE_URL;
    }
    $ret =
      '<table>
    <colgroup>
        <col width="90px">
</colgroup>
<tr><td>'
      .self::renderLikeButton($url).'</td>';
    if ($two_rows) {
      $ret .= '</tr><tr>';
    }
    $ret .= '<td style="padding-left: 6px;">'
      .self::renderTwitterBox($url).'</td></tr></table>';
    return $ret;
  }

}


?>
