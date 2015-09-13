<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
include_once '../parser.php';
include_once '../../lib/utils.php';
DEFINE('WRITEDB_PW', 'Dest1ny');

$start = isset($_GET['srr']) ? $_GET['srr'] : 0; // where to start
$id = isset($_GET['id']) ? $_GET['id'] : 0; 
set_time_limit(0);
ini_set('memory_limit', '32M');
$action_url = BASE_URL.'nix/review_parser.php?pw='.WRITEDB_PW;


$set_url = null;
if (isset($_GET['srr'])) {
	$films = get_movie_array();
	$i=0;
	foreach ($films as $_) {
		$set_url = $_;
		if ($i == $start) {
			break;
		}
		$i++;
	}
}

function cp($t) {
	return trim(strip_tags(trim(str_replace("\n", '', $t))));
}
	
function isValidURL($url) {
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}	

function includeTrailingCharacter($string, $character) {
	if (strlen($string) > 0) {
		if (substr($string, -1) !== $character) {
			return $string . $character;
		} else {
			return $string;
		}
	} else {
		return $character;
	}
}

function write_reviews_to_db($film_id, $reviews) {
	global $link;
	
	foreach ($reviews as $r) {
		$sql = sprintf("INSERT INTO reviews (reviewer, film_id, source_name, source_link, rating, excerpt) VALUES ('%s', %d, '%s', '%s', %d, '%s')",
		mysql_real_escape_string($r['reviewer']),
		$film_id,
		mysql_real_escape_string($r['source_name']),
		mysql_real_escape_string($r['source_link']),
		$r['rating'],
		mysql_real_escape_string($r['excerpt']));

	
		// uncomment if you want to allow writing to db
		$result = mysql_query($sql);		
		if (!$result) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $sql;
			die($message);
		}
	}
	return true;

}
	
$url = $set_url ? $set_url : (isset($_POST['url']) ? $_POST['url'] : null);
if ($url && isValidURL($url)) {
	echo '<h2>preview results from parsing '.$url.'</h2>';

	$html = file_get_html($url);
	$film_parse = false;
	foreach($html->find('div[class="quotes thin_border hreview"]') as $div) {
		$r = array();

		$r['excerpt'] = includeTrailingCharacter(
			cp(str_replace('Read full review..','',
			$div->find('div', 0)->find('p', 0)->find('span', 0)->innertext)),
			'.');
		$r['source_name'] = cp($div->find('.sub_detail', 1)->innertext);
		$r['source_link'] = cp($div->find('div', 0)->find('a', 0)->href);
		$r['reviewer'] = cp($div->find('.reviewer', 0)->innertext);
		$rating = (int) $div->find('.value-title', 1)->title;
		$r['rating'] = $rating ? $rating * 20 : 50;
		$ret[] = $r;
	}

	$error = false;
	$success = false;
	if (isset($_POST['write']) && $_POST['write']) {
		$film_id = $_POST['film_id'];
		if (!$film_id) {
			echo 'you screwed up the film id. go get it again. geez.';
			$error = true;
		} else {
			// film id is valid and we need to write to DB
			if (write_reviews_to_db($film_id, $ret)) {
				echo 'wrote to db against film '.$film_id;
				$success = true;
			} else {
				echo 'error writing reviews to db against film '.$film_id;			
			}
		}
	}

	$parse_error = false;
	if (!$success) {
		if ($ret) {
			$preview_ret = $film_parse ? array($ret) : $ret;
		
			foreach ($preview_ret as $r) {
				foreach ($r as $k => $v) {
					echo '<b>'.$k.'</b>: '.$v.'<br/>';
				}
				echo '<br/>';
			}
		} else {
			echo 'error parsing page';
			$parse_error = true;
		}
	}
	
	if (!$parse_error && ($error || $set_url || $_POST['preview'])) {
		echo '<form action="'.$action_url.'" method="post">
				film id: <input type="text" name="film_id" >
				<input type="hidden" name="write" value="true" >
				<input type="hidden" name="url" value="'.$url.'" >
				<input type="submit" value="Commit to Database">
			</form>';	
	}

} else if (isset($_POST['preview']) && $_POST['preview']) {
	echo 'you screwed up the url, dummy.';
}
echo '

<hr/>
nix\'s super secret magic thing<br/><br/>
<form action="'.$action_url.'" method="post">
<!--   <textarea name="msg" cols=100 rows=20></textarea> -->
	url to parse: <input type="text" name="url" >
    <input type="hidden" name="preview" value="true"><br/>
<input type="radio" name="type" value="review" checked="true"/>review
<input type="radio" name="type" value="wiki" /> wiki	
    <input type="submit" value="Parse">
</form>
</body>
';


function get_movie_array() {
	return array('Chandni Chowk To China' => 'http://www.reviewgang.com/movies/1-Chandni-Chowk-To-China-Review',
'Raaz - The Mystery Continues' => 'http://www.reviewgang.com/movies/2-Raaz---The-Mystery-Continues-Review',
'Luck by Chance' => 'http://www.reviewgang.com/movies/3-Luck-by-Chance-Review',
'Dev D' => 'http://www.reviewgang.com/movies/4-Dev-D-Review',
'Billu' => 'http://www.reviewgang.com/movies/5-Billu-Review',
'Delhi-6' => 'http://www.reviewgang.com/movies/6-Delhi-6-Review',
'New York' => 'http://www.reviewgang.com/movies/7-New-York-Review',
'Love Aaj Kal' => 'http://www.reviewgang.com/movies/8-Love-Aaj-Kal-Review',
'Kaminey' => 'http://www.reviewgang.com/movies/9-Kaminey-Review',
'Wanted' => 'http://www.reviewgang.com/movies/10-Wanted-Review',
'Wake Up Sid' => 'http://www.reviewgang.com/movies/11-Wake-Up-Sid-Review',
'Blue' => 'http://www.reviewgang.com/movies/12-Blue-Review',
'Main Aur Mrs Khanna' => 'http://www.reviewgang.com/movies/13-Main-Aur-Mrs-Khanna-Review',
'London Dreams' => 'http://www.reviewgang.com/movies/14-London-Dreams-Review',
'Aladin' => 'http://www.reviewgang.com/movies/15-Aladin-Review',
'Ajab Prem Ki Ghazab Kahani' => 'http://www.reviewgang.com/movies/16-Ajab-Prem-Ki-Ghazab-Kahani-Review',
'Kurbaan' => 'http://www.reviewgang.com/movies/17-Kurbaan-Review',
'De Dana Dan' => 'http://www.reviewgang.com/movies/18-De-Dana-Dan-Review',
'Paa' => 'http://www.reviewgang.com/movies/19-Paa-Review',
'Rocket Singh' => 'http://www.reviewgang.com/movies/20-Rocket-Singh-Review',
'3 Idiots' => 'http://www.reviewgang.com/movies/21-3-Idiots-Review',
'Raat Gayi Baat Gayi' => 'http://www.reviewgang.com/movies/23-Raat-Gayi-Baat-Gayi-Review',
'Pyaar Impossible' => 'http://www.reviewgang.com/movies/25-Pyaar-Impossible-Review',
'Dulha Mil Gaya' => 'http://www.reviewgang.com/movies/26-Dulha-Mil-Gaya-Review',
'Chance Pe Dance' => 'http://www.reviewgang.com/movies/27-Chance-Pe-Dance-Review',
'Veer' => 'http://www.reviewgang.com/movies/28-Veer-Review',
'Ishqiya' => 'http://www.reviewgang.com/movies/29-Ishqiya-Review',
'Rann' => 'http://www.reviewgang.com/movies/30-Rann-Review',
'Striker' => 'http://www.reviewgang.com/movies/31-Striker-Review',
'My Name is Khan' => 'http://www.reviewgang.com/movies/32-My-Name-is-Khan-Review',
'Toh Baat Pakki' => 'http://www.reviewgang.com/movies/33-Toh-Baat-Pakki-Review',
'Kites' => 'http://www.reviewgang.com/movies/34-Kites-Review',
'Karthik Calling Karthik' => 'http://www.reviewgang.com/movies/35-Karthik-Calling-Karthik-Review',
'Teen Patti' => 'http://www.reviewgang.com/movies/36-Teen-Patti-Review',
'Raajneeti' => 'http://www.reviewgang.com/movies/37-Raajneeti-Review',
'Paathshaala' => 'http://www.reviewgang.com/movies/38-Paathshaala-Review',
'Atithi Tum Kab Jaoge' => 'http://www.reviewgang.com/movies/39-Atithi-Tum-Kab-Jaoge-Review',
'Prince' => 'http://www.reviewgang.com/movies/41-Prince-Review',
'Road Movie' => 'http://www.reviewgang.com/movies/42-Road-Movie-Review',
'Housefull' => 'http://www.reviewgang.com/movies/43-Housefull-Review',
'Hide and Seek' => 'http://www.reviewgang.com/movies/44-Hide-and-Seek-Review',
'Right Yaa Wrong' => 'http://www.reviewgang.com/movies/45-Right-Yaa-Wrong-Review',
'Tum Milo Toh Sahi' => 'http://www.reviewgang.com/movies/46-Tum-Milo-Toh-Sahi-Review',
'Love Sex Aur Dhokha' => 'http://www.reviewgang.com/movies/47-Love-Sex-Aur-Dhokha-Review',
'Hum Tum Aur Ghost' => 'http://www.reviewgang.com/movies/48-Hum-Tum-Aur-Ghost-Review',
'Well Done Abba' => 'http://www.reviewgang.com/movies/49-Well-Done-Abba-Review',
'Leaving Home' => 'http://www.reviewgang.com/movies/50-Leaving-Home-Review',
'The Great Indian Butterfly' => 'http://www.reviewgang.com/movies/51-The-Great-Indian-Butterfly-Review',
'Pankh' => 'http://www.reviewgang.com/movies/52-Pankh-Review',
'Jaane Kahan Se Aayi Hai' => 'http://www.reviewgang.com/movies/53-Jaane-Kahan-Se-Aayi-Hai-Review',
'The Japanese Wife' => 'http://www.reviewgang.com/movies/54-The-Japanese-Wife-Review',
'Phoonk 2' => 'http://www.reviewgang.com/movies/55-Phoonk-2-Review',
'Raavan' => 'http://www.reviewgang.com/movies/56-Raavan-Review',
'Muskurake Dekh Zara' => 'http://www.reviewgang.com/movies/57-Muskurake-Dekh-Zara-Review',
'Badmaash Company' => 'http://www.reviewgang.com/movies/58-Badmaash-Company-Review',
'Sapno Ke Desh Mein' => 'http://www.reviewgang.com/movies/59-Sapno-Ke-Desh-Mein-Review',
'Apartment' => 'http://www.reviewgang.com/movies/60-Apartment-Review',
'Bird Idol' => 'http://www.reviewgang.com/movies/61-Bird-Idol-Review',
'City of Gold' => 'http://www.reviewgang.com/movies/62-City-of-Gold-Review',
'The Blue Umbrella' => 'http://www.reviewgang.com/movies/63-The-Blue-Umbrella-Review',
'Chase' => 'http://www.reviewgang.com/movies/64-Chase-Review',
'Its a Wonderful Afterlife' => 'http://www.reviewgang.com/movies/65-Its-a-Wonderful-Afterlife-Review',
'Admissions Open' => 'http://www.reviewgang.com/movies/66-Admissions-Open-Review',
'Bumm Bumm Bole' => 'http://www.reviewgang.com/movies/67-Bumm-Bumm-Bole-Review',
'I Hate Luv Storys' => 'http://www.reviewgang.com/movies/68-I-Hate-Luv-Storys-Review',
'Udaan' => 'http://www.reviewgang.com/movies/70-Udaan-Review',
'Krantiveer The Revolution' => 'http://www.reviewgang.com/movies/71-Krantiveer-The-Revolution-Review',
'Lamhaa' => 'http://www.reviewgang.com/movies/72-Lamhaa-Review',
'Milenge Milenge' => 'http://www.reviewgang.com/movies/73-Milenge-Milenge-Review',
'Mr. Singh Mrs. Mehta' => 'http://www.reviewgang.com/movies/74-Mr-Singh-Mrs-Mehta-Review',
'Aisha' => 'http://www.reviewgang.com/movies/75-Aisha-Review',
'Once Upon A Time In Mumbaai' => 'http://www.reviewgang.com/movies/76-Once-Upon-A-Time-In-Mumbaai-Review',
'Red Alert The War Within' => 'http://www.reviewgang.com/movies/77-Red-Alert-The-War-Within-Review',
'Peepli Live' => 'http://www.reviewgang.com/movies/78-Peepli-Live-Review',
'Tere Bin Laden' => 'http://www.reviewgang.com/movies/79-Tere-Bin-Laden-Review',
'Lafangey Parindey' => 'http://www.reviewgang.com/movies/80-Lafangey-Parindey-Review',
'Khatta Meetha' => 'http://www.reviewgang.com/movies/81-Khatta-Meetha-Review',
'Hello Darling' => 'http://www.reviewgang.com/movies/82-Hello-Darling-Review',
'We Are Family' => 'http://www.reviewgang.com/movies/83-We-Are-Family-Review',
'Aashayein' => 'http://www.reviewgang.com/movies/84-Aashayein-Review',
'Dabangg' => 'http://www.reviewgang.com/movies/85-Dabangg-Review',
'Anjaana Anjaani' => 'http://www.reviewgang.com/movies/86-Anjaana-Anjaani-Review',
'Antardwand' => 'http://www.reviewgang.com/movies/87-Antardwand-Review',
'The Film Emotional Atyachar' => 'http://www.reviewgang.com/movies/88-The-Film-Emotional-Atyachar-Review',
'Aakrosh' => 'http://www.reviewgang.com/movies/89-Aakrosh-Review',
'Jhootha Hi Sahi' => 'http://www.reviewgang.com/movies/90-Jhootha-Hi-Sahi-Review',
'For Real' => 'http://www.reviewgang.com/movies/92-For-Real-Review',
'Crook' => 'http://www.reviewgang.com/movies/93-Crook-Review',
'Knock Out' => 'http://www.reviewgang.com/movies/94-Knock-Out-Review',
'Golmaal 3' => 'http://www.reviewgang.com/movies/95-Golmaal-3-Review',
'Action Replayy' => 'http://www.reviewgang.com/movies/96-Action-Replayy-Review',
'Rakta Charitra I' => 'http://www.reviewgang.com/movies/97-Rakta-Charitra-I-Review',
'Guzaarish' => 'http://www.reviewgang.com/movies/98-Guzaarish-Review',
'Delhi Belly' => 'http://www.reviewgang.com/movies/99-Delhi-Belly-Review',
'Agent Vinod' => 'http://www.reviewgang.com/movies/100-Agent-Vinod-Review',
'Yamla Pagla Deewana' => 'http://www.reviewgang.com/movies/102-Yamla-Pagla-Deewana-Review',
'No One Killed Jessica' => 'http://www.reviewgang.com/movies/104-No-One-Killed-Jessica-Review',
'Break Ke Baad' => 'http://www.reviewgang.com/movies/105-Break-Ke-Baad-Review',
'Don 2' => 'http://www.reviewgang.com/movies/106-Don-2-Review',
'Ra One' => 'http://www.reviewgang.com/movies/107-Ra-One-Review',
'Dhobi Ghat' => 'http://www.reviewgang.com/movies/108-Dhobi-Ghat-Review',
'That Girl In Yellow Boots' => 'http://www.reviewgang.com/movies/109-That-Girl-In-Yellow-Boots-Review',
'Robot' => 'http://www.reviewgang.com/movies/110-Robot-Review',
'Allah Ke Banday' => 'http://www.reviewgang.com/movies/111-Allah-Ke-Banday-Review',
'Tees Maar Khan' => 'http://www.reviewgang.com/movies/112-Tees-Maar-Khan-Review',
'Dus Tola' => 'http://www.reviewgang.com/movies/113-Dus-Tola-Review',
'Khichdi The Movie' => 'http://www.reviewgang.com/movies/114-Khichdi-The-Movie-Review',
'Do Dooni Chaar' => 'http://www.reviewgang.com/movies/115-Do-Dooni-Chaar-Review',
'Hisss' => 'http://www.reviewgang.com/movies/116-Hisss-Review',
'Khelein Hum Jee Jaan Sey' => 'http://www.reviewgang.com/movies/117-Khelein-Hum-Jee-Jaan-Sey-Review',
'No Problem' => 'http://www.reviewgang.com/movies/118-No-Problem-Review',
'Dil Toh Bachcha Hai Ji' => 'http://www.reviewgang.com/movies/119-Dil-Toh-Bachcha-Hai-Ji-Review',
'Game' => 'http://www.reviewgang.com/movies/120-Game-Review',
'Saat (7) Khoon Maaf' => 'http://www.reviewgang.com/movies/121-Saat-7-Khoon-Maaf-Review',
'Its My Life' => 'http://www.reviewgang.com/movies/122-Its-My-Life-Review',
'Dum Maaro Dum' => 'http://www.reviewgang.com/movies/123-Dum-Maaro-Dum-Review',
'Patiala House' => 'http://www.reviewgang.com/movies/124-Patiala-House-Review',
'Rockstar' => 'http://www.reviewgang.com/movies/125-Rockstar-Review',
'Zindagi Na Milegi Dobara' => 'http://www.reviewgang.com/movies/126-Zindagi-Na-Milegi-Dobara-Review',
'Thank You' => 'http://www.reviewgang.com/movies/127-Thank-You-Review',
'Run Bhola Run' => 'http://www.reviewgang.com/movies/128-Run-Bhola-Run-Review',
'Toonpur Ka Superhero' => 'http://www.reviewgang.com/movies/129-Toonpur-Ka-Superhero-Review',
'Paan Singh Tomar' => 'http://www.reviewgang.com/movies/130-Paan-Singh-Tomar-Review',
'Phas Gaye Re Obama' => 'http://www.reviewgang.com/movies/131-Phas-Gaye-Re-Obama-Review',
'Rakth Charitra II' => 'http://www.reviewgang.com/movies/132-Rakth-Charitra-II-Review',
'Daayen Ya Baayen' => 'http://www.reviewgang.com/movies/133-Daayen-Ya-Baayen-Review',
'Nakshatra' => 'http://www.reviewgang.com/movies/134-Nakshatra-Review',
'Band Baaja Baaraat' => 'http://www.reviewgang.com/movies/135-Band-Baaja-Baaraat-Review',
'Yeh Saali Zindagi' => 'http://www.reviewgang.com/movies/136-Yeh-Saali-Zindagi-Review',
'A Flat' => 'http://www.reviewgang.com/movies/137-A-Flat-Review',
'Dunno Y Na Jaane Kyun' => 'http://www.reviewgang.com/movies/138-Dunno-Y-Na-Jaane-Kyun-Review',
'Shahrukh Bola Khoobsurat Hai Tu' => 'http://www.reviewgang.com/movies/139-Shahrukh-Bola-Khoobsurat-Hai-Tu-Review',
'Mirch' => 'http://www.reviewgang.com/movies/140-Mirch-Review',
'Tera Kya Hoga Johny' => 'http://www.reviewgang.com/movies/141-Tera-Kya-Hoga-Johny-Review',
'332 Mumbai To India' => 'http://www.reviewgang.com/movies/142-332-Mumbai-To-India-Review',
'ADA : A way of Life' => 'http://www.reviewgang.com/movies/143-ADA--A-way-of-Life-Review',
'Tanu Weds Manu' => 'http://www.reviewgang.com/movies/144-Tanu-Weds-Manu-Review',
'Bhoot and Friends' => 'http://www.reviewgang.com/movies/145-Bhoot-and-Friends-Review',
'Turning 30' => 'http://www.reviewgang.com/movies/146-Turning-30-Review',
'Yeh Saali Zindagi' => 'http://www.reviewgang.com/movies/147-Yeh-Saali-Zindagi-Review',
'Utt Pataang' => 'http://www.reviewgang.com/movies/148-Utt-Pataang-Review');

}

?>