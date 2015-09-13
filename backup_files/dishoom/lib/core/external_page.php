<?php

include_once 'page.php';

class ExternalPage extends page {
  private $src, $altSrc;

  public function __construct($src, $alt_src = null) {
    $this->src = $src;
    $this->altSrc = $alt_src;
  }

  public function getIframeSrc() {
    return $this->src;
  }

  protected function getMenuNav() {
    if ($this->altSrc) {
      return
        '<div id="nav" class="group trouble-loading">'
        .render_forced_external_link('[?] <small>Not Loading?</small>',
                                    $this->altSrc)
        .'</div>';
    } else {
      return parent::getMenuNav();
    }
  }

  public function render() {
    $html =
      '<!DOCTYPE html>
       <html xmlns="http://www.w3.org/1999/xhtml"
         xmlns:fb="http://ogp.me/ns/fb#"
         lang="en">
         <head>
           <meta name="viewport" content="width=device-width">
           <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
           <meta http-equiv="Content-Style-Type" content="text/css">
           <meta http-equiv="Content-Language" content="en">
          '.$this->getBaseHeadContent()
         .'<link rel="stylesheet" type="text/css" href="'.BASE_URL.'css/external.css" media="all">'

         // window resize
         .'<script type="text/javascript" src="'.BASE_URL.'js/jquery.1.js"></script>'
       .'</head>'
       .'<body>
           <div id="dish-external-wrapper" class="group">
             <div id="dish-external-container" class="group">
               <div id="logo-wrap" class="float-left">
                 <a href="/" class="dish-logo">Dishoom</a>
               </div>'
               .$this->getMenuNav()
      //              .'<a class="close">x</a>
           .'</div>
           </div>
           <iframe
             class="dish-external-resize"
             id="dish-external-frame"
             src="'.$this->getIframeSrc().'"
             frameborder="0"
             noresize="noresize"
             onload="rimitdish();"
             marginheight="0"
             marginwidth="0"
             width="100%"
             height="900px"
           >
           Onoes! Your browser does not support iframes.
           Check out : <a href="'.$this->getIframeSrc().'">'
           .$this->getIframeSrc().'</a>
         </iframe>
       </body>
     </html>';
    echo $html;
  }

}