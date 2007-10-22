<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the field aspect of Custom
 */
class CRM_Price_Form_Option extends CRM_Core_Form {
    /**
     * the price field id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_fid;

    /**
     * option value  id, used when editing the Option
     *
     * @var int
     * @access protected
     */
    protected $_oid;


    /**
     * Function to set variables up before form is built
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_fid  = CRM_Utils_Request::retrieve('fid', 'Positive',
                                                   $this);
        $this->_oid  = CRM_Utils_Request::retrieve('oid' , 'Positive',
                                                   $this);
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @param null
     * 
     * @return array   array of default values
     * @access public
     */

    function setDefaultValues()
    {
        $defaults = array();
        
        if (isset($this->_oid)) {
            $params = array('id' => $this->_oid);
            
            CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_OptionValue', 
                                          $params, $defaults );
        }
        
        require_once 'CRM/Core/DAO.php';
        require_once 'CRM/Utils/Weight.php';
        
        if ($this->_action & CRM_Core_Action::ADD) {
            $fieldValues = array( 'option_group_id' => CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionGroup",
                                                                                    "civicrm_price_field.amount.{$this->_fid}",
                                                                                    'id', 'name' ) 
                                  );
            
            $defaults['weight']    = CRM_Utils_Weight::getDefaultWeight( 'CRM_Core_DAO_OptionValue', $fieldValues );
            $defaults['is_active'] = 1;
        }
        
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        if ($this->_action == CRM_Core_Action::DELETE) {
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => ts('Delete') ),
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
            // please check with Lobo before u comment this
            $this->addRule('value', ts('Please enter a monetary value for this field.'), 'money');
            
            // weight
            $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'weight'), true);
            $this->addRule('weight', ts(' is a numeric field') , 'numeric');
            
            // is active ?
            $this->add('checkbox', 'is_active', ts('Active?'));
            
            // add a custom form rule
            require_once 'CRM/Custom/Form/Option.php';
            //$this->addFormRule( array( 'CRM_Custom_Form_Option', 'formRule' ) );
            
            // add buttons
            $this->addButtons(array(
                                    array ('type'      => 'next',
                                           'name'      => ts('Save') ),
                                    array ('type'      => 'cancel',
                                           'name'      => ts('Cancel')),
                                    )
                              );
            
            
            // if view mode pls freeze it with the done button.
            if ($this->_action & CRM_Core_Action::VIEW) {
                $this->freeze();
                $this->addButtons(array(
                                        array ('type'      => 'cancel',
                                               'name'      => ts('Done with Preview'),
                                               'isDefault' => true),
                                        )
                                  );
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
        $temp = array();
        if ( empty($fields['optionId'])) {
            $fieldId = $fields['fieldId'];
            
            //check label duplicates within a custom field
            $query = "SELECT count(*) FROM civicrm_custom_option WHERE entity_id = '$fieldId' AND entity_table = 'civicrm_price_field' AND label = '$optionLabel'";
           
            if ( CRM_Core_DAO::singleValueQuery( $query, $temp ) > 0 ) { 
                $errors['label'] = 'There is an entry with the same label.';
            }
            
            //check value duplicates within a custom field
            $query = "SELECT count(*) FROM civicrm_custom_option WHERE entity_id = '$fieldId' AND entity_table = 'civicrm_price_field' AND value = '$optionValue'";
            
            if ( CRM_Core_DAO::singleValueQuery( $query, $temp ) > 0 ) {  
                $errors['value'] = 'There is an entry with the same value.';
            }
                
        } else {

            //capture duplicate entries while updating Custom Options
            $optionId = CRM_Utils_Type::escape( $fields['optionId'], 'Integer' );
            $fieldId  = CRM_Utils_Type::escape( $fields['fieldId'] , 'Integer' );

            //check label duplicates within a custom field
            $query = "SELECT count(*) FROM civicrm_custom_option WHERE entity_id = '$fieldId' AND entity_table = 'civicrm_price_field' AND id != '$optionId' AND label = '$optionLabel'";
            
            if ( CRM_Core_DAO::singleValueQuery( $query, $temp ) > 0 ) {   
                $errors['label'] = 'There is an entry with same label.';
            }
            
            //check value duplicates within a custom field
            $query = "SELECT count(*) FROM civicrm_custom_option WHERE entity_id = '$fieldId' AND entity_table = 'civicrm_price_field' AND id != '$optionId' AND value = '$optionValue'";
            if ( CRM_Core_DAO::singleValueQuery( $query, $temp ) > 0 ) {   
                $errors['value'] = 'There is an entry with same value';
            }
        }

        // price option must be of type Money
        if ( ! CRM_Utils_Rule::money( $fields["value"] ) ) {
            $errors['value'] = ts( 'Please enter a valid value.' );
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Process the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */

    public function postProcess()
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( 'Option' );
        
        $params['is_active']       = CRM_Utils_Array::value( 'is_active', $params, false );
        
        $params['option_group_id'] = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionGroup",
                                                                  "civicrm_price_field.amount.{$this->_fid}",
                                                                  'id', 'name' );
        
        if ( $this->_action == CRM_Core_Action::DELETE ) {
            $label = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionValue",
                                                  $this->_oid,
                                                  'label', 'id' );
            require_once 'CRM/Core/BAO/OptionValue.php';
            CRM_Core_BAO_OptionValue::del( $this->_oid );
            CRM_Core_Session::setStatus( ts( '%1 option has been deleted', 
                                             array( 1 => $label ) ) );
            return;
        }
        
        $ids = array( );
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $ids['optionValue'] = $this->_oid;
        }
        
        require_once 'CRM/Core/BAO/OptionValue.php';
        $optoinValue = CRM_Core_BAO_OptionValue::add( $params, $ids );
        
        CRM_Core_Session::setStatus( ts( 'The option "%1" has been saved', 
                                         array( 1 => $optoinValue->label ) ) );
    }
}
?>
