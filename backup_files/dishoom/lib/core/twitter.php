<?php

class TwitterException extends Exception {

}

class TwitterClient {
  const API_URL = 'https://api.twitter.com/1/';
  const JSON = 'json';
  const XML  = 'xml';

  const CONSUMER_KEY = 'R0whQVV1dq12wc8yFJjyNQ';
  const CONSUMER_SECRET = 'mQFAZxeHzhAJBqqoolAVXWelYEVoftixHO13JWXOmQ';
  const OAUTH_TOKEN = '237601873-GwGPAsSxEq1V2eGmWnjcpREHdG4qkUdaKKYSDDb7';
  const OAUTH_SECRET = 'VeNxTjlClfKfl6UcwYkkMkS2PTBQdPj7YpZ7mVj9CBE';

  private $response_format = self::JSON;
  private $response_formats = array(
    self::JSON,
    self::XML,
  );
  private $oauth;

  public function __construct() {
    $this->connect(self::CONSUMER_KEY,
                   self::CONSUMER_SECRET,
                   self::OAUTH_TOKEN,
                   self::OAUTH_SECRET);
  }

  public function getTweets($handle, $limit = 5) {
    $url = 'statuses/user_timeline';
    $params = array('include_entities' => false,
                    'include_rtw' => false,
                    'screen_name' => $handle,
                    'trim_user' => true,
                    'exclude_replies' => true,
                    'count' => $limit);
    $response = $this->get($url, $params);
    $tweets = json_decode($response, true);
    $ret = array();
    foreach ($tweets as $tweet) {
      $data = array();
      $data['time'] = strtotime($tweet['created_at']);
      $data['message'] = $this->processLinks($tweet['text']);
      $ret[] = $data;
    }
    return $ret;
  }

  /** Method to add hyperlink html tags to any urls, twitter ids or hashtags in the tweet */
  private static function processLinks($text) {
    $text = utf8_decode( $text );
    $text = preg_replace('@(https?://([-\w\.]+)+(d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>',  $text );
    $text = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $text);
    $text = preg_replace("#(^|[\n ])\#([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://hashtags.org/search?query=\\2\" >#\\2</a>'", $text);
    return $text;
  }



  public function setResponseFormat($format) {
    if (in_array($format, $this->response_formats)) {
      $this->response_format = $format;
    } else {
      throw new InvalidArgumentException('Unsupported response format: ' . $format);
    }
  }

  public function get($path, array $params, $expected_status=200) {
    return $this->request($path, $params, $expected_status, OAUTH_HTTP_METHOD_GET);
  }

  public function post($path, array $params, $expected_status=200) {
    return $this->request($path, $params, $expected_status, OAUTH_HTTP_METHOD_POST);
  }

  private function connect($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret) {
    $this->oauth = new OAuth($consumer_key, $consumer_secret, OAUTH_SIG_METHOD_HMACSHA1);
    $this->oauth->setToken($oauth_token, $oauth_token_secret);
    $this->oauth->enableDebug();
  }

  private function request($path, array $params, $expected_status, $method) {
    $response = null;
    try {
      $url      = self::API_URL . $path . '.'. $this->response_format;
      $data     = $this->oauth->fetch($url, $params, $method);
      $response = $this->oauth->getLastResponse();
      $info     = $this->oauth->getLastResponseInfo();
      $status   = (int)$info['http_code'];
      if ($status != $expected_status) {
        throw new RuntimeException("$url: expected HTTP $expected_status; got $status ($response)");
      }
    } catch (OAuthException $e) {
      $message  = $e->getMessage();
      $response = $this->oauth->getLastResponse();
      $info     = $this->oauth->getLastResponseInfo();
      error_log($message);
      error_log(print_r($response, true));
      error_log(print_r($info, true));
      return null;
      //throw new TwitterException($response, (int)$info['http_code']);
    }
    return $response;
  }
}