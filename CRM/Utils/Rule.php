<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/


require_once 'HTML/QuickForm/Rule/Email.php';

class CRM_Utils_Rule {

    static function title( $str ) {
    
        // check length etc
        if ( empty( $str ) || strlen( $str ) < 3 || strlen( $str ) > 127 ) {
            return false;
        }
    
        // Make sure it include valid characters, alpha numeric and underscores
        if ( ! preg_match('/^[a-z][\w\s\'\&\,\$\#\-\.\"\?]+$/i', $str ) ) {
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
        if ( ! preg_match('/^[\w\s]+$/i', $str ) ) {
            return false;
        }

        return true;
    }

    static function phone( $phone ) {
        // check length etc
        if ( empty( $phone ) || strlen( $phone ) > 16 ) {
            return false;
        }
    
        // make sure it include valid characters, (, \s and numeric
        if ( preg_match('/^[\d\(\)\-\.\s]+$/', $phone ) ) {
            return true;
        }
        return false;
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
            preg_match('/^\d\d\d\d-?\d\d-?\d\d$/', $value)) {
            return $value;
        }
        return $default;
    }

    /**
     * check the validity of the date (in qf format)
     * note that only a year is valid, or a mon-year is
     * also valid in addition to day-mon-year
     *
     * @param array $date
     *
     * @return bool true if valid date
     * @static
     * @access public
     */
    static function qfDate($date) {
        if ( ! $date['d'] && ! $date['M'] && ! $date['Y'] ) {
            return true;
        }

        $day = $mnt = 1;
        $year = 0;
        if ($date['d']) $day = $date['d'];
        if ($date['M']) $mnt = $date['M'];
        if ($date['Y']) $year = $date['Y'];

        // if we have day we need mon, and if we have mon we need year
        if ( ( $date['d'] && ! $date['M'] ) ||
             ( $date['d'] && ! $date['Y'] ) ||
             ( $date['M'] && ! $date['Y'] ) ) {
            return false;
        }

        if ( ! empty( $day ) || ! empty( $mnt ) || ! empty( $year ) ) {
            return checkdate( $mnt, $day, $year );
        }
        return true;
    }

    static function integer($value) {
        if (is_int($value)) {
            return true;
        }
    
        if (is_numeric($value) && preg_match('/^\d+$/', $value)) {
            return true;
        }

        return false;
    }

    static function numeric($value) {
        return preg_match( '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/', $value ) ? true : false;
    }

    static function money($value) {
        if ( self::integer( $value ) ) {
            return true;
        }
        return preg_match( '/(^\d+\.\d?\d?$)|(^\.\d\d?$)/', $value ) ? true : false;
    }

    static function string($value, $maxLength = 0) {
        if (is_string($value) &&
            ($maxLength === 0 || strlen($value) <= $maxLength)) {
            return true;
        }
        return false;
    }

    static function boolean($value) {
        return preg_match( 
            '/(^(1|0)$)|(^(Y(es)?|N(o)?)$)|(^(T(rue)?|F(alse)?)$)/i', $value) ?
            true : false;
    }

    static function email($value, $checkDomain = false) {
        static $qfRule = null;
        if ( ! isset( $qfRule ) ) {
            $qfRule =& new HTML_QuickForm_Rule_Email();
        }
        return $qfRule->validate( $value, $checkDomain );
    }

    static function emailList( $list, $checkDomain = false ) {
        $emails = explode( ',', $list );
        foreach ( $emails as $email ) {
            $email = trim( $email );
            if ( ! self::email( $email, $checkDomain ) ) {
                return false;
            }
        }
        return true;
    }

    // allow between 4-6 digits as postal code since india needs 6 and US needs 5 (or 
    // if u disregard the first 0, 4 (thanx excel!)
    // piotr: we need to figure out how to localize such rules
    static function postalCode($value) {
        if ( preg_match('/^\d{4,6}(-\d{4})?$/', $value) ) {
            return true;
        }
        return false;
    }

    /**
     * see how file rules are written in HTML/QuickForm/file.php
     * Checks to make sure the uploaded file is ascii
     *
     * @param     array     Uploaded file info (from $_FILES)
     * @access    private
     * @return    bool      true if file has been uploaded, false otherwise
     */
    static function asciiFile( $elementValue ) {
        if ((isset($elementValue['error']) && $elementValue['error'] == 0) ||
            (!empty($elementValue['tmp_name']) && $elementValue['tmp_name'] != 'none')) {
            return CRM_Utils_File::isAscii($elementValue['tmp_name']);
        }
        return false;
    }

    /**
     * see how file rules are written in HTML/QuickForm/file.php
     * Checks to make sure the uploaded file is html
     *
     * @param     array     Uploaded file info (from $_FILES)
     * @access    private
     * @return    bool      true if file has been uploaded, false otherwise
     */
    static function htmlFile( $elementValue ) {
        if ((isset($elementValue['error']) && $elementValue['error'] == 0) ||
            (!empty($elementValue['tmp_name']) && $elementValue['tmp_name'] != 'none')) {
            return CRM_Utils_File::isHtmlFile($elementValue['tmp_name']);
        }
        return false;
    }

    /**
     * Check if there is a record with the same name in the db
     *
     * @param string $value     the value of the field we are checking
     * @param array  $options   the daoName and fieldName (optional )
     *
     * @return boolean     true if object exists
     * @access public
     * @static
     */
    static function objectExists( $value, $options ) {
        return CRM_Core_DAO::objectExists( $value, $options[0], $options[1], CRM_Utils_Array::value( 2, $options, 'name' ) );
    }

    static function creditCardNumber( $value, $type ) {
        require_once 'Validate/Finance/CreditCard.php';

        return Validate_Finance_CreditCard::number( $value, $type );
    }

    static function cvv( $value, $type ) {
        require_once 'Validate/Finance/CreditCard.php';

        return Validate_Finance_CreditCard::cvv( $value, $type );
    }

}

?>
