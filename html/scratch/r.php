<?php
// example of how to use advanced selector features
include('simple_html_dom.php');
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/base.php';


function rem($text, $removals) {
  if (!is_array($removals)) {
    $removals = array($removals);
  }
  foreach ($removals as $removal) {
    $text = str_replace($removal, '', $text);
  }
  return trim($text);
}
$day_array =
array(
1 => 'M',
2 => 'Tu',
3 => 'W',
4 => 'Th',
5 => 'F',
6 => 'Sa',
7 => 'Su',
);

if (!$_GET || !isset($_GET['sid'])) {
  echo 'malformed';
  exit (1);
}

$spot_id = (int) $_GET['sid'];
//$spot = get_object($spot_id, 'spots');

/*
if (!$spot || !isset($spot['name']) || !$spot['name']) {
   echo 'no spot found';
   exit(1);
}
*/

$form =
'
<form method="post" action="r.php?sid='.$spot_id.'">
enter html code to parse for spot '.$spot_id.'<br/>
<textarea name="htmlinput" rows="20" cols="120">
</textarea>
<br/>
  <input type="radio" name="run" value="dry" checked>dry run<br>
  <input type="radio" name="run" value="write">write first entry<br>
  <input type="radio" name="run" value="overwrite">overwrite other entries

<br/>
<input type="submit" />
</form>
';
echo $form;


if ($_POST && isset($_POST["htmlinput"])) {
  echo '[[ parsing html input for run type: '.$_POST['run'].']]<br/>';
//   echo 'got input: <pre>' . $_POST["htmlinput"] . '</pre><br/>';

$html = str_get_html($_POST["htmlinput"]);

if ($html->find('div._kpj')) {
  echo '[[ found parseable html ]]<br/>';
} else {
  echo '[[ error finding valid html ]]<br/>';
  exit (1);
}

$encode_data = array();
$day_counter = 1;
foreach($html->find('div._kpj') as $day_row) {
echo $day_array[$day_counter] . ': ';
if (stripos($day_row->innertext, 'pick another day')
|| stripos($day_row->innertext, 'Not enough data yet for')) {
   echo 'closed';
   $encode_data[$day_counter] = null;
} else {
  foreach ($day_row->find('div') as $potential_div) {
    if (stripos($potential_div->class, '_Toj') != 0) {
      $bar_divs_container = $potential_div;
      break;
    }
  }
//  $bar_divs_container = $day_row->find('div', 0);

//  echo 'checking bar_divs_container: '.$bar_divs_container;

  $bar_row_array = array();
  foreach ($bar_divs_container->find('div') as $bar_div) {
  //echo $bar_div;
    if ($bar_div->class) {
      $text_num = (int) rem($bar_div->style, array('px;', 'height:'));
      if ($text_num < 1) {
        continue;
        //$bar_row_array[] =  'ERROR';
        //echo 'error processing<pre>'.$bar_div.'</pre><br/>';
      } else {
        $bar_row_array[] =  $text_num;
      }
    } else {
      $bar_row_array[] = 0;
    }
  }
  echo implode($bar_row_array, ','). '</br>';
  $encode_data[$day_counter] = $bar_row_array.'<br/>';
}
$day_counter++;
//echo   '<pre>'.$day_row->innertext.'</pre>';
echo "<br/>";
}
}

echo '[[parse complete]]<br/>';
echo '[[starting validation]]<br/>';
/*
$valid_bars_per_day = 18;
foreach ($encode_data as $day => $day_data) {
  if ($day_data !== null) {
    if (count($day_data) !== $valid_bars_per_day) {
      echo "ERROR: problem with day data, expected ".$valid_bars_per_day.' but got '
           . count($day_data)." for day ".$day."<br/>";
      exit(1);
    }
  }
}
*/
$blob = json_encode($encode_data, JSON_FORCE_OBJECT);
//$blob = json_encode($encode_data, JSON_NUMERIC_CHECK);
echo 'got blob: '.$blob.'<br/>';
echo '[[all data validated and ready to save]]<br/>';


if ($_POST && ($_POST['run'] == 'write' || $_POST['run'] == 'overwrite')) {
echo '[[ starting save ]]<br/>';
  $try_write = DataWriteUtils::updateSpotHours($spot_id, $blob, $_POST['run'] == 'overwrite');
  if ($try_write) {
    echo 'wrote to spot '.$spot_id.' with blob: '.$blob.'</br>';
} else {
    echo 'ERROR writing to spot '.$spot_id.' with blob: '.$blob.'</br>';
}
} else {
  echo 'dry run or not submitted, so exiting<br/>';
}
echo '[[ end save ]]<br/>';




?>