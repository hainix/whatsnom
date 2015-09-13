<?php
$root = '/var/www/html/';
//include_once $root.'lib/utils.php';
include_once $root.'lib/core/twitter.php';
include_once $root.'scripts/script_lib.php';


// Updates twitter handles for a bunch of users
set_time_limit(0);
ini_set('memory_limit', '60M');
global $link;

$vars = parse_args($argv);
hlog($vars);

hlog('[[updating tweets...]]');
$start = idx($vars, 's', 0);

$i = $start;
$exits = 0;
$max_entries_sql = "select id from people where deleted is null "
  ."and twitter is not null";
$result = mysql_query($max_entries_sql);
$max_iterations = mysql_num_rows($result);
hlog('doing max of '.$max_iterations.' iterations');
$client = new TwitterClient();

do {
  $sql =
    sprintf("select id, twitter from people where deleted is null "
            ."and twitter is not null order by rating DESC limit %d, %d",
					  $i++, 1);

  $objects = get_objects_from_sql($sql);

  if ($i >= $max_iterations + 3) {
    hlog('[[ should\'t run any more, reached max iterations, exiting ]]');
    log_exit($i);
    exit(1);
  }

  if (!$objects) {
    $exits++;
    if ($exits > 10) {
      log_exit($i);
      hlog('[[script complete at i = '.$i.']]');
      exit(1);
    } else {
      continue;
    }
  }

  $object = head($objects);

  $id = $object['id'];
  $handle = $object['twitter'];
  if (!$handle) {
    hlog('no handle');
    continue;
  }

  hlog('['.$i.'] fetching tweets for id '.$id.', handle '.$handle);

  $tweets = $client->getTweets($handle);
  if (!$tweets) {
    continue;
    hlog('**** no tweets, aborting *** ');
    exit(1);
  }


  hlog('['.$i.'] starting update for id '.$id);

  foreach ($tweets as $tweet) {
    $sql = sprintf("insert ignore into tweets (person_id, timestamp, message)"
                   ." values (%d, %d, '%s')",
                   $id,
                   $tweet['time'],
                   tr($tweet['message']));

    hlog($sql);
    $result = mysql_query($sql);
    $result = true;
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      hlog('[err]--'.$message);
    } else {
      hlog('--- saved to db for id '.$id.' with new fields');
    }
    unset($result);
    sleep(3);
  }

  // don't overflow client, or get us banned
  hlog('sleeping !');
  for ($sleep_count = mt_rand(20, 60); $sleep_count; $sleep_count--) {
    sleep(1);
    echo $sleep_count.' ';
  }
  hlog('sleeping complete!');
} while (1);


function log_exit($count) {
  $sql = sprintf("insert into revisions (type, changes)"
                 ." values ('%s', '%s')",
                 'tweets',
                 'updated '.$count.' entries');

  hlog($sql);
  $result = mysql_query($sql);
}

?>