<?php
require_once('../lib/utils.php');
sleep(2);
global $link;


if (!$_POST['id']) {
  throw new Exception('no id wtf - '.$_POST['id']);
}

return update_profile_cms_image($_POST['id']);

function update_profile_cms_image($id) {
	global $link;
	slog($data);
	slog('id = '.$id);
        if (!$id) {
            die('no id');
        }

	$q = sprintf("UPDATE images set is_profile = 1 "
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

?>