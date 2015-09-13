<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
include_once '../parser.php';
include_once '../../lib/utils.php';
DEFINE('WRITEDB_PW', 'Dest1ny');

$pw = isset($_GET['pw']) ? $_GET['pw'] : null;
if ($pw != WRITEDB_PW) {
	die('die hacker.');
	return 1;
} else {
	$action_url = 'add_article.php?pw='.WRITEDB_PW;
} 

function escit($t) {return mysql_real_escape_string(stripslashes(strip_tags($t, '<br/><a><br />')));}

function write_article_to_db($title, $newstext, $author, $source_link, $source_name) {
	global $link;
	$sql = sprintf("INSERT INTO news (title, newstext, author, source_link, source_name) "
		."VALUES ('%s', '%s', '%s', '%s', '%s')",
		escit($title),
		escit($newstext),
		escit($author),
		escit($source_link),
		escit($source_name)
		);
	// uncomment if you want to allow writing to db
	$result = mysql_query($sql);		
	if (!$result) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	} else {
		echo 'successfully executed query: '.$sql;
	}
	return true;
}


	
$title = isset($_POST['title']) ? $_POST['title'] : null;
if ($title) {
	echo '<h2>preview article before writing to db</h2>';
	$title = $_POST['title'];
	$newstext = $_POST['newstext'];
	$author = $_POST['author'];
	$source_name = $_POST['source_name'];
	$source_link = $_POST['source_link'];
	$fields = array('title' => $title,
				    'newstext' => $newstext,
					'author' => $author,
					'source_name' => $source_name,
					'source_link' => $source_link);
	foreach ($fields as $key => $val) {
		echo '<b>'.$key.'</b>: '.escit($val).'<br/>';
	}

	$error = false;
	$success = false;
	if (isset($_POST['write']) && $_POST['write']) {
		if (!$title || !$newstext) {
			echo 'you must enter a title and article text.';
			$error = true;
		} else {
			if (write_article_to_db($title, $newstext, $author, $source_link, $source_name)) {
				echo '<h2>wrote to db against article '.$title.'</h2>';
				$success = true;
			} else {
				echo 'error writing reviews to db against article';			
			}
		}
	}
	
	if ($error || (isset( $_POST['preview']) && $_POST['preview'])) {
		echo '<form action="'.$action_url.'" method="post">
				
				<input type="hidden" name="title" value="'.$title.'" >
				<input type="hidden" name="newstext" value="'.$newstext.'" >
				<input type="hidden" name="source_link" value="'.$source_link.'" >
				<input type="hidden" name="source_name" value="'.$source_name.'" >
				<input type="hidden" name="author" value="'.$author.'" >
				<input type="hidden" name="write" value="true" >
				<input type="submit" value="Commit to Database">
			</form>';	
	}

} else if (isset($_POST['preview']) && $_POST['preview']) {
	echo 'gotta enter title and article text, dummy.';
}
echo '

<hr/>
nix\'s article adding thing<br/><br/>
<form action="'.$action_url.'" method="post">
	title of article: <input type="text" name="title">
	<input type="submit" value="Add Article"><br/>
	author: <input type="text" name="author"><br/>
	source name: <input type="text" name="source_name"><br/>
	source link: <input type="text" name="source_link"><br/>

	content of article:
	<textarea rows="50" cols="100" name="newstext"></textarea>
    <input type="hidden" name="preview" value="true" >
</form>
</body>
';

?>