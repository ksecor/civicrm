<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id: Field.php 1419 2005-06-10 12:18:04Z shot $
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the field aspect of Custom
 */
class CRM_Custom_Form_Option extends CRM_Core_Form {
    /**
     * the custom group id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_fid;

    /**
     * The Option id, used when editing the Option
     *
     * @var int
     * @access protected
     */
    protected $_id;


    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_fid = CRM_Utils_Request::retrieve('fid', $this);
        $this->_id  = CRM_Utils_Request::retrieve('id' , $this);
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues()
    {
        $defaults = array();
        $fieldDefaults = array();
        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_CustomOption::retrieve($params, $defaults);
            //$this->_fid = $defaults['custom_field_id'];
            $this->_fid = $defaults['entity_id'];

            $paramsField = array('id' => $this->_fid);            
            CRM_Core_BAO_CustomField::retrieve($paramsField, $fieldDefaults);

            if ( $fieldDefaults['html_type'] == 'CheckBox' || $fieldDefaults['html_type'] == 'Multi-Select' ) {
                $defaultCheckValues = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $fieldDefaults['default_value']);
                if ( in_array($defaults['value'], $defaultCheckValues ) ) 
                    $defaults['default_value'] = 1;
            } else {
                if( $fieldDefaults['default_value'] == $defaults['value'] ) {
                    $defaults['default_value'] = 1;
                }               
            }
        } else {
            $defaults['is_active'] = 1;
        }
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        if ($this->_action == CRM_Core_Action::DELETE) {
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => ts('Delete'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
        } else {
            // lets trim all the whitespace
            $this->applyFilter('__ALL__', 'trim');
            
            // hidden Option Id for validation use
            $this->add('hidden', 'optionId', $this->_id);
            
            //hidden field ID for validation use
            $this->add('hidden', 'fieldId', $this->_fid); 
        
            
            // label
            $this->add('text', 'label', ts('Option Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'label'), true);
            
            $this->add('text', 'value', ts('Option Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'value'), true);
        
            // the above value is used directly by QF, so the value has to be have a rule
            $this->addRule('value', ts('Please enter a valid value for this field.'), 'qfVariable');

            // weight
            $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'weight'), true);
            $this->addRule('weight', ts(' is a numeric field') , 'numeric');
        
            // is active ?
            $this->add('checkbox', 'is_active', ts('Active?'));
            
            // Set the default value for Custom Field
            $this->add('checkbox', 'default_value', ts('Default'));

            // add a custom form rule
            $this->addFormRule( array( 'CRM_Custom_Form_Option', 'formRule' ) );
            
            // add buttons
            $this->addButtons(array(
                                    array ('type'      => 'next',
                                           'name'      => ts('Save'),
                                           'isDefault' => true),
                                    array ('type'      => 'cancel',
                                           'name'      => ts('Cancel')),
                                    )
                              );
            
            
            // if view mode pls freeze it with the done button.
            if ($this->_action & CRM_Core_Action::VIEW) {
                $this->freeze();
                $this->addElement('button', 'done', ts('Done'), array('onClick' => "location.href='civicrm/admin/custom/group/field/option?reset=1&action=browse&fid=" . $this->_fid . "'"));
            }
        }
    }
        
    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$fields ) {

        $optionLabel = CRM_Utils_Type::escape( $fields['label'], 'String' );
        $optionValue = CRM_Utils_Type::escape( $fields['value'], 'String' );
        if ( empty($fields['optionId'])) {
            $fieldId = $fields['fieldId'];
            
            //check label duplicates within a custom field
            $query = "SELECT count(*) FROM civicrm_custom_option WHERE entity_id = '$fieldId' AND entity_table = 'civicrm_custom_field' AND label = '$optionLabel'";
            if ( CRM_Core_DAO::singleValueQuery( $query ) > 0 ) { 
                $errors['label'] = 'There is an entry with the same label.';
            }
            
            //check value duplicates within a custom field
            $query = "SELECT count(*) FROM civicrm_custom_option WHERE entity_id = '$fieldId' AND entity_table = 'civicrm_custom_field' AND value = '$optionValue'";
            if ( CRM_Core_DAO::singleValueQuery( $query ) > 0 ) {  
                $errors['value'] = 'There is an entry with the same value.';
            }
                
        } else {

            //capture duplicate entries while updating Custom Options
            $optionId = CRM_Utils_Type::escape( $fields['optionId'], 'Integer' );
            $fieldId  = CRM_Utils_Type::escape( $fields['fieldId'] , 'Integer' );

            //check label duplicates within a custom field
            $query = "SELECT count(*) FROM civicrm_custom_option WHERE entity_id = '$fieldId' AND entity_table = 'civicrm_custom_field' AND id != '$optionId' AND label = '$optionLabel'";
            if ( CRM_Core_DAO::singleValueQuery( $query ) > 0 ) {   
                $errors['label'] = 'There is an entry with same label.';
            }
            
            //check value duplicates within a custom field
            $query = "SELECT count(*) FROM civicrm_custom_option WHERE entity_id = '$fieldId' AND entity_table = 'civicrm_custom_field' AND id != '$optionId' AND value = '$optionValue'";
            if ( CRM_Core_DAO::singleValueQuery( $query ) > 0 ) {   
                $errors['value'] = 'There is an entry with same value';
            }
        }
        return empty($errors) ? true : $errors;
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */

    public function postProcess()
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues('Option');

        // set values for custom field properties and save
        $customOption                =& new CRM_Core_DAO_CustomOption();
        $customOption->label         = $params['label'];
        $customOption->weight        = $params['weight'];
        $customOption->value         = $params['value'];
        $customOption->is_active     = CRM_Utils_Array::value( 'is_active', $params, false );
       
        if ($this->_action == CRM_Core_Action::DELETE) {
            CRM_Core_BAO_CustomOption::del($this->_id);
             CRM_Core_Session::setStatus(ts('Your multiple choice option has been deleted', array(1 => $customOption->label)));
            return;
            
        }
        
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $customOption->id = $this->_id;
        }

        // need the FKEY - custom field id
        //$customOption->custom_field_id = $this->_fid;
        $customOption->entity_id    = $this->_fid;
        $customOption->entity_table = 'civicrm_custom_field';
        
        $customField =& new CRM_Core_DAO_CustomField();
        $customField->id = $this->_fid;
        if ( $customField->find( true ) && ($customField->html_type == 'CheckBox' || $customField->html_type == 'Multi-Select')) {
            $defVal = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $customField->default_value);

            if ( CRM_Utils_Array::value( 'default_value', $params ) ) {
                if ( !in_array($customOption->value, $defVal) ) {
                    
                    if ( empty($defVal[0]) ) {
                        $customField->default_value = $customOption->value;
                    } else {
                        $customField->default_value .= CRM_Core_BAO_CustomOption::VALUE_SEPERATOR.$customOption->value;                   
                    }
                    
                    $customField->save();
                }
            } else if ( in_array($customOption->value, $defVal) ) {
                $tempVal = array();
                foreach ($defVal as $v ) {
                    if ($v != $customOption->value)
                    $tempVal[] = $v;
                }

                $customField->default_value = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $tempVal);                
                $customField->save(); 
            }           
        } else {            
            if ( CRM_Utils_Array::value( 'default_value', $params ) ) {
                $customField->default_value = $customOption->value;
                $customField->save();
            } else if ( $customField->find( true ) && $customField->default_value == $customOption->value ) {
                // this is the case where this option is the current default value and we have been reset
                $customField->default_value = 'null';
                $customField->save(); 
            }
        }
	
        $customOption->save();
        
        
        CRM_Core_Session::setStatus(ts('Your multiple choice option "%1" has been saved', array(1 => $customOption->label)));
    }
}
?>
