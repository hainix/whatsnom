<?php

final class ListQuery {
  private
    $qualifier,
    $type,
    $city,
    $user = null,
    $count = null;
  function __construct($qualifier, $type, $city) {
    $this->qualifier = $qualifier;
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

  public function getQualifier() {
    return $this->qualifier;
  }

  public function getQueryString() {
    return 'c='.$this->city.'&t='.$this->type.'&q='.$this->qualifier;
  }

  public function getPreHeader() {
    if ($this->user) {
      $pre_header = $this->user['first_name'].'\'s';
      if ($this->qualifier) {
        $pre_header .= ' Top';
      }
    } else if ($this->count == 10) {
      $pre_header = 'Top '.$this->count;
    } else if (!$this->qualifier) {
      $pre_header = 'The';
    } else {
      $pre_header = 'Top';
    }
    return $pre_header;
  }

  public function getShortTitle() {
   return
     ($this->user ?  $this->user['first_name'].'\'s Top ' : 'Top 10 ')
     .($this->getQualifier()
         ? ListQualifiers::getName($this->getQualifier())
         : null)
      .' '
      . ($this->getType()
         ? ListTypes::getName($this->getType())
         : 'Spots')
      . ($this->getCity()
         ? ' <span class="subtle">'.Cities::getName($this->getCity()).'</span>'
         : null)
      ;
  }

  public function getTitle() {
    return
      $this->getPreHeader()
      .' '
      . ($this->getQualifier()
         ? ListQualifiers::getName($this->getQualifier())
         : 'Best')
      .' '
      . ($this->getType()
         ? ListTypes::getName($this->getType())
         : 'Spots')
      . ($this->getCity()
         ? ' in '.Cities::getName($this->getCity())
         : null)
      ;
  }

}