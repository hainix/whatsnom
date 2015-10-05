<?php

abstract class Enum {
  public static function getConstants() {
    $reflect = new ReflectionClass(get_called_class());
    return $reflect->getConstants();
  }

  public static function isValidName($name, $strict = false) {
    $constants = self::getConstants();

    if ($strict) {
      return array_key_exists($name, $constants);
    }

    $keys = array_map('strtolower', array_keys($constants));
    return in_array(strtolower($name), $keys);
  }

  public static function isValidValue($value) {
    $values = array_values(self::getConstants());
    return in_array($value, $values, $strict = true);
  }

  public static function getName($value) {
    if (!$value) {
      return null;
    }
    $keys = array_flip(self::getConstants());
    if (!isset($keys[$value])) {
      return null;
    }
    return self::renderConstant($keys[$value]);
  }

  public static function renderConstant($value){
    return ucwords(strtolower(str_replace('_', ' ', $value)));
  }

}
