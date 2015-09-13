<?php

define('REVIEW_MAX_LENGTH', 100);

function sanitize_review_text($text) {
  $find[] = 'â€œ'; // left side double smart quote
  $find[] = 'â€'; // right side double smart quote
  $find[] = 'â€˜'; // left side single smart quote
  $find[] = 'â€™'; // right side single smart quote
  $find[] = 'â€“'; // en dash
  $find[] = 'â€¦'; // elipsis
  $find[] = 'â€”'; // em dash

  $replace[] = '"';
  $replace[] = '"';
  $replace[] = "'";
  $replace[] = "'";
  $replace[] = "-";
  $replace[] = "...";
  $replace[] = "-";

  $new_text = str_replace($find, $replace, $text);

  if ($text != $new_text) {
    slog($text);
  }
  return $new_text;
}


function render_review($review) {
  list($review_class, $rating) = get_rating_info($review);
  //slog($rating . 'for rating '.$review['rating']. ' and thumb '.$review['thumbs']);
    if (strlen($review['excerpt']) > REVIEW_MAX_LENGTH) {
        $excerpts = array_filter(explode('. ', trim($review['excerpt'])));
        $review['excerpt'] = (count($excerpts) > 1
                              && (strlen($excerpts[0]) +
                                  strlen($excerpts[count($excerpts)-1]) < REVIEW_MAX_LENGTH)) ?
            $excerpts[0].' ... '.$excerpts[count($excerpts)-1] : $excerpts[count($excerpts)-1];
     }
    $review['excerpt'] = sanitize_review_text(
      ensure_ends_with_period($review['excerpt']));

    $footer = '<div class="review-footer">';
    if ($review['reviewer']) {
      $footer .= $review['reviewer'].' · ';
    }
    if ($review['dishoom_article_id']) {
      $review_class .= ' featured-review';
      $source_link =
        render_article_link(
          array('id' => $review['dishoom_article_id']),
          'Dishoom'
        );
    } else {
      $source_link =
        render_external_link(
          $review['source_name'].' '.render_local_image('open_external.gif'),
          $review['source_link']
        );
    }
    $footer .= $source_link;

    if (is_admin()) {
      $footer .= ' ('.render_link('edit', 'cms/edit.php?id='.$review['id'].'&type=review').')';
    }
    $footer .= '</div>';

    $ret = '<li class="review '.$review_class.'">';
    if ($review_class !== 'no-rating') {
      $ret .= '<div class="rating-container review-rating-container"><div class="rating special-font">'
	.$rating.'</div></div>                            ';
}
    $ret .= '<div class="review-text"><p>'.ucfirst(convert_smart_quotes($review['excerpt'])).'</p></div>'
      .$footer.'</li>';

    return $ret;
}

function get_review_list($reviews, $film) {
  $review_list = array();

  // Put dishoom reviews on top
  $dishoom_reviews = array();
  foreach ($reviews as $id => $review) {
    if ($review['dishoom_article_id']) {
      unset($reviews[$id]);
      $dishoom_reviews[] = $review;
    }
  }

  foreach ($dishoom_reviews as $review) {
    $review_list[] = render_review($review);
  }

	foreach ($reviews as $review) {
    if (!$review['excerpt'] || strlen($review['excerpt']) < 5 ||
        (strlen($review['excerpt']) < (REVIEW_MAX_LENGTH / 5) &&
         !starts_with_capital_letter($review['excerpt']) && !ends_with_period($review['excerpt']))) {
		  continue;
		}
    $review_list[] = render_review($review);
	}
  return $review_list;
}


?>
