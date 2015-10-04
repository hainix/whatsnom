<?php

abstract class Cities extends Enum {
  const SF               = 1;
  const SOUTH_BAY        = 2;
  const EAST_BAY         = 3;
  const NYC              = 4;

  public static function getBoundsForCity($city_id) {
    switch ($city_id) {
    case self::NYC:
      return
        '40.876357624832586,-73.72992874123156|40.61625443495148,-74.16114212013781';
    case self::SF:
      return
        '37.81788966822529,-122.32658386230469|37.68215892160926,-122.54219055175781';
    case self::SOUTH_BAY:
      return
        '37.66078949021259,-121.69987986940919|37.11522544736797,-122.56230662722169';
    case self::EAST_BAY:
      return
        '37.968301289655436,-121.95769072975963|37.697143874528415,-122.3889041086658';
    }
    return null;
  }

  /* Override for short city names */
  public static function getName($id) {
    if ($id == self::SF) {
      return 'San Francisco';
    } else if ($id == self::NYC) {
      return 'Manhattan';
    }
    return parent::getName($id);
  }
}
