<?php
//http://dishoomreviews.com/nix/bipasha.php?s=1620
//http://dishoomreviews.com/nix/bipasha.php?s=1601
//http://dishoomreviews.com/nix/bipasha.php?s=1400
//http://dishoomreviews.com/nix/bipasha.php?s=1350

function get_planetbw_rating($film, &$data, &$err, $force = false, $write = false, $html = null, $url = null, $rating = null) {
	if (!$html) {
		$html = get_scraped_review($film, 'planet bollywood', 'movie review site:http://www.planetbollywood.com/');
	}
	if ($html) {
		$r = array();
		$r['source_name'] =  'Planet Bollywood';
		$r['source_link'] =  $url ? $url : $html['url'];
		$html = $html['content'];


		$title = $html->find('td.tableHeadTopRowCenter', 0);
		if ($title) {
			$title = $title->plaintext;	
		} else  {
			$title = rem($html->find('title', 0)->plaintext, 'Bollywood - Film Review -');
		}

		if (!$force) {
			if (!$title) {
				echo '<h2>possibly wrong site?</h2>';
				//echo $html->innertext;
			} else if (!simple_compare($title, $film['title'])) {
				echo '<h2>film mismatch: '.$film['title'].' != '.$title.'</h2>';
				return;	
			} else {
				if (stripos($html->find('title', 0)->plaintext, 'review') === false) {
					echo 'not a review page?<br/>';
					return;
				}
				echo '<h2>title match: '.$film['title'].' == '.$title.'</h2>';
			}
		}
	
		if ($rating ) {
			$r['rating'] = $rating;	
			//update_film_with_reviews($film, $r['rating'], $votes[1], $write);
		}
	
		$article = '';
		$img = null;

		foreach ($html->find('p') as $p) {
			if ($p->align == 'center') {
				continue;	
			}
			if (strpos($p->plaintext, 'Comments') !== false
				&& strpos($p->plaintext, 'About Us') !== false
				&& strpos($p->plaintext, 'Advertise') !== false) {
				break;	
			}
			
			if (   (strpos($p->plaintext, 'Producer :') !== false)
				|| (strpos($p->plaintext, 'Director :') !== false)
				|| (strpos($p->plaintext, 'Director:') !== false)
				|| (strpos($p->plaintext, 'Rating :') !== false)
				|| (strpos($p->plaintext, 'Released on :') !== false)
				|| (strpos($p->plaintext, 'Released on:') !== false)
				|| (strpos($p->plaintext, 'Reviewed by:') !== false)
				|| (strpos($p->plaintext, 'Would you like to contribute') !== false)

				) {
				continue;
			}
			
			if (!$img) {
				$img_tag = $p->find('img', 0);
				if ($img_tag) {
					$img = $img_tag->src;
				}
			}
			$article .= strip_html($p->innertext).'<br/>';
		}
		
		if ($img && strpos($img, 'ratings/') !== false) {
			$r['rating'] = 10 * rem($img, array('../ratings/', '.jpg'));
			$img = null;
		}
		
			
		
		if ($article) {
			for ($i=5; $i !=0; $i--) {
				$article = str_replace('     ', ' ', $article);	
			}
			$article = strip_html($article);
			
			$excerpt = array_filter(explode('<br/>', $article));
			$excerpt = end($excerpt);
			
			$r['article'] = $article;
			$r['excerpt'] = $excerpt;
			if ($img) {
				$r['img_src'] = $img;	
			}
		}
		
		
		$r['run'] = 'planetb2';
		$r['film_id'] = $film['id'];
				
		$html->clear();
		$data[] = $r;
	}	
}

function get_masand_rating($film, &$data, &$err) {
	$html = get_scraped_review($film, 'masand', 'review ibn masand');
	if ($html) {
		$r = array();
		$r['source_name'] =  'IBN';
		$r['source_link'] =  $html['url'];
		$html = $html['content'];
		$content_div = $html->find('div.left-in-contant-text', 0);
		if (!$content_div) {
			return;	
		}
		$article = '';
		foreach ($content_div->find('p[id=text]') as $p) {
			if (strpos($p->plaintext, 'Cast:') !== false
				|| strpos($p->plaintext, 'Direction:') !== false
				|| strpos($p->plaintext, 'Director:') !== false
			) {
				continue;
			}
			if ((stripos($p->plaintext, 'write your own review') !== false)) {
				break;
			}
						
			if (strpos($p->plaintext, 'ating:') !== false) {
				$rating = trim(rem($p->plaintext, 'Rating:'));
				$rating = trim(head(explode('(', $rating)));
				$rating = explode('/', $rating);
				if (isset($rating[0]) && isset($rating[1])) {
					$r['rating'] = ((int) $rating[0]) / ((int) $rating[1]) * 100;	
				}
			} else {	
				$article .= $p->innertext.'<br/>';
			}
		}
		$r['article'] = $article;
		$r['author'] = 'Rajeev Masand';
		
		$player_div = $html->find('div[id=player1]', 0);
		if ($player_div) {
			foreach ($player_div->find('img') as $img) {
				if (strpos($img->src, 'static.ibnlive.com') !== false) {
					$r['img_src'] = $img->src;
					break;	
				}
			}
		}
		if (!isset($r['img_src'])) {
			$img_div = $html->find('div.hm-pic', 0);
			if ($img_div) {
				foreach ($img_div->find('img') as $img) {
					if (strpos($img->src, 'static.ibnlive.com') !== false) {
						$r['img_src'] = $img->src;
						break;	
					}
				}
			}
				
		}
		$html->clear();
		$data[] = $r;		
	}
}


function get_ndtv_rating($film, &$data, &$err) {
	$html = get_scraped_review($film, 'ndtv');
	if ($html) {
		$r = array();
		$r['source_name'] =  $html['source'];
		$r['source_link'] =  $html['url'];
		$html = $html['content'];		
		$author = $html->find('span[id=lb_StoryBy]', 0);
		if ($author) {
			$author = trim(head(explode(',',$author->plaintext)));
			$r['author'] = $author;
			$new_format = true;
		} else if ($html->find('div.page_title', 0)) {
			$title = $html->find('div.page_title', 0);
			if ($title) {
				$title = $title->plaintext;
				if (strpos($title, 'Reviews')) {
					$r['author'] = trim(rem($title, array('Reviews', "'s")));	
				}
			}
			$new_format = false;
		} else {
			return false;	
		}
		
		if ($new_format) {
			$article_new_style = $html->find('span[id=lb_StoryFull]', 0);
			$r['article'] = strip_html($article_new_style->innertext);
			$rating_container = $html->find('span[id=lb_Rating]', 0);
			if ($rating_container) {
				$rating_num = 0;
				foreach ($rating_container->find('img') as $img) {
					if (strpos($img->src, 'star0.gif')) {
						$rating_num += 20;	
					}
				}
				$r['rating'] = $rating_num;
			}
			
			$imgs = $html->find('img.img_table4');
			if ($imgs) {
				foreach ($imgs as $img) {
					if (strpos($img->src, 'images/reviews') !== false) {
						$r['img_src'] = 'http://movies.ndtv.com/'.$img->src;
						continue;	
					}
				}
			}	
		} else {
			// plain blog post
			$article_old_style = $html->find('div.blog-post-content', 0);
			$r['article'] = strip_html($article_old_style->innertext);
			$img = $article_old_style->find('img.mt-image-left', 0);
			if ($img) {
				$r['img_src'] = $img->src;	
			}
		}
		
		
		//echo $html->plaintext;
		$html->clear();
		$data[] = $r;	
	}
}


function get_rediff_rating($film, &$data, &$err) {

	$html = get_scraped_review($film, 'rediff');
	if ($html) {
		$r = array();
		$r['source_name'] =  $html['source'];
		$r['source_link'] =  $html['url'];
		$html = $html['content'];
		
		$images = $html->find('img');
		foreach ($images as $image) {
			preg_match("/.*rating[0-9].*.gif/",$image->src, $matches);
			$rating = (is_array($matches) && $matches) ? $matches[0] : null;
			if ($rating) {
				$rater_arr = explode('/', $rating);
				$rater_str = idx($rater_arr, count($rater_arr)-1);
				$rating = get_rediff_raing_num($rater_str);
				if ($rating) {
					$r['rating'] = $rating;				
				}
				break;
			}
		}	
		
		if ($html->find('h1', 0)) {
			$r['excerpt'] = trim(rem($html->find('h1', 0)->plaintext, 'Review:'));
		}
		
		if ($html->find('.arti_content', 0)) {
			$r['img_src'] = $html->find('img.imgwidth', 0)->src;
			$article =  trim(rem(strip_html($html->find('.arti_content', 0)->innertext),
				array(' [ Images ] ', 'Rediff Rating:')));
		} else if ($html->find('font.sb1', 0)) { 
			// old format 1
			$images = $html->find('img');
			foreach ($images as $img) {
				if (strpos($img->src, 'im.rediff.com/movies/20') || strpos($img->src, 'im.rediff.com/movies/19')) {
					$r['img_src'] = $img->src;
					break;
				}
			}
			
			$subtitles = $html->find('font.sb1');
			foreach ($subtitles as $subtitle) {
				$subtitle = $subtitle->plaintext;
				if (strpos($subtitle, 'IST') && strpos($subtitle, '|')) {
					$subtitle = explode('|', $subtitle);
					$r['author'] = strip_html($subtitle[0]);
					$r['review_time'] = strip_html($subtitle[1]);
					continue;
				}
			}
			
			$article_container = $html->find('font.sb13', 0)->parent;
			$article = '';
			foreach ($article_container->find('p') as $p) {
				$article .= strip_html($p->outertext);
			}
			
		} else {
			$err[] = 'no rediff found for '.$film['id'];
		}
		
		if ($article) {
			$r['article'] = trim(rem(strip_html($article), array('[Images]','Â',' [ Images ] ', 'Rediff Rating:')));
			$data[] = $r;
		}
		
		$html->clear();
	}
}

function get_oneindia_rating($film, &$data, &$err) {
	$html = get_scraped_review($film, 'oneindia');
	if ($html) {
		$r = array();
		$r['source_name'] = 'Times of India';
		$r['source_link'] =  $html['url'];
		$html = $html['content'];
		echo $html->innertext;
		if ($html->find('.align_article_content', 0)) {
			$r['film_id'] = $film['id'];
			if ($html->find('.author_name', 0)) {
				$r['source_name'] = $html->find('.author_name', 0)->first_child()->innertext;
				$r['author']  =  rem(str_replace($html->find('.author_name', 0)->first_child()->innertext,
								'',$html->find('.author_name', 0)->innertext), array('By: ',','));
			} 
			
			if ($html->find('div.left_corner_img', 0)) {
				$img_src = $html->find('div.left_corner_img', 0)->find('img', 0)->src;
			} else {
				$img_src = $html->find('.align_article_content', 0)->find('img', 0)->src;
			}
			if ($img_src) {
				$r['img_src'] = $img_src;
			}
			
			
			$article_div =  $html->find('.align_article_content', 0);			
			$article = rem($article_div->innertext, array($article_div->find('div.space_adjust',0)->innertext));
			
			foreach($html->find('table') as $table) {
				$article = rem($article, $table->outertext);
			}
					
			$r['article'] = strip_html($article);
				
			$data[] = $r;	
		} else {
			$err[] = 'no reviews on oneindia for film '.$film['id']; 
		}
		$html->clear();
	}
}

function get_timesofindia_rating($film_id, &$data, &$err, $url = null) {
	$html = get_scraped_review($film_id, 'timesofindia', 'timesofindia.indiatimes.com', $url);

	if ($html) {
		$r = array();
		$r['source_name'] =  'Times of India';
		$r['source_link'] =  $html['url'];
		$r['film_id'] = $film_id;
		$html = $html['content'];
		//echo $html->outertext;
		if ($html->find('link[rel=canonical]', 0)) {
			$r['source_link'] = $html->find('link[rel=canonical]', 0)->href;
		}
		if ($html->find('div[id=mod-a-body-after-first-para]', 0)) {
			echo 'parsing mod after style';
			$r['article'] = strip_html($html->find('div[id=mod-a-body-after-first-para]', 0)->innertext);
			$img_container = $html->find('div[id=mod-a-body-first-para]', 0);
			$img = $img_container->find('img', 0);
			if ($img) {
				$r['img_src'] = $img->src;	
			}
			$html->clear();
			$data[] = $r;

		} else if ($html->find('span[id=midart]', 0)) {	
			$byline = $html->find('span.byline', 0)->innertext;
			if ($byline) {
				$byline = explode(',', $byline);
				$r['author'] = trim($byline[0]);
				//$r['source_name'] = trim($byline[1]);
				$r['review_time'] = trim($byline[2].', '.$byline[3]);
			}
			$article_span =  $html->find('span[id=midart]', 0);
			$article_containers =  $article_span->find('div');
			if (count($article_containers) == 1) {
				echo 'single div';
				$article_container = $article_containers[0];
				$article_jonx = $article_container->find('span[id=brr]', 0)->outertext;
				$article = str_replace($article_jonx, '', $article_container->innertext);
				
				$rating = null;
				$rating_div = $html->find('td.flmcast', 0);
				if ($rating_div) {
					$rating = $rating_div->first_child()->children(1)->class;
					if ($rating) {
						$rating = rem($rating, 'rtimg');
						if ($rating < 6) {
							$rating *= 10;
						}
						$rating *= 2;
						$r['rating'] = $rating;
					}
				} else {
					$err[] =  'no rating for times of india for '.$film_id;
				}
			} else if ($article_span->find('div.Normal', 1)) {
				$r['img_src'] = 'http://timesofindia.indiatimes.com/'.$article_span->find('div.Normal', 1)->find('img', 0)->src;
				$cur_child = $html->find('span[id=voteresultcontainer]', 0);
				if ($cur_child) {
					$cur_child = $cur_child->parent();
				}

				$article = '';
				foreach ($article_span->find('div') as $div) {
					if ((strpos($div->plaintext, 'Critic\'s Rating') !== false && strlen($div->plaintext) < 60)
					|| (strpos($div->innertext, 'showbelly') !== false && strlen($div->plaintext) < 60)
					|| (strpos($div->plaintext, 'Cast: ') !== false && strlen($div->plaintext) < 60)
					|| (strpos($div->plaintext, 'Direction: ') !== false && strlen($div->plaintext) < 60)
					|| (strpos($div->plaintext, 'Still from ') !== false && strlen($div->plaintext) < 60)					 
					|| (strpos($div->plaintext, 'Duration: ') !== false  && strlen($div->plaintext) < 60)) {
						continue;
					}
					if ($div->find('table', 0)) {
						continue;	
					}
					if ($div->align == 'left') {
						continue;	
					}					
					$article .= $div->innertext;
				}	
				if ($cur_child) {
					while ($cur_child->prev_sibling()) {
						if ($cur_child->tag == 'img') {
							$r['rating'] = get_rating_from_timesofindia_img($cur_child->src);
							break;
						} else {
							$cur_child = $cur_child->prev_sibling();
						}
					}
				}
				
			} else {
				$err[] = 'unknown format for timesofindia article midart layout';
			}
			$r['article'] = trim(rem(strip_html($article), 
				array('*downshowrating=1','Read Indiatimes review', 'Critics rating:')));

	
			$html->clear();
			$data[] = $r;
		} else {
			$err[] =  'no timesofindia for '.$film_id;
		}
	}
}


function get_rating_from_timesofindia_img($t) {
	if (strpos($t, '2493890')) {
		return 70;
	}
	echo 'unknown rating img for TOI';
}

function get_rediff_raing_num($rater_str) {
	$num = (int) rem($rater_str, array('.gif','rating'));
	if ($num > 5) {
		return null;
	} else {
		return $num * 20;
	}
}

function get_existing_reviews($source_link, $film_id) {
	global $link;
	$sql = sprintf("SELECT * FROM  `reviews` WHERE  `source_link` LIKE '%s' AND film_id = %d", $source_link, $film_id);
	echo $sql;
	$r = mysql_query($sql);
	$objs = array();
	if (mysql_num_rows($r)>0) {
		while ($row = mysql_fetch_assoc($r)) {
			$objs[] = $row;
		}
	}
	return $objs;
}

function get_reviews_from_run($source_link, $run) {
	global $link;
	$sql = sprintf("SELECT * FROM  `reviews` WHERE  `source_link` LIKE '%s' AND run = '%s'", $source_link, $run);
	echo $sql;
	$r = mysql_query($sql);
	$objs = array();
	if (mysql_num_rows($r)>0) {
		while ($row = mysql_fetch_assoc($r)) {
			$objs[] = $row;
		}
	}
	return $objs;
}



function get_scraped_review($film, $type, $search_term = null, $url = null) {
	if ($search_term) {
		$term = $film['title'].' '.$search_term;	
	} else {
		$term = $film['title'].' '.$film['year'].' review '.$type;
	}
	if (!$url) {
		$url  = "http://www.google.com/search?hl=en&num=1&q=" . urlencode($term) . "&btnI=I%27m+Feeling+Lucky";
	}
	
	echo '<br/>checking url '.render_link($url, $url, false).':<br/><br/> ';
	$html_str = get_url($url, true);
	if (stripos($html_str, "302 Moved") !== false ) {
		echo '...following 302...';
		$html_str = get_url(match('/HREF="(.*?)"/ms', $html_str, 1), $follow = true);
	}	
	
	return array('content' => str_get_html($html_str), 'url' => $url, 'source' => $type);
}





?>