<?php
include_once 'lib/core/page.php';
include_once 'lib/utils.php';

$about_text =
'<p>We’re looking for Bollywood maniacs to join our team of…well…Bollywood maniacs.

Currently, we’re accepting applications for the following positions:</p>
<br/>
<table>
    <colgroup>
        <col width="150px">
        <col width="*">
    </colgroup>
<tr>
<td>'
  .render_local_image('write.png')
.'</td>
<td>
<h3>Creative Partner</h3>
<p>Bold and creative voices make Dishoom what it is. If you enjoy formal writing, blogging, graphic designing, video production, or podcast recording, Dishoom wants to hear from you.
<br/>
<span style="font-size:10px;">Send us (1) a professional resume and (2) a cover letter outlining how you’d like to contribute to Dishoom.</span></p>
</td>
</tr>
</table>

<br/>
<table>
    <colgroup>
        <col width="150px">
        <col width="*">
    </colgroup>
<tr>
<td>'
  .render_local_image('computer.png')
.'</td>
<td>
<h3>Software Engineer</h3>
<p>Our engineers work in very small teams to ship code, iterate quickly and stay clean. Impact is unmatched. Quality and Performance are at our core. Must have 5+ years software experience.

<br/>
<span style="font-size:10px;">Send us (1) a professional resume and (2) a cover letter outlining how your experience and technical ability will help you hit the ground running at Dishoom.</span></p>
</td></tr></table>

<br/>
<table>
    <colgroup>
        <col width="150px">
        <col width="*">
    </colgroup>
<tr>
<td>'
  .render_local_image('college.png')
.'</td>
<td>
<h3>Internships</h3>
<p>Dishoom is on the hunt for talented and motivated college students to join our team as interns for a three-month period with the possibility of an extension of the internship.
<br/>
<span style="font-size:10px;">'.render_link('Click here', 'internships.php').' for more information on the Dishoom Internship Program.</span></p>
</td>
</tr>
</table>
<br/>

<p>Finally, if you want to contribute in a way not mentioned above, drop us a line. We’re {waiting:s:7913} for you.

Please send all correspondence to our recruiting team at '
  .render_link('jobs@dishoomfilms.com', 'mailto:jobs@dishoomfilms.com').'</p>';

$about_text = render_mentions_text($about_text);

$html =
'<div style="margin: 40px">
<h2>Join <span>Dishoom</span></h2>
<br/>'
.$about_text
.'</div>';

$page = new Page();
$page->setContent($html)
->setTitle('Dishoom | Join Us')
->render();

?>
