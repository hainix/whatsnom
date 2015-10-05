<?php
include_once 'lib/core/page.php';
include_once 'lib/utils.php';
$about_text =
'<br/><p>If so, then welcome! We’ve been waiting for you.</p>
<br/>
<p>Dishoom is an online amusement park that celebrates and shamelessly revels in all things Bollywood: the good and the bad; the wacky and the wackier; the romantic, the action-packed, and the melodramatic.</p>
<br/>
<table><tr><td>'
  .render_local_image('logo/square_114.png')
.'</td><td style="padding-left: 20px;">
<p><span style="font-size: 18px;">Here, you’ll find everything you ever wanted to know about your favorite stars and flavors of the month; you’ll catch up on the reviews, news, and commentary essential to making you an informed Bolly-citizen.
</span></p>
</td></tr></table>
<br/>
<p>Simply put, get ready to have some fun. It’s what {Chichi:p:89802} would want.</p>';

$about_text = render_mentions_text($about_text);


$html =
'<div style="margin: 40px">
<h2>About <span>Dishoom</span></h2>
<br/><br/>'
  .'<h4>'.render_mentions_text(get_quote()).'</h4>'
.$about_text
.'</div>';

$page = new page();
$page->setContent($html)
->setTitle('Dishoom | About')
->render();

function get_quote() {
  $quotes =
  array("Do you remember every last, absurd word of {“Tan Tana Tan Tan Tan Tara”:s:9521}?",
        "Do you pretend that {Lagaan:f:7544} is Hindi cinema’s greatest film (when, deep-down, you know that that honor belongs to {Disco Dancer:f:11931})?",
        "Do you find yourself shrieking “Go, Sanju! Go!” during {Jo Jeeta Wohi Sikandar’s:f:8368} final bicycle race?",
        "Do you and your dad bond over your shared appreciation of {Madhuri:p:61395} in {“Dhak Dhak Karne Laga”:s:5745}?",
        "Do you get uncomfortable watching {Silsila:f:9932}?",
        "Do you grow hungry watching the tomato fight in {Zindagi Na Milegi Dobara’s:f:3946} {“Ik Junoon”:s:16229}?",
        "Do you have nightmares about {Ranjeet:p:25657}?",
        "Do you remember where you were when {“Ek Pal Ka Jeena”:s:15685} happened?",
        "Do you hear ‘70s Bollywood lightning whenever tragedy strikes?",
        "Do you scream like a 12 year-old girl (even though you’re a 42 year-old man) when {Shahrukh Khan:p:82536} makes his entrance in {Kabhi Khushi Kabhie Gham…:f:6145}?",
        "Do you think you’re Raj from {Dilwale Dulhania Le Jayenge:f:1661}?",
        "Do you wish you were {Kimi Katkar:p:80355} in {“Jumma Chumma De De”:s:6600}?",
        "Do you take a picture of {Shahrukh Khan:p:82536} to your hair stylist and say, “I want this”?",
        "Do you practice {Deewaar:f:8260} dialogues in front of the bathroom mirror?",
        "Do you feel both inspired and dirty when you watch {“Satyam Shivam Sundaram:s:16309}?",
        "Do you feel ashamed that you don’t get {Guide:f:8067}?",
        "Do you remember that night you went hungry because mummy was too busy watching {Mother India:f:9195}?",
        "Do you play {Devdas:f:16360} drinking games?",
        "Do you have a friend that grew a soul patch after watching {Dil Chahta Hai:f:15693}?",
        "Do you become confused when you watch {Gadar:f:4364} with your Pakistani boyfriend?",
        "Do you still rewind {“Ishq Kamina”:s:8899} to watch it one more time?",
        "Do you think {Amitabh Bachchan:p:26465} is the only person who should be permitted to recite Urdu poetry?",
        "Do you dream that {Shahrukh:p:82536}, {Salman:p:51608}, and {Aamir:p:39540} will act in a movie together?",
        "Do you have no problem distinguishing {Parveen Babi:p:49399} from {Zeenat Aman:p:48085}?",
        "Do you grow uncontrollably excited when {Anil Kapoor:p:89858} appears for round two of “My Name is Lakhan” in {Ram Lakhan:f:58358}?",
        "Do you cry when {Shahrukh:p:82536} and {Salman:p:51608} fight in {Karan Arjun:f:5550}?",
        "Do you agree that utter bliss is in watching {“My Name is Anthony Gonzalves”:s:16344}?",
        "Do you take your dancing cues from {Shammi Kapoor:p:20439}?",
        "Do you have {“Amma Dekh”:s:16683} on your iPod?",
        "Do you throw on your decade-old GAP sweatshirt after watching “Tum Paas Aaye”?",
        "Do you have a child named {“Shahrukh,”:p:82536} {“Aishwarya,”:p:34343} or {“Gabbar”:f:58541}?",
        "Do you say {“Mogambo…khush hua!”:f:6149} on at least a monthly basis?",
        "Do you make fun of your friend who has a hand-drawn {Kapoor:p:66333} family tree because she had to draw it out?",
        "Do you have no tolerance for your uncultured friend who foolishly asks, “Why are {they:s:9468} dancing on a train?”?",
        "Do you remember {Crime Master Gogo:f:3068}?",
        "Do you feel weird when your mother looks at {Vinod Khanna:p:92824} like that?",
        "Do you, in moments of rage, inexplicably yell, “Maa ka dhood piya hai to bahar aa!”?",
        "Do you think post-{Slumdog:f:6232} {A.R. Rahman:p:35223} fans are lame?",
        "Do you still imagine your wife as {Raveena:p:45332} in “Tu Cheez Badi Hai Mast Mast”?",
        "Do you still imagine your husband as {Rajesh Khanna:p:72439} in {“Mere Sapnon Ki Rani”:s:16317}?",
        "Do you think {Dharmendra’s:p:77304} water tower scene in {Sholay:f:58541} outshines Brando’s entire career?",
        "Do you fast-forward through most of {Mohabbatein:f:8956} to get to the {Amitabh:p:26465}-{Shahrukh:p:82536} showdowns?");
  return idx($quotes, array_rand($quotes));
}


?>
