<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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


class CRM_Report_Utils_Report {

    static function getValueFromUrl( $instanceID = null ) {
        if ( $instanceID ) {
            $optionVal = CRM_Core_DAO::getFieldValue( 'CRM_Report_DAO_Instance',
                                                      $instanceID,
                                                      'report_id' );
        } else {
            $config =& CRM_Core_Config::singleton( );
            $args   = explode( '/', $_GET[$config->userFrameworkURLVar] );

            // remove 'civicrm/report' from args
            array_shift($args);
            array_shift($args);

            // put rest of arguement back in the form of url, which is how value 
            // is stored in option value table
            $optionVal = implode( '/', $args );
        }
        return $optionVal;
    }

    static function getValueIDFromUrl( $instanceID = null ) {
        $optionVal = self::getValueFromUrl( $instanceID );

        if ( $optionVal ) {
            require_once 'CRM/Core/OptionGroup.php';
            $templateInfo = CRM_Core_OptionGroup::getRowValues( 'report_list', "{$optionVal}", 'value' );
            return $templateInfo['id'];
        }

        return false;
    }

    // get instance count for a template 
    static function getInstanceCount( $optionVal ) {
        $sql = "
SELECT count(inst.id)
FROM   civicrm_report_instance inst
WHERE  inst.report_id = %1";

        $params = array( 1 => array( $optionVal, 'String' ) );
        $count  = CRM_Core_DAO::singleValueQuery( $sql, $params );
        return $count;
    }

}
