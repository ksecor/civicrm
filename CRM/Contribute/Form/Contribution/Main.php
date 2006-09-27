<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionBase.php';
require_once 'CRM/Contribute/Payment.php';

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
        $this->assign( 'footer_text', $this->_values['footer_text'] );
        
        // to process Custom data that are appended to URL
        require_once 'CRM/Core/BAO/CustomGroup.php';
        CRM_Core_BAO_CustomGroup::extractGetParams( $this, 'Contribution' );
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
            $fields['state_province'] = $fields['country'] = $fields['email'] = 1;
            
            $contact =& CRM_Contact_BAO_Contact::contactDetails( $contactID, $options, $fields );

            foreach ($fields as $name => $dontCare ) {
                if ( $contact->$name ) {
                    if ( substr( $name, 0, 7 ) == 'custom_' ) {
                        $id = substr( $name, 7 );
                        $this->_defaults[$name] = CRM_Core_BAO_CustomField::getDefaultValue( $contact->$name,
                                                                                             $id,
                                                                                             $options );
                    } else {
                        $this->_defaults[$name] = $contact->$name;
                    }
                }
            }
        }

        //set default membership for mership block
        require_once 'CRM/Member/BAO/Membership.php';
        if ( $membershipBlock = CRM_Member_BAO_Membership::getMemershipBlock($this->id) ) {
            $this->_defaults['selectMembership'] = $membershipBlock['membership_type_default'];
        }

        // hack to simplify credit card entry for testing
        /**
        $this->_defaults['credit_card_type']     = 'Visa';
        $this->_defaults['credit_card_number']   = '4807731747657838';
        $this->_defaults['cvv2']                 = '000';
        $this->_defaults['credit_card_exp_date'] = array( 'Y' => '2008', 'M' => '01' );
        **/
        
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
        if ( $this->_values['amount_block_is_active'] ) {
            $this->buildAmount( );
        }
              
        $config =& CRM_Core_Config::singleton( );
        require_once 'CRM/Contribute/BAO/Premium.php';
        CRM_Contribute_BAO_Premium::buildPremiumBlock( $this , $this->_id ,true );
        
        if ( in_array("CiviMember", $config->enableComponents) ) {
            require_once 'CRM/Member/BAO/Membership.php';
            CRM_Member_BAO_Membership::buildMembershipBlock( $this , $this->_id ,true );
        }

        if ( $this->_values['honor_block_is_active'] ) {
            $this->buildHonorBlock( );
        }

        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );
        
        
        // if payment is via a button only, dont display continue
        if ( $config->paymentBillingMode != CRM_Contribute_Payment::BILLING_MODE_BUTTON ) {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Continue >>'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    ) 
                              );
        }

        $this->addFormRule( array( 'CRM_Contribute_Form_Contribution_Main', 'formRule' ), $this );
    }

    /**
     * build the radio/text form elements for the amount field
     *
     * @return void
     * @access private
     */
    function buildAmount( ) {
        $elements = array( );

        // first build the radio boxes
        if ( ! empty( $this->_values['label'] ) ) {
            require_once 'CRM/Utils/Money.php';
            for ( $index = 1; $index <= count( $this->_values['label'] ); $index++ ) {
                $elements[] =& $this->createElement('radio', null, '',
                                                    CRM_Utils_Money::format($this->_values['value'][$index]) . ' ' . $this->_values['label'][$index],
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
        $this->addRule( 'amount', ts('%1 is a required field.', array(1 => ts('Amount'))), 'required' );
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
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildHonorBlock( $id, $name ) {
        $this->assign("honor_block_is_active",true);
        $this->set("honor_block_is_active",true);

        $this->assign("honor_block_title",$this->_values['honor_block_title']);
        $this->assign("honor_block_text",$this->_values['honor_block_text']);

        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Individual');

        // prefix
        $this->addElement('select', 'honor_prefix_id', ts('Prefix'), array('' => ts('- prefix -')) + CRM_Core_PseudoConstant::individualPrefix());
        // first_name
        $this->addElement('text', 'honor_first_name', ts('First Name'), $attributes['first_name'] );
        
        //last_name
        $this->addElement('text', 'honor_last_name', ts('Middle Name'), $attributes['middle_name'] );
        
        //email
        $this->addElement('text', 'honor_email', ts('Email Address'));
        $this->addRule( "honor_email", ts('Email is not valid.'), 'email' );
    }

    /** 
     * Function to add all the credit card fields
     * 
     * @return None 
     * @access public 
     */
    function buildCreditCard( ) {
        $config =& CRM_Core_Config::singleton( );

        if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_FORM) {
            foreach ( $this->_fields as $name => $field ) {
                $this->add( $field['htmlType'],
                            $field['name'],
                            $field['title'],
                            $field['attributes'] );
            }
            
            $this->addRule( 'cvv2', ts( 'Please enter a valid value for your card security code. This is usually the last 3-4 digits on the card\'s signature panel.' ), 'integer' );

            $this->addRule( 'credit_card_exp_date', ts('Select a valid date greater than today.'), 'currentDate');
        }            
            
        if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_BUTTON ) {
            $this->_expressButtonName = $this->getButtonName( 'next', 'express' );
            $this->add('image',
                       $this->_expressButtonName,
                       $config->paymentExpressButton,
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
       
        if( $fields['selectProduct'] && $fields['selectProduct'] != 'no_thanks' && $self->_values['amount_block_is_active'] ) {
            require_once 'CRM/Contribute/DAO/Product.php';
            require_once 'CRM/Utils/Money.php';
            $productDAO =& new CRM_Contribute_DAO_Product();
            $productDAO->id = $fields['selectProduct'];
            $productDAO->find(true);
            $min_amount = $productDAO->min_contribution;
            if ($fields['amount'] == 'amount_other_radio') {
                if ( $fields['amount_other'] < $min_amount ) {
                    $errors['selectProduct'] = ts('The premium you have selected requires a minimum contribution of %1', array(1 => CRM_Utils_Money::format($min_amount)));
                }
            } else {
                if($fields['amount'] < $min_amount) {
                    $errors['selectProduct'] = ts('The premium you have selected requires a minimum contribution of %1', array(1 => CRM_Utils_Money::format($min_amount)));
                }
            }
        }

         if ($self->_values["honor_block_is_active"]) {
            if ( !((  CRM_Utils_Array::value( 'honor_first_name', $fields ) && 
                      CRM_Utils_Array::value( 'honor_last_name' , $fields )) ||
                      CRM_Utils_Array::value( 'honor_email' , $fields ) )) {
                $errors['_qf_default'] = ts('Honor First Name and Last Name OR an email should be set.');
            }
            
        }
        
        if( $fields['selectMembership'] && $fields['selectMembership'] != 'no_thanks') {
            require_once 'CRM/Member/BAO/Membership.php';
            require_once 'CRM/Member/BAO/MembershipType.php';
            $memBlock       = CRM_Member_BAO_Membership::getMemershipBlock( $self->_id );
            $memTypeDetails = CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $fields['selectMembership']);
            if ( $self->_values['amount_block_is_active'] && ! $memBlock['is_separate_payment']) {
                if ($fields['amount'] == 'amount_other_radio') {
                    if ( $fields['amount_other'] < $memTypeDetails['minimum_fee']) {
                         $errors['selectMembership'] = ts(' The Membership you have selected requires a minimum contribution of %1', array(1 => CRM_Utils_Money::format($memTypeDetails['minimum_fee'])));
                    }
                } else if ( $fields['amount'] <  $memTypeDetails['minimum_fee'] ) {
                    $errors['selectMembership'] = ts(' The Membership you have selected requires a minimum contribution of %1', array(1 => CRM_Utils_Money::format($memTypeDetails['minimum_fee'])));
                }
            }
            
        }

        $payment =& CRM_Contribute_Payment::singleton( $self->_mode );
        $error   =  $payment->checkConfig( $self->_mode );
        if ( $error ) {
            $errors['_qf_default'] = $error;
        }

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
        if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_BUTTON ) {
            if ( CRM_Utils_Array::value( $self->_expressButtonName . '_x', $fields ) ||
                 CRM_Utils_Array::value( $self->_expressButtonName . '_y', $fields ) ||
                 CRM_Utils_Array::value( $self->_expressButtonName       , $fields ) ) {
                return $errors;
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

        // generate and set an invoiceID for this transaction
        $invoiceID = $this->get( 'invoiceID' );
        if ( ! $invoiceID ) {
            $invoiceID = md5(uniqid(rand(), true));
        }
        $this->set( 'invoiceID', $invoiceID );

        $payment =& CRM_Contribute_Payment::singleton( $this->_mode ); 
  
        // default mode is direct
        $this->set( 'contributeMode', 'direct' ); 

        if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_BUTTON ) {
            //get the button name  
            $buttonName = $this->controller->getButtonName( );  
            if ($buttonName == $this->_expressButtonName || 
                $buttonName == $this->_expressButtonName . '_x' || 
                $buttonName == $this->_expressButtonName . '_y' ) { 
                $this->set( 'contributeMode', 'express' ); 
                
                $donateURL = CRM_Utils_System::url( 'civicrm/contribute', '_qf_Contribute_display=1' ); 
                $params['cancelURL' ] = CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_Main_display=1', true, null, false ); 
                $params['returnURL' ] = CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_Confirm_display=1&rfp=1', true, null, false ); 
                $params['invoiceID' ] = $invoiceID;
                
                $token = $payment->setExpressCheckout( $params ); 
                if ( is_a( $token, 'CRM_Core_Error' ) ) { 
                    CRM_Core_Error::displaySessionError( $token ); 
                    CRM_Utils_System::redirect( $params['cancelURL' ] );
                } 

                $this->set( 'token', $token ); 

                if ( $this->_mode == 'test' ) {
                    $paymentURL = "https://" . $config->paymentPayPalExpressTestUrl. "/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
                } else {
                    $paymentURL = "https://" . $config->paymentPayPalExpressUrl . "/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
                    // hack to allow us to test without donating, need to comment out below line before release
                    // $paymentURL = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
                }
                CRM_Utils_System::redirect( $paymentURL ); 
            }
        }
    }

}

?>
