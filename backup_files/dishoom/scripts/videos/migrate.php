<?PHP
include_once '../lib/utils.php';
include_once '../script_lib.php';

$exits = 0;
do {
  $objects = get_objects_from_sql(
    sprintf("select * from videos where name like '%s' limit %d, %d",
            'Beautiful People - %',
            $i++,
            1));
  slog($objects);
  if (!$objects) {
    $exits++;
    if ($exits > 10) {
      hlog('[[script complete]]');
      exit(1);
    } else {
      continue;
    }
  }
  $object = head($objects);
  //  hlog($object);
  $id = $object['id'];
  $new_name = rem($object['name'], 'Beautiful People - ');
  update_object($object, 'name', $new_name);

} while (1);

function update_object($object, $new_field, $new_val, $type = 'videos') {
  $sql =
    sprintf("update ".$type." set ".$new_field." = '%s' where id = %d limit 1",
            $new_val,
            $object['id']);
  global $link;
  //$result = mysql_query($sql);
  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog($message);
    die(1);
  } else {
    hlog('saved data with sql '.$sql);
  }
}