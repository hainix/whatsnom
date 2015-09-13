<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/core/page.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/utils.php';

$html =
'
<div style="margin: 40px">
<!--<h1><span>Ratings Explained</span></h1>-->
<h3>How do you rate movies?</h3>
<br/>
<p>Glad you asked. Movies on Dishoom are rated on a 100-point scale. We compile reviews from Bollywood’s most respected critics, add our two cents, and take a weighted average of these grades to produce the Internet’s smartest rating.'
  .render_mentions_text(' For example, the classic, {Sholay:f:58541}, is a 100-point movie, while {Ram Gopal Varma’s despicable remake of Sholay:f:3223} is a 1-point movie.</p>')
  /*
.'<br/>
<br/>
<h3>How about everything else?</h3>
<br/>
<p>We love numbers. That\'s why we\'ve also rated each star, song, and video on our site, using the same 100-point scale. The higher the number, the better the content.</p>

  */
.'<br/><h4>Our goal is to inform you <span>what is worth your time</span> and what isn’t.</h4>'
  /*
<br/>

<p>Here are the criteria we use to rate our content:</p>
<br/>
<table>
    <colgroup>
        <col width="270px">
        <col width="200px">
        <col width="200px">
    </colgroup>
<tr valign="top" style="text-align: center;">
<td>
<h4>'.render_link('Stars', 'stars.php').'</h4>
<ul>
  <li>relevance</li>
  <li>popular appeal</li>
  <li>contribution to Bollywood</li>
  <li>box office impact</li>
  <li>talent</li>
</ul>
</td>
<td>
<h4>'.render_link('Songs', 'tv/?channel=songs').'</h4>
<ul>
  <li>popularity</li>
  <li>significance</li>
  <li>quality</li>
</ul>
</td>
<td>
<h4>'.render_link('Videos', 'tv').'</h4>
<ul>
  <li>relevance</li>
  <li>entertainment value</li>
</ul>
</td>
</tr>
</table>
  */

.'<br/>
<br/>
<div style="float:right; text-align:right; ">

<h4>Who knew numbers could be so <span>fun</span>?</h4>'
.'</div>
<br/>
<br/>
</div>
';

$page = new page();
$page->setContent($html)
->setTitle('Dishoom | Ratings Explained')
->render();

?>
