<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/litapp/LitAppUtils.php';

$list_id = idx($_GET, 'id');
if (!$list_id || !is_numeric($list_id)) {
  echo 'ERROR: unsupported list_id '.$list_id;
  die(1);
}

$list = idx(LitAppUtils::getLitListResponseForCity($city_id = null, 100), $list_id);
$list = ApiUtils::addListDataToLitList($list);

//slog($list);

$table_rows = array();
foreach ($list['entries'] as $spot) {
  $place = $spot['place'];
  $temp = array($place['id'], $place['name'], $place['rating']);
  $temp[] = $place['lit_hours'] ? 'yep' : 'no!';
  $temp[] = '<a href="r.php?sid='.$place['id'].'" target="_blank">edit</a>';

  $table_rows[] = $temp;
}

//slog($table_rows);

echo '<table border="2" cellpadding="10">';
foreach ($table_rows as $table_row) {
  echo '<tr><td>'.implode($table_row, '</td><td>').'</td></tr>';
}
echo '</table>';