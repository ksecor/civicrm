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
 * Convert the date string "YYYY-MM-DD" to "MM<long> DD, YYYY".
 *
 * @param string $dateString date which needs to converted to human readable format
 *
 * @return string human readable date format | invalid date message
 * @access public
 */
function smarty_modifier_crmDate($dateString)
{
    // this is bad and we should reuse something from qf date which has all the translations, including polish
    static $months;
    if ( ! isset( $months ) ) {
        $months = array(1 => ts('January'), ts('February'), ts('March'), ts('April'), ts('May'), ts('June'), ts('July'), ts('August'), ts('September'), ts('October'), ts('November'), ts('December'));
    }

    if ( $dateString ) {
        list( $year, $mon, $day ) = explode( '-', $dateString, 3 );

        $fDate = '';
        if ( $mon > 0 ) {
            $fDate .= $months[(int ) $mon] . ' ';

            // validation allows month w/o day, but NOT day w/o month
            if ( $day > 0 ) {
                $fDate .= $day;
            }
            $fDate .= ", ";
        }
        $fDate .= $year;
        return $fDate;
    }
    return 'NO DATE SPECIFIED';
}

?>
