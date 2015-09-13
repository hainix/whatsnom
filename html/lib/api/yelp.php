<?php

// From http://non-diligent.com/articles/yelp-apiv2-php-example/

include_once  $_SERVER['DOCUMENT_ROOT'].'/lib/OAuth.php';


// For examaple, search for 'tacos' in 'sf'
//$unsigned_url = "http://api.yelp.com/v2/search?term=tacos&location=sf";


// Set your keys here
$consumer_key = "Jy3dCD6iSSFUXw9ZI1BGcQ";
$consumer_secret = "AR20zHkL7pJapgJjgwOL7oivDB4";
$token = "TIe-H_Fcb9gucUHSR9ab6qO-sT8ZIwnj";
$token_secret = "mdhSWvIv_yHLp4VWmDnmWNv5W0A";

// Token object built using the OAuth library
$token = new OAuthToken($token, $token_secret);

// Consumer object built using the OAuth library
$consumer = new OAuthConsumer($consumer_key, $consumer_secret);

// Yelp uses HMAC SHA1 encoding
$signature_method = new OAuthSignatureMethod_HMAC_SHA1();

function get_yelp_business_info($name, $info = null) {
  $info = $info ?: get_yelp_business($name);
  if (!$info && !property_exists($info, 'error')) {
    return null;
  }

  $data = array();
  $data['rating'] =
    property_exists($info, 'rating')
    ? $info->rating * 20
    : null;
  $data['review_count'] =
    property_exists($info, 'review_count')
    ? $info->review_count
    : null;
  $data['phone'] =
    property_exists($info, 'display_phone')
    ? rem($info->display_phone, '+1-')
    : null;
  $location =
    property_exists($info, 'location')
    ? $info->location
    : null;
  $data['address'] = $location && $location->display_address
    ? implode($location->display_address, ', ')
    : null;
  $data['street_address'] = $data['address']
    ? head($location->display_address)
    : null;
  $image =
    property_exists($info, 'image_url')
    ? $info->image_url
    : null;
  if ($image) {
    $image = str_replace('ms.jpg', 'o.jpg', $image);
  }
  $data['rating_image'] =
    $data['rating']
    ? $info->rating_img_url_small
    : null;
  $data['profile_pic'] = $image;
  $data['yelp_id'] = $info->id;
  $data['name'] = $info->name;
  return $data;
}

function get_yelp_business($name) {
  return get_yelp_info('http://api.yelp.com/v2/business/'.urlencode($name));
}

function get_yelp_info($unsigned_url) {
  global $consumer, $token, $signature_method;

  // Build OAuth Request using the OAuth PHP library. Uses the consumer and token object created above.
  $oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);

  // Sign the request
  $oauthrequest->sign_request($signature_method, $consumer, $token);

  // Get the signed URL
  $signed_url = $oauthrequest->to_url();

  // Send Yelp API Call
  $ch = curl_init($signed_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  $data = curl_exec($ch); // Yelp response
  curl_close($ch);

  // Handle Yelp response data
  $response = json_decode($data);
  return $response;
}

?>
