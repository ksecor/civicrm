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

function _civicrm_store_values( &$fields, &$params, &$values ) {
    $valueFound = false;
    
    foreach ($fields as $name => $field) {
        // ignore all ids for now
        if ( $name === 'id' || substr( $name, -1, 3 ) === '_id' ) {
            continue;
        }
        
        if ( array_key_exists( $name, $params ) ) {
            $values[$name] = $params[$name];
            $valueFound = true;
        }
    }
    return $valueFound;
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

function _civicrm_custom_format_params( &$params, &$values, $extends )
{
    $values['custom'] = array();
    
    $customFields = CRM_Core_BAO_CustomField::getFields( $extends );
    
    foreach ($params as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            /* check if it's a valid custom field id */
            if ( !array_key_exists($customFieldID, $customFields)) {
                return _crm_error('Invalid custom field ID');
            }
            
            /* validate the data against the CF type */
            $valid = CRM_Core_BAO_CustomValue::typecheck(
                                                         $customFields[$customFieldID][2], $value);
            
            if (! $valid) {
                return _crm_error('Invalid value for custom field ' .
                                  $customFields[$customFieldID][1]);
            }
            
            // fix the date field if so
            if ( $customFields[$customFieldID][2] == 'Date' ) {
                $value = str_replace( '-', '', $value );
            }
            
            // fixed for checkbox and multiselect
            $newMulValues = array();
            if ( $customFields[$customFieldID][3] == 'CheckBox' || $customFields[$customFieldID][3] =='Multi-Select') {
                $value = str_replace("|",",",$value);
                $mulValues = explode( ',' , $value );
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $mulValues as $v1 ) {
                    foreach( $custuomOption as $v2 ) {
                        if (( strtolower($v2['label']) == strtolower(trim($v1)) )||( strtolower($v2['value']) == strtolower(trim($v1)) )) {
                            $newMulValues[] = $v2['value'];
                        }
                    }
                }
                
                $value = CRM_Core_BAO_CustomOption::VALUE_SEPERATOR.implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$newMulValues).CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
            } else if( $customFields[$customFieldID][3] == 'Select' || $customFields[$customFieldID][3] == 'Radio' ) {
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $custuomOption as $v2 ) {
                    if( ( strtolower($v2['label']) == strtolower(trim($value)) )||( strtolower($v2['value']) == strtolower(trim($value)))) {
                        $value = $v2['value'];
                        break;
                    }
                }
            }
            
            $values['custom'][$customFieldID] = array(
                                                      'value'   => $value,
                                                      'extends' => $customFields[$customFieldID][3],
                                                      'type'    => $customFields[$customFieldID][2],
                                                      'custom_field_id' => $customFieldID,
                                                      );
        }
    }
}


/**
 * This function ensures that we have the right input parameters
 *
 * We also need to make sure we run all the form rules on the params list
 * to ensure that the params are valid
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new history.
 *                           
 *
 * @return bool true if success false otherwise
 * @access public
 */
function _civicrm_check_required_fields(&$params, $daoName)
{
    if ( ( $params['extends'] == 'Activity' || 
           $params['extends'] == 'Phonecall'  || 
           $params['extends'] == 'Meeting'    || 
           $params['extends'] == 'Group'      || 
           $params['extends'] == 'Contribution' 
           ) && 
         ( $params['style'] == 'Tab' ) ) {
        return _civicrm_create_error(ts("Can not create Custom Group in Tab for ". $params['extends']));
    }

    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    $dao =& new $daoName();
    $fields = $dao->fields();
 
    $missing = array();
    foreach ($fields as $k => $v) {
        if ($k == 'id') {
            continue;
        }
        
        if ($v['required'] && !(isset($params[$k]))) {
            $missing[] = $k;
        }
    }

    if (!empty($missing)) {
        return civicrm_create_error(ts("Required fields ". implode(',', $missing) . " for $daoName are not found"));
    }

    return true;
}





?>
