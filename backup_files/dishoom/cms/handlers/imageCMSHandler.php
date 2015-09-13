<?php
require_once('../lib/utils.php');
sleep(2);
global $link;


if (!$_POST['id']) {
  throw new Exception('no id wtf - '.$_POST['id']);
}

return update_cms_image($_POST['id'], $_POST);

function update_cms_image($id, $data) {
	global $link;
	slog($data);
	slog('id = '.$id);
        if (!$id) {
            die('no id');
        }

	$q = sprintf("UPDATE images set to_delete = 1 "
		."WHERE id = %d LIMIT 1",
		$id);
	$r = mysql_query($q);
	if (!$r) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}

	return $r;
}

function cln($t) {
	$t = stripslashes($t);
	return mysql_real_escape_string(trim($t));
}


function ar($t) {
	return $t ? mysql_real_escape_string(serialize($t)) : '';
}

?>