<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/ImageUtils.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);


$spot_id = 7286;
$spot = get_object($spot_id, 'spots');
slog($spot);

$old_src = $spot['profile_pic'];


$src = ImageUtils::resizeCroppedSrc($old_src, array('width' => 450, 'height' => 150));

echo 'new src:';
echo '<img src="'.BASE_URL.$src.'" /><br/><br/>';

echo 'old src:';
echo '<img src="'.$old_src.'" />';
