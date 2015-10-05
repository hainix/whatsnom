<?php

final class RenderUtils {

  public static function renderLink($text, $link, $extras = array()) {
    return self::_renderLink($text, $link, true, $extras);
  }

  public static function renderExternalLink($text, $link, $extras = array()) {
    $extras['target'] = idx($extras, 'target', '_blank');
    return self::_renderLink($text, $link, false, $extras);
  }

  private static function _renderLink($text, $link, $rel, $extras = array()) {
    if ($rel) {
      $link = BASE_URL. $link;
    }
    $extra = '';
    if ($extras) {
      foreach ($extras as $key => $val) {
        $extra .= ' '.$key.'="'.$val.'" ';
      }
    }
    return '<a href="'.$link.'" '.$extra.'>'.$text.'</a>';
  }

  public static function renderSelectOptions($options, $default = null, $blank_option = '-') {
    $ret = $blank_option ? '<option value="">'.$blank_option.'</option>' : '';
    foreach ($options as $value => $key) {
      $ret .= '<option value="'.$key.'" ';
      if ($default && $default == $key) {
        $ret .= 'selected="true" ';
      }
      $ret .= '>'.ucwords(strtolower(str_replace('_', ' ', $value))).'</option>';
    }
    return $ret;
  }

  public static function noQuotes($string) {
    return str_replace('"', '', $string);
  }

  public static function go404() {
    $page = new Page();
    $page
      ->setContent(
        '<div style="margin-top:150px;">'
        .self::renderMessage(
          'This is not the page you were looking for.',
          'warning.png',
          $header = true
        )
        .'</div>'
      )->render();
    exit(1);
  }

  public static function renderMessage($message, $icon = null, $header = false) {
  $icon_render = $icon
    ?'<div class="inline" style="margin-right: 14px; vertical-align: middle;">'
    .'<img width="50px" height="50px" src="'.BASE_URL.'/images/'.$icon.'" />'
    .'</div>'
    : null;

  $tag = $header ? 'h3' : 'div';
return
'<div class="inline-actions-container">'
  .$icon_render
  .'<'.$tag.' class="inline">'.$message.'</'.$tag.'></div>';
  }

  public static function renderContactForm() {
    return
      '<h3><span>Contact</span> Us</h3>'
      .'<ul class="profile-list"><li id="contact-us-link">'
      .RenderUtils::renderLink(
        '<img class="list-profile" src="'.BASE_URL.'/images/paper_plane.png" />'
        .'Thoughts? Feedback?<span class="user-meta">Let us know!</span>',
        '#'
      )
      .'</li></ul>';
  }


}



?>
