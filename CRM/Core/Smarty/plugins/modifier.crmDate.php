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
    if ($dateString) {
        $config =& CRM_Core_Config::singleton();
        list($year, $month, $day, $rest) = explode('-', $dateString, 3);

        if ($day) {
            return CRM_Utils_Date::customFormat($dateString, $config->dateformatFull);
        } elseif ($month > 0) {
            return CRM_Utils_Date::customFormat($dateString, $config->dateformatPartial);
        } else {
            return CRM_Utils_Date::customFormat($dateString, $config->dateformatYear);
        }
    }
    return '';
}

?>
