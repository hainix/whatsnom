<?php
include_once 'lib/core/page.php';
include_once 'lib/utils.php';


$link = render_link('intern@dishoomfilms.com', 'mailto:intern@dishoomfilms.com');
$about_text =
'<h4><b>DEADLINE TO APPLY:  Sunday, October 21 at 11:59 PM PST.</b></h4>
<br/>
<p>Dishoom is on the hunt for talented and motivated college students to join our team as interns for a three-month period—from November 2012 to February 2013—with the possibility of an extension of the internship. All tasks and projects shall be completed remotely.</p>
<br/>
<h3>Why Dishoom?</h3>
<p>
Because, face it, you love Bollywood.</p>
<br/>
<p>Beyond that, you’ll have the opportunity to contribute to the energy and excitement of a real start-up; to apply your creativity and problem-solving skills in a highly responsive, dynamic environment; and to exchange ideas with top-notch engineering and design, business and strategy, creative and legal talent.</p>
<br/>
<p>While the internship is unpaid, we will provide substantial practical business experience, a favorable letter of recommendation, and the possibility of full-time work with Dishoom
</p>
<br/>

<h3>What\'s In An Internship?</h3>
<p>Dishoom is looking for creative, marketing, and engineering interns. Each intern (1) will be assigned 2-3 projects to be managed and completed over the course of the internship, and (2) will be assigned regular weekly tasks. Scheduling can be adjusted depending on the particular intern, but we anticipate the typical intern will perform approximately 5-10 hours of work per week.</p>
<br/>
<h4>Tasks that creative interns may perform include:</h4>
<ul>
<li>-Drafting news/feature stories, movie reviews, or commentary</li>
<li>-Blogging news or gossip stories</li>
<li>-Graphic design</li>
<li>-Video production</li>
<li>-Podcast recording</li>
</ul>
<br/>
<h4>Tasks that marketing interns may perform include:</h4>
<ul>
<li>-Online outreach </li>
<li>-Grassroots promotion</li>
<li>-Publicity-related design work</li>
</ul>
<br/>
<h4>Tasks that engineering interns may perform include:</h4>
<ul>
<li>-Developing personalized movie recommendation engine</li>
<li>-Integrating continuous music-video playlists</li>
<li>-Implementing memcache backed services</li>
<li>-Bringing your own products to life, start to finish</li>
</ul>
<br/>
<h3>Who Can Apply?</h3>
<p>All current undergraduate or graduate students with a passion for Bollywood and a commitment to quality work are eligible to apply. </p>
<br/>
<p>For the creative and marketing internships, experience relevant to the internship is a strong advantage, but not a prerequisite.<p>
<br/>
<p>For the engineering internship, applicants must meet the following qualifications:</p>
<ul>
<li>-One year web development experience</li>
<li>-Familiarity with Linux/Apache/MySQL/PHP stacks</li>
<li>-Self-driven and efficient development</li>
</ul>
<br/>

<h3>How Do I Apply?</h3>
<br/>
<h4>If you are applying for the <b>creative internship</b>, please email the following to '.$link
.'</h4><ol>
<li>A cover letter stating that you are applying for the creative internship, describing any relevant experience, and explaining how you can contribute to Dishoom</li>
<li>A resume</li>
<li>A sample of your previous work</li>
</ol>
<br/>

<br/>
<h4>If you are applying for the <b>marketing internship</b>, please email the following to '.$link.'</h4>
<ol>
<li>A cover letter stating that you are applying for the marketing internship, describing any relevant experience, and explaining how you can contribute to Dishoom</li>
<li>A resume</li>
</ol>
<br/>

<br/>
<h4>If you are applying for the <b>engineering internship</b>, please email the following to '.$link.'</h4>'
.'<ol>
<li>A cover letter stating that you are applying for the engineering internship, describing any relevant experience, and explaining how you can contribute to Dishoom</li>
<li>A portfolio highlighting web design</li>
<li>A resume</li>
</ol>
<br/>

';

$about_text = render_mentions_text($about_text);

$html =
'<div style="margin: 40px">
<h2>Dishoom <span>Internship Program</span></h2>
<br/>'
.$about_text
.'</div>';

$page = new page();
$page->setContent($html)
->setTitle('Dishoom | Internships')
->render();

?>
