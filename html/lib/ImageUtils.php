<?php

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
    //$src= rawurlencode($src);
    //$src = urlencode($src);
    $ret =
      BASE_URL.'thmb/phpThumb.php?src='.$src;
      //.'&zc=1';
    if ($dimensions['width']) {
      $ret .= '&w='.$width;
    }
    if ($dimensions['height']) {
      $ret .= '&h='.$height;
    }
    return $ret;
  }

  public static function resizeSrc($src, $dimensions) {
    return BASE_URL.'phpthumb/phpThumb.php?src='.urlencode($src)
      .'&w='.$dimensions['width'];
  }
}