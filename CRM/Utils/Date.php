<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Date utilties
 */
class CRM_Utils_Date {

    /**
     * format a date by padding it with leading '0'.
     *
     * @param array  $date ('Y', 'M', 'd')
     * @param string $separator the seperator to use when formatting the date
     * @return string - formatted string for date
     *
     * @static
     */
    static function format( $date, $separator = '' )
    {
        $time = '';
        // print_r($date);
        if ( ! $date || ! is_array( $date ) || ( ! $date['Y'] ) ) {
            return null;
        }

        if ( CRM_Utils_Array::value( 'M', $date ) ) {
            $date['M'] = (int ) $date['M'];
            $date['M'] = ($date['M'] < 10) ? '0' . $date['M'] : $date['M'];
        } else {
            $date['M'] = '00';
        }

        if ( CRM_Utils_Array::value( 'd', $date ) ) {
            $date['d'] = (int ) $date['d'];
            $date['d'] = ($date['d'] < 10) ? '0' . $date['d'] : $date['d'];
        } else {
            $date['d'] = '00';
        }

        if (CRM_Utils_Array::value( 'h', $date ) || CRM_Utils_Array::value( 'i', $date ) || CRM_Utils_Array::value( 's', $date )) {
            // we have time too.. 
            if (CRM_Utils_Array::value( 'h', $date )) {
                if ($date['A'] == 'PM') {
                    if ($date['h'] != 12 ) {
                        $date['h'] = $date['h'] + 12;
                    }
                }
                if (CRM_Utils_Array::value( 'A', $date ) == 'AM' && CRM_Utils_Array::value( 'h', $date ) == 12) {
                    $date['h'] = '00';
                }
                
                $date['h'] = (int ) $date['h'];
                $date['h'] = ($date['h'] < 10) ? '0' . $date['h'] : $date['h'];
            } else {
                $date['h'] = '00';
            }

            if (CRM_Utils_Array::value( 'i', $date )) {
                $date['i'] = (int ) $date['i'];
                $date['i'] = ($date['i'] < 10) ? '0' . $date['i'] : $date['i'];
            } else {
                $date['i'] = '00';
            }

            if (CRM_Utils_Array::value( 's', $date )) {
                $date['s'] = (int ) $date['s'];
                $date['s'] = ($date['s'] < 10) ? '0' . $date['s'] : $date['s'];
            } else {
                $date['s'] = '00';
            }
            $time = $date['h'] . $seperator . $date['i'] . $seperator . $date['s'];
            //$time = $date['h'] . $seperator . $date['i'] . $seperator . $date['s'].$date['A'];
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
    static function &unformat( $date, $separator = '-' ) {
        $value = array( );
        $value['Y'] = $value['M'] = $value['d'] = null;

        if ( empty( $date ) ) {
            return $value;
        }

        list( $year, $mon, $day ) = explode( $separator, $date, 3 );

        $value = array( );

        if ( is_numeric( $year ) && $year > 0 ) {
            $value['Y'] = $year;
        }

        if ( is_numeric( $mon ) && $mon > 0 ) {
            $value['M'] = $mon;
        }

        if ( is_numeric( $day ) && $day > 0 ) {
            $value['d'] = $day;
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
                $abbrMonthNames[$i] = strftime('%b', mktime(0, 0, 0, $i));
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
                $fullMonthNames[$i] = strftime('%B', mktime(0, 0, 0, $i));
            }
        }
        return $fullMonthNames;
    }

    /**
     * create a date in a provided format
     *
     * format keywords (with examples based on '2005-01-01' and C locale):
     * %b - abbreviated month name ('Jan')
     * %B - full month name ('January')
     * %d - day of the month as a decimal number, 0-padded ('01')
     * %e - day of the month as a decimal number, blank-padded (' 1')
     * %E - day of the month as a decimal number ('1')
     * %f - English ordinal suffix for the day of the month ('st')
     * %m - month as a decimal number, 0-padded ('01')
     * %Y - year as a decimal number including the century ('2005')
     * 
     * @param string $date    date in 'YYYY-MM-DD' format
     * @param string $format  the output format
     *
     * @return string  the $format-formatted $date
     *
     * @static
     */
    static function customFormat($dateString, $format = '%B %E%f, %Y %h:%i %A')
    {
        // 1-based (January) month names arrays
        $abbrMonths = self::getAbbrMonthNames();
        $fullMonths = self::getFullMonthNames();

        if ($dateString and $format) {
            $dateParts = explode('-', $dateString);
            $year = (int) $dateParts[0];
            $month = (int) $dateParts[1];
            $day = (int) $dateParts[2];
            if ($day % 10 == 1 and $day != 11) $suffix = 'st';
            elseif ($day % 10 == 2 and $day != 12) $suffix = 'nd';
            elseif ($day % 10 == 3 and $day != 13) $suffix = 'rd';
            else $suffix = 'th';
            
            //added code to display time
            $strTemp = str_replace('-', ' ', $dateString);
            $strTemp = str_replace($year, ' ', $strTemp);
            $strTemp = str_replace($dateParts[1], ' ', $strTemp);
            $strTemp = str_replace($day, ' ', $strTemp);
            
            list($hour, $min) = explode(':', $strTemp);

            if ($hour < 12) {
                if ($hour == 00) {
                    $hour = 12;
                    $type = 'AM';
                } else {
                    $type = 'AM';
                }
            } else {
                if ($hour != 12 ) {
                    $hour = $hour - 12;
                }
                    $type = 'PM';
            }
            
            $date = array(
                          '%b' => $abbrMonths[$month],
                          '%B' => $fullMonths[$month],
                          '%d' => $day > 9 ? $day : '0' . $day,
                          '%e' => $day > 9 ? $day : ' ' . $day,
                          '%E' => $day,
                          '%f' => $suffix,
                          '%m' => $month > 9 ? $month : '0' . $month,
                          '%Y' => $year,
                          '%h' => $hour,
                          '%i' => $min,
                          '%A' => $type
                          );
            return strtr($format, $date);
        } else {
            return '';
        }

    }

}

?>
