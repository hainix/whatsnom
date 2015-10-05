<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/phpThumb/phpThumb.config.php';

final class ImageUtils {

  public static function getPicURLForSpot($spot, $dimensions = null) {
    $pic_url = idx($spot, 'profile_pic', BASE_URL.'images/no-image.png');
    $pic_url = str_replace('o.jpg', 'ls.jpg', $pic_url);
    if ($dimensions) {
      $pic_url = ImageUtils::resizeCroppedSrc(
        $pic_url,
        $dimensions
      );
    }
    return $pic_url;
  }
  public static function resizeCroppedSrc($src,  $dimensions) {
    $width = $dimensions['width'];
    $height = idx($dimensions, 'height');
    $ret = rawurlencode($src);

    if ($dimensions['width']) {
      $ret .= '&w='.$width;
    }
    if ($dimensions['height']) {
      $ret .= '&h='.$height;
    }
    $ret .= '&zc=1';

    return phpThumbURL('src='.$ret, '/phpThumb/phpThumb.php');
  }

  public static function resizeSrc($src, $dimensions) {
    return BASE_URL.'phpthumb/phpThumb.php?src='.urlencode($src)
      .'&w='.$dimensions['width'];
  }
}