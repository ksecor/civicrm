<?php

class CRM_Validate {

  static function name( $str ) {

    // check length etc
    if ( empty( $str ) || strlen( $str ) < 3 || strlen( $str ) > 127 ) {
      return false;
    }
    
    // make sure it include valid characters, alpha numeric and underscores
    if ( ! preg_match('/^[a-z][\w\s\'\&\,\$\#\-\.\"]+$/i', $str ) ) {
      return false;
    }

    return true;
  }

  static function variable( $str ) {
    // check length etc
    if ( empty( $str ) || strlen( $str ) < 3 || strlen( $str ) > 31 ) {
      return false;
    }
    
    // make sure it include valid characters, alpha numeric and underscores
    if ( ! preg_match('/^[a-z][\w]+$/i', $str ) ) {
      return false;
    }

    return true;
  }

  static function phoneNumber( $phone ) {
    // check length etc
    if ( empty( $phone ) || strlen( $phone ) < 10 ) {
      return false;
    }
    
    // make sure it include valid characters, (, \s and numeric
    if ( ! preg_match('/^[\w\(\)\-\.\ ]+$/', $phone ) ) {
      return false;
    }
    return true;
  }


  static function queryString( $qStr ) {

    // check length etc
    if ( empty( $qStr ) || strlen( $qStr ) < 3 || strlen( $qStr ) > 127 ) {
      return false;
    }
    
    // make sure it include valid characters, alpha numeric and underscores
    if ( ! preg_match('/^[\w\s\%\'\&\,\$\#]+$/i', $qStr ) ) {
      return false;
    }

    return true;
  }

  static function url( $url, $domainCheck = false) {
    $purl = parse_url($url);
    if (preg_match('|^http$|i', @$purl['scheme']) && !empty($purl['host'])) {
      if ($domainCheck && function_exists('checkdnsrr')) {
        if (checkdnsrr($purl['host'], 'A')) {
          return true;
        } else {
          return false;
        }
      }
      return true;
    }
    return false;
  }

  static function dateStr($value, $default = null) {
    if (is_string($value) &&
        preg_match('/^\d\d\d\d-\d\d-\d\d$/', $value)) {
      return $value;
    }
    return $default;
  }
  
  static function integer($value, $default = null) {
    if (is_int($value)) {
      return $value;
    }
    
    if (is_numeric($value) && preg_match('/^\d+$/', $value)) {
      return $value;
    }

    return $default;
  }

  static function string($value, $maxLength = 0, $default = null) {
    if (is_string($value) &&
        ($maxLength <= 0 || strlen($value) <= $maxLength)) {
      return $value;
    }
    return $default;
  }
  
}

?>