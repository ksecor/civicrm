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

require_once 'CRM/Core/DAO/CustomOption.php';


/**
 * Business objects for managing custom data options.
 *
 */
class CRM_Core_BAO_CustomOption extends CRM_Core_DAO_CustomOption {

    const VALUE_SEPERATOR = "";

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Core_BAO_CustomOption object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults )
    {
        $customOption =& new CRM_Core_DAO_CustomOption( );
        $customOption->copyValues( $params );
        if ( $customOption->find( true ) ) {
            CRM_Core_DAO::storeValues( $customOption, $defaults );
            return $customOption;
        }
        return null;
    }

     /**
     * takes an associative array and creates a custom option object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_DAO_CustomField object
     * @access public
     * @static
     */
    static function create(&$params)
    {
        $customOptionBAO =& new CRM_Core_BAO_CustomOption();
        $customOptionBAO->copyValues($params);
        return $customOptionBAO->save();
    }
    
    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active )
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_CustomOption', $id, 'is_active', $is_active );
    }


    /**
     * returns all active options ordered by weight for 
     *
     * @param  int      $fieldId         field whose options are needed
     * @param  boolean  $inactiveNeeded  do we need inactive options ?
     *
     * @return array $customOption all active options for fieldId
     * @static
     */
    static function getCustomOption($fieldId, $inactiveNeeded=false, $entityTable='civicrm_custom_field')
    {       
        $customOptionDAO =& new CRM_Core_DAO_CustomOption();
        $customOptionDAO->entity_id    = $fieldId;
        $customOptionDAO->entity_table = $entityTable;
        if (!$inactiveNeeded) {
            $customOptionDAO->is_active = 1;
        }
        $customOptionDAO->orderBy('weight ASC, label ASC');
        $customOptionDAO->find();
        
        $customOption = array();
        while ($customOptionDAO->fetch()) {
            $customOption[$customOptionDAO->id] = array();
            $customOption[$customOptionDAO->id]['id']    = $customOptionDAO->id;
            $customOption[$customOptionDAO->id]['label'] = $customOptionDAO->label;
            $customOption[$customOptionDAO->id]['value'] = $customOptionDAO->value;
        }
        return $customOption;
    }

    /**
     * Function to get the values of the checkboxes
     *
     * param $fieldId integer field id
     *
     * @static
     * @access public
     */
    static function getCustomValues($fieldId)
    {
        $customValueDAO =& new CRM_Core_DAO_CustomValue();
        $customValueDAO->custom_field_id = $fieldId;
        $customValueDAO->find(true);
        $values = $customValueDAO->char_data;
        $customValue = array();
        $customValue = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR ,$values);
        return $customValue;
    }

    static function getOptionLabel( $fieldId, $value, $fieldType = null, $entityTable = 'civicrm_custom_field' ) {
        $label = $value;
        switch ($fieldType) {
        case null:
        case 'Radio':
        case 'Select':
            $dao =& new CRM_Core_DAO_CustomOption();
            $dao->entity_id    = $fieldId;
            $dao->entity_table = $entityTable;
            $dao->value = $value;
            if ($dao->find(true)) {
                $label = $dao->label;
            }
            $dao->free();
            break;
        }
        return $label;
    }


    /**
     * Function to delete Option
     *
     * param $optionId integer option id
     *
     * @static
     * @access public
     */
    static function del($optionId) 
    {
        require_once 'CRM/Core/BAO/CustomField.php';
        require_once 'CRM/Core/BAO/CustomValue.php';
        
        $optionDAO =& new CRM_Core_DAO_CustomOption();
        $optionDAO->id = $optionId;
        $optionDAO->entity_table = "civicrm_custom_field";
        $optionDAO->find();
        $optionDAO->fetch();
        $custom_field_id = $optionDAO->entity_id;
        $value = $optionDAO->value;
        $fieldDAO = & new CRM_Core_DAO_CustomField();
        $fieldDAO->id = $custom_field_id;
        $fieldDAO->find();
        $fieldDAO->fetch();
        $customValueDAO = & new CRM_Core_DAO_CustomValue();
        $customValueDeleteDAO = & new CRM_Core_DAO_CustomValue();
        $customValueSaveDAO = & new CRM_Core_DAO_CustomValue();

        //added multiselect in if-statement below
        if( $fieldDAO->html_type !='CheckBox' && $fieldDAO->html_type !='Multi-Select' ) {

            $customValueDAO->custom_field_id = $custom_field_id;
            $customValueDAO->find();
            while($customValueDAO->fetch()) {
                if ($fieldDAO->data_type == 'Int') {
                    if( $customValueDAO->int_data == $value ) {
                        $customValueDeleteDAO->id = $customValueDAO->id;
                        $customValueDeleteDAO->delete();
                    }
                } else { 
                    if ($fieldDAO->data_type == 'Float') {
                        if( $customValueDAO->float_data == $value ) {
                            $customValueDeleteDAO->id = $customValueDAO->id;
                            $customValueDeleteDAO->delete();
                        }
                    } else {
                        if ($fieldDAO->data_type == 'Money') {
                            if( $customValueDAO->decimal_data == $value ) {
                                $customValueDeleteDAO->id = $customValueDAO->id;
                                $customValueDeleteDAO->delete();
                            }
                        } else {
                            if ($fieldDAO->data_type == 'Memo') {
                                if( $customValueDAO->memo_data == $value ) {
                                    $customValueDeleteDAO->id = $customValueDAO->id;
                                    $customValueDeleteDAO->delete();
                                }
                            } else {
                                if( $customValueDAO->char_data == $value ) {
                                    $customValueDeleteDAO->id = $customValueDAO->id;
                                    $customValueDeleteDAO->delete();
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $customValueDAO->custom_field_id = $custom_field_id;
            $customValueDAO->find();
            while($customValueDAO->fetch()) {
                $optionValues =  $customValueDAO->char_data;
                $customValue = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR ,$optionValues);
                
                if( in_array ($value,$customValue) ) {
                    if( count($customValue) == 1) {
                        $customValueDeleteDAO->id = $customValueDAO->id;
                        $customValueDeleteDAO->delete();
                    } else {
                        $customValueSaveDAO = $customValueDAO;
                        $keyArray = array_keys($customValue,$value);
                        unset($customValue[$keyArray[0]]);
                        $newCustomValue = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$customValue);
                        $customValueSaveDAO->char_data = $newCustomValue;
                        $customValueSaveDAO->save();
                    }
                }
            }
        } 

        $optionDAO->delete();
        return null;
    }

    static function updateCustomValues($params) 
    {
        require_once 'CRM/Core/BAO/CustomValue.php';
        
        $optionDAO =& new CRM_Core_DAO_CustomOption();
        $optionDAO->id = $params['optionId'];
        $optionDAO->entity_table = "civicrm_custom_field";
        $optionDAO->find();
        $optionDAO->fetch();
        $oldValue = $optionDAO->value;
        $custom_field_id = $optionDAO->entity_id;
        $customValueDAO = & new CRM_Core_DAO_CustomValue();
        $customValueSaveDAO = & new CRM_Core_DAO_CustomValue();
        $customValueDAO->custom_field_id = $custom_field_id;
        $customValueDAO->find();

        while($customValueDAO->fetch()) {
            if ($customValueDAO->int_data == $oldValue) {
                $customValueSaveDAO->id = $customValueDAO->id;
                $customValueSaveDAO->int_data = $params['value'];
                $customValueSaveDAO->save();
            } else if ($customValueDAO->float_data == $oldValue) {
                $customValueSaveDAO->id = $customValueDAO->id;
                $customValueSaveDAO->float_data = $params['value'];
                $customValueSaveDAO->save();
            } else if ($customValueDAO->decimal_data == $oldValue) {
                $customValueSaveDAO->id = $customValueDAO->id;
                $customValueSaveDAO->decimal_data = $params['value'];
                $customValueSaveDAO->save();
            } else if ($customValueDAO->memo_data == $oldValue) {
                $customValueSaveDAO->id = $customValueDAO->id;
                $customValueSaveDAO->memo_data = $params['value'];
                $customValueSaveDAO->save();
            } else {
                if ( $customValueDAO->char_data == $oldValue) {
                    $updateValue = $params['value'];
                } else {
                    $updateValue = str_replace(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . $oldValue . CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . $params['value'] . CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $customValueDAO->char_data );
                }
                
                $customValueSaveDAO->id = $customValueDAO->id;
                $customValueSaveDAO->char_data = $updateValue;
                $customValueSaveDAO->save();
            }
        }
    }

    /**
     * return the custom options associated with a specific entity id/table
     * as a name/value pair
     *
     * @param string $entity_table name of the table
     * @param string $entity_id   
     * @param array  $values       array tos tore the options in
     *
     * @return void
     * @static
     */
    static function getAssoc( $entity_table, $entity_id, &$values ) {
        require_once 'CRM/Core/DAO/CustomOption.php';  
        $dao =& new CRM_Core_DAO_CustomOption( );  
        $dao->entity_table = $entity_table;
        $dao->entity_id    = $entity_id;  
        $dao->find( ); 

        // now extract the amount 
        $values['value'] = array( ); 
        $values['label'] = array( ); 
        $index  = 1; 
         
        while ( $dao->fetch( ) ) { 
            $values['value'    ][$index] = $dao->value; 
            $values['label'    ][$index] = $dao->label; 
            $values['amount_id'][$index] = $dao->id; 
            $index++; 
        } 
    }

}
?>
