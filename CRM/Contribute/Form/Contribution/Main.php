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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionBase.php';
require_once 'CRM/Utils/Payment.php';

/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Contribute_Form_Contribution_Main extends CRM_Contribute_Form_ContributionBase {

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        
        parent::preProcess( );

        $this->assign( 'intro_text', $this->_values['intro_text'] );
    }

    function setDefaultValues( ) {
        // check if the user is registered and we have a contact ID
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        if ( $contactID ) {
            $options = array( );
            $fields = array( );
            foreach ( $this->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
            $fields['state_province'] = $fields['country'] = 1;
            $contact =& CRM_Contact_BAO_Contact::contactDetails( $contactID, $options, $fields );
            foreach ($this->_fields as $name => $field ) { 
                if ( $contact->$name ) {
                    $this->_defaults[$name] = $contact->$name;
                }
            }
        }

        // hack to simplify credit card entry for now
        $this->_defaults['credit_card_type']     = 'Visa';
        $this->_defaults['credit_card_number']   = '4807731747657838';
        $this->_defaults['cvv2']                 = '000';
        $this->_defaults['credit_card_exp_date'] = array( 'Y' => '2006', 'M' => '01' );

        return $this->_defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->applyFilter('__ALL__', 'trim');

        $this->add( 'text', 'email', ts( 'Email Address' ), array( 'size' => 30, 'maxlength' => 60 ), true );
 
        $this->buildCreditCard( );

        $this->buildAmount( );

        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );

        $this->addButtons(array( 
                                array ( 'type'      => 'next', 
                                        'name'      => ts('Continue >>'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );

        $this->addFormRule( array( 'CRM_Contribute_Form_Contribution_Main', 'formRule' ), $this );
    }

    function buildAmount( ) {
        $elements = array( );

        // first build the radio boxes
        if ( ! empty( $this->_values['label'] ) ) {
            for ( $index = 1; $index <= count( $this->_values['label'] ); $index++ ) {
                $elements[] =& $this->createElement('radio', null, '',
                                                    '$' . $this->_values['value'][$index] . ' ' . $this->_values['label'][$index],
                                                    $this->_values['value'][$index],
                                                    array('onclick'=>'clearAmountOther();'));
                if ( $this->_values['value'][$index] == $this->_values['default_amount'] ) {
                    $this->_defaults["amount"] = $this->_values['value'][$index];
                }
            }
        }

        if ( $this->_values['is_allow_other_amount'] ) {
            $elements[] =& $this->createElement('radio', null, '',
                                                ts('Other Amount'), 'amount_other_radio');

            $this->assign( 'is_allow_other_amount', true );
            $this->addElement('text', 'amount_other',
                       ts('Other Amount'), array( 'size' => 10, 'maxlength' => 10, 'onfocus'=>'useAmountOther();' )
                       );
            $this->addRule( 'amount_other', ts( 'Please enter a valid amount (numbers and decimal point only).' ), 'money' );
        } else {
            $this->assign( 'is_allow_other_amount', false );
        }


        $this->addGroup( $elements, 'amount', ts('Contribution Amount'), '<br />' );
        $this->addRule( 'amount', ts('Amount is a required field'), 'required' );
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
            CRM_Core_BAO_UFGroup::buildQuickForm( $id, $this, $name, $this->_fields );
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

        if ( $config->paymentBillingMode & CRM_Utils_Payment::BILLING_MODE_FORM) {
            foreach ( $this->_fields as $name => $field ) {
                $this->add( $field['htmlType'],
                            $field['name'],
                            $field['title'],
                            $field['attributes'] );
            }
            
            $this->addRule( 'cvv2', ts( 'Please enter a valid value for your card security code. This is usually the last 3-4 digits on the card\'s signature panel.' ), 'integer' );
            
            $this->addRule( 'credit_card_exp_date', ts('Select a valid date.'), 'qfDate');
        }            
            
        if ( $config->paymentBillingMode & CRM_Utils_Payment::BILLING_MODE_BUTTON ) {
            $this->_expressButtonName = $this->getButtonName( 'next', 'express' );
            $this->add('image',
                       $this->_expressButtonName,
                       'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',
                       array( 'class' => 'form-submit' ) );
        }
        
    }

    /** 
     * global form rule 
     * 
     * @param array $fields  the input form values 
     * @param array $files   the uploaded files if any 
     * @param array $options additional user data 
     * 
     * @return true if no errors, else array of errors 
     * @access public 
     * @static 
     */ 
    static function formRule( &$fields, &$files, &$self ) { 
        $errors = array( ); 

        if ( $fields['amount'] == 'amount_other_radio' ) {
            if ( $self->_values['min_amount'] > 0 ) {
                $min = $self->_values['min_amount'];
                if ( $fields['amount_other'] < $min ) {
                    $errors['amount_other'] = ts( 'This amount has to be greater than %1', 
                                                  array ( 1 => $min ) );
                }
            }

            if ( $self->_values['max_amount'] > 0 ) {
                $max = $self->_values['max_amount'];
                if ( $fields['amount_other'] > $max ) {
                    $errors['amount_other'] = ts( 'This amount has to be less than %1', 
                                                  array ( 1 => $max ) );
                }
            }
        }

        // make sure either 
        // return if this is express mode
        $config =& CRM_Core_Config::singleton( );
        if ( $config->paymentBillingMode & CRM_Utils_Payment::BILLING_MODE_BUTTON ) {
            if ( CRM_Utils_Array::value( $self->_expressButtonName . '_x', $fields ) ||
                 CRM_Utils_Array::value( $self->_expressButtonName . '_y', $fields ) ||
                 CRM_Utils_Array::value( $self->_expressButtonName       , $fields ) ) {
                return $errors;
            }
        }

        foreach ( $self->_fields as $name => $fld ) {
            if ( $fld['is_required'] &&
                 CRM_Utils_System::isNull( CRM_Utils_Array::value( $name, $fields ) ) ) {
                $errors[$name] = ts( "%1 is a required field", array( 1 => $fld['title'] ) );
            }
        }

        // make sure that credit card number and cvv are valid
        require_once 'CRM/Utils/Rule.php';
        if ( CRM_Utils_Array::value( 'credit_card_type', $fields ) ) {
            if ( CRM_Utils_Array::value( 'credit_card_number', $fields ) &&
                 ! CRM_Utils_Rule::creditCardNumber( $fields['credit_card_number'], $fields['credit_card_type'] ) ) {
                $errors['credit_card_number'] = ts( "Please enter a valid Credit Card Number" );
            }
            
            if ( CRM_Utils_Array::value( 'cvv2', $fields ) &&
                 ! CRM_Utils_Rule::cvv( $fields['cvv2'], $fields['credit_card_type'] ) ) {
                $errors['cvv2'] =  ts( "Please enter a valid Credit Card Verification Number" );
            }
        }

        return empty( $errors ) ? true : $errors;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $config =& CRM_Core_Config::singleton( );

        // get the submitted form values. 
        $params = $this->controller->exportValues( $this->_name ); 
        $params['currencyID']     = $config->defaultCurrency;

        $params['payment_action'] = 'Sale'; 
        $params['amount'] = ( $params['amount'] == 'amount_other_radio' ) ? $params['amount_other'] : $params['amount'];

        $this->set( 'amount', $params['amount'] ); 

        $payment =& CRM_Utils_Payment::singleton( ); 
  
        if ( $config->paymentBillingMode & CRM_Utils_Payment::BILLING_MODE_BUTTON ) {
            //get the button name  
            $buttonName = $this->controller->getButtonName( );  
            if ($buttonName == $this->_expressButtonName || 
                $buttonName == $this->_expressButtonName . '_x' || 
                $buttonName == $this->_expressButtonName . '_y' ) { 
                $this->set( 'contributeMode', 'express' ); 
                
                $donateURL = CRM_Utils_System::url( 'civicrm/contribute', '_qf_Contribute_display=1' ); 
                $params['cancelURL' ] = CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_Main_display=1', true, null, false ); 
                $params['returnURL' ] = CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_Confirm_display=1&rfp=1', true, null, false ); 
                
                $token = $payment->setExpressCheckout( $params ); 
                if ( is_a( $token, 'CRM_Core_Error' ) ) { 
                    CRM_Core_Error::displaySessionError( $token ); 
                    CRM_Utils_System::redirect( $params['cancelURL' ] );
                } 

                $this->set( 'token', $token ); 
                
                $paymentURL = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
                CRM_Utils_System::redirect( $paymentURL ); 
            }
        } else { 
            $this->set( 'contributeMode', 'direct' ); 
        } 
    }

}

?>