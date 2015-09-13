<?php

include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';

// this file takes the name of a movie and guesses its wikipedia link          
set_time_limit(0);
ini_set('memory_limit', '60M');
//$url = 'http://en.wikipedia.org/wiki/Bollywood_films_of_2011';
$url = 'http://en.wikipedia.org/wiki/Bollywood_films_of_2012';

$dom = file_get_html($url);
$ret = array();
foreach($dom->find('table[class=wikitable]') as $table ) {
    foreach ($table->find('tr') as $tr) {
      foreach ($tr->find('td') as $td) {
	if (!$td) {
	  continue;
	}
	$a = $td->find('a', 0);
	if ($a) {
	  $ret[] = array('handle' =>  rem($a->href, '/wiki/'),
			 'title' => $a->plaintext);
	  break;
	}
      }
    }
}
slog($ret);
foreach ($ret as $data) {
  add_wiki_to_db($data, 2012);
}

function add_wiki_to_db($data, $year) {
  $handle = $data['handle'];
  $title = $data['title'];
  if (!$handle) {
    return false;
  }
  $sql = sprintf("insert ignore into films (wiki_handle, year, title) "
                 ."values ('%s', %d, '%s')",
                 tr($handle),
                 $year,
		 tr($title));
  //$result = true;
  $result = mysql_query($sql);

  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog($message);
    return false;
  } else {
    echo $sql;
    echo "\n";
  }
  return true;

}

?>

