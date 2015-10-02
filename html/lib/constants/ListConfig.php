<?php
abstract class ListGenreTypes extends Enum {
  const DRINK            = 1;
  const ACTIVITIES       = 4;
  const FOOD             = 2;
  const CUISINE          = 3;

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
  //const CREPE            = 9;
  const DIM_SUM          = 10;
  //const DRINK            = 11;
  const FAST_FOOD        = 41;
  const FRENCH           = 12;
  const GERMAN           = 13;
  //const HALAL            = 14;
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
  const DATE_BAR           = 48;
  const HAPPY_HOUR         = 49;
  const CHEAP_EATS         = 50;
  const SPEAKEASIES        = 51;
  const ROOFTOP_BARS       = 52;
  const FIRST_DATE_IDEAS   = 53;
  const FIRST_DATE_EATS    = 54;
  const FANCY_DINNER_DATE  = 55;
  const MICHELIN_STARS     = 56;
}



final class ListTypeConfig {
  const ID            = 'id';
  const LIST_NAME     = 'readable_list_name';
  const ENTRY_NAME    = 'readable_entry_name';
  const PLURAL_ENTRY  = 'plural_entry_name';
  const ICON          = 'icon';
  const COVER         = 'cover';
  const GENRE         = 'genre';

  const NUM_PER_LIST  = 30;

  public static $config = array(

    ListTypes::SPEAKEASIES => array(
      self::ID           => ListTypes::SPEAKEASIES,
      self::LIST_NAME    => 'Speakeasies',
      self::ENTRY_NAME   => 'Speakeasy',
      self::PLURAL_ENTRY => 'Speakeasies',
      self::COVER        => 'speakeasies.jpg',
      self::GENRE        => ListGenreTypes::DRINK,
    ),
    ListTypes::MICHELIN_STARS => array(
      self::ID           => ListTypes::MICHELIN_STARS,
      self::LIST_NAME    => 'Michelin Stars',
      self::ENTRY_NAME   => 'Michelin Star Restaurant',
      self::PLURAL_ENTRY => 'Michelin Star Restaurants',
      self::COVER        => 'michelin.jpg',
      self::GENRE        => ListGenreTypes::FOOD,
    ),
    ListTypes::ASIAN => array(
      self::ID           => ListTypes::ASIAN,
      self::LIST_NAME    => 'Asian',
      self::ENTRY_NAME   => 'Asian Food',
      self::PLURAL_ENTRY => 'Asian Food',
      self::COVER        => 'asian.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::BARBEQUE => array(
      self::ID           => ListTypes::BARBEQUE,
      self::LIST_NAME    => 'Barbeque',
      self::ENTRY_NAME   => 'Barbeque Joint',
      self::PLURAL_ENTRY => 'Barbeque Joints',
      self::COVER        => 'barbeque.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::BRUNCH => array(
      self::ID           => ListTypes::BRUNCH,
      self::LIST_NAME    => 'Brunch',
      self::ENTRY_NAME   => 'Brunch Spot',
      self::PLURAL_ENTRY => 'Brunch Spots',
      self::COVER        => 'brunch.jpg',
      self::GENRE        => ListGenreTypes::FOOD,
    ),
    ListTypes::BURGER => array(
      self::ID           => ListTypes::BURGER,
      self::LIST_NAME    => 'Burgers',
      self::ENTRY_NAME   => 'Burger Joint',
      self::PLURAL_ENTRY => 'Burger Joints',
      self::COVER        => 'burger.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::BURMESE => array(
      self::ID           => ListTypes::BURMESE,
      self::LIST_NAME    => 'Burmese',
      self::ENTRY_NAME   => 'Burmese Food',
      self::PLURAL_ENTRY => 'Burmese Food',
      self::COVER        => 'burmese.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::CAJUN => array(
      self::ID           => ListTypes::CAJUN,
      self::LIST_NAME    => 'Cajun',
      self::ENTRY_NAME   => 'Cajun Food',
      self::PLURAL_ENTRY => 'Cajun Food',
      self::COVER        => 'cajun.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::DESSERT => array(
      self::ID           => ListTypes::DESSERT,
      self::LIST_NAME    => 'Dessert',
      self::ENTRY_NAME   => 'Dessert Spot',
      self::PLURAL_ENTRY => 'Dessert Spots',
      self::COVER        => 'dessert.jpg',
      self::GENRE        => ListGenreTypes::FOOD,
    ),
    ListTypes::CHEESESTEAK => array(
      self::ID           => ListTypes::CHEESESTEAK,
      self::LIST_NAME    => 'Cheesesteak',
      self::ENTRY_NAME   => 'Cheesesteak Spot',
      self::PLURAL_ENTRY => 'Cheesesteak Spots',
      self::COVER        => 'cheesesteak.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::DIM_SUM => array(
      self::ID           => ListTypes::DIM_SUM,
      self::LIST_NAME    => 'Dim Sum',
      self::ENTRY_NAME   => 'Dim Sum',
      self::PLURAL_ENTRY => 'Dim Sum',
      self::COVER        => 'dimsum.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::FRENCH => array(
      self::ID           => ListTypes::FRENCH,
      self::LIST_NAME    => 'French',
      self::ENTRY_NAME   => 'French Food',
      self::PLURAL_ENTRY => 'French Food',
      self::COVER        => 'french.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::GERMAN => array(
      self::ID           => ListTypes::GERMAN,
      self::LIST_NAME    => 'German',
      self::ENTRY_NAME   => 'German Food',
      self::PLURAL_ENTRY => 'German Food',
      self::COVER        => 'german.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::HOT_DOG => array(
      self::ID           => ListTypes::HOT_DOG,
      self::LIST_NAME    => 'Hot Dog',
      self::ENTRY_NAME   => 'Hot Dog Spot',
      self::PLURAL_ENTRY => 'Hot Dog Spots',
      self::COVER        => 'hotdog.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::HOT_POT => array(
      self::ID           => ListTypes::HOT_POT,
      self::LIST_NAME    => 'Hot Pot',
      self::ENTRY_NAME   => 'Hot Pot Spot',
      self::PLURAL_ENTRY => 'Hot Pot Spots',
      self::COVER        => 'hotpot.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::ICE_CREAM => array(
      self::ID           => ListTypes::ICE_CREAM,
      self::LIST_NAME    => 'Ice Cream',
      self::ENTRY_NAME   => 'Ice Cream Joint',
      self::PLURAL_ENTRY => 'Ice Cream Joints',
      self::COVER        => 'icecream.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::INDONESIAN => array(
      self::ID           => ListTypes::INDONESIAN,
      self::LIST_NAME    => 'Indonesian',
      self::ENTRY_NAME   => 'Indonesian Food',
      self::PLURAL_ENTRY => 'Indonesian Food',
      self::COVER        => 'indonesian.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::ITALIAN => array(
      self::ID           => ListTypes::ITALIAN,
      self::LIST_NAME    => 'Italian',
      self::ENTRY_NAME   => 'Italian Food',
      self::PLURAL_ENTRY => 'Italian Food',
      self::COVER        => 'italian.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::JAPANESE => array(
      self::ID           => ListTypes::JAPANESE,
      self::LIST_NAME    => 'Japanese',
      self::ENTRY_NAME   => 'Japanese Food',
      self::PLURAL_ENTRY => 'Japanese Food',
      self::COVER        => 'japanese.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::MALAYSIAN => array(
      self::ID           => ListTypes::MALAYSIAN,
      self::LIST_NAME    => 'Malaysian',
      self::ENTRY_NAME   => 'Malaysian Food',
      self::PLURAL_ENTRY => 'Malaysian Food',
      self::COVER        => 'malaysian.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::MEXICAN => array(
      self::ID           => ListTypes::MEXICAN,
      self::LIST_NAME    => 'Mexican',
      self::ENTRY_NAME   => 'Mexican Food',
      self::PLURAL_ENTRY => 'Mexican Food',
      self::COVER        => 'mexican.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::PERUVIAN => array(
      self::ID           => ListTypes::PERUVIAN,
      self::LIST_NAME    => 'Peruvian',
      self::ENTRY_NAME   => 'Peruvian Food',
      self::PLURAL_ENTRY => 'Peruvian Food',
      self::COVER        => 'peruvian.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::PIZZA => array(
      self::ID           => ListTypes::PIZZA,
      self::LIST_NAME    => 'Pizza',
      self::ENTRY_NAME   => 'Pizza Spot',
      self::PLURAL_ENTRY => 'Pizza Spots',
      self::COVER        => 'pizza.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::RAW => array(
      self::ID           => ListTypes::RAW,
      self::LIST_NAME    => 'Raw Food',
      self::ENTRY_NAME   => 'Raw Food',
      self::PLURAL_ENTRY => 'Raw Food',
      self::COVER        => 'raw.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::SALAD => array(
      self::ID           => ListTypes::SALAD,
      self::LIST_NAME    => 'Salad',
      self::ENTRY_NAME   => 'Salad Spot',
      self::PLURAL_ENTRY => 'Salad Spot',
      self::COVER        => 'salad.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::SANDWICH => array(
      self::ID           => ListTypes::SANDWICH,
      self::LIST_NAME    => 'Sandwich',
      self::ENTRY_NAME   => 'Sandwich Spot',
      self::PLURAL_ENTRY => 'Sandwich Spots',
      self::COVER        => 'sandwich.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::SEAFOOD => array(
      self::ID           => ListTypes::SEAFOOD,
      self::LIST_NAME    => 'Seafood',
      self::ENTRY_NAME   => 'Seafood',
      self::PLURAL_ENTRY => 'Seafood',
      self::COVER        => 'seafood.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::SOUL => array(
      self::ID           => ListTypes::SOUL,
      self::LIST_NAME    => 'Soul Food',
      self::ENTRY_NAME   => 'Soul Food',
      self::PLURAL_ENTRY => 'Soul Food',
      self::COVER        => 'soul.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::SOUTHERN => array(
      self::ID           => ListTypes::SOUTHERN,
      self::LIST_NAME    => 'Southern',
      self::ENTRY_NAME   => 'Southern',
      self::PLURAL_ENTRY => 'Southern',
      self::COVER        => 'southern.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::SPANISH => array(
      self::ID           => ListTypes::SPANISH,
      self::LIST_NAME    => 'Spanish',
      self::ENTRY_NAME   => 'Spanish Food',
      self::PLURAL_ENTRY => 'Spanish Food',
      self::COVER        => 'spanish.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::STEAK => array(
      self::ID           => ListTypes::STEAK,
      self::LIST_NAME    => 'Steak',
      self::ENTRY_NAME   => 'Steakhouse',
      self::PLURAL_ENTRY => 'Steakhouses',
      self::COVER        => 'steakhouse.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::SUSHI => array(
      self::ID           => ListTypes::SUSHI,
      self::LIST_NAME    => 'Sushi',
      self::ENTRY_NAME   => 'Sushi',
      self::PLURAL_ENTRY => 'Sushi',
      self::COVER        => 'sushi.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::TAPAS => array(
      self::ID           => ListTypes::TAPAS,
      self::LIST_NAME    => 'Tapas',
      self::ENTRY_NAME   => 'Tapas',
      self::PLURAL_ENTRY => 'Tapas',
      self::COVER        => 'tapas.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::TEX_MEX => array(
      self::ID           => ListTypes::TEX_MEX,
      self::LIST_NAME    => 'Tex Mex',
      self::ENTRY_NAME   => 'Tex Mex',
      self::PLURAL_ENTRY => 'Tex Mex',
      self::COVER        => 'texmex.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::VIETNAMESE => array(
      self::ID           => ListTypes::VIETNAMESE,
      self::LIST_NAME    => 'Vietnamese',
      self::ENTRY_NAME   => 'Vietnamese Food',
      self::PLURAL_ENTRY => 'Vietnamese Food',
      self::COVER        => 'vietnamese.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::VEGETARIAN => array(
      self::ID           => ListTypes::VEGETARIAN,
      self::LIST_NAME    => 'Vegetarian',
      self::ENTRY_NAME   => 'Vegetarian Food',
      self::PLURAL_ENTRY => 'Vegetarian Food',
      self::COVER        => 'vegetarian.jpg',
      self::GENRE        => ListGenreTypes::FOOD,
    ),
    ListTypes::WINE => array(
      self::ID           => ListTypes::WINE,
      self::LIST_NAME    => 'Wine Bars',
      self::ENTRY_NAME   => 'Wine Bar',
      self::PLURAL_ENTRY => 'Wine Bars',
      self::COVER        => 'wine.jpg',
      self::GENRE        => ListGenreTypes::DRINK,
    ),
    ListTypes::WINGS => array(
      self::ID           => ListTypes::WINGS,
      self::LIST_NAME    => 'Wings',
      self::ENTRY_NAME   => 'Wings',
      self::PLURAL_ENTRY => 'Wings',
      self::COVER        => 'wings.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::THAI => array(
      self::ID           => ListTypes::ASIAN,
      self::LIST_NAME    => 'Thai',
      self::ENTRY_NAME   => 'Thai Food',
      self::PLURAL_ENTRY => 'Thai Food',
      self::COVER        => 'thai.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::INDIAN => array(
      self::ID           => ListTypes::INDIAN,
      self::LIST_NAME    => 'Indian',
      self::ENTRY_NAME   => 'Indian Food',
      self::PLURAL_ENTRY => 'Indian Food',
      self::COVER        => 'indian.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),
    ListTypes::ROOFTOP_BARS => array(
      self::ID           => ListTypes::ROOFTOP_BARS,
      self::LIST_NAME    => 'Rooftop Bars',
      self::ENTRY_NAME   => 'Rooftop Bar',
      self::PLURAL_ENTRY => 'Rooftop Bars',
      self::COVER        => 'rooftop.jpg',
      self::GENRE        => ListGenreTypes::DRINK,
    ),
    ListTypes::FIRST_DATE_IDEAS => array(
      self::ID           => ListTypes::FIRST_DATE_IDEAS,
      self::LIST_NAME    => 'First Date Ideas',
      self::ENTRY_NAME   => 'First Date Idea',
      self::PLURAL_ENTRY => 'First Date Ideas',
      self::COVER        => 'firstdateideas.jpg',
      self::GENRE        => ListGenreTypes::ACTIVITIES,
    ),
    ListTypes::FIRST_DATE_EATS => array(
      self::ID           => ListTypes::FIRST_DATE_EATS,
      self::LIST_NAME    => 'First Date Eats',
      self::ENTRY_NAME   => 'Casual First Date Spot',
      self::PLURAL_ENTRY => 'Casual First Date Spots',
      self::COVER        => 'firstdate.jpg',
      self::GENRE        => ListGenreTypes::FOOD,
    ),
    ListTypes::FANCY_DINNER_DATE => array(
      self::ID           => ListTypes::FANCY_DINNER_DATE,
      self::LIST_NAME    => 'Fancy Dinner Date',
      self::ENTRY_NAME   => 'Fancy Dinner Date Spot',
      self::PLURAL_ENTRY => 'Fancy Dinner Date Spots',
      self::COVER        => 'dinnerdate.jpg',
      self::GENRE        => ListGenreTypes::FOOD,
    ),
    ListTypes::CHEAP_EATS => array(
      self::ID           => ListTypes::CHEAP_EATS,
      self::LIST_NAME    => 'Cheap Eats',
      self::ENTRY_NAME   => 'Cheap Eats Spot',
      self::PLURAL_ENTRY => 'Cheap Eats Spots',
      self::COVER        => 'cheapeats.jpg',
      self::GENRE        => ListGenreTypes::FOOD,
    ),

    ListTypes::DATE_BAR => array(
      self::ID           => ListTypes::DATE_BAR,
      self::LIST_NAME    => 'Date Bars',
      self::ENTRY_NAME   => 'Date Bar',
      self::PLURAL_ENTRY => 'Date Bars',
      self::COVER        => 'datebar.jpg',
      self::GENRE        => ListGenreTypes::DRINK,
    ),

    ListTypes::HAPPY_HOUR => array(
      self::ID           => ListTypes::HAPPY_HOUR,
      self::LIST_NAME    => 'Happy Hour',
      self::ENTRY_NAME   => 'Happy Hour Spot',
      self::PLURAL_ENTRY => 'Happy Hour Spots',
      self::COVER        => 'happyhour.jpg',
      self::GENRE        => ListGenreTypes::DRINK,
    ),

    ListTypes::RAMEN => array(
      self::ID           => ListTypes::RAMEN,
      self::LIST_NAME    => 'Ramen',
      self::ENTRY_NAME   => 'Ramen Joint',
      self::PLURAL_ENTRY => 'Ramen Joints',
      self::COVER        => 'ramen.jpg',
      self::GENRE        => ListGenreTypes::CUISINE,
    ),

  );

}
