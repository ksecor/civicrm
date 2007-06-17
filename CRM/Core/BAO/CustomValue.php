<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Core/DAO/CustomValue.php';


/**
 * Business objects for managing custom data values.
 *
 */
class CRM_Core_BAO_CustomValue extends CRM_Core_DAO_CustomValue 
{

    /**
     * Validate a value against a CustomField type
     *
     * @param string $type  The type of the data
     * @param string $value The data to be validated
     * 
     * @return boolean True if the value is of the specified type
     * @access public
     * @static
     */
    public static function typecheck($type, $value) 
    {
        switch($type) {
        case 'Memo':
            return true;
            
        case 'String':
            return CRM_Utils_Rule::string($value);
            
        case 'Int':
            return CRM_Utils_Rule::integer($value);
            
        case 'Float':
        case 'Money':
            return CRM_Utils_Rule::numeric($value);
            
        case 'Date':
            return CRM_Utils_Rule::date($value);
            
        case 'Boolean':
            return CRM_Utils_Rule::boolean($value);
            
        case 'StateProvince':
            return
                array_key_exists(strtolower($value),
                                 array_change_key_case( array_flip( CRM_Core_PseudoConstant::stateProvinceAbbreviation() ), CASE_LOWER ) )
                || array_key_exists(strtolower($value),
                                    array_change_key_case( array_flip( CRM_Core_PseudoConstant::stateProvince() ), CASE_LOWER ) );
        case 'Country':
            return
                array_key_exists(strtolower($value),
                         array_change_key_case( array_flip( CRM_Core_PseudoConstant::countryIsoCode() ), CASE_LOWER ) )
                || array_key_exists(strtolower($value),
                            array_change_key_case( array_flip( CRM_Core_PseudoConstant::country() ), CASE_LOWER ) );
        case 'Link':
            return CRM_Utils_Rule::string($value);
        }
        return false;
    }
    
    /**
     * Create a new CustomValue record
     *
     * @param array $params  The values for the new record
     *
     * @return object  The new BAO
     * @access public
     * @static
     */
    public static function create(&$params) 
    {
        $customValue =& new CRM_Core_BAO_CustomValue();
        
        $customValue->copyValues($params);
        
        // lets find the object if one exists
        // this allow us to use only one custom value / field for a given contact
        $customValue->find( true );

        switch($params['type']) {
        case 'StateProvince':
            if ( !is_numeric($params['value'])) {
                $states = array( );
                $states['state_province'] = $params['value'];
                
                CRM_Contact_BAO_Contact::lookupValue( $states, 'state_province', 
                                                      CRM_Core_PseudoConstant::stateProvince(), true );
                if ( !$states['state_province_id'] ) {
                    CRM_Contact_BAO_Contact::lookupValue( $states, 'state_province',
                                                          CRM_Core_PseudoConstant::stateProvinceAbbreviation(), true );
                }
                $customValue->int_data = $states['state_province_id'];
            } else {                
                $customValue->int_data = $params['value'];
            }
            
            break;
            
        case 'Country':
            if ( !is_numeric($params['value'])) {
                $countries = array( );
                $countries['country'] = $params['value'];
                
                CRM_Contact_BAO_Contact::lookupValue( $countries, 'country', 
                                                      CRM_Core_PseudoConstant::country(), true );
                if ( !$countries['country_id'] ) {
                    CRM_Contact_BAO_Contact::lookupValue( $countries, 'country',
                                                          CRM_Core_PseudoConstant::countryIsoCode(), true );
                }
                $customValue->int_data = $countries['country_id'];
            } else {                
                $customValue->int_data = $params['value'];
            }
            
            break;
      
        case 'File':
            // need to add/update civicrm_entity_file
            require_once 'CRM/Core/DAO/EntityFile.php'; 
            $entityFileDAO =& new CRM_Core_DAO_EntityFile();
            
            
            if ( $params['file_id'] ) {
                $entityFileDAO->file_id = $params['file_id'];
                $entityFileDAO->find(true);
            }
            
            $entityFileDAO->entity_table = $params['entity_table'];
            $entityFileDAO->entity_id    = $params['entity_id'];
            $entityFileDAO->file_id      = $params['file_id'];
            $entityFileDAO->save();
            
        case 'String':
            $customValue->char_data = $params['value'];
            break;
            
        case 'Boolean':
            $customValue->int_data = 
                CRM_Utils_String::strtobool($params['value']);
            break;
            
        case 'Int':
            $customValue->int_data = $params['value'];
            break;
            
        case 'Float':
            $customValue->float_data = $params['value'];
            break;
            
        case 'Money':
            $customValue->decimal_data = number_format( $params['value'], 2, '.', '' );
            break;
            
        case 'Memo':
            $customValue->memo_data = $params['value'];
            break;
            
        case 'Date':
            $customValue->date_data = $params['value'];
            break;
        
        case 'Link':
            $customValue->char_data = $params['value'];
            break;
        }
        $customValue->save();       
        return $customValue;
    }
    
    /**
     * given a 'civicrm' type string, return the mysql data store area
     *
     * @param string $type the civicrm type string
     *
     * @return the mysql data store placeholder
     * @access public
     * @static
     */
    public static function typeToField($type) 
    {
        switch ($type) {
        case 'String':
        case 'File':
            return 'char_data';
        case 'Boolean':
        case 'Int':
        case 'StateProvince':
        case 'Country':
            return 'int_data';
        case 'Float':
            return 'float_data';
        case 'Money':
            return 'decimal_data';
        case 'Memo':
            return 'memo_data';
        case 'Date':
            return 'date_data';
        case 'Link':
            return 'char_data';            
        default:
            return null;
        }
    }
    
    /**
     * given a field return the type associated with it
     *
     * @param string $type the civicrm type string
     *
     * @return the mysql data store placeholder
     * @access public
     * @static
     */
    public static function fieldToType($type) 
    {
        switch ($type) {
        case 'char_data':
        case 'memo_data':
            return 'String';
        case 'int_data':
            return 'Int';
        case 'float_data':
        case 'decimal_data':
            return 'Float';
        case 'date_data':
            return 'Date';
        default:
            return null;
        }
    }
    
    /**
     * return the mysql type of the current value.
     * If boolean type, set the isBool flag too (since int and bool share
     * the same mysql type, we need another differentiator
     *
     * @param boolean  $isBool (reference )  set to true if boolean
     * 
     * @return string the mysql type
     * @access public
     */
    public function getField( &$isBool, $cf = null) 
    {
        if ($cf == null) {
            $cf =& new CRM_Core_BAO_CustomField();
            $cf->id = $this->custom_field_id;
            
            if (! $cf->find(true)) {
                return null;
            }
        }
        
        $isBool = $cf->data_type == 'Boolean' ? true : false;
        
        return $this->typeToField($cf->data_type);
    }
    
    /**
     * returns the string value of the curren object
     *
     * @param boolean $translateBoolean should a boolean value be translated to yes/no
     *
     * @return string the value
     * @access public
     */
    public function getValue($translateBoolean = false) 
    {
        $field = $this->getField($var1);
        
        if ($translateBoolean && $cf->data_type == 'Boolean') {
            return $this->$field ? 'yes' : 'no';
        }
        
        if ( $cf->data_type == 'Money' ) {
            return number_format( $this->$field, 3, '.', '' );
        }
        
        return $this->$field;
    }
    
    /**
     * Find all the custom values for a given contact.
     *
     * @param int $contactId  the id of the contact
     * @return array $values  Array of CustomValue objects
     * @access public
     * @static
     */
    public static function getContactValues($contactId) 
    {
        if ( ! $contactId ) {
            // adding this year since an empty contact id could have serious repurcussions
            // like looping forever
            CRM_Core_Error::backtrace( );
            CRM_Core_Error::fatal( 'Please file an issue with the backtrace' );
            return null;
        }

        $customValue =& new CRM_Core_BAO_CustomValue();
        
        $customValue->entity_id = $contactId;
        $customValue->entity_table = 'civicrm_contact';
        
        $customValue->find();
        $values = array();

        require_once 'api/utils.php';
        while ($customValue->fetch()) {
            $value = array( );
            _crm_object_to_array( $customValue, $value );
            // this is the last time we have access to the BAO object,
            // so add the value to the result array (CRM-1840)
            $value['value'] = $customValue->getValue();
            $values[] = $value;
        }
        $customValue->free( );
        return $values;
    }
    
    /**
     * update the custom calue for a given contact id and field id
     *
     * @param int    $contactId contact id
     * @param int    $cfId      custom field id
     * @param string $value     the value to set the field
     *
     * @return void
     * @access public
     * @static
     */
    public static function updateValue($contactId, $cfId, $value) 
    {
        $customValue =& new CRM_Core_BAO_CustomValue();
        
        $customValue->custom_field_id = $cfId;
        $customValue->entity_table = 'civicrm_contact';
        $customValue->entity_id = $contactId;
        
        $customValue->find(true);
        
        $cf =& new CRM_Core_BAO_CustomField();
        $cf->id = $cfId;
        $cf->find(true);
        if ($cf->data_type == 'StateProvince') {
            $states =& CRM_Core_PseudoConstant::stateProvince();
            if (CRM_Utils_Rule::integer($value)) {
                $customValue->int_data = $value;
                $customValue->char_data = 
                    CRM_Utils_Array::value($value, $states);
            } else {
                $customValue->int_data = 
                    CRM_Utils_Array::key($value, $states);
                $customValue->char_data = $value;
            }
        } elseif ($cf->data_type == 'Country') {
            $countries =& CRM_Core_PseudoConstant::country();
            if (CRM_Utils_Rule::integer($value)) {
                $customValue->int_data = $value;
                $customValue->char_data = 
                    CRM_Utils_Array::value($value, $countries);
            } else {
                $customValue->int_data = 
                    CRM_Utils_Array::key($value, $countries);
                $customValue->char_data = $value;
            }
        } else {
            $isBool = false;
            $field = $customValue->getField($isBool, $cf);
            if ($isBool) {
                $value = CRM_Utils_String::strtobool($value);
            }
            $customValue->$field = $value;
        }
        
        $customValue->save();
    }

}

?>
