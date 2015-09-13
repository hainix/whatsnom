<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';
$spot_id = $_GET['s'];
$position = $_GET['p'];
if ($spot_id) {
  $spot = get_object($spot_id, 'spots');
  $fake_entry = array('position' => $position);
  echo
    Modules::listItem(
      $fake_entry,
      $spot,
      $placeholder = false,
      $editable = true
    );
}