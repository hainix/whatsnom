<?php
date_default_timezone_set('America/Los_Angeles');
define('BASE_URL', 'http://www.whatsnom.com/');

$link = mysql_connect('localhost', 'root', 'Dest1ny') or die("Cannot connect to the local database ".mysql_error());
mysql_select_db("nom") or die("Cannot select db");

?>