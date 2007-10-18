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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/Registration.php';
require_once 'CRM/Core/Payment.php';

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
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        // check if the user is registered and we have a contact ID
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        if ( $contactID ) {
            $options = array( );
            $fields = array( );
            require_once "CRM/Core/BAO/CustomGroup.php";
            if ( ! empty($this->_fields)) {
                foreach ( $this->_fields as $name => $dontCare ) {
                    $fields[$name] = 1;
                }
            }
            $names = array("first_name", "middle_name", "last_name");
            foreach ($names as $name) {
                $fields[$name] = 1;
            }
            $fields["state_province-{$this->_bltID}"] = 1;
            $fields["country-{$this->_bltID}"       ] = 1;
            $fields["email-{$this->_bltID}"         ] = 1;
            
            require_once 'CRM/Core/BAO/UFGroup.php';
            CRM_Core_BAO_UFGroup::setProfileDefaults( $contactID, $fields, $this->_defaults );

            foreach ($names as $name) {
                if ( isset( $this->_defaults[$name] ) ) {
                    $this->_defaults["billing_" . $name] = $this->_defaults[$name];
                }
            }
        }

        //set custom field defaults
        if ( ! empty( $this->_fields ) ) {
            require_once "CRM/Core/BAO/CustomField.php";
            foreach ( $this->_fields as $name => $field ) {
                if ( $customFieldID = CRM_Core_BAO_CustomField::getKeyID($name) ) {
                    if ( !isset( $this->_defaults[$name] )) { //fix for CRM-1743 
                        CRM_Core_BAO_CustomField::setProfileDefaults( $customFieldID, $name, $this->_defaults,
                                                                      null, CRM_Profile_Form::MODE_REGISTER );
                    }
                }
            }
        }

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
        $config =& CRM_Core_Config::singleton( );

        $this->add( 'text',
                    "email-{$this->_bltID}",
                    ts( 'Email Address' ),
                    array( 'size' => 30, 'maxlength' => 60 ), true );

        if ( $this->_values['event']['is_monetary'] ) {
            self::buildAmount( $this );

            require_once 'CRM/Core/Payment/Form.php';
            CRM_Core_Payment_Form::buildCreditCard( $this );
        }

        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );

        $session =& CRM_Core_Session::singleton( );
        $userID = $session->get( 'userID' );
        if ( ! $userID ) {
            $createCMSUser = false;
            if ( $this->_values['custom_pre_id'] ) {
                $profileID = $this->_values['custom_pre_id'];
                $createCMSUser = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_UFGroup', $profileID, 'is_cms_user' );
            }
            if ( ! $createCMSUser &&
                 $this->_values['custom_post_id'] ) {
                $profileID = $this->_values['custom_post_id'];
                $createCMSUser = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_UFGroup', $profileID , 'is_cms_user');
            }

            if ( $createCMSUser ) {
                require_once 'CRM/Core/BAO/CMSUser.php';
                CRM_Core_BAO_CMSUser::buildForm( $this, $profileID , true );
            }
        }

        $uploadNames = $this->get( 'uploadNames' );
        $buttonName = empty( $uploadNames ) ? 'next' : 'upload';

        // if payment is via a button only, dont display continue
        if ( $this->_paymentProcessor['billing_mode'] != CRM_Core_Payment::BILLING_MODE_BUTTON ||
             ! $this->_values['event']['is_monetary']) {
            $this->addButtons(array( 
                                    array ( 'type'      => $buttonName, 
                                            'name'      => ts('Continue >>'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    ) 
                              );
        }
        $this->addFormRule( array( 'CRM_Event_Form_Registration_Register', 'formRule' ),
                            $this );
       
    }

    /**
     * build the radio/text form elements for the amount field
     *
     * @return void
     * @access private
     */
    static public function buildAmount( &$form, $required = true ) {
        $elements = array( );
        if ( isset($form->_priceSetId) ) {
            $form->add( 'hidden', 'priceSetId', $form->_priceSetId );
            $form->assign( 'priceSet', $form->_priceSet );
            require_once 'CRM/Core/BAO/PriceField.php';
            foreach ( $form->_values['custom']['fields'] as $field ) {
                $fieldId = $field['id'];
                $elementName = 'price_' . $fieldId;
                CRM_Core_BAO_PriceField::addQuickFormElement(
                    $form, $elementName, $fieldId, false,
                    $field['is_required']
                );
            }
        }
        else if ( ! empty( $form->_values['custom']['label'] ) ) {
            require_once 'CRM/Utils/Money.php';
            for ( $index = 1; $index <= count( $form->_values['custom']['label'] ); $index++ ) {
                $elements[] =& $form->createElement('radio', null, '',
                                                    CRM_Utils_Money::format($form->_values['custom']['value'][$index]) . ' ' . 
                                                    $form->_values['custom']['label'][$index], 
                                                    $form->_values['custom']['amount_id'][$index] );
            }
            $form->_defaults['amount'] = CRM_Utils_Array::value('default_fee_id',$form->_values['event_page']);
            $form->addGroup( $elements, 'amount', ts('Event Fee(s)'), '<br />' );
            if ( $required ) {
                $form->addRule( 'amount', ts('Fee Level is a required field.'), 'required' );
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
        if ( $this->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_FORM ) {
            foreach ( $this->_fields as $name => $field ) {
                $this->add( $field['htmlType'],
                            $field['name'],
                            $field['title'],
                            $field['attributes'],
                            $field['is_required'] );
            }

            $this->addRule( 'cvv2', ts( 'Please enter a valid value for your card security code. This is usually the last 3-4 digits on the card\'s signature panel.' ), 'integer' );

            $this->addRule( 'credit_card_exp_date', ts('Select a valid date greater than today.'), 'currentDate');
        }            
            
        if ( $this->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_BUTTON ) {
            $this->_expressButtonName = $this->getButtonName( 'next', 'express' );
            $this->add('image',
                       $this->_expressButtonName,
                       $this->_paymentProcessor['url_button'],
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
    static function formRule(&$fields, &$files, $self) {
        $errors = array( );

        if ( $self->_values['event']['is_monetary'] ) {
            // return if this is express mode
            $config =& CRM_Core_Config::singleton( );
            if ( $self->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_BUTTON ) {
                if ( CRM_Utils_Array::value( $self->_expressButtonName . '_x', $fields ) ||
                     CRM_Utils_Array::value( $self->_expressButtonName . '_y', $fields ) ||
                     CRM_Utils_Array::value( $self->_expressButtonName       , $fields ) ) {
                    return empty( $errors ) ? true : $errors;
                }
            }
	    
	    
            foreach ( $self->_fields as $name => $fld ) {
                if ( $fld['is_required'] &&
                     CRM_Utils_System::isNull( CRM_Utils_Array::value( $name, $fields ) ) ) {
                    $errors[$name] = ts( '%1 is a required field.', array( 1 => $fld['title'] ) );
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
        if ($this->_values['event']['is_monetary']) {
            $config =& CRM_Core_Config::singleton( );
            
            // we first reset the confirm page so it accepts new values
            $this->controller->resetPage( 'Confirm' );
            
            // get the submitted form values. 
            $params = $this->controller->exportValues( $this->_name ); 
            
            $params['currencyID']     = $config->defaultCurrency;

            self::processPriceSetAmount( $this, $params );

            $this->set( 'amount', $params['amount'] ); 
            $this->set( 'amount_level', $params['amount_level'] ); 

            // generate and set an invoiceID for this transaction
            $invoiceID = $this->get( 'invoiceID' );
            if ( ! $invoiceID ) {
                $invoiceID = md5(uniqid(rand(), true));
            }
            $this->set( 'invoiceID', $invoiceID );
            
            $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event', $this->_paymentProcessor ); 
            // default mode is direct
            $this->set( 'contributeMode', 'direct' ); 
            
            if ( $this->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_BUTTON ) {
                //get the button name  
                $buttonName = $this->controller->getButtonName( );  
                if ($buttonName == $this->_expressButtonName || 
                    $buttonName == $this->_expressButtonName . '_x' || 
                    $buttonName == $this->_expressButtonName . '_y' ) { 
                    $this->set( 'contributeMode', 'express' ); 
                    
                    $params['cancelURL' ] = CRM_Utils_System::url( 'civicrm/event/register', '_qf_Register_display=1', true, null, false ); 
                    $params['returnURL' ] = CRM_Utils_System::url( 'civicrm/event/register', '_qf_Confirm_display=1&rfp=1', true, null, false ); 
                    $params['invoiceID' ] = $invoiceID;
                    
                    $token = $payment->setExpressCheckout( $params ); 
                    if ( is_a( $token, 'CRM_Core_Error' ) ) { 
                        CRM_Core_Error::displaySessionError( $token ); 
                        CRM_Utils_System::redirect( $params['cancelURL' ] );
                    } 
                    
                    $this->set( 'token', $token ); 
                    
                    $paymentURL =
                        $this->_paymentProcessor['url_site'] .
                        "/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
                    
                    CRM_Utils_System::redirect( $paymentURL ); 
                }
            } else if ( $this->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_NOTIFY ) {
                $this->set( 'contributeMode', 'notify' );
            }
        }
    }//end of function

    static function processPriceSetAmount( &$form, &$params ) {
        if ( ! empty( $params['priceSetId'] ) ) {
            $totalPrice = 0;
            $lineItem = array();

            foreach ($form->_priceSet['fields'] as $fieldId => $field) {
                $fieldName = 'price_' . $fieldId;
                if ( empty( $params[$fieldName] ) ) {
                    // skip if nothing was submitted for this field
                    continue;
                }
                switch ($field['html_type']) {

                case 'Text':
                    $qty = $params[$fieldName];
                    $optionId = key($field['options']);
                    $price = $field['options'][$optionId]['value'];
                    $lineItem[$optionId] = array(
                                                 'price_field_id'   => $field['id'],
                                                 'custom_option_id' => $optionId,
                                                 'label'            => $field['label'],
                                                 'qty'              => $qty,
                                                 'unit_price'       => $price,
                                                 'line_total'       => $qty * $price,
                                                 );
                    $totalPrice += ( $qty * $price );
                    break;

                case 'Radio':
                case 'Select':
                    $optionId = $params[$fieldName];
                    $optionLabel = $field['options'][$optionId]['label'];
                    $price = $field['options'][$optionId]['value'];
                    $lineItem[$optionId] = array(
                                                 'price_field_id'   => $field['id'],
                                                 'custom_option_id' => $optionId,
                                                 'label'            => $field['label'] . ': ' . $optionLabel,
                                                 'qty'              => 1,
                                                 'unit_price'       => $price,
                                                 'line_total'       => $price
                                                 );
                    $totalPrice += $price;
                    break;

                case 'CheckBox':
                    foreach ( $params[$fieldName] as $optionId => $option ) {
                        $optionLabel = $field['options'][$optionId]['label'];
                        $price = $field['options'][$optionId]['value'];
                        $lineItem[$optionId] = array(
                                                     'price_field_id'   => $field['id'],
                                                     'custom_option_id' => $optionId,
                                                     'label'            => $field['label'] . ': ' . $optionLabel,
                                                     'qty'              => 1,
                                                     'unit_price'       => $price,
                                                     'line_total'       => $price
                                                     );
                        $totalPrice += $price;
                    }
                    break;
                    
                }
            }
            $params['amount'] = $totalPrice;
            $params['amount_level'] = $form->_values['event']['title'];
            $form->set( 'lineItem', $lineItem );
        }
        else {
            $params['amount_level'] = $form->_values['custom']['label']
                [array_search( $params['amount'], $form->_values['custom']['amount_id'])];
            $params['amount'] = $form->_values['custom']['value']
                [array_search( $params['amount'], $form->_values['custom']['amount_id'])];
        }
    }

}

?>
