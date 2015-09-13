<?php
include_once 'lib/core/page.php';
include_once 'lib/utils.php';

$html =
'
<div style="margin: 40px">
<h3>Dishoom\'s <span>Terms of Service</span></h3>
<br/>
<table><tr><td>'
  .render_local_image('legal.png')
.'</td><td style="padding: 20px;"><h4>It’s all fun and games until…</h4>
<p>
<br/>Let’s keep that “until” from happening. Please read the following terms and conditions of use (the “Terms of Service”). <b>By continuing to access or use the godsend that is Dishoom (including DishoomFilms.com, Dishoom.tv, and all other services provided by Dishoom LLC), you agree to the Terms of Service.</b>
</td></tr></table>
<br/>
If you don’t agree or can’t comply with ALL of what follows, YOU MAY NOT ACCESS OR USE DishoomFilms.com, Dishoom.tv, or any other service provided by Dishoom LLC. Maybe you can read a book instead?
<br/><br/>
1. These Terms of Service govern your access and use of DishoomFilms.com, Dishoom.tv, all other services provided by Dishoom LLC, and all content, information, data, and products provided therein. This is a legally binding agreement between you (yes…you) and Dishoom LLC. “You” includes, without limitation, the accidental browser, the repeat visitor, the diehard Dishoom fanatic; essentially, if you access or use any service provided by Dishoom LLC, you are agreeing to be bound by these Terms of Service.
<br/><br/>
2. These Terms of Service can (and will) be modified and reposted without notice at any time. Visit this page regularly to ensure that you keep abreast of any and all changes that may affect your access or use of the site. If you continue to access or use DishoomFilms.com, Dishoom.tv, or any other service provided by Dishoom LLC, you accept the revisions in there entirety, and you agree to comply with them.
<br/><br/>
3. Intellectual property is important. It ensures that Dishoom LLC continues to provide you with top-of-the-line content, entertainment, and fun. The content, information, data, and products contained within DishoomFilms.com, Dishoom.tv, and all other services provided by Dishoom LLC are protected by trademark and copyright laws. The “Dishoom” mark is a trademark of Dishoom LLC. All content, information, and data is protected by copyright and owned by Dishoom LLC. You agree to DishoomFilms.com, Dishoom.tv, all services provided by Dishoom LLC, and all content, information, data, and products provided therein lawfully and agree that you will not infringe the intellectual property rights of Dishoom LLC over all services it provides. None of said content, information, data, products, or services may be used in connection with any other product or service, in any manner that is likely to cause confusion among customers, or in any manner that disparages or discredits DishoomFilms.com, Dishoom.tv, any services provided by Dishoom LLC, or Dishoom LLC itself. This includes things like the use of robots, scripts, screen scraping or any other automated access of any Dishoom LLC product. We\'re watching. To keep it simple: don’t steal. What would maa say?
</p>
<br/>
<div style="float:right;"><h3>Any <span>questions</span>?</h3></div>

<br/><br/><small>
Hit us up: <a href="mailto:legal@dishoomfilms.com">legal@dishoomfilms.com</a>
 · Last updated on Friday, April 6, 2012.</small>
</div>
';

$page = new page();
$page->setContent($html)
->setTitle('Dishoom | Terms and Legal Stuff')
->render();

?>
