<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

/**
 * Date utilties
 */
class CRM_Utils_Date 
{

    /**
     * format a date by padding it with leading '0'.
     *
     * @param array  $date ('Y', 'M', 'd')
     * @param string $separator   the seperator to use when formatting the date
     * @param string $invalidDate what to return if the date is invalid
     *
     * @return string - formatted string for date
     *
     * @static
     */
    static function format( $date, $separator = '', $invalidDate = 0 )
    {
        if ( is_numeric($date) && 
             ( ( strlen($date) == 8 ) || ( strlen($date) == 14 ) ) ) {
            return $date;
        }

        if ( ! is_array( $date ) ||
             CRM_Utils_System::isNull( $date ) ||
             empty( $date['Y'] ) ) {
            return $invalidDate;
        }

        $date['Y'] = (int ) $date['Y'];
        if ( $date['Y'] < 1000 || $date['Y'] > 2999 ) {
            return $invalidDate;
        }

        if ( array_key_exists( 'm', $date ) ) {
            $date['M'] = $date['m'] ;
        } else if ( array_key_exists('F',$date) ) {
            $date['M'] = $date['F'] ;
        }
            
        if ( CRM_Utils_Array::value( 'M', $date ) ) {
            $date['M'] = (int ) $date['M'];
            if ( $date['M'] < 1 || $date['M'] > 12 ) {
                return $invalidDate;
            }
        } else {
            $date['M'] = 1;
        }

        if ( CRM_Utils_Array::value( 'd', $date ) ) {
            $date['d'] = (int ) $date['d'];
        } else {
            $date['d'] = 1;
        }

        if ( ! checkdate( $date['M'], $date['d'], $date['Y'] ) ) {
            return $invalidDate;
        }

        $date['M'] = sprintf( '%02d', $date['M'] );
        $date['d'] = sprintf( '%02d', $date['d'] );

        $time = '';
        if (CRM_Utils_Array::value( 'H', $date ) != null ||
            CRM_Utils_Array::value( 'h', $date ) != null ||
            CRM_Utils_Array::value( 'i', $date ) != null ||
            CRM_Utils_Array::value( 's', $date ) != null) {
            // we have time too.. 
            if (CRM_Utils_Array::value( 'h', $date )) {
                if ( CRM_Utils_Array::value( 'A', $date ) == 'PM' or CRM_Utils_Array::value( 'a', $date ) == 'pm') {
                    if ($date['h'] != 12 ) {
                        $date['h'] = $date['h'] + 12;
                    }
                }
                if ( (CRM_Utils_Array::value( 'A', $date ) == 'AM' or CRM_Utils_Array::value( 'a', $date ) == 'am') &&
                     CRM_Utils_Array::value( 'h', $date ) == 12 ) {
                    $date['h'] = '00';
                }
                
                $date['h'] = (int ) $date['h'];
            } else {
                $date['h'] = 0;
            }

            // in 24-hour format the hour is under the 'H' key
            if (CRM_Utils_Array::value('H', $date)) {
                $date['H'] = (int) $date['H'];
            } else {
                $date['H'] = 0;
            }

            if (CRM_Utils_Array::value( 'i', $date )) {
                $date['i'] = (int ) $date['i'];
            } else {
                $date['i'] = 0;
            }

            if ($date['h'] == 0 && $date['H'] != 0) {
                $date['h'] = $date['H'];
            }

            if (CRM_Utils_Array::value( 's', $date )) {
                $date['s'] = (int ) $date['s'];
            } else {
                $date['s'] = 0;
            }

            $date['h'] = sprintf( '%02d', $date['h'] );
            $date['i'] = sprintf( '%02d', $date['i'] );
            $date['s'] = sprintf( '%02d', $date['s'] );

            if ( $separator ) {
                $time = '&nbsp;';
            }
            $time .= $date['h'] . $separator . $date['i'] . $separator . $date['s'];
        }

        return $date['Y'] . $separator . $date['M'] . $separator . $date['d'] . $time;
    }

    /**
     * given a string in mysql format, transform the string 
     * into qf format
     *
     * @param string $date a mysql type date string
     *
     * @return array       a qf formatted date array
     * @static
     */     
    static function &unformat( $date, $separator = '-' ) 
    {
        $value = array( );
        if ( empty( $date ) ) {
            return $value;
        }

        $value['Y'] = $value['M'] = $value['d'] = null;

        if ( $separator != '' ) {
            list( $year, $mon, $day ) = explode( $separator, $date, 3 );
        } else {
            $year = substr( $date, 0, 4 );
            $mon  = substr( $date, 4, 2 );
            $day  = substr( $date, 6, 2 );
        } 
        
        if( strlen( $day ) > 2 ) {
            if( substr_compare( $day,':', 3 ) ) {
                $time = substr( $day, 3, 8 );
                $day  = substr( $day, 0, 2 );
                list( $hr, $min, $sec ) = explode( ':', $time, 3 );
            }
        }
        
        if ( is_numeric( $year ) && $year > 0 ) {
            $value['Y'] = $year;
        }

        if ( is_numeric( $mon ) && $mon > 0 ) {
            $value['M'] = $mon;
        }

        if ( is_numeric( $day ) && $day > 0 ) {
            $value['d'] = $day;
        }

        if ( isset( $hr ) && is_numeric( $hr ) && $hr >= 0 ) {
            $value['h'] = $hr;
            $value['H'] = $hr;
            if( $hr > 12 ) {
                $value['h'] -= 12;
                $value['H'] = $hr;
                $value['A'] = 'PM';
                $value['a'] = 'pm';
            } else if( $hr == 0 ) {
                $value['h'] = 12;
                $value['A'] = 'AM';
                $value['a'] = 'am';
            } else if( $hr == 12 ) {
                $value['A'] = 'PM';
                $value['a'] = 'pm';
            } else {
                $value['A'] = 'AM';
                $value['a'] = 'am';
            }
        }
        
        if ( isset( $min ) && is_numeric( $min ) && $min >= 0 ) {
            $value['i'] = $min;
        }
        return $value;
    }

    /**
     * return abbreviated weekday names according to the locale
     *
     * @return array  0-based array with abbreviated weekday names
     *
     * @static
     */
    static function &getAbbrWeekdayNames()
    {
        static $abbrWeekdayNames;
        if (!isset($abbrWeekdayNames)) {

            // set LC_TIME and build the arrays from locale-provided names
            // June 1st, 1970 was a Monday
            CRM_Core_I18n::setLcTime();
            for ($i = 0; $i < 7; $i++) {
                $abbrWeekdayNames[$i] = strftime('%a', mktime(0, 0, 0, 6, $i, 1970));
            }
        }
        return $abbrWeekdayNames;
    }

    /**
     * return full weekday names according to the locale
     *
     * @return array  0-based array with full weekday names
     *
     * @static
     */
    static function &getFullWeekdayNames()
    {
        static $fullWeekdayNames;
        if (!isset($fullWeekdayNames)) {

            // set LC_TIME and build the arrays from locale-provided names
            // June 1st, 1970 was a Monday
            CRM_Core_I18n::setLcTime();
            for ($i = 0; $i < 7; $i++) {
                $fullWeekdayNames[$i] = strftime('%A', mktime(0, 0, 0, 6, $i, 1970));
            }
        }
        return $fullWeekdayNames;
    }

    /**
     * return abbreviated month names according to the locale
     *
     * @return array  1-based array with abbreviated month names
     *
     * @static
     */
    static function &getAbbrMonthNames()
    {
        static $abbrMonthNames;
        if (!isset($abbrMonthNames)) {

            // set LC_TIME and build the arrays from locale-provided names
            CRM_Core_I18n::setLcTime();
            for ($i = 1; $i <= 12; $i++) {
                $abbrMonthNames[$i] = strftime('%b', mktime(0, 0, 0, $i, 10, 1970 ));
            }
        }
        return $abbrMonthNames;
    }

    /**
     * return full month names according to the locale
     *
     * @return array  1-based array with full month names
     *
     * @static
     */
    static function &getFullMonthNames()
    {
        static $fullMonthNames;
        if (!isset($fullMonthNames)) {

            // set LC_TIME and build the arrays from locale-provided names
            CRM_Core_I18n::setLcTime();
            for ($i = 1; $i <= 12; $i++) {
                $fullMonthNames[$i] = strftime('%B', mktime(0, 0, 0, $i, 10, 1970));
            }
        }
        return $fullMonthNames;
    }

    static function unixTime( $string ) 
    {
        if ( empty( $string ) ) {
            return 0;
        }
        $v = self::unformat( $string );
        
        if ( empty( $v ) ) {
            return 0;
        }
        
        if ( CRM_Utils_Array::value( 'A', $v ) == 'PM' or CRM_Utils_Array::value( 'a', $v ) == 'pm') {
            $v['h'] += 12;
        }
        
        return mktime( CRM_Utils_Array::value( 'h', $v ),
                       CRM_Utils_Array::value( 'i', $v ), 
                       59, $v['M'], $v['d'], $v['Y'] );
    }

    /**
     * create a date and time string in a provided format
     *
     * %b - abbreviated month name ('Jan'..'Dec')
     * %B - full month name ('January'..'December')
     * %d - day of the month as a decimal number, 0-padded ('01'..'31')
     * %e - day of the month as a decimal number, blank-padded (' 1'..'31')
     * %E - day of the month as a decimal number ('1'..'31')
     * %f - English ordinal suffix for the day of the month ('st', 'nd', 'rd', 'th')
     * %H - hour in 24-hour format, 0-padded ('00'..'23')
     * %I - hour in 12-hour format, 0-padded ('01'..'12')
     * %k - hour in 24-hour format, blank-padded (' 0'..'23')
     * %l - hour in 12-hour format, blank-padded (' 1'..'12')
     * %m - month as a decimal number, 0-padded ('01'..'12')
     * %M - minute, 0-padded ('00'..'60')
     * %p - lowercase ante/post meridiem ('am', 'pm')
     * %P - uppercase ante/post meridiem ('AM', 'PM')
     * %Y - year as a decimal number including the century ('2005')
     * 
     * @param string $date    date and time in 'YYYY-MM-DD hh:mm:ss' format
     * @param string $format  the output format
     * @param array  $dateParts  an array with the desired date parts
     *
     * @return string  the $format-formatted $date
     *
     * @static
     */
    static function customFormat($dateString, $format = null, $dateParts = null)
    {
        // 1-based (January) month names arrays
        $abbrMonths = self::getAbbrMonthNames();
        $fullMonths = self::getFullMonthNames();

        if ( ! $format ) {
            $config =& CRM_Core_Config::singleton();

            if ($dateParts) {
                if (array_intersect(array('h', 'H'), $dateParts)) {
                    $format = $config->dateformatDatetime;
                } elseif (array_intersect(array('d', 'j'), $dateParts)) {
                    $format = $config->dateformatFull;
                } elseif (array_intersect(array('m', 'M'), $dateParts)) {
                    $format = $config->dateformatPartial;
                } else {
                    $format = $config->dateformatYear;
                }
            } else {
                if ( strpos($dateString, '-') ) {
                    $month  = (int) substr($dateString,  5, 2);
                    $day    = (int) substr($dateString,  8, 2);
                } else {
                    $month  = (int) substr($dateString,  4, 2);
                    $day    = (int) substr($dateString,  6, 2);
                }

                if (strlen($dateString) > 10) {
                    $format = $config->dateformatDatetime;
                } elseif ($day > 0) {
                    $format = $config->dateformatFull;
                } elseif ($month > 0) {
                    $format = $config->dateformatPartial;
                } else {
                    $format = $config->dateformatYear;
                }
            }

        }

        if ($dateString) {
            if ( strpos($dateString, '-') ) {
                $year   = (int) substr($dateString,  0, 4);
                $month  = (int) substr($dateString,  5, 2);
                $day    = (int) substr($dateString,  8, 2);
                
                $hour24 = (int) substr($dateString, 11, 2);
                $minute = (int) substr($dateString, 14, 2);
            } else {
                $year   = (int) substr($dateString,  0, 4);
                $month  = (int) substr($dateString,  4, 2);
                $day    = (int) substr($dateString,  6, 2);
                
                $hour24 = (int) substr($dateString, 8, 2);
                $minute = (int) substr($dateString, 10, 2);
            }

            if     ($day % 10 == 1 and $day != 11) $suffix = 'st';
            elseif ($day % 10 == 2 and $day != 12) $suffix = 'nd';
            elseif ($day % 10 == 3 and $day != 13) $suffix = 'rd';
            else $suffix = 'th';
            
            if ($hour24 < 12) {
                if ($hour24 == 00) $hour12 = 12;
                else $hour12 = $hour24;
                $type = 'AM';
            } else {
                if ($hour24 == 12) $hour12 = 12;
                else $hour12 = $hour24 - 12;
                $type = 'PM';
            }
            
            $date = array(
                          '%b' => CRM_Utils_Array::value( $month, $abbrMonths ),
                          '%B' => CRM_Utils_Array::value( $month, $fullMonths ),
                          '%d' => $day > 9 ? $day : '0' . $day,
                          '%e' => $day > 9 ? $day : ' ' . $day,
                          '%E' => $day,
                          '%f' => $suffix,
                          '%H' => $hour24 > 9 ? $hour24 : '0' . $hour24,
                          '%h' => $hour12 > 9 ? $hour12 : '0' . $hour12,
                          '%I' => $hour12 > 9 ? $hour12 : '0' . $hour12,
                          '%k' => $hour24 > 9 ? $hour24 : ' ' . $hour24,
                          '%l' => $hour12 > 9 ? $hour12 : ' ' . $hour12,
                          '%m' => $month  > 9 ? $month  : '0' . $month,
                          '%M' => $minute > 9 ? $minute : '0' . $minute,
                          '%i' => $minute > 9 ? $minute : '0' . $minute,
                          '%p' => strtolower($type),
                          '%P' => $type,
                          '%A' => $type,
                          '%Y' => $year
                          );

            return strtr($format, $date);

        } else {
            return '';
        }

    }

    /**
     * converts the format string from POSIX notation to PHP notation
     *
     * example: converts '%Y-%m-%d' to 'Y-M-d'
     * note: the blank-padded sequences are converted to non-blank-padded ones
     *
     * @param string $format  format string in POSIX % notation
     * @param array  $filter  only include these elements for the date string
     *
     * @return string  format string in PHP notation
     *
     * @static
     */
    static function posixToPhp($format, $filter = null)
    {
        static $replacements = array(
            '%b' => 'M',
            '%B' => 'F',
            '%d' => 'd',
            '%e' => 'j',
            '%E' => 'j',
            '%f' => 'S',
            '%H' => 'H',
            '%I' => 'h',
            '%k' => 'G',
            '%l' => 'g',
            '%m' => 'm',
            '%M' => 'i',
            '%p' => 'a',
            '%P' => 'A',
            '%Y' => 'Y'
        );
        if ( $filter ) {
            $filteredReplacements = $replacements;
            foreach ( $replacements as $key => $value ) {
                if ( in_array( $value, $filter ) ) {
                    $filteredReplacements[$key] = $value;
                } else {
                    $filteredReplacements[$key] = null;
                }
            }
            return  strtr($format, $filteredReplacements);
        } else {
            return strtr($format, $replacements);
        }
    }

    /**
     * converts the date/datetime from MySQL format to ISO format
     *
     * @param string $mysql  date/datetime in MySQL format
     * @return string        date/datetime in ISO format
     * @static
     */
    static function mysqlToIso($mysql)
    {
        $year   = substr($mysql,  0, 4);
        $month  = substr($mysql,  4, 2);
        $day    = substr($mysql,  6, 2);
        $hour   = substr($mysql,  8, 2);
        $minute = substr($mysql, 10, 2);
        $second = substr($mysql, 12, 2);
        
        $iso = '';
        if ($year)             $iso .= "$year";
        if ($month) {
            $iso .= "-$month";
            if ( $day ) {
                $iso .= "-$day";
            }
        }

        if ($hour) {
            $iso .= " $hour";
            if ($minute) {
                $iso .= ":$minute";
                if ($second) {
                    $iso .= ":$second";
                }
            }
        }
        return $iso;
    }

    /**
     * converts the date/datetime from ISO format to MySQL format
     *
     * @param string $iso  date/datetime in ISO format
     * @return string      date/datetime in MySQL format
     * @static
     */
    static function isoToMysql($iso)
    {
        $dropArray = array('-' => '', ':' => '', ' ' => '');
        return strtr($iso, $dropArray);
    }

    /**
     * converts the any given date to default date format.
     *
     * @param array  $params     has given date-format
     * @param int    $dateType   type of date  
     * @param string $dateParam  index of params
     * @static
     */
    function convertToDefaultDate( &$params, $dateType, $dateParam ) 
    {
        $now = getDate();
        $cen = substr($now['year'],  0, 2);
        $prevCen = $cen - 1;

        if ($params[$dateParam]) {
            //suppress hh:mm if it exists
            $value = preg_replace("/(?: [01]\d|2[0-3]|\d):(?:[0-4]\d|5[1-9])/", "", $params[$dateParam] );
        }
        
        switch( $dateType ) {
            
        case 1 :
            if ( ! preg_match('/^\d\d\d\d-?(\d|\d\d)-?(\d|\d\d)$/', $value) ) {
                return false;
            } 
            break;
        case 2 :
            if ( ! preg_match('/^(\d|\d\d)[-\/](\d|\d\d)[-\/]\d\d$/', $value) ) {
                return false;
            }
            break;
        case 4 :
            if ( ! preg_match('/^(\d|\d\d)[-\/](\d|\d\d)[-\/]\d\d\d\d$/', $value ) ) {
                return false;
            }
            break;
        case 8 :
            if ( ! preg_match('/^[A-Za-z]*.[ \t]?\d\d\,[ \t]?\d\d\d\d$/', $value ) ) {
                return false;
            }
            break;
        case 16 :
            if ( ! preg_match('/^\d\d-[A-Za-z]{3}.*-\d\d$/', $value ) && ! preg_match('/^\d\d[-\/]\d\d[-\/]\d\d$/', $value ) ) {
                return false; 
            }
            break;
        case 32 :
            if ( ! preg_match('/^(\d|\d\d)[-\/](\d|\d\d)[-\/]\d\d\d\d/', $value) ) {
                return false;
            }
            break;
        }

        if ( $dateType == 1 ) {
            $formattedDate = explode( "-", $value );
            if ( count($formattedDate) == 3 ) {
                $year   = (int) $formattedDate[0];
                $month  = (int) $formattedDate[1];
                $day    = (int) $formattedDate[2];
                
            } else if ( count($formattedDate) == 1 && ( strlen($value) == 8 ) ){
                return true;
            } else { 
                return false;
            }
        }


        if ( $dateType == 2 || $dateType == 4) {
            $formattedDate = explode( "/", $value );
            if ( count($formattedDate) != 3 ) {
                $formattedDate = explode( "-" , $value ); 
            } 
            if ( count($formattedDate) == 3 ) {
                $year   = (int) $formattedDate[2];
                $month  = (int) $formattedDate[0];
                $day    = (int) $formattedDate[1];
            } else {
                return false;
            }    
        }
        if ( $dateType == 8 ) {
            $dateArray = explode(' ',$value);
            $dateArray[1] = (int) substr($dateArray[1], 0, 2); //ignore comma(,) 
            
            $monthInt = 0;
            $fullMonths = self::getFullMonthNames();
            foreach ($fullMonths as $key => $val) {
                if (strtolower($dateArray[0]) == strtolower($val)) {
                    $monthInt = $key; 
                    break;
                }
            }
            if (!$monthInt) {
                $abbrMonths = self::getAbbrMonthNames();
                foreach ($abbrMonths as $key => $val) {
                    if (strtolower(trim($dateArray[0], "." )) == strtolower($val)) {
                        $monthInt = $key; 
                        break;
                    }
                }
            }
            $year   = (int) $dateArray[2];
            $day    = (int) $dateArray[1];
            $month  = (int) $monthInt;
        }
        if ( $dateType == 16 ) {
            $dateArray = explode('-',$value);
            if ( count ( $dateArray ) != 3 ) {
                $dateArray = explode('/', $value);
            }

            if ( count ( $dateArray ) == 3 ) {
                $monthInt = 0;
                $fullMonths = self::getFullMonthNames();
                foreach ( $fullMonths as $key => $val ) {
                    if ( strtolower( $dateArray[1] ) == strtolower( $val )) {
                        $monthInt = $key; 
                        break;
                    }
                }
                if ( !$monthInt ) {
                    $abbrMonths = self::getAbbrMonthNames();
                    foreach ( $abbrMonths as $key => $val ) {
                        if ( strtolower(trim($dateArray[1], "." )) == strtolower( $val )) {
                            $monthInt = $key; 
                            break;
                        }
                    }
                }
                if ( !$monthInt ) {
                    $monthInt = $dateArray[1];
                }
                
                $year   = (int) $dateArray[2];
                $day    = (int) $dateArray[0];
                $month  = (int) $monthInt;
            } else {
                return false; 
            }
        }
        if ( $dateType == 32 ) {
            $formattedDate = explode( "/", $value );
            if ( count($formattedDate) == 3 ) {
                $year   = (int) $formattedDate[2];
                $month  = (int) $formattedDate[1];
                $day    = (int) $formattedDate[0];
            } else {
                return false;
            }
        }
        
        $month = ($month < 10)? "0" . "$month" : $month;
        $day   = ($day < 10)  ? "0" . "$day"   : $day;

        $year = (int ) $year;
        // simple heuristic to determine what century to use
        // 01 - 09 is always 2000 - 2009
        // 10 - 99 is always 1910 - 1999
        if ( $year < 10 ) {
            $year = $cen . '0' . $year;
        } else if ( $year < 100 ) {
            $year = $prevCen . $year;
        }
        
        if ($params[$dateParam]) {
            $params[$dateParam] = "$year$month$day";
        }
        //if month is invalid return as error
        if ( $month !== '00' && $month <= 12 ) {
            return true;
        }
        return false;
    }
    
    /**
     * resolves the given relative time interval into finite time limits
     *
     * @param  array $relativeTerm relative time frame like this, previous, etc
     * @param  int   $unit         frequency unit like year, month, week etc..
     * @return array $dateRange    start date and end date for the relative time frame
     * @static
     */
    function relativeToAbsolute( $relativeTerm, $unit) 
    {
        $now  = getDate();
        $from = $to = $dateRange = array();
        $from['H']  = $from['i'] = $from['s'] = 0;
        $to['H'] = 11;
        $to['i'] = $to['s'] = 59;
        
        switch( $unit ) {

        case 'year':
            switch( $relativeTerm ) {
            case 'this':
                $from['d'] = $from['M'] = 1;
                $to['d'] = 31;
                $to['M'] = 12;
                $to['Y'] = $from['Y'] = $now['year'];
                break;

            case 'previous':
                $from['M'] = $from['d'] = 1;
                $to['d'] = 31;
                $to['M'] = 12;
                $to['Y'] = $from['Y'] = $now['year'] - 1;
                break;

            case 'previous_before':
                $from['M'] = $from['d'] = 1;
                $to['d'] = 31;
                $to['M'] = 12;
                $to['Y'] = $from['Y'] = $now['year'] - 2;
                break;

            case 'previous_2':
                $from['M'] = $from['d'] = 1;
                $to['d'] = 31;
                $to['M'] = 12;
                $from['Y'] = $now['year'] - 2;
                $to['Y'] = $now['year'] - 1;
                break;

            case 'earlier':
                $to['d'] = 31;
                $to['M'] = 12;
                $to['Y'] = $now['year'] - 1;
                unset($from);
                break;
                
            case 'greater':
                $from['M'] = $from['d'] = 1;
                $from['Y'] = $now['year'];
                unset($to);
                break;
            }
            break;
            
        case 'quarter':
            switch( $relativeTerm ) {
                
            case 'this':
                $quarter   = (int)$now['mon']/4;
                $from['d'] = 1;
                $from['M'] = (3 * $quarter ) - 2;
                $to['M']   = 3 * $quarter;
                $to['Y']   = $from['Y'] = $now['year'];
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $to['M'], $now['year']);
                break;

            case 'previous':
                $difference = 1;
                $quarter   = (int)$now['mon']/4;
                $quarter = $quarter - $difference;
                $subtractYear = 0;
                if ( $quarter <= 0 ) { 
                    $subtractYear = 1;
                    $quarter += 4;
                }
                $from['d'] = 1;
                $from['M'] = (3 * $quarter ) - 2;
                $to['M']   = 3 * $quarter;
                $to['Y']   = $from['Y'] = $now['year'] - $subtractYear;
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $to['M'],  $to['Y']);
                break;

            case 'previous_before':
                $difference = 2;
                $quarter   = (int)$now['mon']/4;
                $quarter = $quarter - $difference;
                if ( $quarter <= 0 ) { 
                    $subtractYear = 1;
                    $quarter += 4;
                }
                $from['d'] = 1;
                $from['M'] = (3 * $quarter ) - 2;
                $to['M']   = 3 * $quarter;
                $to['Y']   = $from['Y'] = $now['year'] - $subtractYear;
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $to['M'], $to['Y'] );
                break;

            case 'previous_2':
                $difference = 2;
                $quarter   = (int)$now['mon']/4;
                $quarter = $quarter - $difference;
                if ( $quarter <= 0 ) { 
                    $subtractYear = 1;
                    $quarter += 4;
                }
                $from['d'] = 1;
                $from['M'] = (3 * $quarter ) - 2;
                $to['M']   = 4 * $quarter;
                $to['Y']   = $from['Y'] = $now['year'] - $subtractYear;
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $to['M'], $to['Y'] );
                break;

            case 'earlier':
                $quarter   = (int)$now['mon']/4 - 1;
                if ( $quarter <= 0 ) { 
                    $subtractYear = 1;
                    $quarter += 4;
                }
                $to['M']   = 3 * $quarter;
                $to['Y']   = $from['Y'] = $now['year'] - $subtractYear;
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $to['M'],  $to['Y']);
                unset($from);
                break;
                
            case 'greater':
                $quarter   = (int)$now['mon']/4;
                $from['d'] = 1;
                $from['M'] = (3 * $quarter ) - 2;
                $from['Y'] = $now['year'];
                unset($to);
                break;
            }
            break;
            
        case 'month':
            switch( $relativeTerm ) {
            case 'this':
                $from['d'] = 1;
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $now['mon'], $now['year']);
                $from['M'] = $to['M'] = $now['mon'];
                $to['Y'] = $from['Y'] = $now['year'];
                break;
                
            case 'previous':
                $from['d'] = 1;
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $now['mon'] - 1, $now['year']);
                $from['M'] = $to['M'] = $now['mon'] - 1;
                $to['Y'] = $from['Y'] = $now['year'];
                break;

            case 'previous_before':
                $from['d'] = 1;
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $now['mon'] - 2, $now['year']);
                $from['M'] = $to['M'] = $now['mon'] - 2;
                $to['Y'] = $from['Y'] = $now['year'];
                break;

            case 'previous_2':
                $from['d'] = 1;
                $to['d']   = cal_days_in_month(CAL_GREGORIAN, $now['mon'] - 1, $now['year']);
                $from['M'] = $now['mon'] - 2;
                $to['M'] = $now['mon'] - 1;
                $to['Y'] = $from['Y'] = $now['year'];
                break;

            case 'earlier':
                //before end of past month
                $to['d'] = cal_days_in_month(CAL_GREGORIAN, $now['mon'] - 1, $now['year']);
                $to['M'] = $now['mon'] - 1;
                $to['Y'] = $now['year'];
                unset($from);
                break;
                
            case 'greater':
                $from['d'] = 1;
                $from['M'] = $now['mon'];;
                $from['Y'] = $now['year'];
                unset($to);
                break;
            }
            break;
            
        case 'week':
            switch( $relativeTerm ) {
            case 'this':
                $from['d'] = $now['mday'];
                $from['M'] = $now['mon'];
                $from['Y'] = $now['year'];
                $from = self::intervalAdd( 'day', -1*($now['wday']), $from );
                $to   = self::intervalAdd( 'day', 6, $from );
                $to['H'] = 11;
                $to['i'] = $to['s'] = 59;
                break;
                
            case 'previous':
                $from['d'] = $now['mday'];
                $from['M'] = $now['mon'];
                $from['Y'] = $now['year'];
                $from = self::intervalAdd( 'day', -1*($now['wday'])-7, $from );
                $to   = self::intervalAdd( 'day', 6, $from );
                $to['H'] = 11;
                $to['i'] = $to['s'] = 59;
                break;

            case 'previous_before':
                $from['d'] = $now['mday'];
                $from['M'] = $now['mon'];
                $from['Y'] = $now['year'];
                $from = self::intervalAdd( 'day', -1*($now['wday'])-14, $from );
                $to   = self::intervalAdd( 'day', 6, $from );
                $to['H'] = 11;
                $to['i'] = $to['s'] = 59;
                break;
                
            case 'previous_2':
                $from['d'] = $now['mday'];
                $from['M'] = $now['mon'];
                $from['Y'] = $now['year'];
                $from = self::intervalAdd( 'day', -1*($now['wday'])-14, $from );
                $to   = self::intervalAdd( 'day', 13, $from );
                $to['H'] = 11;
                $to['i'] = $to['s'] = 59;
                break;

            case 'earlier':
                $to['d'] = $now['mday'];
                $to['M'] = $now['mon'];
                $to['Y'] = $now['year'];
                $to['H'] = 11;
                $to['i'] = $to['s'] = 59;
                $to   = self::intervalAdd( 'day', -1*($now['wday'])-1, $to );
                unset($from);
                break;
                
            case 'greater':
                $from['d'] = $now['mday'];
                $from['M'] = $now['mon'];
                $from['Y'] = $now['year'];
                $from = self::intervalAdd( 'day', -1*($now['wday']), $from );
                unset($to);
                break;
            }
            break;

        case 'day':
            switch( $relativeTerm ) {
            case 'this':
                $from['d'] = $to['d'] = $now['mday'];
                $from['M'] = $to['M'] = $now['mon'];
                $from['Y'] = $to['Y'] = $now['year'];
                break;
                
            case 'previous':
                $from['d'] = $now['mday'];
                $from['M'] = $now['mon'];
                $from['Y'] = $now['year'];
                $from = self::intervalAdd( 'day', -1, $from );
                $to['d'] = $from['d'];
                $to['M'] = $from['M'];
                $to['Y'] = $from['Y'];
                break;

            case 'previous_before':
                $from['d'] = $now['mday'];
                $from['M'] = $now['mon'];
                $from['Y'] = $now['year'];
                $from = self::intervalAdd( 'day', -2, $from );
                $to['d'] = $from['d'];
                $to['M'] = $from['M'];
                $to['Y'] = $from['Y'];
                break;
                
            case 'previous_2':
                $from['d'] = $to['d'] = $now['mday'];
                $from['M'] = $to['M'] = $now['mon'];
                $from['Y'] = $to['Y'] = $now['year'];
                $from = self::intervalAdd( 'day', -2, $from );
                $to   = self::intervalAdd( 'day', -1, $to );
                break;
                
            case 'earlier':
                $to['d'] = $now['mday'];
                $to['M'] = $now['mon'];
                $to['Y'] = $now['year']; 
                unset($from);
                break;
                
            case 'greater':
                $from['d'] = $now['mday'];
                $from['M'] = $now['mon'];;
                $from['Y'] = $now['year'];
                unset($to);
                break;
            }
            break;
        }
        
        foreach ( array( 'from', 'to' ) as $item ) {
            if ( !empty ( $$item ) ) {
                $dateRange[$item] = $$item;
            } else {
                $dateRange[$item] = null;
            }
        }
        //CRM_Core_Error::debug( '$date', $dateRange );
        return $dateRange;
    }

    /**
     * splits the given date range into given units
     *
     * @param  array $from          start date for the time frame to be splitted
     * @param  array $to            end date for the time frame to be splitted
     * @param  int   $unit          frequency unit like year, month, week etc..
     * @return array $dateSplitted  array of dates having splitted time period between 
     *                              from date and to date on the basis of unit
     * @static
     */
    function splitIntoInterval( $from, $to, $unit ) 
    {
        $fromFormat = self::format($from);
        $toFormat   = self::format($to);
        
        //compare to ensure from date > to date 
        if ( self::overdue( $toFormat, $fromFormat ) == true ) {
            return false;
        }
        
        switch( $unit ) {
        case 'month':
            $i = 0;
            $dateSplitted[$i] = array ('from' => $from,
                                       'to'   => array( 'd' => cal_days_in_month(CAL_GREGORIAN, $from['M'], $from['Y']),
                                                        'M' => $from['M'],
                                                        'Y' => $from['Y']
                                                        ) );
            
            // check whether the month  end date  < to date         
            while( self::overdue( self::format($dateSplitted[$i]['to']), $toFormat ) == true ) {
                $i++;
                $dateSplitted[$i] = array ('from' => self::intervalAdd( 'day', 1, $dateSplitted[$i-1]['to'], true ));
                $dateSplitted[$i]['to'] = array(
                                                'd' => cal_days_in_month(CAL_GREGORIAN, $dateSplitted[$i]['from']['M'], $dateSplitted[$i]['from']['Y']),
                                                'M' => $dateSplitted[$i]['from']['M'],
                                                'Y' => $dateSplitted[$i]['from']['Y']
                                                );
            }
            $dateSplitted[$i]['to'] = $to;
            break;

        case 'day'  :
            $i = 0;
            $dateSplitted[$i] = array ('from' => $from,
                                       'to'   => $from );
            while( self::overdue( self::format($dateSplitted[$i]['to']), $toFormat ) == true ) {
                $i++;
                $dateSplitted[$i]['from'] = self::intervalAdd( 'day', 1, $dateSplitted[$i-1]['to'], true );
                $dateSplitted[$i]['to'] = $dateSplitted[$i]['from'];
            }
            $dateSplitted[$i]['to'] = $to;
            break;
            
        case 'year' :
            $i = 0;
            $dateSplitted[$i] = array ('from' => $from,
                                       'to'   => array( 'd' => 31,
                                                        'M' => 12,
                                                        'Y' => $from['Y']
                                                        ) );
            
            // check whether the month  end date  < to date         
            while( self::overdue( self::format($dateSplitted[$i]['to']), $toFormat ) == true ) {
                $i++;
                $dateSplitted[$i] = array ('from' => self::intervalAdd( 'day', 1, $dateSplitted[$i-1]['to'], true ));
                $dateSplitted[$i]['to'] = array( 'd' => 31,
                                                 'M' => 12,
                                                 'Y' => $dateSplitted[$i]['from']['Y']
                                                 );
            }
            $dateSplitted[$i]['to'] = $to;

            break;

        case 'quarter':
            break;

        case 'week':
            break;
        } 
        
        //CRM_Core_Error::debug( '$dateSplitted', $dateSplitted );
        return $dateSplitted;
    }

    static function isDate( &$date ) 
    {
        if ( ! is_array( $date )                    ||
             CRM_Utils_System::isNull( $date )      ||
             ! CRM_Utils_Array::value( 'Y', $date ) ||
             ! CRM_Utils_Array::value( 'M', $date ) ||
             ! CRM_Utils_Array::value( 'd', $date ) ) {
            return false;
        }
        return true;
    }

    static function overdue( $date, $now = null ) 
    {
        $mysqlDate = self::isoToMysql( $date );
        if ( ! $now ) {
            $now = date( 'YmdHis' );
        } else {
            $now = self::isoToMysql( $now );
        }
        
        return ( $mysqlDate >= $now ) ? false : true;
    }
    
    /**
     * Function to get customized today
     *
     * This function is used for getting customized today. To get
     * actuall today pass 'dayParams' as null. or else pass the day,
     * month, year values as array values
     * Example: $dayParams = array( 'day' => '25', 'month' => '10',
     *                              'year' => '2007' ); 
     * 
     * @param  Array  $dayParams   Array of the day, month, year
     *                             values.
     * @param  string $format      expected date format( default
     *                             format is 2007-12-21 )
     * 
     * @return string  Return the customized todays date (Y-m-d)
     * @static
     */
    static function getToday( $dayParams = null, $format = "Y-m-d" )
    {
        if ( is_null( $dayParams ) || empty( $dayParams ) ) {
            $today = date( $format );
        } else {
            $today = date( $format, mktime( 0, 0, 0, 
                                            $dayParams['month'], 
                                            $dayParams['day'], 
                                            $dayParams['year'] ) );
        }
        
        return $today;
    }

    /**
     * Function to find whether today's date lies in 
     * the given range
     * 
     * @param  date  $startDate  start date for the range 
     * @param  date  $endDate    end date for the range 
     
     * @return true              todays date is in the given date range
     * @static
     */
    static function getRange( $startDate, $endDate  )
    {
        $today = date( "Y-m-d" );
        $mysqlStartDate = self::isoToMysql( $startDate );
        $mysqlEndDate   = self::isoToMysql( $endDate );
        $mysqlToday     = self::isoToMysql( $today );
        
        if ( ( isset( $mysqlStartDate ) && isset( $mysqlEndDate ) ) && ( ( $mysqlToday >= $mysqlStartDate ) && ( $mysqlToday <= $mysqlEndDate ) ) ){
            return true;
        } elseif ( ( isset( $mysqlStartDate ) && ! isset( $mysqlEndDate ) ) && ( ( $mysqlToday >= $mysqlStartDate ) ) ) {
            return true;
        } elseif ( ( ! isset( $mysqlStartDate ) && isset( $mysqlEndDate ) ) && ( ( $mysqlToday <= $mysqlEndDate ) ) ) {
            return true;
        }
        return false;
    }
    

    static function getAllDefaultValues( &$defaults, $format = null, $time = null ) 
    {
        if ( ! $format ) {
            // lets include EVERYTHING for now
            $format = 'a-A-d-h-H-i-g-G-j-M-S-Y';
        }
        // always include 'm' (see hack for QF below)
        $format .= '-m-F';

        if ( ! $time ) {
            $time = time( );
        }

        $val = date( $format, $time );
        $values = explode( '-', $val    );
        $keys   = explode( '-', $format );
        if ( count( $values ) != count( $keys ) ) {
            CRM_Core_Error::fatal( ts( 'Please contact CiviCRM support' ) );
        }
        for ( $i = 0; $i < count( $values ); $i++ ) {
            $defaults[$keys[$i]] = $values[$i];
        }
        // for some strange reason QF wants it as M, so we oblige for now
        $defaults['M'] = $defaults['m'];
        $defaults['F'] = $defaults['m'];
    }

    /**
     * Function to convert hours/minutes in minutes
     *
     * @param int $hour    hours
     * @param int $minute  minute
     *
     * @return int $time time is minutes
     * @access public
     * @static
     */
    static function standardizeTime( $hour = null, $minute = null ) 
    {
        $time = $minute;
        
        if ( $hour ) {
            $time = $time + ( $hour * 60 );
        }

        return $time;
    }

    /**
     * Function to convert minutes to hours/minutes
     *
     * @param int $time  time in minutes
     *
     * @return array array  associated array of hours and minutes
     * @access public
     * @static
     */
    static function unstandardizeTime( $time = null ) 
    {
        $hour = $minute = null;

        $hour   = floor( $time / 60);
        $minute = $time - floor( $time / 60 ) * 60;

        // always convert minutes in interval of 5
        $minute = (integer)( $minute/15 ) * 15;

        return array( $hour, $minute );
    }

    /**
     * Function to calculate Age in Years if greater than one year else in months
     * 
     * @param date $birthDate Birth Date
     *
     * @return int array $results contains years or months
     * @access public
     */
    public function calculateAge($birthDate) 
    {     
        $results = array( );
        $formatedBirthDate  = CRM_Utils_Date::customFormat($birthDate,'%Y-%m-%d'); 
        
        $bDate      = explode('-',$formatedBirthDate);
        $birthYear  = $bDate[0]; 
        $birthMonth = $bDate[1]; 
        $birthDay   = $bDate[2]; 
        $year_diff  = date("Y") - $birthYear; 
        
        switch ($year_diff) {
        case 1: 
            $month = (12 - $birthMonth) + date("m");
            if ( $month < 12 ) {
                if (date("d") < $birthDay) {
                    $month--;
                }
                $results['months'] =  $month;
            } elseif ( $month == 12 && (date("d") < $birthDay) ) {
                $results['months'] = $month-1;
            } else { 
                $results['years'] =  $year_diff;
            }
            break;
        case 0:
            $month = date("m") - $birthMonth;
            $results['months'] = $month;
            break;
        default:
            $results['years'] = $year_diff;
            if ( ( date("m") < $birthMonth ) || ( date("m") == $birthMonth ) && ( date("d") < $birthDay ) ) {
                $results['years']--;
            } 
        }

        return $results;
    }
    
    /**
     * Function to calculate next payment date according to provided  unit & interval
     * 
     * @param string $unit     frequency unit like year,month, week etc..
     *
     * @param int    $interval frequency interval.
     *
     * @param array  $date     start date of pledge.
     *
     * @return array $result contains new date with added interval
     * @access public
     */
    function intervalAdd($unit, $interval, $date, $dontCareTime = false ) 
    {  
        $hours   = $date['H'];
        $minutes = $date['i'];
        $seconds = $date['s'];
        $month   = $date['M'];
        $day     = $date['d'];
        $year    = $date['Y'];
        
        $date = mktime ($hours, $minutes, $seconds, $month, $day, $year);
       
        switch ( $unit ) {

        case 'year':
            $date   =   mktime ($hours, $minutes, $seconds, $month, $day, $year+$interval);
            break;
        case 'month':
            $date   =   mktime ($hours, $minutes, $seconds, $month+$interval, $day, $year);
            break;
        case 'week':
            $interval = $interval * 7;
            $date   =   mktime ($hours, $minutes, $seconds, $month, $day+$interval, $year);
            break;
        case 'day':
            $date   =   mktime ($hours, $minutes, $seconds, $month, $day+$interval, $year);
            break;
        }
        
       
        $scheduleDate = explode ( "-", date("n-j-Y-H-i-s", $date ) );
                              
        $date = array( );
        $date['M'] = $scheduleDate[0];
        $date['d'] = $scheduleDate[1];
        $date['Y'] = $scheduleDate[2];
        if ( $dontCareTime == false) {
            $date['H'] = $scheduleDate[3];
            $date['i'] = $scheduleDate[4];
            $date['s'] = $scheduleDate[5];
        }
        return $date;
    }
    
}


