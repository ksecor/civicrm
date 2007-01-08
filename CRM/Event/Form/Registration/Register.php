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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org. If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/Registration.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration_Register extends CRM_Event_Form_Registration
{

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        parent::preProcess( );
    }

    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->assign('eventPage', $this->_values['event_page']);
        $this->assign('paidEvent', $this->_values['event']['is_monetary']);

        if ( $this->_values['event']['is_monetary'] ) {
            $this->buildAmount( );
            //$this->buildBillingBlock( );
            $this->buildCreditCard( );
        }
        require_once 'CRM/Core/DAO/UFJoin.php';
        $customField =& new CRM_Core_DAO_UFJoin();
        $customField->entity_id    = $this->_id;
        $customField->entity_table = 'civicrm_event';
        $customField->find();
        while( $customField->fetch() ) {
            $this->buildCustom( $customField->uf_group_id, 'customPre'  );
            $this->buildCustom( $customField->uf_group_id, 'customPost' );
        }

        
        $this->addButtons(array(
                                array ( 'type'      => 'back',
                                        'name'      => ts('<< Previous') ),
                                array ( 'type'      => 'next',
                                        'name'      => ts('Continue'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
    }
    
    function setDefaultValues( ) {
        // check if the user is registered and we have a contact ID
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        if ( $contactID ) {
            $options = array( );
            $fields = array( );
            require_once "CRM/Core/BAO/CustomGroup.php";
            $removeCustomFieldTypes = array ('Contribution');
            foreach ( $this->_fields as $name => $dontCare ) {
                //don't set custom data Used for Contribution (CRM-1344)
                if ( substr( $name, 0, 7 ) == 'custom_' ) {  
                    $id = substr( $name, 7 );
                    if ( ! CRM_Core_BAO_CustomGroup::checkCustomField( $id, $removeCustomFieldTypes )) {
                        continue;
                    }
                }
                $fields[$name] = 1;
            }
            $fields['state_province'] = $fields['country'] = $fields['email'] = 1;

            require_once 'CRM/Core/BAO/UFGroup.php';
            CRM_Core_BAO_UFGroup::setProfileDefaults( $contactID, $fields, $this->_defaults );
        }
        return $this->_defaults;
    }

    /**
     * build the radio/text form elements for the amount field
     *
     * @return void
     * @access private
     */
    public function buildAmount( ) {
        $elements = array( );
        // build the radio boxes
        if ( ! empty( $this->_values['event']['feeLevel'] ) ) {
            require_once 'CRM/Utils/Money.php';
            foreach( $this->_values['event']['feeLevel'] as $option => $val ) {
                $elements[] =& $this->createElement('radio', null, '',
                                                    CRM_Utils_Money::format($val['value']) . ' ' . $val['label'] );
            }
            $this->addGroup( $elements, 'amount', ts('Fee Level'), '<br />' );
        }
    }
    
    /**
     * build the billing block fields
     *
     * @return void
     * @access private
     */
    public function buildBillingBlock( ) {
        $billAttribute = array( 'size' => 30, 'maxlength' => 60 );
        
        $this->addElement('text', 'first_name', ts('First Name'), $billAttribute );
        $this->addElement('text', 'middle_name', ts('Middle Name'), $billAttribute );
        $this->addElement('text', 'last_name', ts('Last Name'), $billAttribute );
        $this->addElement('text', "city", ts('City'), $billAttribute );
        $this->addElement('text', "postal_code", ts('Zip / Postal Code'), $billAttribute );
        $this->addElement('select', "state_province_id", ts('State / Province'),
                          array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince());
        $this->addElement('select', "country_id", ts('Country'),
                          array('' => ts('- select -')) + CRM_Core_PseudoConstant::country());
    }
    
    /**  
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustom( $id, $name ) {
        if ( $id ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            require_once 'CRM/Profile/Form.php';
            $session =& CRM_Core_Session::singleton( );
            $contactID = $session->get( 'userID' );
            if ( $contactID ) {
                if ( CRM_Core_BAO_UFGroup::filterUFGroups($id)  ) {
                    $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD ); 
                    $this->assign( $name, $fields );
                    foreach($fields as $key => $field) {
                        CRM_Core_BAO_UFGroup::buildProfile($this, $field,CRM_Profile_Form::MODE_CREATE);
                        $this->_fields[$key] = $field;
                    }
                }
            } else {
                $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD ); 
                $this->assign( $name, $fields );
                foreach($fields as $key => $field) {
                    CRM_Core_BAO_UFGroup::buildProfile($this, $field,CRM_Profile_Form::MODE_CREATE);
                    $this->_fields[$key] = $field;
                }
            }
        }
    }
    

    /** 
     * Function to add all the credit card fields
     * 
     * @return None 
     * @access public 
     */
    function buildCreditCard( ) {
        $config =& CRM_Core_Config::singleton( );

        //if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_FORM) {
            foreach ( $this->_fields as $name => $field ) {
                $this->add( $field['htmlType'],
                            $field['name'],
                            $field['title'],
                            $field['attributes'] );
            }

            $this->addRule( 'cvv2', ts( 'Please enter a valid value for your card security code. This is usually the last 3-4 digits on the card\'s signature panel.' ), 'integer' );

            $this->addRule( 'credit_card_exp_date', ts('Select a valid date greater than today.'), 'currentDate');
            //}            
            
            //if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_BUTTON ) {
            $this->_expressButtonName = $this->getButtonName( 'next', 'express' );
            $this->add('image',
                       $this->_expressButtonName,
                       $config->paymentExpressButton,
                       array( 'class' => 'form-submit' ) );
            //}
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $regValue = $this->exportValues( );
        $this->set('registrationValue',$regValue);
 
    }//end of function
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Event Registration');
    }
    
}
?>
