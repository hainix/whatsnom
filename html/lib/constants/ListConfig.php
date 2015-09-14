<?php
abstract class ListGenreTypes extends Enum {
  const DRINK            = 1;
  const FOOD             = 2;

  public static function getGenreFromListType($list_type) {
    return ListTypeConfig::$config[$list_type][ListTypeConfig::GENRE];
  }
}

abstract class ListTypes extends Enum {
  const ASIAN            = 1;
  const BARBEQUE         = 2;
  const BRUNCH           = 3;
  const BURGER           = 4;
  const BURMESE          = 44;
  const CAFE             = 5;
  const CAJUN            = 6;
  const DESSERT          = 43;
  const CHEESESTEAK      = 7;
  const CHINESE          = 8;
  const CREPE            = 9;
  const DIM_SUM          = 10;
  const DRINK            = 11;
  const FAST_FOOD        = 41;
  const FRENCH           = 12;
  const GERMAN           = 13;
  const HALAL            = 14;
  const HOT_DOG          = 15;
  const HOT_POT          = 16;
  const ICE_CREAM        = 42;
  const INDIAN           = 17;
  const INDONESIAN       = 18;
  const ITALIAN          = 19;
  const JAPANESE         = 20;
  const MALAYSIAN        = 21;
  const MEXICAN          = 22;
  const MEDITERRANEAN    = 23;
  const PERUVIAN         = 24;
  const PIZZA            = 25;
  const RAMEN            = 26;
  const RAW              = 27;
  const SALAD            = 28;
  const SANDWICH         = 29;
  const SEAFOOD          = 30;
  const SOUL             = 31;
  //const SOUP             = 32;
  const SOUTHERN         = 33;
  const SPANISH          = 34;
  const STEAK            = 35;
  const SUSHI            = 45;
  const TAPAS            = 36;
  const TEX_MEX          = 37;
  const THAI             = 38;
  const VIETNAMESE       = 39;
  const VEGETARIAN       = 46;
  const WINE             = 47;
  const WINGS            = 40;
  const DATE_BAR         = 48;
  const HAPPY_HOUR       = 49;
}



final class ListTypeConfig {
  const ID          = 'id';
  const LIST_NAME   = 'readable_list_name';
  const ENTRY_NAME  = 'readable_entry_name';
  const ICON        = 'icon';
  const GENRE       = 'genre';

  public static $config = array(

    ListTypes::DATE_BAR => array(
      self::ID           => ListTypes::DATE_BAR,
      self::LIST_NAME    => 'Date Bars',
      self::ENTRY_NAME   => 'Date Bar',
      self::ICON         => 'wine',
      self::GENRE        => ListGenreTypes::DRINK,
    ),

    ListTypes::HAPPY_HOUR => array(
      self::ID           => ListTypes::HAPPY_HOUR,
      self::LIST_NAME    => 'Happy Hour',
      self::ENTRY_NAME   => 'Happy Hour Spot',
      self::ICON         => 'beer',
      self::GENRE        => ListGenreTypes::DRINK,
    ),

    ListTypes::RAMEN => array(
      self::ID           => ListTypes::RAMEN,
      self::LIST_NAME    => 'Ramen',
      self::ENTRY_NAME   => 'Ramen Joint',
      self::ICON         => 'chopsticks',
      self::GENRE        => ListGenreTypes::FOOD,
    ),

  );

}

