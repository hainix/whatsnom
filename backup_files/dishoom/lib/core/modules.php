<?php

final class Modules {
  public static function getMobileHeadContent() {
    $ret =
      '<link rel="stylesheet" href="'.BASE_URL.'css/add2home.css">'
."<script type='text/javascript'>
var addToHomeConfig = {
animationIn: 'bubble',
animationOut: 'drop',
lifespan:10000,
expire:0,
touchIcon:true,
message:'Get <strong>Dishoom</strong> on your %device for Free! Tap %icon and then <strong>Add to Home Screen</strong>.'
};
</script>"
      .'<script type="application/javascript" src="'.BASE_URL.'js/add2home.js"></script>'
      .'<link href="'.MEDIA_BASE.'assets/startup_screen.png" rel="apple-touch-startup-image" />'
      .'<meta name="apple-mobile-web-app-capable" content="yes" />'
      .'<meta name="viewport" content="width=device-width; initial-scale=1.0; '
      .'maximum-scale=1.0; user-scalable=0;" />'
      .' <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />';
    $sizes = array(57, 72, 114);
    foreach ($sizes as $size) {
      $ret .= '<link rel="apple-touch-icon-precomposed" sizes="'.$size.'x'.$size
        .'" href="'.MEDIA_BASE.'assets/square_'.$size.'.png">';
    }
    $ret .=
      '<script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.'
      .'parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script>';
    // to scroll load, for mobile
    $ret .= '<script type="text/javascript">
        window.addEventListener("load",function() {
          // Set a timeout...
          setTimeout(function(){
            // Hide the address bar!
            window.scrollTo(0, 1);
          }, 0);
        });
      </script>';

    return $ret;
  }



  public static function getContentForModule($module) {
    switch ($module) {
    case 'vertical-ticker':
      // http://buildinternet.com/project/totem/
      return
        '<script src="'.BASE_URL.'js/jquery.vertical-ticker.min.js"></script>';

    case 'fancybox':
      return
      '<link rel="stylesheet" type="text/css" href="'.BASE_URL.'js/fancybox/jquery.fancybox.css" media="screen" />
            			<script type="text/javascript" src="'.BASE_URL.'js/fancybox/jquery.fancybox.pack.js"></script>
            			<link rel="stylesheet" type="text/css" href="'.BASE_URL.'js/fancybox/helpers/jquery.fancybox-thumbs.css" media="screen" />
            			<script type="text/javascript" src="'.BASE_URL.'js/fancybox/helpers/jquery.fancybox-thumbs.js"></script>'
        . self::getFancyboxStarterScript();
    case 'ui-custom':
      return
        '<script src="'.BASE_URL.'js/jquery-ui-1.8.16.custom.min.js"></script>'
        .'<script src="'.BASE_URL.'js/jquery.mousewheel.min.js"></script>'
        .'<script src="'.BASE_URL.'js/jquery.ui.touch-punch.min.js"></script>';

    case 'expander':
      return
        '<script src="'.BASE_URL.'js/jquery.expander.min.js"></script>';

    case 'playlist-slider':
      //  http://codecanyon.net/item/jquery-banner-rotator-content-slider-carousel/full_screen_preview/1534434
      return
         '<script src="'.BASE_URL.'js/allinone_bannerWithPlaylist.js"></script>'
      .'<link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/allinone_bannerWithPlaylist.css" media="all">';
    case 'kenburns':
      //http://codecanyon.net/item/responsive-kenburner-slider-jquery-plugin/1633038
      return
        '<script src="'.BASE_URL.'js/jquery.themepunch.plugins.min.js"></script>'
        .'<script src="'.BASE_URL.'js/jquery.themepunch.kenburn.min.js"></script>'
        .'<link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/kenburns.css" media="all">';

    case 'thumbnails-slider':
      return
         '<script src="'.BASE_URL.'js/allinone_thumbnailsBanner.js"></script>'
        .'<script src="'.BASE_URL.'js/reflection.js"></script>'
        .'<link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/allinone_thumbnailsBanner.css" media="all">';


    case 'banner-rotator':
      return
         '<script src="'.BASE_URL.'js/allinone_bannerRotator.js"></script>'
        .'<link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/allinone_bannerRotator.css" media="all">';

    case 'accordian-slider':
      return
        '<script src="'.BASE_URL.'js/jquery.hrzAccordion.js"></script>';
    case 'carousel-slider':
      return
        '<script src="'.BASE_URL.'js/allinone_carousel.js"></script>'
        .'<link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/allinone_carousel.css" media="all">';

    case 'carousel':
      return
        '<script type="text/javascript" src="'.BASE_URL.'js/jquery.carouFredSel-5.5.5-packed.js"></script>
<link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/carousel.css" media="all">';
    case 'multi-selector':
      return
        '<script type="text/javascript" src="'.BASE_URL.'js/jquery.multiSelector.js"></script>
<link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/multi_selector.css" media="all">';


    case 'news':
      return
        '<link rel="stylesheet" type="text/css" media="all" href="'.BASE_URL.'css/news.css">';
    case 'poll':
      return
        '<link rel="stylesheet" type="text/css" media="all" href="'.BASE_URL.'css/poll.css">';
    case 'main-slider':
     return  '<link rel="stylesheet" id="slider-elegant-css" href="'.BASE_URL.'css/slider-elegant.css" type="text/css" media="all">
    <script type="text/javascript">
var yiw_slider_type = "elegant",
        yiw_slider_elegant_easing = null,
yiw_slider_elegant_fx = "fade",
yiw_slider_elegant_speed = 500,
yiw_slider_elegant_timeout = 9000,
yiw_slider_elegant_caption_speed = 500;
    </script>';
    case 'tabs':
      return '
    <script type="text/javascript">
        // for tab slider
        var yiw_prettyphoto_style = "pp_default";
    </script>
<script type="text/javascript" src="'.BASE_URL.'js/jquery.prettyPhoto.js"></script>';
//.'<link rel="stylesheet" id="jquery-tipsy-css" href="'.BASE_URL.'css/tipsy.css" type="text/css" media="all">
//<script type="text/javascript" src="'.BASE_URL.'js/jquery.tipsy.js"></script>';
    case 'orbit-slider':
      return
'<script type="text/javascript" src="'.BASE_URL.'js/jquery.orbit-1.2.3.min.js"></script>'
.'<link rel="stylesheet" href="'.BASE_URL.'css/orbit-1.2.3.css" type="text/css" media="all">';



    default: slog('unknown module '.$module);
    }
  }

  public static function getFancyboxStarterScript() {
    // TODO - bring back fancy images
    return "<script type='text/javascript'>
                            $(document).ready(function() {

/*
                                $('a.fancy').fancybox({
                                'transitionOut'	: 'none',
                                'padding' : 0,
                                'overlayShow'	:  true,
                                'transitionIn'	: 'fade',
                                'titlePosition'	:	'over',
                                'onComplete'	:	function() {
                                        $('#fancybox-wrap').hover(function() {
                                                $('#fancybox-title').show();
                                        }, function() {
                                                $('#fancybox-title').hide();
                                        });
                                }
                              });
$('a.fancybox-thumb').fancybox({
prevEffect: 'elastic',
nextEffect: 'elastic',
helpers: {
title: {
type: 'inside'
},
overlay: {
opacity : 0.8,
css : {
'background-color' : '#000'
}
},
thumbs: {
width: 50,
height: 50
}
}
});
*/

                            $('a.video_link').click(function() {
                            $.fancybox({
			        'hideOnContentClick': true,
                                'padding'		: 0,
                                'autoScale'		: false,
                                'transitionIn'      	: 'none',
                                'transitionOut'         : 'none',
                                'title'			: this.title,
                                'width'                 : 680,
                                'height'		: 495,
                                'href'			: this.href.replace(new RegExp('watch\\?v=', 'i'), 'v/'),
                                'type'			: 'swf',
                                'swf'			: {
                                'wmode'                 : 'transparent',
                                'allowfullscreen'	: 'true'
                                }
                        });

	return false;
});
                            });
                        </script>";


  }

  }


?>