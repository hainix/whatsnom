<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/utils.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/core/external_page.php';

$src = idx($_GET, 's');
$alt_src = idx($_GET, 'a');
if (!$src) {
  go_404();
}

if ($alt_src) {
  $alt_src = urldecode($alt_src);
}

$page = new ExternalPage(urldecode($src), $alt_src);
$page->render();

