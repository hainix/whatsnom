<?php

final class TwitterClient {
  private $handle;
  public function __construct($handle) {
    $this->handle = $handle;
  }

  private static function getTweetsXML($twitter_id) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, "http://twitter.com/statuses/user_timeline/$twitter_id.xml");
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($c, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($c);
    $responseInfo = curl_getinfo($c);
    curl_close($c);
    if (intval($responseInfo['http_code']) == 200) {
      if (class_exists('SimpleXMLElement')) {
        $xml = new SimpleXMLElement($response);
        return $xml;
      } else {
        return $response;
      }
    } else {
      return false;
    }
  }

  /** Method to add hyperlink html tags to any urls, twitter ids or hashtags in the tweet */
  private static function processLinks($text) {
    $text = utf8_decode( $text );
    $text = preg_replace('@(https?://([-\w\.]+)+(d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>',  $text );
    $text = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $text);
    $text = preg_replace("#(^|[\n ])\#([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://hashtags.org/search?query=\\2\" >#\\2</a>'", $text);
    return $text;
  }

  public function getTweets($limit = 5,
                            $includeReplies = false) {
    $twitter_id = $this->handle;
    $i = 0;
    $ret = array();
    if ($twitter_xml = self::getTweetsXML($twitter_id)) {
      foreach ($twitter_xml->status as $key => $status) {
        if ($includeReplies == true
            | substr_count($status->text,"@") == 0 | strpos($status->text,"@") != 0) {
          $message = self::processLinks($status->text);
          $ret[] = array('message' => $message,
                         'time' => strtotime($status->created_at));
          ++$i;
          if ($i == $limit) {
            break;
          }
        }
      }
      return $ret;
    }
    return null;
  }

}

?>