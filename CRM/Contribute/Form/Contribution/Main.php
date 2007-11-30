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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionBase.php';
require_once 'CRM/Core/Payment.php';

/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Contribute_Form_Contribution_Main extends CRM_Contribute_Form_ContributionBase 
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        parent::preProcess( );

        $this->assign( 'intro_text' , $this->_values['intro_text'] );
        $this->assign( 'footer_text', $this->_values['footer_text'] );
        
        // to process Custom data that are appended to URL
        require_once 'CRM/Core/BAO/CustomGroup.php';
        CRM_Core_BAO_CustomGroup::extractGetParams( $this, 'Contribution' );
    }

    function setDefaultValues( ) 
    {
        // check if the user is registered and we have a contact ID
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        if ( $contactID ) {
            $options = array( );
            $fields = array( );
            require_once "CRM/Core/BAO/CustomGroup.php";
            $removeCustomFieldTypes = array ('Contribution', 'Membership');
            foreach ( $this->_fields as $name => $dontCare ) {
                //don't set custom data Used for Contribution (CRM-1344)
                if ( substr( $name, 0, 7 ) == 'custom_' ) {  
                    $id = substr( $name, 7 );
                    if ( ! CRM_Core_BAO_CustomGroup::checkCustomField( $id, $removeCustomFieldTypes )) {
                        continue;
                    }
                } else if ( ( substr( $name, 0, 13 ) == 'contribution_' ) || (substr( $name, 0, 11 ) == 'membership_' ) ) { //ignore component fields
                    continue;
                }
                $fields[$name] = 1;
            }

            $names = array("first_name", "middle_name", "last_name");
            foreach ($names as $name) {
                $fields[$name] = 1;
            }
            $fields["state_province-{$this->_bltID}"] = 1;
            $fields["country-{$this->_bltID}"       ] = 1;
            $fields["email-{$this->_bltID}"         ] = 1;
            $fields["email-Primary"                 ] = 1;

            require_once "CRM/Core/BAO/UFGroup.php";
            CRM_Core_BAO_UFGroup::setProfileDefaults( $contactID, $fields, $this->_defaults );

            // use primary email address if billing email address is empty
            if ( empty( $this->_defaults["email-{$this->_bltID}"] ) &&
                 ! empty( $this->_defaults["email-Primary"] ) ) {
                $this->_defaults["email-{$this->_bltID}"] = $this->_defaults["email-Primary"];
            }

            foreach ($names as $name) {
                if ( ! empty( $this->_defaults[$name] ) ) {
                    $this->_defaults["billing_" . $name] = $this->_defaults[$name];
                }
            }
        }

        //set custom field defaults
        require_once "CRM/Core/BAO/CustomField.php";
        foreach ( $this->_fields as $name => $field ) {
            if ( $customFieldID = CRM_Core_BAO_CustomField::getKeyID($name) ) {
                if ( !isset( $this->_defaults[$name] ) ) {
                    CRM_Core_BAO_CustomField::setProfileDefaults( $customFieldID, $name, $this->_defaults,
                                                                  null, CRM_Profile_Form::MODE_REGISTER );
                }
            }
        }

        //set default membership for membershipship block
        require_once 'CRM/Member/BAO/Membership.php';
        if ( $membershipBlock = CRM_Member_BAO_Membership::getMembershipBlock($this->_id) ) {
            $this->_defaults['selectMembership'] = CRM_Utils_Array::value( 'membership_type_default',
                                                                           $membershipBlock );
        }

        // hack to simplify credit card entry for testing
        $this->_defaults['credit_card_type']     = 'Visa';
        $this->_defaults['amount']               = 168;
        $this->_defaults['credit_card_number']   = '4807731747657838';
        $this->_defaults['cvv2']                 = '000';
        $this->_defaults['credit_card_exp_date'] = array( 'Y' => '2008', 'M' => '01' );

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

        $this->applyFilter('__ALL__', 'trim');
        $this->add( 'text', "email-{$this->_bltID}",
                    ts( 'Email Address' ), array( 'size' => 30, 'maxlength' => 60 ), true );

        if ( $this->_values['is_monetary'] ) {
            require_once 'CRM/Core/Payment/Form.php';
            CRM_Core_Payment_Form::buildCreditCard( $this );
        }

        $this->_separateMembershipPayment = false;
        if ( in_array("CiviMember", $config->enableComponents) ) {
            $isTest = 0;
            if ( $this->_action & CRM_Core_Action::PREVIEW ) {
                $isTest = 1;
            }
            
            require_once 'CRM/Member/BAO/Membership.php';
            $this->_separateMembershipPayment = CRM_Member_BAO_Membership::buildMembershipBlock( $this , $this->_id , 
                                                                                                 true, null, false, 
                                                                                                 $isTest );
        }
        $this->set( 'separateMembershipPayment', $this->_separateMembershipPayment );

        if ( $this->_values['amount_block_is_active'] ) {
            $this->buildAmount( $this->_separateMembershipPayment );

            if ( $this->_values['is_monetary'] &&
                 $this->_values['is_recur']    &&
                 $this->_paymentProcessor['is_recur'] ) {
                $this->buildRecur( );
            }
        }
              
        if ( $this->_values['is_monetary'] &&
             $this->_values['is_pay_later'] ) {
            $this->buildPayLater( );
        }

        require_once 'CRM/Contribute/BAO/Premium.php';
        CRM_Contribute_BAO_Premium::buildPremiumBlock( $this , $this->_id ,true );

        if ( $this->_values['honor_block_is_active'] ) {
            $this->buildHonorBlock( );
        }

        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );
       
        //to create an cms user 
        $session =& CRM_Core_Session::singleton( );
        $userID = $session->get( 'userID' );
        if ( ! $userID ) {
            $createCMSUser = false;
            if ( $this->_values['custom_pre_id'] ) {
                $profileID = $this->_values['custom_pre_id'];
                $createCMSUser = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_UFGroup',  $profileID,'is_cms_user' );
            }
            if ( ! $createCMSUser &&
                 $this->_values['custom_post_id'] ) {
                $profileID = $this->_values['custom_post_id'];
                $createCMSUser = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_UFGroup', $profileID, 'is_cms_user' );
            }

            if ( $createCMSUser ) {
                require_once 'CRM/Core/BAO/CMSUser.php';
                CRM_Core_BAO_CMSUser::buildForm( $this, $profileID , true );
            }
        }
        
        // if payment is via a button only, dont display continue
        if ( $this->_paymentProcessor['billing_mode'] != CRM_Core_Payment::BILLING_MODE_BUTTON ||
             ! $this->_values['is_monetary']) {
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
    function buildAmount( $separateMembershipPayment = false ) 
    {
        $elements = array( );

        // first build the radio boxes
        if ( ! empty( $this->_values['label'] ) ) {
            require_once 'CRM/Utils/Money.php';
            for ( $index = 1; $index <= count( $this->_values['label'] ); $index++ ) {
                $elements[] =& $this->createElement('radio', null, '',
                                                    CRM_Utils_Money::format($this->_values['value'][$index]) . ' ' . $this->_values['label'][$index],
                                                    $this->_values['amount_id'][$index],
                                                    array('onclick'=>'clearAmountOther();'));
            }
        }

        if ( isset( $this->_values['default_amount_id'] ) ) {
            $this->_defaults['amount'] = $this->_values['default_amount_id'];
        }
        
        if ( $this->_values['is_allow_other_amount'] ) {
            if ( ! empty($this->_values['label'] ) ) {
                $elements[] =& $this->createElement('radio', null, '',
                                                    ts('Other Amount'), 'amount_other_radio');

                $this->addGroup( $elements, 'amount', ts('Contribution Amount'), '<br />' );

                if ( ! $separateMembershipPayment ) {
                    $this->addRule( 'amount', ts('%1 is a required field.', array(1 => ts('Amount'))), 'required' );
                }
                $this->add('text', 'amount_other', ts( 'Other Amount' ), array( 'size' => 10, 'maxlength' => 10, 'onfocus'=>'useAmountOther();') );
            } else {
                $this->add('text', 'amount_other', ts( 'Contribution Amount' ), array( 'size' => 10, 'maxlength' => 10, 'onfocus'=>'useAmountOther();'),true );             
            }
            $this->assign( 'is_allow_other_amount', true );

            $this->addRule( 'amount_other', ts( 'Please enter a valid amount (numbers and decimal point only).' ), 'money' );
        } else {
            if ( ! empty($this->_values['label'] ) ) {
                $this->addGroup( $elements, 'amount', ts('Contribution Amount'), '<br />' );
            
                if ( ! $separateMembershipPayment ) {
                    $this->addRule( 'amount', ts('%1 is a required field.', array(1 => ts('Amount'))), 'required' );
                }
            }
            $this->assign( 'is_allow_other_amount', false );
        }
    }
    

    /**  
     * Function to add the honor block
     *  
     * @return None  
     * @access public  
     */ 
    function buildHonorBlock(  ) {
        $this->assign("honor_block_is_active",true);
        $this->set("honor_block_is_active",true);

        $this->assign("honor_block_title",$this->_values['honor_block_title']);
        $this->assign("honor_block_text",$this->_values['honor_block_text']);

        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact');

        // radio button for Honor Type
        $honorOptions = array( );
        $honor =CRM_Core_PseudoConstant::honor( ); 
        foreach ($honor as $key => $var) {
            $honorTypes[$key] = HTML_QuickForm::createElement('radio', null, null, $var, $key);
        }
        $this->addGroup($honorTypes, 'honor_type_id', null);
        
        // prefix
        $this->addElement('select', 'honor_prefix_id', ts('Honoree Prefix'), array('' => ts('- prefix -')) + CRM_Core_PseudoConstant::individualPrefix());
        // first_name
        $this->addElement('text', 'honor_first_name', ts('Honoree First Name'), $attributes['first_name'] );
        
        //last_name
        $this->addElement('text', 'honor_last_name', ts('Honoree Last Name'), $attributes['last_name'] );
        
        //email
        $this->addElement('text', 'honor_email', ts('Honoree Email Address'));
        $this->addRule( "honor_email", ts('Honoree Email is not valid.'), 'email' );
    }

    /**
     * build elements to enable pay later functionality
     *
     * @access public
     */
    function buildPayLater( ) {
        $this->addElement( 'checkbox',
                           'is_pay_later',
                           $this->_values['pay_later_text'] );
    }

    /** 
     * build elements to collect information for recurring contributions
     *
     * @access public
     */
    function buildRecur( ) {
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_ContributionRecur' );

        $elements = array( );
      	$elements[] =& $this->createElement('radio', null, '', ts( 'I want to make a one-time contribution.' ), 0 );
      	$elements[] =& $this->createElement('radio', null, '', ts( 'I want to contribute this amount' ), 1 );
        $this->addGroup( $elements, 'is_recur', null, '<br />' );
        $this->_defaults['is_recur'] = 0;
        
        $this->add( 'text', 'frequency_interval', ts( 'Every' ),
                    $attributes['frequency_interval'] );
        $this->addRule( 'frequency_interval', ts( 'Frequency must be a whole number (EXAMPLE: Every 3 months).' ), 'integer' );

        $units = array( 0       => ts( ' -period-' ),
                        'day'   => ts( 'day(s)'      ),
                        'week'  => ts( 'week(s)'     ),
                        'month' => ts( 'month(s)'    ),
                        'year'  => ts( 'year(s)'     ) );
        $this->add( 'select', 'frequency_unit', null, $units );

        $this->add( 'text', 'installments', ts( 'installments' ),
                    $attributes['installments'] );
        $this->addRule( 'installments', ts( 'Number of installments must be a whole number.' ), 'integer' );

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
    static function formRule( &$fields, &$files, &$self ) 
    { 
        $errors = array( ); 

        $amount = self::computeAmount( $fields, $self );

        if ( isset( $fields['selectProduct'] ) &&
             $fields['selectProduct'] != 'no_thanks' &&
             $self->_values['amount_block_is_active'] ) {
            require_once 'CRM/Contribute/DAO/Product.php';
            require_once 'CRM/Utils/Money.php';
            $productDAO =& new CRM_Contribute_DAO_Product();
            $productDAO->id = $fields['selectProduct'];
            $productDAO->find(true);
            $min_amount = $productDAO->min_contribution;
            if ( $amount < $min_amount ) {
                $errors['selectProduct'] = ts('The premium you have selected requires a minimum contribution of %1', array(1 => CRM_Utils_Money::format($min_amount)));
            }
        }

        if ( $self->_values["honor_block_is_active"] ) {
            // make sure there is a first name and last name if email is not there
            if ( ! CRM_Utils_Array::value( 'honor_email' , $fields ) ) {
                if ( !  CRM_Utils_Array::value( 'honor_first_name', $fields ) &&
                     CRM_Utils_Array::value( 'honor_last_name' , $fields ) ) {
                    $errors['_qf_default'] = ts('In Honor Of - First Name and Last Name, OR an Email Address is required.');
                } else if ( CRM_Utils_Array::value( 'honor_first_name', $fields ) &&
                            ! CRM_Utils_Array::value( 'honor_last_name' , $fields ) ) {
                    $errors['_qf_default'] = ts('In Honor Of - First Name and Last Name, OR an Email Address is required.');
                }
            }
        }

        if ( isset( $fields['is_recur'] ) && $fields['is_recur'] ) {
            if ( $fields['frequency_interval'] <= 0 ) {
                $errors['frequency_interval'] = ts('Please enter a number for how often you want to make this recurring contribution (EXAMPLE: Every 3 months).'); 
            }
            if ( $fields['frequency_unit'] == '0' ) {
                $errors['frequency_unit'] = ts('Please select a period (e.g. months, years ...) for how often you want to make this recurring contribution (EXAMPLE: Every 3 MONTHS).'); 
            }
        }

        if ( CRM_Utils_Array::value('selectMembership', $fields) && 
             $fields['selectMembership'] != 'no_thanks') {
            require_once 'CRM/Member/BAO/Membership.php';
            require_once 'CRM/Member/BAO/MembershipType.php';
            $memBlock       = CRM_Member_BAO_Membership::getMembershipBlock( $self->_id );
            $memTypeDetails = CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $fields['selectMembership']);
            if ( $self->_values['amount_block_is_active'] &&
                 CRM_Utils_Array::value( 'is_separate_payment', $memBlock ) ) {
                require_once 'CRM/Utils/Money.php';
                if ( $amount < CRM_Utils_Array::value('minimum_fee',$memTypeDetails) ) {
                    $errors['selectMembership'] =
                        ts(' The Membership you have selected requires a minimum contribution of %1',
                           array( 1 => CRM_Utils_Money::format($memTypeDetails['minimum_fee'] ) ) );
                }
            } else {
                // we dont have an amount, so lets get an amount for cc checks
                $amount = $memTypeDetails['minimum_fee'];
            }
        }

        if ( $self->_values['is_monetary'] ) {
            if ( ( CRM_Utils_Array::value('amount',$fields) == 'amount_other_radio' )
                 || $fields['amount_other'] ) {
                if ( !$amount ) {
                    $errors['amount_other'] = ts('Amount is required field.');
                }
                
                if ( CRM_Utils_Array::value('min_amount',$self->_values) ) {
                    $min = $self->_values['min_amount'];
                    if ( $fields['amount_other'] < $min ) {
                        $errors['amount_other'] = ts( 'This amount has to be greater than %1', 
                                                      array ( 1 => $min ) );
                    }
                }
                    
                if ( CRM_Utils_Array::value('max_amount',$self->_values) > 0 ) {
                    $max = $self->_values['max_amount'];
                    if ( $fields['amount_other'] > $max ) {
                        $errors['amount_other'] = ts( 'This amount has to be less than %1', 
                                                      array ( 1 => $max ) );
                    }
                }
            }
        }

        // return if this is express mode
        $config =& CRM_Core_Config::singleton( );
        if ( $self->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_BUTTON ) {
            if ( CRM_Utils_Array::value( $self->_expressButtonName . '_x', $fields ) ||
                 CRM_Utils_Array::value( $self->_expressButtonName . '_y', $fields ) ||
                 CRM_Utils_Array::value( $self->_expressButtonName       , $fields ) ) {
                return $errors;
            }
        }

        // also return if paylater mode
        if ( CRM_Utils_Array::value( 'is_pay_later', $fields ) ) {
            return empty( $errors ) ? true : $errors;
        }
        
        // if the user has chosen a free membership or the amount is less than zero
        // i.e. we skip calling the payment processor and hence dont need credit card
        // info
        if ( (float ) $amount <= 0.0 ) {
            return $errors;
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

    public function computeAmount( &$params, &$form ) {

        $amount = null;

        // first clean up the other amount field if present
        if ( isset( $params['amount_other'] ) ) {
            $params['amount_other'] = CRM_Utils_Rule::cleanMoney( $params['amount_other'] );
        }
        
        if ( CRM_Utils_Array::value('amount',$params) == 'amount_other_radio' || ! empty( $params['amount_other'] ) ) {
            $amount = $params['amount_other'];
        } else {
            if ( CRM_Utils_Array::value('amount_id',$form->_values) ) {
                $amountID = array_search( CRM_Utils_Array::value('amount',$params),
                                          CRM_Utils_Array::value('amount_id',$form->_values) );
            }
            
            if ( ! empty( $form->_values['value'] ) &&
                 $amountID ) {
                $params['amount_level'] =
                    $form->_values['label'][$amountID];
                $amount = 
                    $form->_values['value'][$amountID];
            }
        }
        return $amount;
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
    
        // we first reset the confirm page so it accepts new values
        $this->controller->resetPage( 'Confirm' );

        // get the submitted form values. 
        $params = $this->controller->exportValues( $this->_name ); 

        $params['currencyID']     = $config->defaultCurrency;

        $params['amount'] = self::computeAmount( $params, $this );

        if ( ! $params['amount'] && $params['selectMembership'] ) {
            $memFee = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', $params['selectMembership'], 'minimum_fee' );
            if ( $memFee && ! $this->_separateMembershipPayment ) {
                $params['amount'] = $memFee;
            } else {
                $params['amount'] = 0;
            }
        }
        
        if ( ! isset( $params['amount_other'] ) ) {
            $this->set( 'amount_level', $params['amount_level'] ); 
        }

        $this->set( 'amount', $params['amount'] ); 

        // generate and set an invoiceID for this transaction
        $invoiceID = $this->get( 'invoiceID' );
        if ( ! $invoiceID ) {
            $invoiceID = md5(uniqid(rand(), true));
        }
        $this->set( 'invoiceID', $invoiceID );

        // required only if is_monetary and valid postive amount 
        if ( $this->_values['is_monetary'] && (float ) $params['amount'] > 0.0 ) {
            
            $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Contribute', $this->_paymentProcessor ); 

            // default mode is direct
            $this->set( 'contributeMode', 'direct' ); 

            if ( $this->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_BUTTON ) {
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
                        
                        $paymentURL = $this->_paymentProcessor['url_site'] . "/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
                        CRM_Utils_System::redirect( $paymentURL ); 
                    }
            } else if ( $this->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_NOTIFY ) {
                $this->set( 'contributeMode', 'notify' );
            }
        }      
    }
    
}

?>
