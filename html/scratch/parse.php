<?php
// example of how to use advanced selector features
include('simple_html_dom.php');

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



$form =
"
<form method='post' action='parse.php'>
enter html code to parse:
<textarea name='htmlinput'>
</textarea>
<input type='submit' />
</form>
";
echo $form;


if ($_POST && isset($_POST["htmlinput"])) {
  echo '[[ parsing html input ]]<br/>';
//   echo 'got input: <pre>' . $_POST["htmlinput"] . '</pre><br/>';

$html = str_get_html($_POST["htmlinput"]);

if ($html->find('div._kpj')) {
  echo '[[ found parseable html ]]<br/>';
} else {
  echo '[[ error finding valid html ]]<br/>';
  exit (1);
}

$day_counter = 1;
foreach($html->find('div._kpj') as $day_row) {
echo $day_array[$day_counter] . ': ';
if (stripos($day_row->innertext, 'pick another day')) {
   echo 'closed';
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
}
$day_counter++;
//echo   '<pre>'.$day_row->innertext.'</pre>';
echo "<br/>";
}
}

echo '[[parse complete]]';


/*
// -----------------------------------------------------------------------------
// descendant selector
$str = <<<HTML
<div>
    <div>
        <div class="foo bar">ok</div>
    </div>
</div>
HTML;

$html = str_get_html($str);
echo $html->find('div div div', 0)->innertext . '<br>'; // result: "ok"

// -----------------------------------------------------------------------------
// nested selector
$str = <<<HTML
<ul id="ul1">
    <li>item:<span>1</span></li>
    <li>item:<span>2</span></li>
</ul>
<ul id="ul2">
    <li>item:<span>3</span></li>
    <li>item:<span>4</span></li>
</ul>
HTML;

$html = str_get_html($str);
foreach($html->find('ul') as $ul) {
    foreach($ul->find('li') as $li)
        echo $li->innertext . '<br>';
}

// -----------------------------------------------------------------------------
// parsing checkbox
$str = <<<HTML
<form name="form1" method="post" action="">
    <input type="checkbox" name="checkbox1" value="checkbox1" checked>item1<br>
    <input type="checkbox" name="checkbox2" value="checkbox2">item2<br>
    <input type="checkbox" name="checkbox3" value="checkbox3" checked>item3<br>
</form>
HTML;

$html = str_get_html($str);
foreach($html->find('input[type=checkbox]') as $checkbox) {
    if ($checkbox->checked)
        echo $checkbox->name . ' is checked<br>';
    else
        echo $checkbox->name . ' is not checked<br>';
}
*/
?>