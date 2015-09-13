<?php

final class YelpUtils {
  const YELP_BASE = 'http://www.yelp.com/';
  public function renderYelpLink($spot, $text = null) {
    $text = $text ?: idx($spot, 'name');
    if (!$text) {
      return null;
    }
    return
      RenderUtils::renderExternalLink(
        $text,
        self::getYelpURLForHandle($spot['yelp_id'])
      );
  }

  public static function getYelpURLForHandle($handle) {
    return self::YELP_BASE.'biz/'.$handle;
  }

  public function renderYelpStars($spot) {
    if (!idx($spot, 'rating')) {
      return null;
    }
    $offset = (floor($spot['rating'] / 10) - 1) * 19;
    $stars = '<div class="stars" style="background-position: 0px -'.$offset.'px;"></div>';
    return self::renderYelpLink($spot, $stars);
  }

}
