<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';

set_time_limit(0);
ini_set('memory_limit', '60M');
global $link;

$vars = parse_args($argv);
hlog($vars);

$start = idx($vars, 's', 0);

$i = $start;
$exits = 0;
do {
  $objects = get_objects_from_sql(
				  sprintf("select * from reviews  LIMIT %d, %d",
					  $i++, 1));
  
  if (!$objects) {
    $exits++;
    if ($exits > 10) {
      hlog('[[script complete]]');
      exit(1);
    } else {
      continue;
    }
  }
  if (true || $i % 50 === 0) {
    //    sleep(2);
  }

  if (!$objects) {
    hlog('no objects, moving on');
    continue;
  }
  foreach ($objects as $object) {
    $new_excerpt = 
      convert_smart_quotes2($object['excerpt']);
    /*
    			    ucfirst(iconv('iso-8859-1', 'UTF-8', 
					  convert_smart_quotes2($object['excerpt']))));
    */    
    //    $new_excerpt = $object['excerpt'];
    $sql = sprintf("update reviews set excerpt = '%s' where id = %d LIMIT 1",
		   trim(mysql_real_escape_string($new_excerpt)),
		   $object['id']);
    hlog($sql);

    $result = mysql_query($sql);
    //$result = true;
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      hlog('[err]--'.$message);
    } else {
      hlog('--- saved to db for id '.$object['id'].' with new fields');
    }
    unset($result);
  }
} while (1);

function convert_smart_quotes2($string) {
  $search = array(chr(0xe2) . chr(0x80) . chr(0x98),
		  chr(0xe2) . chr(0x80) . chr(0x99),
		  chr(0xe2) . chr(0x80) . chr(0x9c),
		  chr(0xe2) . chr(0x80) . chr(0x9d),
		  chr(0xe2) . chr(0x80) . chr(0x93),
		  chr(0xe2) . chr(0x80) . chr(0x94),
		  chr(226) . chr(128) . chr(153),
		  'â€™','â€œ','â€<9d>','â€"','Â  ',
		  'Ã¢ÂÂ\200',
		  'Ã¢Â',
		  "'Â",
		  'Ã',
		  'Â',
		  'Â',
		  'Â´'
);

  $replace = array("'","'",'"','"',' - ',' - ',"'","'",'"','"',' - ',' ', "'", "'", "'", "'", "'", "'", "'");

  return str_replace($search, $replace, $string);
}
?>