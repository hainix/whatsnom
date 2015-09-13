<?php

function get_youtube_page_link($handle) {
  return 'http://youtu.be/'.$handle;
}

function get_youtube_link($id, $k = 'v') {
	return 'http://www.youtube.com/'.$k.'/'.$id.'&hl=en&fs=1&autoplay=1&hd=1&iv_load_policy=3&showsearch=0&modestbranding=1&autohide=1&showinfo=0';
}

function render_youtube_embed($handle,
                              $start_time = 90,
                              $auto_play = 0,
                              $dimensions = array('width' => 660,
                                                  'height' => 460)) {
  $options = 'autohide=1&showinfo=0&modestbranding=0&autoplay='.$auto_play.'&iv_load_policy=3&wmode=transparent&allowfullscreen=true&origin='.BASE_URL;
  if ($start_time) {
    $options .= '&start='.$start_time;
  }


    $embed_url = 'http://www.youtube.com/embed/'.$handle.'?'.$options;;

  return
'<div class="video-container">'
    .'<iframe src="'.$embed_url.'" frameborder="0" width="'.$dimensions['width'].'
" height="'.$dimensions['height'].'"></iframe>'
.'</div>';

  // autoplay and mute
  $player_id = rand(0, 9999);
  $my_player_id = rand(0, 9999);
return
'<script src="http://www.google.com/jsapi"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"></script>
<div class="video-container">
    <div id="ytapiplayer_'.$player_id.'">You need Flash player 8+ and JavaScript enabled to view this video.</div>
</div>
    <script type="text/javascript">
  google.load("swfobject", "2.1");
function onYouTubePlayerReady(playerId) {
  ytplayer = document.getElementById("myytplayer_'.$my_player_id.'");
  //ytplayer.playVideo();
  //ytplayer.mute();
}
var params = { allowScriptAccess: "always", allowFullScreen: "true" };
var atts = { id: "myytplayer_'.$my_player_id.'" };
swfobject.embedSWF("http://www.youtube.com/v/'.$handle.'?enablejsapi=1&playerapiid=ytplayer&'.$options.'",
                   "ytapiplayer_'.$player_id.'", "'.$dimensions['width'].'", "'.$dimensions['height'].'", "8", null, null, params, atts)
    </script>';
  }
?>
