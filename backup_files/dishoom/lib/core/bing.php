<?php

function get_current_images($query, $num = 50) {
  // max of 50 for now, can loop with offsets if you want more

  $apiNumber = 'B57B353A3C8F403031FB7FB9FAB6CA780ED47B96';
  $url = 'http://api.bing.net/json.aspx?AppId='.$apiNumber.'&Query=' . urlencode($query)
    .'&Sources=Image&Version=2.2&Market=en-US&Adult=Moderate&Image.Count=' . $num . '&Image.Offset=0&JsonType=raw';
  $file = file_get_contents($url, 1000000);

  if (!$file) {
    return false;
  }
  $image_result = json_decode($file, true);
  // slog($image_result);
  if (!$image_result || !isset($image_result['SearchResponse']['Image']['Results'])) {
    return false;
  }

  $ret = array();
  foreach ($image_result['SearchResponse']['Image']['Results'] as
           $image_response) {
    $ret[] =
      array(
        'img_src'   => $image_response['MediaUrl'],
        'thumb_src' => $image_response['Thumbnail']['Url'],
        'name'      => $image_response['Title']
      );
  }

  return $ret;

}


?>