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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
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
     * @protected
     */
    protected $_id;

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
     * @protected
     */
    protected $_params;

    /** 
     * The fields involved in this contribution page
     * 
     * @var array 
     * @protected 
     */ 
    protected $_fields;

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

        // make sure we have a valid payment class, else abort
        if ( ! $config->paymentClass ) {
            CRM_Core_Error::fatal( ts( 'CIVICRM_CONTRIBUTE_PAYMENT_PROCESSOR is not set.' ) );
        }

        // current contribution page id 
        $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                  $this );
        if ( ! $this->_id ) {
            $pastContributionID = $session->get( 'pastContributionId' );
            if ( ! $pastContributionID ) {
                CRM_Core_Error::fatal( ts( 'We could not find contribution details for your request. Please try your request again.' ) );
            } else {
                CRM_Core_Error::fatal( ts( 'This contribution has already been submitted. Click <a href="%1">here</a> if you want to make another contribution.', array( 1 => CRM_Utils_System::url( 'civicrm/contribute/transact', 'reset=1&id=' . $pastContributionID ) ) ) );
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

        $this->_values = $this->get( 'values' );
        $this->_fields = $this->get( 'fields' );

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

            $session->set( 'pastContributionThermometer', $this->_values['is_thermometer'] );

            if ( ($config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_FORM) && $this->_values['is_monetary'] ) {
                $this->setCreditCardFields( );
            }

            $this->set( 'values', $this->_values );
            $this->set( 'fields', $this->_fields );
        }

        // check if one of the (amount , membership)  bloks is active or not
        require_once 'CRM/Member/BAO/Membership.php';
        $membership = CRM_Member_BAO_Membership::getMembershipBlock( $this->_id );
        if ( ! $this->_values['amount_block_is_active'] && ! $membership['is_active'] ) {
            CRM_Core_Error::fatal( ts( 'The requested online contribution page is missing a required Contribution Amount section or Membership section. Please check with the site administrator for assistance.' ) );
        }
        if ( $this->_values['amount_block_is_active'] ) {
            $this->set('amount_block_is_active',$this->_values['amount_block_is_active' ]);
        }
        if ( !empty($membership) && $membership["is_separate_payment"] && $config->paymentProcessor == "PayPal_Standard" ) {
            CRM_Core_Error::fatal( ts( 'This contribution page is configured to support separate contribution
and membership payments. The PayPal Website Payments Standard plugin
does not currently support multiple simultaneous payments. Please
contact the site administrator and notify them of this error' ) );
        }
        
        $this->_contributeMode = $this->get( 'contributeMode' );
        $this->assign( 'contributeMode', $this->_contributeMode ); 

        //assigning is_monetary and is_email_receipt to template
        $this->assign( 'is_monetary', $this->_values['is_monetary'] );
        $this->assign( 'is_email_receipt', $this->_values['is_email_receipt'] );


        //assign cancelSubscription URL to templates
        $this->assign( 'cancelSubscriptionUrl',
                       self::cancelSubscriptionURL( $config, $this->_mode ) );
        
        // assigning title to template in case someone wants to use it, also setting CMS page title
        $this->assign( 'title', $this->_values['title'] );
        CRM_Utils_System::setTitle($this->_values['title']);  

        $this->_defaults = array( );

    }

    static function cancelSubscriptionURL( &$config, $mode ) {
        $cancelSubscriptionURL = null;
        if ( $config->paymentProcessor == 'PayPal_Standard' ) {
            if ( $mode == 'live' ) {
                $cancelSubscriptionURL = "https://{$config->paymentPayPalExpressUrl}/cgi-bin/webscr?cmd=_subscr-find&alias=" . urlencode($config->paymentUsername['live']);
            } else {
                $cancelSubscriptionURL = "https://{$config->paymentPayPalExpressTestUrl}/cgi-bin/webscr?cmd=_subscr-find&alias=" . urlencode($config->paymentUsername['test']);
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
        $name = $this->_params['first_name'];
        if ( CRM_Utils_Array::value( 'middle_name', $this->_params ) ) {
            $name .= " {$this->_params['middle_name']}";
        }
        $name .= " {$this->_params['last_name']}";
        $this->assign( 'name', $name );
        $this->set( 'name', $name );

        $vars = array( 'amount', 'currencyID',
                       'credit_card_type', 'trxn_id' );
 
        $config =& CRM_Core_Config::singleton( );
        if ( $this->_values['is_recur'] && 
             $config->enableRecurContribution ) {
            $this->assign( 'is_recur_enabled', 1 );
            $vars = array_merge( $vars, array( 'is_recur', 'frequency_interval', 'frequency_unit',
                                               'installments' ) );
        }

        foreach ( $vars as $v ) {
            if ( CRM_Utils_Array::value( $v, $this->_params ) ) {
                $this->assign( $v, $this->_params[$v] );
            }
        }

        // assign the address formatted up for display
        $addressParts  = array('street_address', 'city', 'postal_code', 'state_province', 'country');
        $addressFields = array();
        foreach ($addressParts as $part) {
            $addressFields[$part] = $this->_params[$part];
        }
        require_once 'CRM/Utils/Address.php';
        $this->assign('address', CRM_Utils_Address::format($addressFields));

        if ( $this->_contributeMode == 'direct' ) {
            $date = CRM_Utils_Date::format( $this->_params['credit_card_exp_date'] );
            $date = CRM_Utils_Date::mysqlToIso( $date );
            $this->assign( 'credit_card_exp_date', $date );
            $this->assign( 'credit_card_number',
                           CRM_Utils_System::mungeCreditCard( $this->_params['credit_card_number'] ) );
        }

        $this->assign( 'email',
                       $this->controller->exportValue( 'Main', 'email' ) );

        // also assign the receipt_text
        $this->assign( 'receipt_text', $this->_values['receipt_text'] );
    }

    /** 
     * create all fields needed for a credit card transaction
     *                                                           
     * @return void 
     * @access public 
     */ 
    function setCreditCardFields( ) {
        
        $this->_fields['first_name'] = array( 'htmlType'   => 'text', 
                                              'name'       => 'first_name', 
                                              'title'      => ts('First Name'), 
                                              'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                              'is_required'=> true );
                                         
        $this->_fields['middle_name'] = array( 'htmlType'   => 'text', 
                                               'name'       => 'middle_name', 
                                               'title'      => ts('Middle Name'), 
                                               'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                               'is_required'=> false );
        
        $this->_fields['last_name'] = array( 'htmlType'   => 'text', 
                                             'name'       => 'last_name', 
                                             'title'      => ts('Last Name'), 
                                             'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                             'is_required'=> true );
                                         
        $this->_fields['street_address'] = array( 'htmlType'   => 'text', 
                                                  'name'       => 'street_address', 
                                                  'title'      => ts('Street Address'), 
                                                  'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                                  'is_required'=> true );
                                         
        $this->_fields['city'] = array( 'htmlType'   => 'text', 
                                        'name'       => 'city', 
                                        'title'      => ts('City'), 
                                        'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                        'is_required'=> true );
                                         
        $this->_fields['state_province_id'] = array( 'htmlType'   => 'select', 
                                                     'name'       => 'state_province_id', 
                                                     'title'      => ts('State / Province'), 
                                                     'attributes' => array( '' => ts( '- select -' ) ) +
                                                                     CRM_Core_PseudoConstant::stateProvince( ),
                                                     'is_required'=> true );
                                         
        $this->_fields['postal_code'] = array( 'htmlType'   => 'text', 
                                               'name'       => 'postal_code', 
                                               'title'      => ts('Postal Code'), 
                                               'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                               'is_required'=> true );
                                         
        $this->_fields['country_id'] = array( 'htmlType'   => 'select', 
                                              'name'       => 'country_id', 
                                              'title'      => ts('Country'), 
                                              'attributes' => array( '' => ts( '- select -' ) ) + 
                                                              CRM_Core_PseudoConstant::country( ),
                                              'is_required'=> true );
                                         
        $this->_fields['credit_card_number'] = array( 'htmlType'   => 'text', 
                                                      'name'       => 'credit_card_number', 
                                                      'title'      => ts('Card Number'), 
                                                      'attributes' => array( 'size' => 20, 'maxlength' => 20 ), 
                                                      'is_required'=> true );
                                         
        $this->_fields['cvv2'] = array( 'htmlType'   => 'text', 
                                        'name'       => 'cvv2', 
                                        'title'      => ts('Security Code'), 
                                        'attributes' => array( 'size' => 5, 'maxlength' => 10 ), 
                                        'is_required'=> true );
                                         
        $this->_fields['credit_card_exp_date'] = array( 'htmlType'   => 'date', 
                                                        'name'       => 'credit_card_exp_date', 
                                                        'title'      => ts('Expiration Date'), 
                                                        'attributes' => CRM_Core_SelectValues::date( 'creditCard' ),
                                                        'is_required'=> true );

        require_once 'CRM/Contribute/PseudoConstant.php';
        $creditCardType = array( ''           => '- select -') + CRM_Contribute_PseudoConstant::creditCard( );
        $this->_fields['credit_card_type'] = array( 'htmlType'   => 'select', 
                                                    'name'       => 'credit_card_type', 
                                                    'title'      => ts('Card Type'), 
                                                    'attributes' => $creditCardType,
                                                    'is_required'=> true );
    }

}

?>
