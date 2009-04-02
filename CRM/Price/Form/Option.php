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

            // fix the display of the monetary value, CRM-4038
            require_once 'CRM/Utils/Money.php';
            $defaults['name'] = CRM_Utils_Money::format($defaults['name'], null, '%a');
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
            return;
        } else {
            // lets trim all the whitespace
            $this->applyFilter('__ALL__', 'trim');
            
            // hidden Option Id for validation use
            $this->add('hidden', 'optionId', $this->_oid);
            
            //hidden field ID for validation use
            $this->add('hidden', 'fieldId', $this->_fid); 
            
            $optionGeoup = "civicrm_price_field.amount.{$this->_fid}";
            
            // label
            $this->add('text', 'label', ts('Option Label'),null, true);
             
            // value
            $this->add('text', 'name', ts('Option Amount'),null, true);
                      
            // the above value is used directly by QF, so the value has to be have a rule
            // please check with Lobo before u comment this
            $this->addRule('name', ts('Please enter a monetary value for this field.'), 'money');
            
            // weight
            $this->add('text', 'weight', ts('Weight'), null, true);
            $this->addRule('weight', ts('is a numeric field') , 'numeric');
            
            // is active ?
            $this->add('checkbox', 'is_active', ts('Active?'));

            //is default 
            $this->add('checkbox', 'is_default', ts('Default'));
            
            if ( $this->_fid ) {
                //hide the default checkbox option for text field
                $htmlType = CRM_Core_DAO::getFieldValue( 'CRM_Core_BAO_PriceField', $this->_fid, 'html_type' );
                $this->assign( 'hideDefaultOption', false );
                if ( $htmlType == 'Text' ) {
                    $this->assign( 'hideDefaultOption', true );
                }
            }
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
        
        $this->addFormRule( array( 'CRM_Price_Form_Option', 'formRule' ), $this );
    }
    
    /**
     * global validation rules for the form
     *
     * @param array  $fields   (referance) posted values of the form
     *
     * @return array    if errors then list of errors to be posted back to the form,
     *                  true otherwise
     * @static
     * @access public
     */

    static function formRule( &$fields, &$files, &$form ) 
    {
        $errors       = array( );
        $customOption = array( );
        $groupParams  = array( 'name' => "civicrm_price_field.amount.{$form->_fid}" );
        $htmlType = CRM_Core_DAO::getFieldValue( 'CRM_Core_BAO_PriceField', $form->_fid, 'html_type' );
        if ( $htmlType == 'Text' && $fields['name'] <= 0 ) {
            $errors['name'] = ts( 'Amount must be greater than zero When Price Field is of Text type' );  
        } else if ($fields['name'] < 0 ) {
            $errors['name'] = ts( 'Amount must be greater than zero' ); 
        }

        require_once 'CRM/Core/OptionValue.php';
        CRM_Core_OptionValue::getValues( $groupParams, $customOption );
                
        foreach( $customOption as $key => $value ) {
            if( !( $value['id'] == $form->_oid ) && ( $value['value'] == $fields['weight'] ) ) {
                $errors['value'] = ts( 'Duplicate option value' );  
            }
            if( !( $value['id']==$form->_oid ) && ( $value['label'] == $fields['label'] ) ) {
                $errors['label'] = ts( 'Duplicate option label' );  
            }
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
        if ( $this->_action == CRM_Core_Action::DELETE ) {
            $label = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionValue",
                                                  $this->_oid,
                                                  'label', 'id' );
            require_once 'CRM/Core/BAO/OptionValue.php';
            CRM_Core_BAO_OptionValue::del( $this->_oid );
            CRM_Core_Session::setStatus( ts( '%1 option has been deleted.', 
                                             array( 1 => $label ) ) );
            return;
        }
        
        // store the submitted values in an array
        $params = $this->controller->exportValues( 'Option' );
        
        $params['is_active']       = CRM_Utils_Array::value( 'is_active', $params, false );
        $params['is_default']      = CRM_Utils_Array::value( 'is_default', $params, false );
        
        $params['option_group_id'] = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionGroup",
                                                                  "civicrm_price_field.amount.{$this->_fid}",
                                                                  'id', 'name' );
        $groupName                 = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', $params['option_group_id'], 'name' );
        if ( $groupName ) {
            $fieldName      = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_PriceField', substr( $groupName, 27 ), 'label') ;
            $params['description'] = $fieldName.' - '.$params['label'] ;
        }  
        $params['value'] = $params['weight'];
        // fix the display of the monetary value, CRM-4038
        $params['name'] = CRM_Utils_Rule::cleanMoney( $params['name'] );

        $ids = array( );
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $ids['optionValue'] = $this->_oid;
        }
        
        require_once 'CRM/Core/BAO/OptionValue.php';
        $optoinValue = CRM_Core_BAO_OptionValue::add( $params, $ids );
        CRM_Core_Session::setStatus( ts( 'The option \'%1\' has been saved.', 
                                         array( 1 => $optoinValue->label ) ) );
    }
}

