1;2c#!/usr/local/bin/php                                                                           
<?php
include_once '../script_lib.php';
set_time_limit(0);
ini_set('memory_limit', '32M');
$vars = parse_args($argv);
$start = idx($vars, 's', 0);


$i = $start;
do {
  $film = get_object_from_sql(
    sprintf("select  * from films where wiki is not null and stars is null "
	    ."limit %d, %d",
	    $i++, 1));
  if (!$film) {
    hlog('[[script complete]]');
    continue;
    //exit(1);
  }

  hlog('['.$i.'] processing film '.$film['title'].' ('.$film['id'].')');

  // first, make sure we have a wiki handle.
  if (!$film['wiki_handle']) {
    hlog('no handle, and we are not autofetching handles right now. skip');
    continue;
    $film['wiki_handle'] = fetch_wiki_handle($film['title'], $film['year']);      
    if (!$film['wiki_handle']) {
      hlog('since no wiki handle found, skipping film entirely');
      continue;  
    }
  }
  hlog('proceeding with handle '.$film['wiki_handle']);
   // so now we have a handle - either the true handle or a proxy
   
  // now, go get the wiki itself. if we have it stored, use it. otherwise, fetch it.
  if (!$film['wiki']) {
    $wiki_response =   fetch_wiki_info($film['wiki_handle']);  
    if (!$wiki_response) {
        hlog('no wiki text found, skipping');
        continue;    
    }
    $film['wiki_handle'] = $wiki_response['wiki_handle'];
    $film['wiki'] = $wiki_response['wiki'];
    update_wiki_db_fields($film); 
  } else {
    hlog('already have wiki text, sweet');
  }
  
  
  $new_fields_from_wiki = parse_wiki_text($film);
  //hlog($new_fields_from_wiki);

  if ($new_fields_from_wiki) {
    update_new_wiki_fields_to_db($film, $new_fields_from_wiki);
  } else {
    hlog('already had all fields from the wiki page'); 
  }
} while (1) ;


function fetch_wiki_handle($title, $year) {
  $year = (int) $year;
  $search_term = str_replace(' ', '_', $title);
  $search_url = sprintf(
    'http://en.wikipedia.org/w/api.php?action=opensearch&search=%s&limit=1',
    $search_term
  );
  
  hlog('no existing handle, curling wiki opensearch api from url '.$search_url);
  $results = get_url_contents($search_url);
  //hlog('wiki handle search api returned response '.print_r($results, true));
  if (!$results || sizeof($results) < 2 || !$results[1]) {
      hlog('no handle found for film '.$title.')');
      return null;
  }
  $handle = head($results[1]);
  if ($handle) {
      hlog('successfully found handle '.$handle.' for film '.$title);
      return $handle;
  }
  return null;
}


function fetch_wiki_info($handle) {
	if (!$handle) {
		hlog("wtf, you can't get a wiki without a handle!");
		return false;
	} 
	
	$contents_url = sprintf('http://en.wikipedia.org/w/api.php?action=query&redirects=1&export=1&prop=revisions&titles=%s&rvprop=tags|content&format=json&rvparse=1', urlencode($handle));
	hlog('now fetching content for handle '.$handle.' from url '.$contents_url);

	$res = get_url_contents($contents_url);
	$wiki_content = (head(idx(idx($res, 'query'), 'pages')));
	$true_handle = idx($wiki_content, 'title');
	if (!$true_handle) {
	   hlog('no true handle!? failing');
	   return null;   
	}
	hlog('found true wiki handle = '.$true_handle);
	if (!idx($wiki_content, 'revisions')) {
	  hlog('no wiki content');
	  return null;
	}
	$wiki_text = idx(head(idx($wiki_content, 'revisions')), '*');
	if (!$wiki_text) {
		hlog('no wiki text! onies');
		return null;
	}
    return array('wiki_handle' => str_replace(' ', '_', $true_handle), 'wiki' => $wiki_text);
}

function parse_wiki_text($film) {
  $wiki_text = $film['wiki'];
  if (!$wiki_text) {
    hlog("can't parse wiki text without wiki text");
    return false;   
  }
  $dom = str_get_html($wiki_text);
  $info_table = $dom->find('table[class=infobox]', 0);
  if (stripos($info_table->plaintext, 'directed') === false) {
    hlog('[ERROR] this doesn\'t look like a film page, no director!! hmmm');
    return false;
  }
  
  $r = array();
  foreach ($info_table->find('tr') as $row) {
    if (!idx($film, 'release_date') && stripos($row->plaintext, 'Release date') !== false) {
      $r['release_date'] = rem($row->find('td', 0)->plaintext, $row->find('td', 0)->first_child()->plaintext);
    } else if (!idx($film, 'box_office') && stripos($row->plaintext, 'box office') !== false) {
      $r['box_office'] = 
	rem(trim(remove_annotations($row->find('td', 0)->plaintext)), '}');
    } else if (!idx($film, 'box_office') && stripos($row->plaintext, 'budget') !== false) {
      $r['budget'] = 
	trim(remove_annotations($row->find('td', 0)->plaintext));
    } else if (!idx($film,'directors') && stripos($row->plaintext, 'direct') !== false) {
      $r['directors'] = process_people($row->find('td', 0), 'director');
    } else if (!idx($film,'producers') && stripos($row->plaintext, 'produced by') !== false) {
      $r['producers'] = process_people($row->find('td', 0), 'producer');
    }  else if (!idx($film,'music_directors') && stripos($row->plaintext, 'music by') !== false) {
      $r['music_directors'] = process_people($row->find('td', 0), 'music_director');
    } else if (!$film['stars'] && stripos($row->plaintext, 'starring') !== false) {
      $r['stars'] = process_people($row->find('td', 0), 'star');
    } else if (!idx($film,'distributor') && stripos($row->plaintext, 'distributed by') !== false) {
      $r['distributor'] = trim($row->find('td', 0)->innertext);
    } else if (!idx($film,'runtime') && stripos($row->plaintext, 'running time') !== false) {
      $r['runtime'] = trim(remove_annotations($row->find('td', 0)->plaintext));
    }
  }
  
  if (!idx($film, 'wiki_plot')) {
    $wiki_plot = null;
    foreach ($dom->find('h2') as $h2) {
      if (stripos($h2->plaintext, 'plot') !== false || stripos($h2->plaintext, 'synopsis') !== false) {
	$wiki_plot = '';
	$node = $h2->next_sibling();
	while ($node && stripos($node->outertext, 'class="mw-headline"') === false) {
	  $wiki_plot .= $node->outertext;
	  $node = $node->next_sibling();
	}
	break;
      }
    }
    if ($wiki_plot) {
      $r['wiki_plot'] = remove_annotations($wiki_plot);
    }
  }
  if (!idx($film,'wiki_summary')) {
    $r['wiki_summary'] = 
      remove_annotations(strip_html($dom->find('p', 0)->innertext));
  }
  return $r;
}

function process_people($dom, $type) {
  $people = array();
  foreach ($dom->find('a') as $a) {
    $handle = rem($a->href, '/wiki/');
    $existing_id = get_person_id_from_handle($handle);
    if ($existing_id) {
      $people[] = $existing_id;
    } else {
      $new_id = add_empty_person_to_db($handle, $a->plaintext, $type);
      if (!$new_id) {
	hlog('[ERR] something went terribly wrong with id person insert');
      } else {
	$people[] = $new_id;
      }
    }
  }
  return $people;
}

function get_url_contents($url) {
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: dishoom (+http://dishooms.com/tools/)');
	$res = curl_exec($ch);
	curl_close($ch);
	return json_decode($res, true);
}

function add_empty_person_to_db($handle, $name, $type) {
  global $link;
  do {
    $id = rand(20000, 99999);
    hlog('------gen random id '.$id);
    $temp = get_object($id, 'people');
  } while ($temp);
  
  $sql = sprintf("insert ignore into people (name, wiki_handle, id, type) "
		 ."values ('%s', '%s', %d, '%s')",
		 tr($name),
		 tr($handle),
		 $id,
		 $type);

  //hlog('fake running sql '.$sql);                                          
  //$result = true;                                                          
  $result = mysql_query($sql);

  if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $sql;
    hlog($message);
    return false;
  } else {
    hlog('--- added person ' .$name.' with handle '.$handle.' for id '.$id.' of type '.$type);
  }
  return $id;

}

function update_wiki_db_fields($film) {
  if (!$film) {
    hlog('trying to update db with missing info?');
    return false;
  }
  
  if (!$film['id']) {
    hlog('trying to update db with no film id');
    return false;
  }

  global $link;
    $sql = sprintf("update films set wiki_handle = '%s', wiki = '%s' where id = %d limit 1", 
        tr($film['wiki_handle']),
        tr($film['wiki']),
        $film['id']);

    // TODO REMOVE
    //hlog('fake running sql '.$sql);
    //$result = true;
    $result = mysql_query($sql);

    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      hlog($message);
    } else {
      hlog('saved wiki fields to db for handle '.$film['wiki_handle']);
    }
  return true;
}

function update_new_wiki_fields_to_db($film, $new_fields) {
  if (!$new_fields) {
    hlog('trying to update db with no new fields?');
    return false;
  }
  
  if (!$film['id']) {
    hlog('trying to update db with no film id');
    return false;
  }

  global $link;
  $arr_fields = array('stars', 'directors', 'producers', 'music_directors');
  foreach ($new_fields as $field_name => $field_value) {
    if (in_array($field_name, $arr_fields)) {
      //$field_value = ar($field_value);   
      $field_value = implode(',', $field_value);
    } else {
        $field_value = tr($field_value);   
    }
    $sql = sprintf("update films set ".$field_name." = '%s' where id = %d limit 1", 
        $field_value,
        $film['id']);

    // TODO REMOVE
    hlog('fake running sql '.$sql);
    $result = true;
    //$result = mysql_query($sql);    
    if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $sql;
      hlog($message);
    } else {
      hlog('saved new field '.$field_name.' to db for id '.$film['id']);
    }
  }
  return true;
}

function get_person_id_from_handle($handle) {
  $sql = 	
    sprintf("select id from people where wiki_handle = '%s' limit 1", $handle);
  $person = get_object_from_sql($sql);
  if ($person and idx($person, 'id')) {
    return $person['id'];
  }
  return null;
}


?>