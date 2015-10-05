<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/cms/cms_lib.php';

/**
 *
 * HTML5 Image uploader with Jcrop
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2012, Script Tutorials
 * http://www.script-tutorials.com/
 */
define ('ASSET_MEDIA_LOCATION', '../../images/media/');
function uploadImageFile() { // Note: GD library is required for this function

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maxWidthOrHeight = 1200;
    //$iWidth = $iHeight = 200; // desired image result dimensions
    $iJpgQuality = 90;

    if ($_FILES) {

      // if no errors and size less than 1000kb
      if (! $_FILES['image_file']['error']
          && $_FILES['image_file']['size'] < 1000 * 1024) {
        if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
          // new unique filename
          $image_handle = time();
          $sTempFileName = ASSET_MEDIA_LOCATION . $image_handle;
          // move uploaded file into cache folder
          move_uploaded_file($_FILES['image_file']['tmp_name'], $sTempFileName);

          // change file permission to 644
          @chmod($sTempFileName, 0644);

          if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
            $aSize = getimagesize($sTempFileName); // try to obtain image info
            if (!$aSize) {
              @unlink($sTempFileName);
              return;
            }

            // check for image type
            switch($aSize[2]) {
            case IMAGETYPE_JPEG:
              $sExt = '.jpg';

              // create a new image from file
              $vImg = @imagecreatefromjpeg($sTempFileName);
              break;
            case IMAGETYPE_GIF:
                $sExt = '.gif';

                // create a new image from file
                $vImg = @imagecreatefromgif($sTempFileName);
                break;
            case IMAGETYPE_PNG:
              $sExt = '.png';

              // create a new image from file
              $vImg = @imagecreatefrompng($sTempFileName);
              break;
            default:
              @unlink($sTempFileName);
              return;
            }

            $source_width = (int)$_POST['w'];
            $source_height = (int)$_POST['h'];
            if ($source_width > $source_height) {
              // Landscape
              $new_width = $maxWidthOrHeight;
              $new_height = round($maxWidthOrHeight / $source_width * $source_height);
            } else {
              // Portrait
              $new_width = round($maxWidthOrHeight / $source_height * $source_width);
              $new_height = $maxWidthOrHeight;
            }


            // create a new true color image
            $vDstImg = @imagecreatetruecolor( $new_width, $new_height );

            // copy and resize part of an image with resampling
            imagecopyresampled($vDstImg, $vImg, 0, 0, (int)$_POST['x1'], (int)$_POST['y1'], $new_width, $new_height, $source_width, $source_height);

            // define a result image filename
            $sResultFileName = $sTempFileName . $sExt;

            // output image to file
            imagejpeg($vDstImg, $sResultFileName, $iJpgQuality);
            @unlink($sTempFileName);

            return $sResultFileName;
          }
        }
      }
    }
  }
}
$sImage = uploadImageFile();
$image_handle =     str_replace(ASSET_MEDIA_LOCATION, '', $sImage);
$image_id =
  preg_replace(
    '/[^0-9]/',
    '',
    $image_handle
  );

// Add to cms with source, if set
if ($image_handle && $image_id) {
  add_cms_media($image_id, $_POST['site']);
}

$handle_syntax = '{i:'.$image_handle.'}';
echo '
<!DOCTYPE html>
<html lang="en" >
    <head>
<title>Dishoom - Image Uplaoded</title>
        <link href="css/main.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="demo">
  <div class="bheader">
<div align="center">
<a href="http://www.dishoomfilms.com">
                <img src="http://www.dishoomfilms.com/images/logo/logo_140.png" />
</a>
</div>
    <h2>Attractive Person Saved</h2>
  </div>
  <div class="bbody">
<div align="center">
<div style="margin:20px;">
<h1>'.$handle_syntax.'</h1>
</div>
<img src="'.$sImage.'" />
<br/>
<ul><li>to reference this image in an article, use '.$handle_syntax
.'</li><li>to set this as the primary image handle, use '.$image_handle.'</li></ul>
 </div>
<br/>
<a href="index.html"><button type="button" name="" value="" class="css3button">Upload More</button></a>
</div>
  </body></html>';

function add_cms_media($id, $source_link = null) {
  global $link;
  if ($source_link) {
    $sql = sprintf("INSERT INTO media (id, source_link) VALUES (".$id.", '%s')",
                   $source_link);
  } else {
    $sql = "INSERT INTO media (id) values (".$id.")";
  }
  $r = mysql_query($sql);
  if (!$r) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    die($message);
  }
}
