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

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Contribute_Form_ContributionBase extends CRM_Core_Form
{
    
    /**
     * the id of the contribution page that we are proceessing
     *
     * @var int
     * @public
     */
    public $_id;

    /**
     * the mode that we are in
     * 
     * @var string
     * @protect
     */
    public $_mode;

    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

    /**
     * the paymentProcessor attributes for this page
     *
     * @var array
     * @protected
     */
    public $_paymentProcessor;

    /**
     * the default values for the form
     *
     * @var array
     * @protected
     */
    protected $_defaults;

    /**
     * The params submitted by the form and computed by the app
     *
     * @var array
     * @public
     */
    public $_params;

    /** 
     * The fields involved in this contribution page
     * 
     * @var array 
     * @public
     */ 
    public $_fields;

    /**
     * The billing location id for this contribiution page
     *
     * @var int
     * @protected
     */
    public $_bltID;

    /**
     * Cache the amount to make things easier
     *
     * @var float
     * @public
     */
    public $_amount;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );

        // current contribution page id 
        $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                  $this );
        if ( ! $this->_id ) {
            $pastContributionID = $session->get( 'pastContributionID' );
            if ( ! $pastContributionID ) {
                CRM_Core_Error::fatal( ts( 'We could not find contribution details for your request. Please try your request again.' ) );
            } else {
                CRM_Core_Error::fatal( ts( 'This contribution has already been submitted. Click <a href=\'%1\'>here</a> if you want to make another contribution.', array( 1 => CRM_Utils_System::url( 'civicrm/contribute/transact', 'reset=1&id=' . $pastContributionID ) ) ) );
            }
        } else {
            $session->set( 'pastContributionID', $this->_id );
        }

        // we do not want to display recently viewed items, so turn off
        $this->assign       ( 'displayRecent' , false );

        // action
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'add' );
        $this->assign( 'action'  , $this->_action   ); 

        // current mode
        $this->_mode = ( $this->_action == 1024 ) ? 'test' : 'live';

        $this->_values           = $this->get( 'values' );
        $this->_fields           = $this->get( 'fields' );
        $this->_bltID            = $this->get( 'bltID'  );
        $this->_paymentProcessor = $this->get( 'paymentProcessor' );

        if ( ! $this->_values ) {
            // get all the values from the dao object
            $this->_values = array( );
            $this->_fields = array( );

            require_once 'CRM/Contribute/BAO/ContributionPage.php';
            CRM_Contribute_BAO_ContributionPage::setValues( $this->_id, $this->_values );

            // check if form is active
            if ( ! $this->_values['is_active'] ) {
                // form is inactive, die a fatal death
                CRM_Core_Error::fatal( ts( 'The page you requested is currently unavailable.' ) );
            }

            // also check for billing informatin
            // get the billing location type
            $locationTypes =& CRM_Core_PseudoConstant::locationType( );
            $this->_bltID = array_search( 'Billing',  $locationTypes );
            if ( ! $this->_bltID ) {
                CRM_Core_Error::fatal( ts( 'Please set a location type of %1', array( 1 => 'Billing' ) ) );
            }
            $this->set   ( 'bltID', $this->_bltID );

            // check for is_monetary status
            $isMonetary = CRM_Utils_Array::value( 'is_monetary', $this->_values );


            if ( $isMonetary ) {
                $ppID = CRM_Utils_Array::value( 'payment_processor_id', $this->_values );
                if ( ! $ppID ) {
                    CRM_Core_Error::fatal( ts( 'A payment processor must be selected for this contribution page (contact the site administrator for assistance).' ) );
                }
                
                
                $ppID = CRM_Utils_Array::value( 'payment_processor_id', $this->_values );
                
                if ( !$ppID ) {
                    CRM_Core_Error::fatal( ts( 'Please set a payment processor in your contribution page' ) );
                }
                
                require_once 'CRM/Core/BAO/PaymentProcessor.php';
                $this->_paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $ppID,
                                                                                      $this->_mode );

                // ensure that processor has a valid config
                $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Contribute', $this->_paymentProcessor );
                $error = $payment->checkConfig( );
                if ( ! empty( $error ) ) {
                    CRM_Core_Error::fatal( $error );
                }

                $this->set( 'paymentProcessor', $this->_paymentProcessor );
            }                
            
            // this avoids getting E_NOTICE errors in php
            $setNullFields = array( 'amount_block_is_active',
                                    'honor_block_is_active' ,
                                    'is_allow_other_amount' ,
                                    'footer_text' );
            foreach ( $setNullFields as $f ) {
                if ( ! isset( $this->_values[$f]  ) ) {
                    $this->_values[$f] = null;
                }
            }
            
            //check if Membership Block is enabled, if Membership Fields are included in profile
            //get membership section for this contribution page
            require_once 'CRM/Member/DAO/MembershipBlock.php';
            $dao =& new CRM_Member_DAO_MembershipBlock();
            $dao->entity_table = 'civicrm_contribution_page';
            $dao->entity_id    = $this->_id; 
            
            $membershipEnable = false;
            
            if ( $dao->find(true) && $dao->is_active ) {
                $membershipEnable = true;
            }
            
            require_once "CRM/Core/BAO/UFField.php";
            if ( $this->_values['custom_pre_id'] ) {
                $preProfileType  = CRM_Core_BAO_UFField::getProfileType( $this->_values['custom_pre_id'] );
            }
            
            if ( $this->_values['custom_post_id'] ) {
                $postProfileType = CRM_Core_BAO_UFField::getProfileType( $this->_values['custom_post_id'] );
            }
            
            if ( ( ( isset($postProfileType) && $postProfileType == 'Membership' ) || ( isset($preProfileType) && $preProfileType == 'Membership' ) ) && !$membershipEnable ) {
                CRM_Core_Error::fatal( ts('This page includes a Profile with Membership fields - but the Membership Block is NOT enabled. Please notify the site administrator.') );
            }

            $this->set( 'values', $this->_values );
            $this->set( 'fields', $this->_fields );

        }
        // we do this outside of the above conditional to avoid 
        // saving the country/state list in the session (which could be huge)
        if ( ( $this->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_FORM ) &&
             CRM_Utils_Array::value('is_monetary', $this->_values) ) {
            require_once 'CRM/Core/Payment/Form.php';
            CRM_Core_Payment_Form::setCreditCardFields( $this );
        }

        $this->assign_by_ref( 'paymentProcessor', $this->_paymentProcessor );

        // check if this is a paypal auto return and redirect accordingly
        if ( CRM_Core_Payment::paypalRedirect( $this->_paymentProcessor ) ) {
            $url = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                          "_qf_ThankYou_display=1&qfKey={$this->controller->_key}" );
            CRM_Utils_System::redirect( $url );
        }
        
        // make sure we have a valid payment class, else abort
        if ( CRM_Utils_Array::value('is_monetary',$this->_values) &&
             ! $this->_paymentProcessor['class_name'] ) {
            CRM_Core_Error::fatal( ts( 'Payment processor is not set for this page' ) );
        }

        // check if one of the (amount , membership)  bloks is active or not
        require_once 'CRM/Member/BAO/Membership.php';
        $membership = CRM_Member_BAO_Membership::getMembershipBlock( $this->_id );
        if ( ! $this->_values['amount_block_is_active'] &&
             ! $membership['is_active'] ) {
            CRM_Core_Error::fatal( ts( 'The requested online contribution page is missing a required Contribution Amount section or Membership section. Please check with the site administrator for assistance.' ) );
        }

        if ( $this->_values['amount_block_is_active'] ) {
            $this->set('amount_block_is_active',$this->_values['amount_block_is_active' ]);
        }

        if ( ! empty($membership) &&
             $membership["is_separate_payment"] &&
             $this->_paymentProcessor['payment_processor_type'] == "PayPal_Standard" ) {
            CRM_Core_Error::fatal( ts( 'This contribution page is configured to support separate contribution and membership payments. The PayPal Website Payments Standard plugin does not currently support multiple simultaneous payments. Please contact the site administrator and notify them of this error' ) );
        }

        $this->_contributeMode = $this->get( 'contributeMode' );
        $this->assign( 'contributeMode', $this->_contributeMode ); 

        //assigning is_monetary and is_email_receipt to template
        $this->assign( 'is_monetary', $this->_values['is_monetary'] );
        $this->assign( 'is_email_receipt', $this->_values['is_email_receipt'] );
        $this->assign( 'bltID', $this->_bltID );

        //assign cancelSubscription URL to templates
        $this->assign( 'cancelSubscriptionUrl',
                       self::cancelSubscriptionURL( $this->_paymentProcessor, $this->_mode ) );
        
        // assigning title to template in case someone wants to use it, also setting CMS page title
        $this->assign( 'title', $this->_values['title'] );
        CRM_Utils_System::setTitle($this->_values['title']);  
        
        $this->_defaults = array( );
        
        $this->_amount   = $this->get( 'amount' );
    }

    static function cancelSubscriptionURL( &$paymentProcessor, $mode = null ) 
    {
        $cancelSubscriptionURL = null;
        if ( $paymentProcessor['payment_processor_type'] == 'PayPal_Standard' ) {
            $cancelSubscriptionURL = "{$paymentProcessor['url_site']}cgi-bin/webscr?cmd=_subscr-find&alias=" .
                urlencode( $paymentProcessor['user_name'] );
        } else if ( $paymentProcessor['payment_processor_type'] == 'AuthNet_AIM' ) {
            if ( $mode == 'test' ) {
                $cancelSubscriptionURL = "https://test.authorize.net";
            } else {
                $cancelSubscriptionURL = "https://authorize.net";
            }
        }
        return $cancelSubscriptionURL;
    }
    
    /** 
     * set the default values
     *                                                           
     * @return void 
     * @access public 
     */ 
    function setDefaultValues( ) {
        return $this->_defaults;
    }

    /** 
     * assign the minimal set of variables to the template
     *                                                           
     * @return void 
     * @access public 
     */ 
    function assignToTemplate( ) {
        $name = CRM_Utils_Array::value( 'billing_first_name', $this->_params );
        if ( CRM_Utils_Array::value( 'billing_middle_name', $this->_params ) ) {
            $name .= " {$this->_params['billing_middle_name']}";
        }
        $name .= ' ' . CRM_Utils_Array::value( 'billing_last_name', $this->_params );
        $name = trim( $name );
        $this->assign( 'name', $name );
        $this->set( 'name', $name );

        $vars = array( 'amount', 'currencyID',
                       'credit_card_type', 'trxn_id', 'amount_level' );
 
        $config =& CRM_Core_Config::singleton( );
        if ( isset($this->_values['is_recur']) && 
             $this->_paymentProcessor['is_recur'] ) {
            $this->assign( 'is_recur_enabled', 1 );
            $vars = array_merge( $vars, array( 'is_recur', 'frequency_interval', 'frequency_unit',
                                               'installments' ) );
        }

        if( isset($this->_params['amount_other']) || isset($this->_params['selectMembership']) ) {
            $this->_params['amount_level'] = '';
        }
       
        foreach ( $vars as $v ) {
            if ( CRM_Utils_Array::value( $v, $this->_params ) ) {
                $this->assign( $v, $this->_params[$v] );
            }
        }


        // assign the address formatted up for display
        $addressParts  = array( "street_address-{$this->_bltID}",
                                "city-{$this->_bltID}",
                                "postal_code-{$this->_bltID}",
                                "state_province-{$this->_bltID}",
                                "country-{$this->_bltID}");
        $addressFields = array();

        foreach ($addressParts as $part) {
            list( $n, $id ) = explode( '-', $part );
            $addressFields[$n] = CRM_Utils_Array::value( $part, $this->_params );
        }
        require_once 'CRM/Utils/Address.php';
        $this->assign('address', CRM_Utils_Address::format($addressFields));

        if ( $this->_contributeMode == 'direct' && $this->_amount > 0.0 ) {
            $date = CRM_Utils_Date::format( $this->_params['credit_card_exp_date'] );
            $date = CRM_Utils_Date::mysqlToIso( $date );
            $this->assign( 'credit_card_exp_date', $date );
            $this->assign( 'credit_card_number',
                           CRM_Utils_System::mungeCreditCard( $this->_params['credit_card_number'] ) );
        }

        $this->assign( 'email',
                       $this->controller->exportValue( 'Main', "email-{$this->_bltID}" ) );
        
        // also assign the receipt_text
        if ( isset( $this->_values['receipt_text'] ) ) {
            $this->assign( 'receipt_text', $this->_values['receipt_text'] );
        }

        // assign pay later stuff
        $this->_params['is_pay_later'] = CRM_Utils_Array::value( 'is_pay_later', $this->_params, false );
        $this->assign( 'is_pay_later', $this->_params['is_pay_later'] );
        if ( $this->_params['is_pay_later'] ) {
            $this->assign( 'pay_later_text'   , $this->_values['pay_later_text']    );
            $this->assign( 'pay_later_receipt', $this->_values['pay_later_receipt'] );
        }
    }

    /**  
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustom( $id, $name ) 
    {
        if ( $id ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            require_once 'CRM/Profile/Form.php';
            $session =& CRM_Core_Session::singleton( );
            $contactID = $session->get( 'userID' );
            
            // we don't allow conflicting fields to be
            // configured via profile - CRM 2100
            $fieldsToIgnore = array( 'receive_date'           => 1,
                                     'trxn_id'                => 1,
                                     'invoice_id'             => 1,
                                     'net_amount'             => 1,
                                     'fee_amount'             => 1,
                                     'non_deductible_amount'  => 1,
                                     'total_amount'           => 1,
                                     'amount_level'           => 1,
                                     'contribution_status_id' => 1
                                     );

            $fields = null;
            if ( $contactID ) {
                require_once "CRM/Core/BAO/UFGroup.php";
                if ( CRM_Core_BAO_UFGroup::filterUFGroups($id, $contactID)  ) {
                    $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD );
                }
            } else {
                $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD ); 
            }

            if ( $fields ) {
                // unset any email-* fields since we already collect it, CRM-2888
                foreach ( array_keys( $fields ) as $fieldName ) {
                    if ( substr( $fieldName, 0, 6 ) == 'email-' ) {
                        unset( $fields[$fieldName] );
                    }
                }
                
                if (array_intersect_key($fields, $fieldsToIgnore)) {
                    $fields = array_diff_key( $fields, $fieldsToIgnore );
                    CRM_Core_Session::setStatus("Some of the profile fields cannot be configured for this page.");
                }
                
                $this->assign( $name, $fields );
                
                foreach($fields as $key => $field) {
                    CRM_Core_BAO_UFGroup::buildProfile($this, $field,CRM_Profile_Form::MODE_CREATE);
                    $this->_fields[$key] = $field;
                }
            }
        }
    }
    
}

?>
