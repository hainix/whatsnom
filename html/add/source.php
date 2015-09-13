<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/base.php';
$city_id = $_GET['c'];
$query = isset($_GET['query']) ? $_GET['query'] : null;

// TODO cache
$sql =
  sprintf(
    "SELECT id, name, address from spots where deleted is NULL and city_id = %d "
    ."ORDER BY review_count DESC",
    $city_id
  );

$r = mysql_query($sql);
if (!$r) {
  //  echo mysql_error();
  return null;
}

$rows = array();
if (mysql_num_rows($r) > 0) {
  while ($row = mysql_fetch_assoc($r)) {
    $rows[$row['id']] = $row;
  }
}


$render_rows = array();
$limit = 20;
foreach ($rows as $id => $spot) {
  if (!$query ||
      stristr(clean_search_query($spot['name']), clean_search_query($query))) {
    $key = clean_search_query($spot['name']);
    $clean_address = null;
    if (isset($render_rows[$key])) {
      // Add address to first instance, if needed
      if (!$render_rows[$key]['address']) {
        $duplicate_address =
          preg_replace(
            '/\s*\d+$/',
            '',
            $rows[$render_rows[$key]['id']]['address']
          );
        $render_rows[$key]['address'] = $duplicate_address;
      }

      // Add address to duplicate
      $clean_address = preg_replace( '/\s*\d+$/', '', $spot['address']);
      $key .= clean_search_query($clean_address);
    }

    $render_rows[$key] = array(
      'id' => $id,
      'name' => $spot['name'],
      'address' => $clean_address,
    );
  }
  if (count($render_rows) == $limit) {
    break;
  }
}

echo
'{
  "suggestions": [';
$i = 0;
foreach ($render_rows as $render_row) {
  $i++;
  $address = null;
  if ($render_row['address']) {
    $address = ' ('.$render_row['address'].')';
  }
  echo '{ "value": "'.$render_row['name'].$address
    .'", "data": "'.$render_row['id'].'" }';
  if (count($render_rows) != $i) {
    echo ', ';
  }
}
echo '    ]
}';


function clean_search_query($string) {
  return preg_replace('/\s+/', '', $string);
}