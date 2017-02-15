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

$str = <<< HTML
<div class="_qqi mod" data-md="123"><!--m--><div jsl="$t t-pwbuG-LbAt4;$x 0;" class="r-iJdFqHtyrZsU" data-hveid="181"><div class="_pk _bpj"><span>Popular times</span><div class="_opj"><div class="_npj ab_button iJdFqHtyrZsU-c2fYkqmTb0o" aria-selected="false" role="button" tabindex="0" jsaction="r.Vf5I25b6Mz4" data-rtid="iJdFqHtyrZsU" jsl="$x 1;" data-ved="0ahUKEwiRsIKs547SAhUGxCYKHdCCAO4QnmEItgEwHA"><span class="_cpj iJdFqHtyrZsU-EJkKu06Wjh4">Thursdays</span><span class="_dpj"></span></div><ul class="_Uoj iJdFqHtyrZsU-_AHwAYCueXs" style="display:none" role="menu" data-ved="0ahUKEwiRsIKs547SAhUGxCYKHdCCAO4QnWEItwEwHA"><li role="menuitem" tabindex="0" jsaction="r.xMTlHWFAkUQ" data-rtid="iJdFqHtyrZsU" jsl="$x 2;" data-day="1" class="">Mondays</li><li role="menuitem" tabindex="0" jsaction="r.xMTlHWFAkUQ" data-rtid="iJdFqHtyrZsU" jsl="$x 2;" class="" data-day="2">Tuesdays</li><li role="menuitem" tabindex="0" jsaction="r.xMTlHWFAkUQ" data-rtid="iJdFqHtyrZsU" jsl="$x 2;" data-day="3" class="">Wednesdays</li><li role="menuitem" tabindex="0" jsaction="r.xMTlHWFAkUQ" data-rtid="iJdFqHtyrZsU" jsl="$x 2;" data-day="4" class="lubh-sel">Thursdays</li><li role="menuitem" tabindex="0" jsaction="r.xMTlHWFAkUQ" data-rtid="iJdFqHtyrZsU" jsl="$x 2;" data-day="5" class="">Fridays</li><li role="menuitem" tabindex="0" jsaction="r.xMTlHWFAkUQ" data-rtid="iJdFqHtyrZsU" jsl="$x 2;" data-day="6" class="">Saturdays</li><li role="menuitem" tabindex="0" jsaction="r.xMTlHWFAkUQ" data-rtid="iJdFqHtyrZsU" jsl="$x 2;" data-day="0" class="">Sundays</li></ul></div><div class="_Voj"><g-bubble jsl="$t t-R7dwiTmE0C4;$x 0;" class="r-iFHkYDhQmsio"><a href="javascript:void(0)" data-theme="0" data-width="164" style="display:inline-block" class="g-bbll" aria-haspopup="true" role="button" jsaction="r.saTe4DDW138" data-rtid="iFHkYDhQmsio" jsl="$x 1;" data-ved="0ahUKEwiRsIKs547SAhUGxCYKHdCCAO4QoGEIuAEwHA"><div class="_vpj" aria-label="More info"></div></a><div class="g-bblc"><div class="_upj">Based on visits to this place.</div></div></g-bubble></div></div><div class="_lpj"></div><div class="_qpj"><div role="button" tabindex="0" jsaction="r.YGfOKxUfYe4" data-rtid="iJdFqHtyrZsU" jsl="$x 3;" class="_Woj"><div class="_gpj _Zoj" data-ved="0ahUKEwiRsIKs547SAhUGxCYKHdCCAO4Q1XQIuQEwHA"></div></div><div role="button" tabindex="0" jsaction="r.PaYB1-SNSug" data-rtid="iJdFqHtyrZsU" jsl="$x 4;" class="_Yoj"><div class="_gpj _apj" data-ved="0ahUKEwiRsIKs547SAhUGxCYKHdCCAO4Q1XQIugEwHA"></div></div><div class="_ppj iJdFqHtyrZsU-JjrbqILQZ8E" style="height: 107px;"><div class="_kpj _ENk iJdFqHtyrZsU-6IfGnM2WAv8" aria-label="" tabindex="-1" aria-hidden="true" style="position: absolute; display: none; transform: translate3d(-100%, 0px, 0px); padding-top: 11px;"><div data-day="1" class="_wpj">Closed Mondays—pick another day</div></div><div class="_kpj _ENk iJdFqHtyrZsU-6IfGnM2WAv8" aria-label="Histogram showing popular times on Tuesdays" tabindex="-1" aria-hidden="true" style="position: absolute; display: none; transform: translate3d(-100%, 0px, 0px);"><span class="_kjk iJdFqHtyrZsU-MIB3RsZcA1s"><span class="_jjk">peak</span></span><div class="iJdFqHtyrZsU-pgwF18xgFOM _Toj"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div class="lubh-bar" style="height:4px"></div><div class="lubh-bar lubh-sel" style="height:10px"></div><div class="lubh-bar" style="height:12px"></div><div class="lubh-bar" style="height:8px"></div><div class="lubh-bar" style="height:3px"></div></div><div class="iJdFqHtyrZsU-17AgC1q3TaA _epj"><div><div class="_ipj"><div>6a</div></div></div><div></div><div></div><div><div class="_ipj"><div>9a</div></div></div><div></div><div></div><div><div class="_ipj"><div>12p</div></div></div><div></div><div></div><div><div class="_ipj"><div>3p</div></div></div><div></div><div></div><div><div class="_ipj"><div>6p</div></div></div><div></div><div></div><div><div class="_ipj"><div>9p</div></div></div><div></div><div></div><div><div class="_ipj"><div>12a</div></div></div><div></div><div></div><div><div class="_ipj"><div>3a</div></div></div></div></div><div class="_kpj _ENk iJdFqHtyrZsU-6IfGnM2WAv8" aria-label="" tabindex="-1" aria-hidden="true" style="position: absolute; display: block; transform: translate3d(-100%, 0px, 0px); padding-top: 11px;"><div data-day="3" class="_wpj">Closed Wednesdays—pick another day</div></div><div class="_kpj _ENk iJdFqHtyrZsU-6IfGnM2WAv8" aria-label="Histogram showing popular times on Thursdays" tabindex="-1" aria-hidden="false" style="position: static; display: block; transform: translate3d(0%, 0px, 0px);"><span class="_kjk iJdFqHtyrZsU-MIB3RsZcA1s"><span class="_jjk">peak</span></span><div class="iJdFqHtyrZsU-pgwF18xgFOM _Toj"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div class="lubh-bar" style="height: 16px;"></div><div class="lubh-bar" style="height: 35px;"></div><div class="lubh-bar" style="height: 60px;"></div><div class="lubh-bar" style="height: 60px;"></div><div class="lubh-bar" style="height: 32px;"></div></div><div class="iJdFqHtyrZsU-17AgC1q3TaA _epj"><div><div class="_ipj"><div>6a</div></div></div><div></div><div></div><div><div class="_ipj"><div>9a</div></div></div><div></div><div></div><div><div class="_ipj"><div>12p</div></div></div><div></div><div></div><div><div class="_ipj"><div>3p</div></div></div><div></div><div></div><div><div class="_ipj"><div>6p</div></div></div><div></div><div></div><div><div class="_ipj"><div>9p</div></div></div><div></div><div></div><div><div class="_ipj"><div>12a</div></div></div><div></div><div></div><div><div class="_ipj"><div>3a</div></div></div></div></div><div class="_kpj _ENk iJdFqHtyrZsU-6IfGnM2WAv8" aria-label="Histogram showing popular times on Fridays" tabindex="-1" aria-hidden="true" style="position: absolute; display: block; transform: translate3d(100%, 0px, 0px);"><span class="_kjk iJdFqHtyrZsU-MIB3RsZcA1s"><span class="_jjk">peak</span></span><div class="iJdFqHtyrZsU-pgwF18xgFOM _Toj"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div class="lubh-bar" style="height: 17px;"></div><div class="lubh-bar" style="height: 39px;"></div><div class="lubh-bar" style="height: 56px;"></div><div class="lubh-bar" style="height: 54px;"></div><div class="lubh-bar" style="height: 34px;"></div></div><div class="iJdFqHtyrZsU-17AgC1q3TaA _epj"><div><div class="_ipj"><div>6a</div></div></div><div></div><div></div><div><div class="_ipj"><div>9a</div></div></div><div></div><div></div><div><div class="_ipj"><div>12p</div></div></div><div></div><div></div><div><div class="_ipj"><div>3p</div></div></div><div></div><div></div><div><div class="_ipj"><div>6p</div></div></div><div></div><div></div><div><div class="_ipj"><div>9p</div></div></div><div></div><div></div><div><div class="_ipj"><div>12a</div></div></div><div></div><div></div><div><div class="_ipj"><div>3a</div></div></div></div></div><div class="_kpj _ENk iJdFqHtyrZsU-6IfGnM2WAv8" aria-label="Histogram showing popular times on Saturdays" tabindex="-1" aria-hidden="true" style="position: absolute; display: none; transform: translate3d(100%, 0px, 0px);"><span class="_kjk iJdFqHtyrZsU-MIB3RsZcA1s"><span class="_jjk">peak</span></span><div class="iJdFqHtyrZsU-pgwF18xgFOM _Toj"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div class="lubh-bar" style="height: 20px;"></div><div class="lubh-bar" style="height: 39px;"></div><div class="lubh-bar" style="height: 67px;"></div><div class="lubh-bar" style="height: 75px;"></div><div class="lubh-bar" style="height: 47px;"></div></div><div class="iJdFqHtyrZsU-17AgC1q3TaA _epj"><div><div class="_ipj"><div>6a</div></div></div><div></div><div></div><div><div class="_ipj"><div>9a</div></div></div><div></div><div></div><div><div class="_ipj"><div>12p</div></div></div><div></div><div></div><div><div class="_ipj"><div>3p</div></div></div><div></div><div></div><div><div class="_ipj"><div>6p</div></div></div><div></div><div></div><div><div class="_ipj"><div>9p</div></div></div><div></div><div></div><div><div class="_ipj"><div>12a</div></div></div><div></div><div></div><div><div class="_ipj"><div>3a</div></div></div></div></div><div class="_kpj _ENk iJdFqHtyrZsU-6IfGnM2WAv8" aria-label="" tabindex="-1" aria-hidden="true" style="position: absolute; display: none; padding-top: 11px; transform: translate3d(-100%, 0px, 0px);"><div data-day="0" class="_wpj">Closed Sundays—pick another day</div></div></div></div><div class="iJdFqHtyrZsU-94XBqNcr5rY _hpj"></div><div class="iJdFqHtyrZsU-54Dx67A-z9k _hpj"><div data-day="2">Histogram showing popular times on Tuesdays</div><div data-day="4">Histogram showing popular times on Thursdays</div><div data-day="5">Histogram showing popular times on Fridays</div><div data-day="6">Histogram showing popular times on Saturdays</div></div></div><!--n--></div>
HTML;

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
$html = str_get_html($str);

$day_counter = 1;
foreach($html->find('div._kpj') as $day_row) {
echo $day_array[$day_counter] . ': ';
if (stripos($day_row->innertext, 'pick another day')) {
   echo 'closed';
} else {
  $bar_divs_container = $day_row->find('div', 0);

  $bar_row_array = array();
  foreach ($bar_divs_container->find('div') as $bar_div) {
  //echo $bar_div;
    if ($bar_div->class) {
      $text_num = (int) rem($bar_div->style, array('px;', 'height:'));
      if ($text_num < 1) {
        $bar_row_array[] =  'ERROR';
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