<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/funcs.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/base.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/read.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';
$user_id = idx($_GET, 'uid');
$type = idx($_GET, 'type');
$target_id = idx($_GET, 'target_id');

// Only support spot bookmarks for now
if (!$user_id || !$target_id || !$type
    || !DataReadUtils::isSupportedAssoc($type)) {
  exit(1);
}

$existing_assoc = DataReadUtils::getAssoc($user_id, $target_id, $type, $bookmark_type);
$new_votes = null;
$new_votes_icon = null;

if ($existing_assoc && !$existing_assoc['deleted']) {
  DataWriteUtils::removeAssoc($user_id, $target_id, $type, $bookmark_type, $list_id);
  if ($type == 'bookmarks') {
    echo
      '<img src="'.BASE_URL.'images/heart.png" width="20px" height="20px"/>';
  } else if ($type == 'votes') {
    $list = get_object($target_id, 'lists');
    $new_votes = (int) DataWriteUtils::alterListVotes($list, -1);
    $new_votes_icon = 'star.png';
  }
} else {
  DataWriteUtils::addAssoc($user_id, $target_id, $type);
  if ($type == 'bookmarks') {
    echo
      '<img src="'.BASE_URL.'images/heart-saved.png" width="20px" height="20px"/>';
  } else if ($type == 'votes') {
    $list = get_object($target_id, 'lists');
    $new_votes = (int) DataWriteUtils::alterListVotes($list, 1);
    $new_votes_icon = 'star-saved.png';
  }
}

if ($type == 'votes') {
  echo
    '<img src="'.BASE_URL.'images/'.$new_votes_icon.'" class="inline small-icon" />'
    .'<strong>'.$new_votes .'</strong> ';
}
