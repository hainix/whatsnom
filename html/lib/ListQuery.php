<?php

final class ListQuery {
  private
    $type,
    $city,
    $user = null,
    $count = null;
  function __construct($type, $city) {
    $this->type = $type;
    $this->city = $city;
  }

  public function setCount($count) {
    $this->count = $count;
    return $this;
  }

  public function setUser($user) {
    $this->user = $user;
    return $this;
  }

  public function getCity() {
    return $this->city;
  }

  public function getCount() {
    return $this->count;
  }

  public function getType() {
    return $this->type;
  }

  public function getQueryString() {
    return 'c='.$this->city.'&t='.$this->type;
  }

  public function getPreHeader() {
    if ($this->user) {
      $pre_header = $this->user['first_name'].'\'s';
    } else if ($this->count == 10) {
      $pre_header = 'Top '.$this->count;
    } else {
      $pre_header = 'Best';
    }
    return $pre_header.' ';
  }

  public function getShortTitle() {
   return
     ($this->user ?  $this->user['first_name'].'\'s Top ' : 'Top ')
     .$this->getNoun()

      . ($this->getCity()
         ? ' <span class="subtle">'.Cities::getName($this->getCity()).'</span>'
         : null)
      ;
  }

  private function getNoun() {
    $noun = 'Spots';
    if ($this->getType()) {
      $type = $this->getType();
      if (idx(ListTypeConfig::$config, $type)) {
        $type_config = ListTypeConfig::$config[$type];
        $noun = $type_config[ListTypeConfig::PLURAL_ENTRY];
      } else {
        $noun = ListTypes::getName($this->getType());
      }
    }
    return $noun;
  }

  public function getCoverURL() {
    if ($this->getType()) {
      $type = $this->getType();
      if (idx(ListTypeConfig::$config, $type)) {
        $type_config = ListTypeConfig::$config[$type];
        return BASE_URL.'covers/'.$type_config[ListTypeConfig::COVER];
      }
    }
    return false;
  }

  public function getTitle() {

    return
    $this->getPreHeader()
     .$this->getNoun()
      . ($this->getCity()
         ? ' in '.Cities::getName($this->getCity())
         : null)
      ;
  }

}