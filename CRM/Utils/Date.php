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

}

?>
