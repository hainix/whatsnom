<?php
define('FB_APP_ID', '299948876841231');
define('FB_APP_SEC', 'a801a5c6aa83043990b8f3a44331f04e');
define('FB_APP_VER', 'v2.3');


if(!session_id()) {
    session_start();
}

include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
require_once( $_SERVER['DOCUMENT_ROOT'].'/lib/api/fb-sdk-v5.5/autoload.php');

$fb = new Facebook\Facebook([
  'app_id'                => FB_APP_ID,
  'app_secret'            => FB_APP_SEC,
  'default_graph_version' => FB_APP_VER,
]);

// Handle logout first
if ($_GET && isset($_GET['logout'])) {
  $fb->destroySession();
}

$helper = $fb->getCanvasHelper();

if ($_SESSION && isset($_SESSION['fb_access_token'])) {
  try {
    // Returns a `Facebook\FacebookResponse` object
    $response =
      $fb->get(
        '/me?fields=id,name,first_name,picture',
        $_SESSION['fb_access_token']
      );
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }
}
$fb_user = $response->getGraphUser();


final class FacebookUtils {
  public static function getUser() {
    global $fb_user;

    if ($fb_user && isset($fb_user['id'])) {
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
          "INSERT IGNORE INTO users (id, name, first_name, profile_pic_url, "
          ."last_updated) VALUES "
          ."(%.0f, '%s', '%s', '%s', %d)",
          $fb_user['id'],
          DataReadUtils::cln($fb_user['name']),
          DataReadUtils::cln($fb_user['first_name']),
          $fb_user['picture']['url'],
          $now
        );
      global $link;
      $r = mysql_query($sql);
      if (!$r) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $sql;
        die($message);
      }
      $entry_id = mysql_insert_id();
      $db_user =  $db_user ?: get_object($entry_id, 'users');
    }

    if ($db_user && $db_user['last_updated'] + (SEC_IN_DAY * 3) < $now) {
      $sql =
        sprintf(
          "UPDATE users SET profile_pic_url = '%s', last_updated = %d"
          ." WHERE id = %.0f LIMIT 1",
          $fb_user['picture']['url'],
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
    global $fb;
    $helper = $fb->getRedirectLoginHelper();
    return $helper->getLoginUrl(BASE_URL.'fb-callback.php');
  }

  public static function getLogoutURL($params = array()) {
    return 'https://www.facebook.com/logout.php';
    global $fb;
    $helper = $fb->getRedirectLoginHelper();
    return $helper->getLogoutUrl($params);
  }

  public static function getAppID() {
    return FB_APP_ID;
  }

  public static function renderLikeButton($url) {
    return
      '<div class="fb-like" data-href="'.$url.'" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>';
  }

  public static function render_fb_comments($url, $posts = 20, $width = 550) {
    return
      '<div class="fb-comments" data-href="'.$url.'" data-num-posts="'.$posts.'" data-width="'.$width.'"></div>';
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

  public static function render_share_box($url = null, $two_rows = false) {
    if (!$url) {
      $url = '';//BASE_URL;
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
