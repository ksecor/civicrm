<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

function _civicrm_initialize( ) {
    require_once 'CRM/Core/Config.php';
    $config =& CRM_Core_Config::singleton( );
}

function civicrm_create_error( $msg ) {
    $values = array( );
    
    $values['is_error']      = 1;
    $values['error_message'] = $msg;
    return $values;
}

function civicrm_create_success( $result = 1 ) {
    $values = array( );
    
    $values['is_error'] = 0;
    $values['result']   = $result;
    return $values;
}

/**
 * Check if the given array is actually an error
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return boolean true if error, false otherwise
 * @static void
 * @access public
 */
function civicrm_error( $params ) {
    return ( array_key_exists( 'is_error', $params ) &&
             $params['is_error'] ) ? true : false;
}


/**
 * Converts an object to an array 
 *
 * @param  object   $dao           (reference )object to convert
 * @param  array    $dao           (reference )array
 * @return array
 * @static void
 * @access public
 */
function _civicrm_object_to_array( &$dao, &$values )
{
    $tmpFields = $dao->fields();
    $fields = array();
    //rebuild $fields array to fix unique name of the fields
    foreach( $tmpFields as $key => $val ) {
        $fields[$val["name"]]  = $val;
    }
    
    foreach( $fields as $key => $value ) {
        if (array_key_exists($key, $dao)) {
            $values[$key] = $dao->$key;
        }
    }
}

?>
