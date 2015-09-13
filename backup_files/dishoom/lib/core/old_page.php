<?php
include_once 'modules.php';

class OldPage {
  protected $content,
    $extraHeadItems = '',
    $page_type,
    $description = null,
    $modules = array(),
    $title = 'Dishoom';

  function __construct($page_type = 'none') {
		$this->page_type = $page_type;
	}

  protected function getPageType() {
    return $this->page_type;
  }

  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

	public function setContent($content)  {
		$this->content = $content;
		return $this;
	}

	public function setDescription($content) {
	  $this->description = $content;
	  return $this;
	}

  protected function getLogo() {
    if ($this->homeHeaderEverywhere() || $this->getPageType() == 'home') {
      return '<img src="'.BASE_URL.'images/logo/logo_140.png" alt="Dishoom">';
    } else {
      return '<img src="'.BASE_URL.'/images/logo/logo_100.png" alt="Dishoom">';
    }
  }

  protected function getMobileLogo() {
    return '<img src="'.BASE_URL.'images/logo/logo_200.png" alt="Dishoom">';
  }

	public function getDescription() {
	  if ($this->description) {
	    return $this->description;
	  }
	  return 'Dishoom | Your portal for Bollywood Stars, Reviews, and News. Bollywood...with a Punch.';
	}

	public function addHeadContent($content)  {
		$this->extraHeadItems = $this->extraHeadItems.' '.$content;
		return $this;
	}

	protected function canSee() {
	  return true;
	}

  protected function getContent() {
    if (!is_admin() && self::isUnderConstruction()) {
  return '<div style="text-align:center; padding: 200px 40px;">
<h1>we are down for regular maintenance</h1><div style="padding-top: 14px; font-size: 16px;">brb</div>
</div>';
    }

    return $this->content;
  }


	public function render() {
		$this->start_page();

		if ($this->canSee()) {
		  echo $this->getContent();
		} else {
		  go_404(/* die */ false);
		}

		$this->end_page();
	}

  public function requireModule($module_names) {
    if (!is_array($module_names)) {
      $module_names = array($module_names);
    }
    foreach ($module_names as $module_name) {
      $this->modules[$module_name] = true;
    }
    return $this;
  }

  public function getModuleHeadContent() {
    if (!$this->modules) {
      return null;
    }
    $head = '';
    foreach (array_keys($this->modules) as $module) {
      $head .= Modules::getContentForModule($module);
    }
    return $head;
  }

  private function getHeaderFontName() {
    return array('Lato', 700);
  }

	public function getHeadContent() {
	  //http://fancyapps.com/fancybox/
    /*
<link rel="pingback" href="/xmlrpc.php">
<script type="text/javascript" src="'.BASE_URL.'js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="'.BASE_URL.'js/jquery.cycle.min.js"></script>'
    */
    $stylesheet_name = 'style.css';

	  $head =
      '<script src="https://www.google.com/jsapi?key=ABQIAAAATho9O4Hn8fQB1cFpMuK-5hTWYTeqpgtDkC468M02Xk1lT0yuOxQ4z1QRJmKuqq5MsVnb35IsuwTsYw" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" media="all" href="'.BASE_URL.'css/'.$stylesheet_name.'">
<link rel="stylesheet" type="text/css" media="screen and (max-width: 960px)" href="'.BASE_URL.'css/lessthen800.css">
<link rel="stylesheet" type="text/css" media="screen and (max-width: 600px)" href="'.BASE_URL.'css/lessthen600.css">
<link rel="stylesheet" type="text/css" media="screen and (max-width: 480px)" href="'.BASE_URL.'css/lessthen480.css">'
      .$this->getFontsCSS()
      .$this->getModuleHeadContent();
	  return $head;
	}

  protected function getFontsCSS() {

    $head = "<link href='http://fonts.googleapis.com/css?family=Lato:700,900|Muli' rel='stylesheet' type='text/css'>";
    $head .= '<style type="text/css">
    h1, h2, h3, h4, h5, h6 ';
    $font_data = $this->getHeaderFontName();
    if (!is_array($font_data)) {
      $head .= '{ font-family:  "'.$font_data.'" !important; }';
      // For weighted font
    } else {
      $head .= '{ font-family:  "'.$font_data[0].'" !important; font-weight: '.$font_data[1].'}';
      $head .= ' h1, h2, h3 {font-weight: 900 !important;} ';
    }

    $head .= '</style>';
    return $head;
  }


  protected function getBaseHeadContent() {
    if (is_mobile()) {
      $mobile_ready_title = 'Dishoom';
      $mobile_head_content = Modules::getMobileHeadContent();
    } else {
      $mobile_ready_title = $this->title;
      $mobile_head_content = null;
    }

    return
      '<title>'.$mobile_ready_title.'</title>
      <meta name="description" content="'.$this->getDescription().'">
      <meta property="og:site_name" content="Dishoom"/>
      <meta property="fb:app_id" content="'.get_fb_appid().'"/>
      <meta property="fb:admins" content="7906796"/>
      <meta property="og:title" content="'.$this->title.'" />
      <meta property="og:description" content="'.$this->getDescription().'" />'
      .$mobile_head_content
    .'<link rel="stylesheet" type="text/css" media="all" href="'.BASE_URL.'css/reset.css">'
    .'<link rel="stylesheet" type="text/css" media="all" href="'.BASE_URL.'css/nav.css">'

      // jquery
.'<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>'

      .'<script src="'.BASE_URL.'js/timeago.js"></script>'
      .'<script type="text/javascript">
        jQuery(document).ready(function() {
          jQuery("abbr.timeago").timeago();
        });
      </script>'

      .self::getAnalyticsCode()
      // hovercards
      .'<script src="'.BASE_URL.'js/jquery.hovercard.min.js"></script>'

      .get_search_header();
  }

  protected function getAnalyticsCode() {
      // Google analytics
    if (is_admin()) {
      return null;
    }
    return'<script type="text/javascript">
        var _gaq = _gaq || []; _gaq.push(["_setAccount", "UA-26965917-1"]);
        _gaq.push(["_trackPageview"]);
        (function() {
        var ga = document.createElement("script");
        ga.type = "text/javascript"; ga.async = true;
        ga.src = ("https:" == document.location.protocol
          ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(ga, s);
        })();
      </script>';
  }

  protected function getMenuNav() {

    $menu_navs = array();

    /*
    // Home button on other pages
    if ($this->getPageType() != 'home') {
      $menu_navs = array('home' => '');
    }

    $menu_navs = array_merge(
      $menu_navs,
      array(
      'movies' => 'films.php',
      'stars' => 'stars.php',
      'news'  => 'news.php',
      //'watch' => 'tv?hsh='.mt_rand(1, 9999),
      'watch' => 'watch.php',
      )
    );
    */

    if (is_admin()) {
      $menu_navs['cms'] = 'cms';
    }

    $menu_nav = '<!-- START NAV -->
        <div id="nav" class="group">
          <ul id="menu-main-navigation" class="level-1 white">';
    foreach ($menu_navs as $menu_nav_name => $path) {

      $extra_menu_class = null;
      if ($menu_nav_name == 'home') {
        // Don't show home on mobile, ever
        $extra_menu_class = ' for-not-mobile';
      }

      $menu_nav_item = '<li class="menu-item'.$extra_menu_class.'">';
      $menu_nav_item .= '<a class="special-font';
      if ($this->getPageType() == $menu_nav_name) {
        $menu_nav_item .= ' active';
      }
      $menu_nav_item .= '" href="'.BASE_URL.$path.'" >'
        .ucwords($menu_nav_name).'</a>';

      $menu_nav_item .= '</li>';

      $menu_nav .= $menu_nav_item;
    }

    $menu_nav .= '<li class="menu-item search-menu-item">'.render_search_form().'</li>';

    // show like button on home
    if (true && ($this->homeHeaderEverywhere() || $this->getPageType() == 'home')) {
      $menu_nav .=
        '<li class="menu-item menu-item-no-top for-not-mobile">'
        .'<div style="width: 50px; overflow: hidden;">'
        .'<div class="fb-like" data-href="https://www.facebook.com/dishoomfilms" data-send="false" data-layout="box_count" data-width="50" data-show-faces="false" data-font="arial"></div>'
        .'</div>'
        .'</li>';
    }
    $menu_nav .= '</ul>';


    return $menu_nav;
  }

  protected function homeHeaderEverywhere() {
    return true;
  }

  protected function getHeader() {
    $header = '<div id="header-container"><div id="header" class="group';
    if ($this->homeHeaderEverywhere() || $this->getPageType() == 'home') {
      $header .= ' home-header';
    } else {
      $header .= ' not-home-header';
    }
    $header .= ' ">
         <!-- START LOGO -->
        <div id="logo" class="group">
          <a href="'.BASE_URL.'" title="Dishoom" class="for-not-mobile">'
            .$this->getLogo().'
          </a>
        </div>
        <!-- END LOGO -->'
      .$this->getMenuNav()
      .'</div></div></div>';
    return $header;
  }

  protected function showWrapper() {
    return true;
  }
  protected function start_page() {
    $html =
      '<!DOCTYPE html>
       <html xmlns="http://www.w3.org/1999/xhtml"
         xmlns:fb="http://ogp.me/ns/fb#"
         lang="en">
         <head>
           <base href="'.BASE_URL.'" />
           <meta name="viewport" content="width=device-width">
           <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
           <meta http-equiv="Content-Style-Type" content="text/css">
           <meta http-equiv="Content-Language" content="en">'
           .$this->getBaseHeadContent()
           .$this->getHeadContent()
           .$this->extraHeadItems
           .self::getFBTrackingPixel()
       .'</head>';
                $html .=
                  '<div class="home page _bgWrapper '
                  .'responsive  yes_js">'
                  .$this->getHeader()
                .'<!-- START WRAPPER -->
                    <div class="';
                if ($this->showWrapper()) {
                  $html .= 'wrapper';
                }
                $html .= ' group">';
                echo $html;
        }

        protected function end_page() {
          $login_div = null;

          $user = get_fb_user();
          if (!$user) {
            $login_url = get_fb_login_url();
            $login_text = 'Login';
          } else {
            $login_url = get_fb_logout_url();
            $login_text = 'Logout';
            /*
              $login_div = '<div id="loggedin_pic"><a href="'.get_fb_logout_url().'">
              <fb:profile-pic linked="false" uid="loggedinuser" size="square" width="40" height="40"></fb:profile-pic>
              </a></div><div id="loggedin_status">Welcome back, '.$user['first_name'].'!</div>';
            */
          }


          $login_div = render_forced_external_link($login_text, $login_url);

          $terms = render_link('Legal', 'legalese.php');
          $join_us_link = render_link('Join Us', 'join.php');
          $about_link = render_link('About Dishoom', 'about.php');
          $contact_link =
            '<a href="javascript:UserVoice.showPopupWidget();" title="Open feedback & support dialog (powered by UserVoice)">Contact</a>';

          echo
            '<!-- START COPYRIGHT -->
             <div id="copyright" class="group two-columns">
               <div class="inner group">
                 <p class="left">Copyright <a href="'.BASE_URL.'"><strong>Dishoom</strong></a>'
                   .' '.$login_div.' · '.$about_link.' · '.$terms
                   .' · '.$join_us_link.' · '.$contact_link
               .'</p>'
               .'<p class="right">'
                 .'<a href="https://www.facebook.com/pages/Dishoom/406351686077155'
                 .'" class="socials facebook" title="Facebook">facebook</a>
                   <a target="_blank" href="https://twitter.com/dishoomfilms" class="socials twitter" title="Twitter">twitter</a>
                   <a href="javascript:UserVoice.showPopupWidget();" class="socials mail" title="Mail">mail</a>
                <!--
                <a href="/#" class="socials rss" title="Rss">rss</a>
                <a href="/#" class="socials youtube" title="Youtube">youtube</a>
                <a href="/#" class="socials flickr" title="Flickr">flickr</a>
                <a href="/#" class="socials skype" title="Skype">skype</a>
                <a href="/#" class="socials linkedin" title="Linkedin">linkedin</a>
                -->
                </p>
              </div>
            </div>
            <!-- END COPYRIGHT -->
            </div> <!-- end wrapper -->
          </div></div>';
        echo
          "<div id='fb-root'></div>
             <script>
              window.fbAsyncInit = function() {
                FB.init({
                  appId   : '".get_fb_appid()."',";
        $session =  get_fb_session();
        if ($session) {
          echo "session : ".get_fb_session().",";
        }

        echo "    status  : true, // check login status
                  cookie  : false, // enable cookies to allow the server to access the session
                  xfbml   : true // parse XFBML
                });

                // whenever the user logs in, we refresh the page
                FB.Event.subscribe('auth.login', function() {
                  window.location.reload();
                });
              };

              (function() {
                var e = document.createElement('script');
                e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
                e.async = true;
                document.getElementById('fb-root').appendChild(e);
              }());
            </script>
        ";
        echo self::getFeedbackWidget();
        echo '</body></html>';
        }

        public static function getFeedbackWidget() {
return "
<script type='text/javascript'>
            var uvOptions = {};
          (function() {
            var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
            uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/fju00v2OqYSVfqqJCHbSpg.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
          })();
</script>";
        }

        private static function getFBTrackingPixel() {
          return "
<script type='text/javascript'>
var fb_param = {};
fb_param.pixel_id = '6011592627670';
fb_param.value = '0.00';
fb_param.currency = 'USD';
(function(){
  var fpw = document.createElement('script');
  fpw.async = true;
  fpw.src = '//connect.facebook.net/en_US/fp.js';
            var ref = document.getElementsByTagName('script')[0];
          ref.parentNode.insertBefore(fpw, ref);
        })();
</script>
<noscript><img height='1' width='1' alt='' style='display:none' src='https://www.facebook.com/offsite_event.php?id=6011592627670&amp;value=0&amp;currency=USD' /></noscript>
";
        }

        private static function isUnderConstruction() {
          return false;
        }
}


?>