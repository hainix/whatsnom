<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/utils.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/display/hovercard.php';

$id = idx($_GET, 'id');
$type = idx($_GET, 'type');
$show_name = idx($_GET, 'name');


echo render_hovercard_body(get_object($id, $type), $show_name);

?>