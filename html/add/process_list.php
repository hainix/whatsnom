<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';

define('MIN_ENTRIES_FOR_LIST', 3); // TODO increase
define('ADD_SPOT_DEBUG', false);
$user = FacebookUtils::getUser();
if (!$user) {
  RenderUtils::go404();
}

$creator_id = $_POST['creator_id'];
unset($_POST['creator_id']);
if ($user['id'] != $creator_id) {
  RenderUtils::go404();
}

$list_id = (int) idx($_POST, 'list_id');
if ($list_id) {
  unset($_POST['list_id']);
} else {
  $qualifier_id = (int) $_POST['qualifier_id'];
  $type_id = (int) $_POST['type_id'];
  $city_id = (int) $_POST['city_id'];
  unset($_POST['qualifier_id']);
  unset($_POST['type_id']);
  unset($_POST['city_id']);
}

$spots = array();
$tips = array();
$max_spot_position = 0;
foreach ($_POST as $field => $value) {
  if (!$value
      || (!strstr($field, 'spot_') && !strstr($field, 'tip_'))) {
    continue;
  }

  $position = (int) preg_replace("/[^0-9]/","",$field);
  if (strstr($field, 'spot_')) {
    if ($position > $max_spot_position) {
      $max_spot_position = $position;
    }
    $spots[$position] = $value;
  } else if (strstr($field, 'tip_')) {
    $tips[$position] = $value;
  } else {
    slog('unknown field '.$field.' with value '.$value);
  }
}

$error = null;
if (count($spots) < MIN_ENTRIES_FOR_LIST) {
  // TODO nice boxify
  $error = 'Rats! We need at least '.MIN_ENTRIES_FOR_LIST.' spots.';
} else if (count($spots) != count(array_flip($spots))) {
  slog($spots);
  $error = 'Oops! Looks like you repeated a spot.';
} else if ($max_spot_position != count($spots)) {
  $error = 'Whoops, make sure you fill out consecutive spots.';
}

if ($error) {
  echo '<div class="error">'.$error.'</div>';
  exit(1);
}

if (!$list_id) {
  $existing_list =
    DataReadUtils::getListForCreator(
      $qualifier_id,
      $type_id,
      $city_id,
      $creator_id
    );
  $list_id = idx($existing_list, 'id');
  if (ADD_SPOT_DEBUG) {
    slog('got existing list id = '.$list_id.' for '.$qualifier_id.' '.$type_id.' '.$city_id);
  }
  if (!$list_id) {
    $list_id =
      DataWriteUtils::createList(
        $qualifier_id,
        $type_id,
        $city_id,
        $creator_id
      );
    if (ADD_SPOT_DEBUG) {
      slog('created new list id = '.$list_id.' for '.$qualifier_id.' '.$type_id.' '.$city_id);
    }
  }
} else if (ADD_SPOT_DEBUG) {
  slog('editing existing list id '.$list_id);
}

if (!$list_id) {
  $error = 'Error Creating List';
  echo '<div class="error">'.$error.'</div>';
  exit(1);
}

foreach ($spots as $position => $spot_id) {
  // TODO (handle updating here)
  if (!DataWriteUtils::addEntryToList(
        $list_id,
        $position,
        $spot_id,
        idx($tips, $position)
      )) {
    if (ADD_SPOT_DEBUG) {
      slog('error inserting entry into list '.$list_id
           .' for position '.$position);
    }
    break;
  }
}

echo '<div class="success">Saved.</div>';
