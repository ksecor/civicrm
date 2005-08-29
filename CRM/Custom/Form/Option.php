<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Donald A. Lobo 01/15/2005
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
            $this->_fid = $defaults['custom_field_id'];
            
            $paramsField = array('id' => $this->_fid);
            CRM_Core_BAO_CustomField::retrieve($paramsField, $fieldDefaults);
            if( $fieldDefaults['default_value'] == $defaults['value'] ) {
                $defaults['default_value'] = 1;
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

        // lets trim all the whitespace
        $this->applyFilter('__ALL__', 'trim');

        if (CRM_Core_Action::UPDATE) {
            // hidden Option Id for validation use
            $this->add('hidden', 'optionId', $this->_id);
        }

        //hidden field ID for validation use
        $this->add('hidden', 'fieldId', $this->_fid); 
        
        
        // label
        $this->add('text', 'label', ts('Option Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'label'), true);
        //$this->addRule('label', ts('Please enter a valid label for this field.'), 'label');
	
        // value
        $this->add('text', 'value', ts('Option Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'value'), true);
        
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

        if (CRM_Core_Action::ADD && $fields['optionId'] == '') {
            $optionLabel = $fields['label'];
            $optionValue = $fields['value'];
            $fieldId = $fields['fieldId'];
            
            //check label duplicates within a custom field
            $daoLabel =& new CRM_Core_DAO();
            $query = "SELECT * FROM civicrm_custom_option WHERE custom_field_id = '$fieldId' AND label = '$optionLabel'";
            $daoLabel->query($query);
                    
            $result = $daoLabel->getDatabaseResult();
            $row    = $result->fetchRow();
            
            if ($row > 0) {
                $errors['label'] = 'There is an entry with same Label';
            }
            
            //check value duplicates within a custom field
            $daoValue =& new CRM_Core_DAO();
            $query = "SELECT * FROM civicrm_custom_option WHERE custom_field_id = '$fieldId' AND value = '$optionValue'";
            $daoValue->query($query);
                    
            $result = $daoValue->getDatabaseResult();
            $row    = $result->fetchRow();
            
            if ($row > 0) {
                $errors['value'] = 'There is an entry with same value';
            }
                
        }

        //capture duplicate entries while updating Custom Options
        if (CRM_Core_Action::UPDATE) {

            $optionLabel = $fields['label'];
            $optionValue = $fields['value'];
            $optionId = $fields['optionId'];
            $fieldId = $fields['fieldId'];

            //check label duplicates within a custom field
            $daoLabel =& new CRM_Core_DAO();
            $query = "SELECT * FROM civicrm_custom_option WHERE custom_field_id ='$fieldId' AND id != '$optionId' AND label = '$optionLabel'";
            
            $daoLabel->query($query);
                    
            $resultLabel = $daoLabel->getDatabaseResult();
            $rowLabel    = count($resultLabel->fetchRow());
            
            if ($rowLabel > 0) {

                $errors['label'] = 'There is an entry with same Label';
            }
            
            //check value duplicates within a custom field
            $daoValue =& new CRM_Core_DAO();
            $query = "SELECT * FROM civicrm_custom_option WHERE custom_field_id ='$fieldId' AND id != '$optionId' AND value = '$optionValue'";
            $daoValue->query($query);
                    
            $resultValue = $daoValue->getDatabaseResult();
            $rowValue    = $resultValue->fetchRow();

            if ($rowValue > 0) {
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
       
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $customOption->id = $this->_id;
        }

        // need the FKEY - custom field id
        $customOption->custom_field_id = $this->_fid;
        
        $customField =& new CRM_Core_DAO_CustomField();
        $customField->id = $this->_fid;
        if ( CRM_Utils_Array::value( 'default_value', $params ) ) {
            $customField->default_value = $customOption->value;
            $customField->save();
        } else if ( $customField->find( true ) &&
                    $customField->default_value == $customOption->value ) {
            // this is the case where this option is the current default value and we have been reset
            $customField->default_value = 'null';
            $customField->save(); 
        }
	
        $customOption->save();
        
        
        CRM_Core_Session::setStatus(ts('Your multiple choice option "%1" has been saved', array(1 => $customOption->label)));
    }
}
?>
