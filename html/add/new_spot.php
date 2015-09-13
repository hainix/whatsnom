<?php
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/page.php';

$city_id = idx($_GET, 'c');
$type_id = idx($_GET, 't');
$query = idx($_GET, 'query');
$user = FacebookUtils::getUser();
if (!$user) {
  $page = new Page();
  $page
    ->setType('add')
    ->setContent(
      RenderUtils::renderMessage(
        'Sign in to add new spots.'
      )
    )->render();
  exit(1);
}

if (!$city_id || !$query) {
  RenderUtils::go404();
}

include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/data/write.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/api/yelp.php';

$bounds = Cities::getBoundsForCity($city_id);
$url = 'http://api.yelp.com/v2/search?term='.$query
  .'&limit=20&bounds='.$bounds;
$api_response = get_yelp_info($url);
//slog($api_response);

$result_table = null;
$header_message =
  'Rats! Couldn\'t find any spots called "'.$query.'"';
if ($api_response && $api_response->businesses) {
  // Parse response
  $businesses = array();
  foreach ($api_response->businesses as $api_business) {
    $businesses[$api_business->id] =
      get_yelp_business_info($api_business->id, $api_business);
  }

  $existing_spots =
    DataReadUtils::getSpotsFromHandles(array_keys($businesses));
  $existing_spot_keys =
    array_flip(array_pull($existing_spots, 'yelp_id'));

  // Split out new suggestions from known ones
  $existing_businessses = array();
  $new_businesses = array();
  foreach ($businesses as $business) {
    if (isset($existing_spot_keys[$business['yelp_id']])) {
      $existing_businesses[] = $business;
    } else {
      $new_businesses[] = $business;
    }
  }

  $ret = '<div align="center">';
  if ($new_businesses) {
    $header_message = 'Found '
      . ((count($new_businesses) == 1)
         ? 'one new spot'
         : count($new_businesses) .' new spots')
      .' matching "'.$query.'":';

    // Render table
    $result_table = '<table>';
    foreach ($new_businesses as $business) {
      $rand_id = rand(100000, 999999);
      $icon =
        '<form id="add-new-spot-form-'.$rand_id.'">
           <img src="'.BASE_URL.'images/plus.png" class="small-icon" id="add-new-spot-icon-'.$rand_id.'" />
           <input type="hidden" name="city_id" value="'.$city_id.'" />
           <input type="hidden" name="type_id" value="'.$type_id.'" />
           <input type="hidden" name="yelp_id" value="'.$business['yelp_id'].'" />
        </form>';
      $submit_script =
        "<script>
           $(function() {
             $('#add-new-spot-icon-".$rand_id."').click(function() {
               var postData = $('#add-new-spot-form-".$rand_id."').serialize();
               $('#add-new-spot-form-".$rand_id."').html('<img class=\'small-icon\' src=\'".BASE_URL."images/spinner.gif\' />');
               var formURL = '".BASE_URL."ajax/add_yelp_spot.php';
               $.ajax({
                 type: 'POST',
                 url: formURL,
                 data: postData,
                 success: function(data) {
                   $('#add-new-spot-form-".$rand_id."').html(data);
                 }
               });
              return false;
            });
          });
        </script>";
      $result_table .= '<tr>'
        .'<td style="vertical-align: middle;">'
        .$icon
        .$submit_script
        .'</td>'
        .'<td><h4>'
        .RenderUtils::renderExternalLink(
          $business['name'],
          YelpUtils::getYelpURLForHandle($business['yelp_id'])
        )
        .'</h4>'
        .'<img class="rating-stars" src="'.$business['rating_image'].'" />'
        .$business['street_address']
        .'</td>'
        .'</tr>';
    }
    $result_table .= '</table><br/>';
  }
  $ret .= '</div>';
}

$content =
  '<div class="add-spot-container">'
  .RenderUtils::renderMessage(
      '<h5>'.$header_message.'</h5>'
  )
  .$result_table
  .'</div>';

$page = new Page();
$page
->setType(PageTypes::DIALOG)
->setContent($content)
->render();


