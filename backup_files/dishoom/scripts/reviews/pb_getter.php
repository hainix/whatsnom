<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
<div align="left">
<?php
include_once '../../lib/utils.php';
include_once '../parser.php';
include_once '../script_lib.php';
set_time_limit(0);
//ini_set('memory_limit', '32M');

$start = isset($_GET['s']) ? $_GET['s'] : 0; // where to start
$id = isset($_GET['id']) ? $_GET['id'] : 0; 
define('WORKABLE_CHUNK_SIZE', 1);

$html = str_get_html(get_url('http://dishoomreviews.com/nix/planetTEST.php')); // obtained from empty search on pb review site
$data = array();
$mapped_ids = get_film_id_mapping_new();
$missing_films = array();
foreach ($html->find('table') as $table) {
	$r = array();
	$a = $table->find('td', 0)->find('a', 0);
	if (!$a) {
		continue;	
	}
	$r['url'] = $a->href;
	$r['title'] = $a->plaintext;
	$title_index = str_replace("'",'', rem($r['title'], ' (New)'));

	$film_id = get_id_from_film_name($title_index);

	if (!$film_id) {
		foreach ($mapped_ids as $tid => $name) {
			if (strcmp($title_index, $name) == 0) {
				$film_id = $tid;
				break;	
			} 
		}
	}
	if ($film_id) {
		$r['film_id'] = $film_id;	
	} else {
		$missing_films[] = $title_index;
		continue;	
	}
	
	$rating = $table->find('tr[bgcolor=#eeefff]', 0)->plaintext;
	$rating = explode('out of 10', $rating);
	$r['pb_rating'] = strip_html(rem($rating[0], array('.', 'PB Rating:')));
	$public_rating = explode(':', $rating[1]);
	$votes =  rem($public_rating[0], array('Public Rating (by ', 'unique users)'));
	$r['votes'] = $votes;
	$r['public_rating'] = (int) floor(($public_rating[1] * 100) / 10);
	
	$data[] = $r;
}

echo json_encode($data);
//echo '<pre>'.print_r($data, true).'</pre>';
//echo '<pre>'.print_r($missing_films, true).'</pre>';


function title_strip($a) {
	return trim(strtolower(rem($a, array(' ','.',',','-',':',"'"))));	
}

function update_film_with_reviews($film, $rating, $votes, $write = false) {
	print_r($film);
	$new_votes = $film['votes'] + $votes;
	$new_rating = (int) ($film['rating']*$film['votes'] + ($rating * $votes) ) / ($new_votes);
	$q = sprintf('UPDATE films SET votes = %d , rating = %d WHERE id = %d', $new_votes, $new_rating, $film['id']);
	if ($write) {
		echo '<h2>REAL updating: '.$q.'</h2>';
		$r = mysql_query($q);
	} else {
		echo '<h2>FAKE updating: '.$q.'</h2>';
	}
}

function get_film_id_mapping_new() {
	return array (
	'800956' => 'Life In A...Metro',
	'366304' => 'Chokher Bali',
	'313395' => 'Kitne Door Kitne Paas',
	'306840' => 'Koi Mere Dil Se Pooche',
	'319736' => 'Legend of Bhagat Singh',
	'473367' => 'Jaane Tu Ya Jaane Na',
	'449994' => 'Jodhaa-Akbar',
	'449159' => '15th Park Avenue',
	'305173' => 'Aamdani Atthanni Kharcha Rupaiya',
	'363409' => 'Aan: Men at Work',
	'363409' => 'Aan - Men At Work',
	'397882' => 'Ab Tumhare Hawale Watan Sathiyo',
	'106270' => 'Anari No 1',
	'326983' => 'Jaani Dushman - Ek Anokhi Kahani',
	'349115' => 'Baaz - A Bird in Danger',
	'1230448' => 'Billu Barber',
	'303785' => 'Bollywood Hollywood',
	'361411' => 'Bride And Prejudice',
	'447890' => 'Chaahat - Ek Nasha',
	'1194608' => 'Ek Vivaah Aisa Bhi',
	'1608777' => 'Love Sex Aur Dhokha',
	'318956' => 'Tum Se Achcha Kaun Hain',
	'339878' => 'Waah Tera Kya Kehna',
	'1242530' => 'What`s Your Raashee?',
	'459293' => 'Gandhi My Father',
	'411469' => 'Hazaaron Khwahishen Aisi',
	'320097' => 'Hum Kisise Kum Nahin',
	'405046' => 'Insaaf - The Justice',
	'1608777' => 'Love Sex Aur Dhokha',
	'307116' => 'Maa Tujhe Salaam',
	'346457' => 'Mangal Pandey',
	'860454' => 'Mp3 - Mera Pehla Pehla Pyaar',
	'419992' => 'My Brother Nikhil',
	'470869' => 'Neal N Nikki',
	'274019' => 'Pyaar Deewana Hota Hai',
	'449389' => 'Shaadi No 1',
	'284479' => 'Sharaarat',
	'1327833' => 'Sorry Bhai!',
	'476884' => 'Taxi No. 9211',
	'310254' => 'Tumko Na Bhool Payenge',
	'420332' => 'Veer Zaara',
	'390614' => 'WAQT the race against time',
	'470614' => 'Yun Hota To Kya Hota',
	'387678' => 'Waisa Bhi Hota Hai: Part II',
	'1694542' =>  'Tanu Weds Manu',
	'1754920' =>  'Ye Saali Zindagi',
	'1727496' =>  'Dil to Bacha Hai Ji',
	'1666184' =>  'Turning 30!!!',
	'1189006' =>  'Toonpur ka superhero',
	'1727535' =>  'Rakht Charitra 2 reprise',
	'1773015' =>  'Phas Gaye Re Obama',
	'1637691' =>  'Khele Hum Jee Jaan Se',
	'1738293' =>  'Khichdi The Movie',
	'1245732' =>  'Red Alert - The War Within',
	'1291465' =>  'Rajneeti',
	'1602476' =>  'Badmaash Company',
	'1194236' =>  'Paathshaala',
	'1608777' =>  'Love Sex Aur Dhoka',
	'1191130' =>  'Right Yaaa Wrong',
	'1210356' =>  'Raat Gayi Baat Gayi',
	'1174041' =>  'Main Aur Mrs Khanna',
	'1242530' =>  'Whats your rashee?',
	'1135931' =>  'Chintuji',
	'1002963' =>  'Y M I - Yeh Mera India',
	'1176911' =>  'Quickgun Murugun',
	'1501301' =>  'Morning Walk',
	'1229390' =>  'Short Kut',
	'1419916' =>  'Chowrasta - Crossroads of Love',
	'893585' =>  'Detective Nani',
	'1479857' =>  'Zor Laga Ke Haiya',
	'1204913' =>  'Karma - Crime Passion Reincarnation',
	'1454567' =>  'Team - The Force',
	'422950' =>  'Hum Phirr Milenge',
	'389337' =>  'Runway',
	'438894' =>  'Kisse Pyaar Karoon?',
	'317355' =>  'Dhoondte Reh Jaaoge',
	'483701' =>  'Karma Aur Holi',
	'1305840' =>  'Aloo Chat',
	'1202517' =>  'Barah Anna',
	'1146285' =>  'Videsh',
	'1105709' =>  '8x10 Tasveer',
	'1327833' =>  'Sorry Bhai',
	'1292703' =>  'Oye Lucky Lucky Oye',
	'1340838' =>  'Raaz - The Mystery Continues',
	'1438486' =>  'Chal Chala Chal',
	'1043451' =>  'Delhi 6',
	'1391894' =>  'Siddharth The Prisoner',
	'473367' =>  'Jaane Tu Ya Jaane Na',
	'1179781' =>  'Mission Istanbul',
	'1126516' =>  'Money Hai To Honey Hai',
	'986264' =>  'Tare Zameen Par',
	'1206283' =>  'Black and White',
	'1068956' =>  'Khuda Ke Liye',
	'1228726' =>  'Mr White Mr Black',
	'1077248' =>  'Johnny Gaddar',
	'1074201' =>  'Its Breaking News',
	'982875' =>  'Gauri The Unborn',
	'459293' =>  'Gandhi My Father',
	'871510' =>  'CHAKDE! INDIA',
	'64733102' =>  'Ram Gopal Varma ki Aag',
	'800956' =>  'Metro',
	'995840' =>  'Aap Kaa Suroor',
	'1312135' =>  'Oh My God',
	'989633' =>  'delhiiheights',
	'456144' =>  'Lage Raho Munnabhai',
	'480572' =>  'Pyar Ke Side Effects',
	'466460' =>  'Khosla ka Ghosla',
	'857385' =>  'Kudiyon Ka Hai Zamana',
	'946999' =>  'Deadline - Sirf 24 Ghante',
	'441048' =>  'Dhoom 2' );
}
?>
</div>