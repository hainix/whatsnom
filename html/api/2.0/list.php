<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/api/ApiUtils.php';

// Returns data for all spots in a given list

$list_id = idx($_GET, 'list_id');


if (!$list_id || !is_numeric($list_id)) {
  echo 'ERROR: unsupported list_id '.$list_id;
  die(1);
}

$list = get_object($list_id, 'lists');
$list = ApiUtils::addListDataToList($list);

$response = $list;

header('Cache-Control: no-cache, must-revalidate');
header('content-type: application/json; charset=utf-8');

$response =
'
[
{
    "id": 1,
    "name": "Quan An Ngon",
    "address": "18 Phan Boi Chau | Hoan Kiem Dist, Hanoi 100000, Vietnam",
    "phone": "84903246963",
    "location": {
      "lat": 21.030746,
      "lon": 105.811913,
      "distance": 3.2
    },
    "rating": 4.5,
    "scores": [
      {
        "id": 1,
        "name": "Foodies",
        "score": 98
      }
    ],
    "thumb": "assets/img/restaurant/thumb/img_1.jpg"
}
]
';


// JSONP
if (idx($_GET, 'format') == 'json') {
  echo $_GET['callback'] . '('.json_encode($response, JSON_NUMERIC_CHECK).')';
} else {
  slog($response);
}
