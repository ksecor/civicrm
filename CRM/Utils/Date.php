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
     * @param array $date ('Y', 'M', 'd')
     * @return string - formatted string for date
     *
     * @access public
     * @static
     */
    static function format($date)
    {
        if (!$date) {
            return null;
        }

        if (empty($date['Y']) || empty($date['M']) || empty($date['d'])) {
            return null;
        }

        $date['M'] = ($date['M'] < 10) ? '0' . $date['M'] : $date['M'];
        $date['d'] = ($date['d'] < 10) ? '0' . $date['d'] : $date['d'];
        return $date['Y'] . $date['M'] . $date['d'];
    }
}
?>
