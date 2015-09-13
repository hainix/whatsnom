<?php
define('THMB_SIZE', 40);
define('GRID_MAX_SIZE', 30);
define('PHOTO_GRID_MAX_SLIDES', 1);

 class ProfilePic {
  private $id, $type, $height, $width = 100, $shouldCrop, $numPhotos = false,
    $linked = true, $isThumb = false;
  public function __construct($id, $type) {
    $this->id = $id;
    $this->type = $type;
  }

  public function setIsThumb($thumb) {
    $this->isThumb = true;
    return $this;
  }

  public function setLinked($linked) {
    $this->linked = $linked;
    return $this;
  }

  public function setNumPhotos($num_pics) {
    $this->numPhotos = $num_pics;
    return $this;
  }

  public function setHeight($height) {
    $this->height = $height;
    return $this;
  }

  public function setWidth($width) {
    $this->width = $width;
    return $this;
  }

  public function setCropped($crop) {
    $this->shouldCrop = $crop;
    return $this;
  }

  public function getWidth() {
    return $this->width;
  }

  public function getHeight() {
    return $this->height;
  }

  // Special logic, including ids we have multiple
  // pics for, etc
  public function getPersonPath() {
    if ($this->numPhotos) {
      $rand_id = mt_rand(0, $this->numPhotos - 1);
    } else {
      $rand_id = 0;
    }
    return $this->id . '_' . $rand_id;
  }

  public function getSrc() {
    if ($this->type == 'person') {
      $path = self::getPersonPath();
    } else {
      $path = $this->id;
    }

    $src = MEDIA_BASE.$this->type.'/'.$path.'.jpg';

    if ($this->shouldCrop) {
      return ImageUtils::resizeCroppedSrc($src,
                                          $this->getDimensions());
    } else {
      return ImageUtils::resizeSrc($src,
                                   $this->getDimensions());
    }
  }

  private function getDimensions() {
      return
        array('width' => $this->getWidth(),
              'height' => $this->getHeight());
  }

  public function render() {
    $src = $this->getSrc();
    $img = '<img src="' . $src . '" ';
    if ($this->isThumb) {
      $img .= ' class="thumb_image" ';
    }
    if ($this->getHeight()) {
      $img .= ' height="'.$this->getHeight().'px" ';
    }

    if ($this->getWidth()) {
      $img .= ' width="'.$this->getWidth().'px" ';
    }

    $img .= ' />';
    if ($this->linked) {
      $fake_obj = array('id' => $this->id, 'type' => $this->type);
      return render_object_link_no_hovercard($fake_obj, $img);
    }
    return $img;
  }
}

final class ImageUtils {
  public static function resizeCroppedSrc($src,  $dimensions) {
    $width = $dimensions['width'];
    $height = idx($dimensions, 'height');
    $src = urlencode($src);
    $ret =
      BASE_URL.'phpthumb/phpThumb.php?src='.$src.'&zc=1';
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



function get_cropped_profile_pic_src($object, $dimensions) {
  return _get_profile_pic_src_helper($object,
                                     $object['type'],
                                     idx($dimensions, 'width',
                                         THMB_SIZE),
                                     true,
                                     idx($dimensions, 'height'));
}


function get_profile_pic_src($id_or_object, $type,
                             $dimensions) {
  return _get_profile_pic_src_helper($id_or_object,
                                     $type,
                                     idx($dimensions, 'width',
                                         THMB_SIZE),
                                     false,
                                     idx($dimensions, 'height'));
}

function render_profile_pic_square($id_or_object, $type, $width = THMB_SIZE) {
  $id = is_array($id_or_object) ? $id_or_object['id'] : $id_or_object;
  $pic = new ProfilePic($id, $type);

  if (is_array($id_or_object)
      && idx($id_or_object, 'num_photos')
      && $id_or_object['num_photos'] > 1) {
    $pic->setNumPhotos($id_or_object['num_photos']);
  }

  return
    $pic
    ->setLinked(true)
    ->setIsThumb(true)
    ->setCropped(true)
    ->setWidth($width)
    ->setHeight($width)
    ->setLinked(true)
    ->render();
}

function render_profile_pic($id_or_object, $type, $dimensions = null) {
  return render_image_helper(get_profile_pic_src($id_or_object,
                                                 $type,
                                                 $dimensions),
                             true,
                             false,
                             $dimensions);
}

function render_linked_profile_pic($id_or_object, $type) {
  $tag = render_profile_pic($id_or_object, $type);
  switch ($type) {
    case 'person': return render_person_link_no_hovercard(array('id' => $id), $tag);
    case 'film': return render_film_link_no_hovercard(array('id' => $id), $tag);
  }
}

function _get_profile_pic_src_helper($id_or_object, $type, $scale, $crop, $height) {
  $num_photos = null;
  if (is_array($id_or_object)) {
    $id = $id_or_object['id'];
    if (idx($id_or_object, 'num_photos') && $id_or_object['num_photos'] > 1) {
      $num_photos = $id_or_object['num_photos'];
    }
  } else if (is_numeric($id_or_object)) {
    $id = $id_or_object;
  } else {
    slog('malformed id of type '.$type);
    return false;
  }

  $profile_pic = new ProfilePic($id, $type);
  if ($scale) {
    $profile_pic->setWidth($scale);
  }

  if ($height) {
    $profile_pic->setHeight($height);
  }

  if ($num_photos) {
    $profile_pic->setNumPhotos($num_photos);
  }

  if ($crop) {
    $profile_pic->setCropped(true);
  }

  return $profile_pic->getSrc();

}

function render_local_image($path, $dimensions = null) {
  return render_image_helper($path, false, true, $dimensions);
}

function render_thumb_image($path, $dimensions = null) {
  return render_image_helper($path, true, FALSE, $dimensions);
}

function render_image($path, $dimensions = null, $thumb = false) {
  return render_image_helper($path, $thumb, false, $dimensions);
}

function render_image_helper($path, $thumb, $rel, $dimensions) {
  $path = $rel ? BASE_URL.'images/'.$path : $path;

  $img = '<img src="' . $path . '" ';
  /*
  if ($thumb) {
    $img .= ' class="thumb_image" ';
  }

  if ($dimensions && idx($dimensions, 'height') && idx($dimensions, 'width')) {
    $img .= ' height="'.$dimensions['height'].'px" '
      .'width="'.$dimensions['width'].'px" ';
  }
  */

  $img .= ' />';
  return $img;
}

function url_exists($url, $timeout = 2) {
  if ($timeout) {
    $hdrs = get_headers_curl($url, $timeout);
  } else {
    $hdrs = @get_headers($url);
  }
  return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
}

function get_headers_curl($url, $timeout = 15) {
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL,            $url);
  curl_setopt($ch, CURLOPT_HEADER,         true);
  curl_setopt($ch, CURLOPT_NOBODY,         true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT,        $timeout);

  $r = curl_exec($ch);
  $r = split("\n", $r);
  return $r;
}


function get_gallery($images) {
    // images should have thumb, src, title
    $name = 'gallery_'.rand(0,999);
    // TODO make this grid
    $pics = array();
    $images = array_slice($images, 0, PHOTO_GRID_MAX_SLIDES * GRID_MAX_SIZE);
    foreach ($images as $image) {
      $thumb = $image['thumb'];
      $pics[] =
        render_forced_external_link(render_image($thumb),
                                    $image['src'],
                                    array('class' => 'fancybox-thumb',
                                          'rel' =>   'fancybox-thumb',
                                          //			  'title' => $image['title']
                                    ));

    }
    return implode('', $pics);
}


?>