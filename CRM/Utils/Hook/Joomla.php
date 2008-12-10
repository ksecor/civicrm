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

class CRM_Utils_Hook_Joomla {

    static function invoke( $numParams,
                            &$arg1, &$arg2, &$arg3, &$arg4, &$arg5,
                            $fnSuffix ) {
        $result = array( );
        $fnName = "joomla_{$fnSuffix}";
        if ( function_exists( $fnName ) ) {
            if ( $numParams == 1 ) {
                $result = $fnName( $arg1 );
            } else if ( $numParams == 2 ) {
                $result = $fnName( $arg1, $arg2 );
            } else if ( $numParams == 3 ) {
                $result = $fnName( $arg1, $arg2, $arg3 );
            } else if ( $numParams == 4 ) {
                $result = $fnName( $arg1, $arg2, $arg3, $arg4 );
            } else if ( $numParams == 5 ) {
                $result = $fnName( $arg1, $arg2, $arg3, $arg4, $arg5 );
            }
        }
        return empty( $result ) ? true : $result;
    }
}