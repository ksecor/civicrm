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
     * @access public
     * @static
     */
    static function format( $date, $separator = '' )
    {
        if ( ! $date || ! is_array( $date ) || ( ! $date['Y'] ) ) {
            return null;
        }

        if ( $date['M'] ) {
            $date['M'] = (int ) $date['M'];
            $date['M'] = ($date['M'] < 10) ? '0' . $date['M'] : $date['M'];
        } else {
            $date['M'] = '00';
        }

        if ( $date['d'] ) {
            $date['d'] = (int ) $date['d'];
            $date['d'] = ($date['d'] < 10) ? '0' . $date['d'] : $date['d'];
        } else {
            $date['d'] = '00';
        }
        
        return $date['Y'] . $separator . $date['M'] . $separator . $date['d'];
    }

    /**
     * given a string in mysql format, transform the string 
     * into qf format
     *
     * @param string $date a mysql type date string
     *
     * @return array       a qf formatted date array
     * @static
     * @access public
     */     
    static function unformat( $date, $separator = '-' ) {
        list( $year, $mon, $day ) = explode( $separator, $date, 3 );

        $value = array( );
        $value['Y'] = $value['M'] = $value['d'] = null;

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
     * create a date in a provided format
     *
     * format keywords (with examples based on '2005-01-01' and C locale):
     * %b - abbreviated month name ('Jan')
     * %B - full month name ('January')
     * %d - day of the month as a decimal number, 0-padded (01)
     * %e - day of the month as a decimal number, blank-padded (' 1')
     * %m - month as a decimal number, 0-padded ('01')
     * %Y - year as a decimal number including the century ('2005')
     * 
     * @param string $date    date in 'YYYY-MM-DD' format
     * @param string $format  the output format
     *
     * @return string  the $format-formatted $date
     *
     * @static
     * @access public
     */
    static function customFormat($dateString, $format = '%B %e, %Y')
    {
        // 1-based (January) month names arrays
        static $abbrMonths;
        static $fullMonths;

        if (!isset($abbrWeekdays)) {

            // with the config being set up to, e.g., pl_PL: try pl_PL.UTF-8 at first,
            // if it's not present try pl_PL, finally - fall back to C
            $config =& CRM_Core_Config::singleton();
            setlocale(LC_TIME, $config->lcMessages . '.UTF-8', $config->lcMessages, 'C');

            // build the arrays from locale-provided names
            for ($i = 1; $i <= 12; $i++) {
                list($abbrMonths[$i], $fullMonths[$i]) = explode("\n", strftime("%b\n%B", mktime(0, 0, 0, $i)));
            }
        }

        if ($dateString and $format) {
            $dateParts = explode('-', $dateString);
            $year = (int) $dateParts[0];
            $month = (int) $dateParts[1];
            $day = (int) $dateParts[2];
            $date = array(
                '%b' => $abbrMonths[$month],
                '%B' => $fullMonths[$month],
                '%d' => $day > 9 ? $day : '0' . $day,
                '%e' => $day > 9 ? $day : ' ' . $day,
                '%m' => $month > 9 ? $month : '0' . $month,
                '%Y' => $year
            );
            return strtr($format, $date);
        } else {
            return '';
        }

    }

}

?>
