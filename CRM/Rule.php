<?php

require_once 'HTML/QuickForm/Rule/Email.php';

class CRM_Rule {

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

    static function phone( $phone ) {
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


    static function query( $query ) {

        // check length etc
        if ( empty( $query ) || strlen( $query ) < 3 || strlen( $query ) > 127 ) {
            return false;
        }
    
        // make sure it include valid characters, alpha numeric and underscores
        if ( ! preg_match('/^[\w\s\%\'\&\,\$\#]+$/i', $query ) ) {
            return false;
        }

        return true;
    }

    static function url( $url, $checkDomain = false) {
        $options = array( 'domain_check'    => $checkDomain,
                          'allowed_schemes' => array( 'http', 'https', 'mailto', 'ftp' ) );

        return Validate::uri( $url, $options );
    }

    static function date($value, $default = null) {
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

    static function email($value, $checkDomain = false) {
        static $qfRule = null;
        if ( ! isset( $qfRule ) ) {
            $qfRule = new HTML_QuickForm_Rule_Email();
        }
        return $qfRule->validate( $value, $checkDomain );
    }

}

?>
