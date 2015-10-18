<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/FacebookUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/include_constants.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/YelpUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ImageUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/Modules.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/RenderUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ListQuery.php';

 class Page {
  protected
    $content,
    $description = null,
    $extraHeadContent = null,
    $pageType,
    $query = null;

  public function setType($type) {
    $this->pageType = $type;
    return $this;
  }

  public function setQuery($query) {
    $this->query = $query;
    return $this;
  }

	public function setContent($content)  {
		$this->content = $content;
		return $this;
	}

	private function getDescription() {
    if ($this->isHomepage()) {
      return $this->getDefaultDescription();
    } else if ($this->query) {
      return $this->query->getTitle();
    }
    return $this->getDefaultTitle();
	}

  private function getSiteName() {
    return "What's Nom";
  }

  private function isHomepage() {
    return ($this->pageType == PageTypes::BROWSE
            && (!$_GET || !$this->query));
  }

  private function getDefaultTitle() {
    return $this->getSiteName()." ·Curated Top Lists";
  }

  private function getDefaultDescription() {
    return "Curated Top Lists, from Resident Experts";
  }

	private function getTitle() {
    if ($this->isHomepage()) {
      return $this->getDefaultTitle();
    } else if ($this->query && $this->pageType == PageTypes::BROWSE) {
      return $this->query->getTitle() .' · '.$this->getSiteName();
    }
    return $this->getDefaultTitle();
	}

  public function addHeadContent($head_content) {
    $this->extraHeadContent .= $head_content;
    return $this;
  }

	private function getHeadContent() {
    return $this->getBaseHeadContent() . $this->extraHeadContent;
	}

  private function getURL() {
    if (!$this->query) {
      return BASE_URL;
    }
    return BASE_URL.'?'.$this->query->getQueryString();
  }

  protected function getBaseHeadContent() {
    return
'<!-- Mobile Specific Metas
  ================================================== -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">'
      .'<title>'.$this->getTitle().'</title>
      <meta name="description" content="'.$this->getDescription().'">
      <meta property="og:site_name" content="'.$this->getSiteName().'"/>
      <meta property="fb:app_id" content="'.FacebookUtils::getAppID().'"/>
      <meta property="og:title" content="'.$this->getTitle().'" />
      <meta property="og:description" content="'.$this->getDefaultDescription().'" />'
//      <meta property="og:image" content="'.BASE_URL.'images/logo-1024x1024.jpg" />
.'<meta property="og:image" content="'.BASE_URL.'images/ads/wide_fb_share_banner.jpg" />'
      .'<meta property="og:type" content="website" />
      <meta property="og:url" content="'.$this->getURL().'" />'
.'<!-- JS -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="'.BASE_URL.'js/filter.js"></script>
<script src="'.BASE_URL.'js/jquery.fancybox.pack.js"></script>'
      .'<script>$(".lightbox").fancybox({
    openEffect: \'fade\',
        helpers : {
          title : {
            type : \'over\'
                }
          }
        });</script>'
.$this->getAnalyticsCode()
.'<!-- END JS -->'
.'<!-- CSS -->
  <link rel="stylesheet" href="'.BASE_URL.'css/base.css">
  <link rel="stylesheet" href="'.BASE_URL.'css/skeleton.css">
  <link rel="stylesheet" href="'.BASE_URL.'css/layout.css">
  <link rel="stylesheet" href="'.BASE_URL.'css/filter.css">
  <link rel="stylesheet" href="'.BASE_URL.'css/jquery.fancybox.css">
  <link rel="stylesheet" href="'.BASE_URL.'css/style.css">
  <!-- END CSS -->

  <!-- FONTS -->
  <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700|Pacifico" rel="stylesheet" type="text/css">
  <!-- END FONTS -->

  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <!-- Favicons
  ================================================== -->
  <link rel="shortcut icon" href="'.BASE_URL.'images/favicon.ico?v=3">
  <link rel="apple-touch-icon" href="'.BASE_URL.'images/apple-touch-icon.png">
  <link rel="apple-touch-icon" sizes="72x72" href="'.BASE_URL.'images/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="114x114" href="'.BASE_URL.'images/apple-touch-icon-114x114.png">';

  }

  private function getAnalyticsCode() {
    if (is_admin()) {
      return null;
    }
    // Google analytics
    return
"<script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                                                                     m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                                                                                                                        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-51945399-1', 'whatsnom.com');
    ga('send', 'pageview');

</script>";

  }

  private function getLoginModule() {
    $user = FacebookUtils::getUser();

    if (!$user) {
      $login_module =
        RenderUtils::renderExternalLink(
          'Sign In',
          FacebookUtils::getLoginURL()
        );
    } else {
      $logout_url = FacebookUtils::getLogoutURL();
      $profile_url = idx($user, 'profile_pic_url');
      if ($profile_url) {
        /*
        $login_module =
          '<ul class="profile-drop-menu">
             <li>
               <a href="#"><img src="'.$profile_url.'" /></a>
               <ul>
                 <li><a href="#">My Bookmarks</a></li>
                 <li><a href="#">My Lists</a></li>
                 <li>'.RenderUtils::renderExternalLink('Log out',
                                                       $logout_url).'</li>
               </ul>
            </li>
          </ul>'
          .'<script>
            $(document).ready(function() {$(".profile-drop-menu").dropit();});
           </script>';
        */
        $login_module =
          RenderUtils::renderLink(
            '<img src="'.$profile_url.'" />',
            'me'
          );

      } else {
        slog('error - no profile pic');
      }
    }

    return
      '<div class="login-module">
         <div class="profile-pic">'
          .$login_module
       .'</div>
      </div>';
  }

  protected function getHeader() {
    if ($this->pageType == PageTypes::DIALOG) {
      return null;
    }
    return $this->getDesktopHeader() . $this->getMobileHeader();
  }

  private function getMobileHeader() {
    $ret =
      '<div id="mobile-header" class="hide-on-desktop">'
      .'<div class="mobile-logo"><h1>'.RenderUtils::renderLink('Nom?', '').'</h1></div>'
       .'<div id="mobile-search-container" class="mobile-search-container">'
         .'<div class="mobile-search-form">'
           .'<img class="search-icon" src="'.BASE_URL.'images/search.png" />'
           .'<div class="search-summary inline">'
      . ($this->query ? $this->query->getShortTitle() : 'Search Top Spots')
             .'<div class="overlay-right-fade"></div>'
           .'</div>'
         .'</div>'
       .'</div>'
     .'</div>';
    $js =
      '<script>
         $("#mobile-search-container").click(function(e) {
           $("html, body").animate({
             scrollTop: $("#filter-form").offset().top
           }, 200);
         });
      </script>';
    return $ret . $js;
  }

  private function getDesktopHeader() {
    $ret = '<div id="header" class="hide-on-mobile ';
    if ($this->pageType == PageTypes::BROWSE
        || $this->pageType == PageTypes::ADD) {
      //$ret .= ' header-with-overlay ';
    }
    $ret .= '" >
    <div class="logo">
      <div class="logo-subtitle">
        Curated Top Lists
      </div>
      <h1>'.RenderUtils::renderLink('What\'s Nom?', '').'</h1>
    </div>'
    .$this->getLoginModule()
    .'</div>';
    return $ret;
  }

  public function render() {
    $html =
      '<!DOCTYPE html>
       <html xmlns="http://www.w3.org/1999/xhtml"
         xmlns:fb="http://ogp.me/ns/fb#"
         lang="en">
         <head>
           <base href="'.BASE_URL.'" />
           <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
           <meta http-equiv="Content-Style-Type" content="text/css">
           <meta http-equiv="Content-Language" content="en">'
           .$this->getHeadContent()
      .'</head><body>'
      .$this->getHeader()
      .'<div class="container">';

    // These need search forms and are hidden on mobile
    if ($this->pageType == PageTypes::ADD
        || $this->pageType == PageTypes::BROWSE) {
/*
      $html .=
        Modules::renderDesktopSearchForm(
          $this->query
        );
*/
    }
    $html .= $this->content
      .'</div>';
    $html .= $this->getFBMarkup();
    $html .= self::getFeedbackWidget();
    $html .= '</body></html>';
    echo $html;
  }

  public static function getFeedbackWidget() {
    return "
<script type='text/javascript'>
UserVoice=window.UserVoice||[];(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/FnUgVv3jrkw0NvBgwcJCGw.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})();
UserVoice.push(['set', {
  accent_color: '#6f5499',
  trigger_color: 'white',
  trigger_background_color: 'rgba(46, 49, 51, 0.6)'
}]);
UserVoice.push(['addTrigger', '#contact-us-link', { mode: 'satisfaction',  trigger_position: 'top-left'  }]);
UserVoice.push(['autoprompt', {}]);
</script>";
  }

  private function getFBMarkup() {
    return
    '<div id="fb-root"></div>
      <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId='.
      FacebookUtils::getAppID().'&version=v2.0";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, \'script\', \'facebook-jssdk\'));</script>';
  }

}


?>