<?php
include_once('../lib/utils.php');
if (!is_admin()) {
  go_404();
}

echo phpinfo();
?>