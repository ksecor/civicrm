<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Contribute_Form_Contribution_Confirm extends CRM_Contribute_Form_ContributionBase 
{

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $config =& CRM_Core_Config::singleton( );
        parent::preProcess( );

        if ( $this->_contributeMode == 'express' ) {
            // rfp == redirect from paypal
            $rfp = CRM_Utils_Request::retrieve( 'rfp', 'Boolean',
                                                CRM_Core_DAO::$_nullObject, false, null, 'GET' );
            if ( $rfp ) {
                require_once 'CRM/Core/Payment.php'; 
                $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Contribute', $this->_paymentProcessor );
                $expressParams = $payment->getExpressCheckoutDetails( $this->get( 'token' ) );

                $this->_params['payer'       ] = $expressParams['payer'       ];
                $this->_params['payer_id'    ] = $expressParams['payer_id'    ];
                $this->_params['payer_status'] = $expressParams['payer_status'];

                require_once 'CRM/Core/Payment/Form.php';
                CRM_Core_Payment_Form::mapParams( $this->_bltID, $expressParams, $this->_params, false );

                // fix state and country id if present
                if ( ! empty( $this->_params["state_province_id-{$this->_bltID}"] ) && $this->_params["state_province_id-{$this->_bltID}"] ) {
                    $this->_params["state_province-{$this->_bltID}"] =
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] ); 
                }
                if ( ! empty( $this->_params["country_id-{$this->_bltID}"] ) && $this->_params["country_id-{$this->_bltID}"] ) {
                    $this->_params["country-{$this->_bltID}"]        =
                        CRM_Core_PseudoConstant::countryIsoCode( $this->_params["country_id-{$this->_bltID}"] ); 
                }

                // set a few other parameters for PayPal
                $this->_params['token']          = $this->get( 'token' );

                $this->_params['amount'        ] = $this->get( 'amount' );
                $this->_params['currencyID'    ] = $config->defaultCurrency;
                $this->_params['payment_action'] = 'Sale';

                // also merge all the other values from the profile fields
                $values = $this->controller->exportValues( 'Main' );
                $skipFields = array( 'amount', 'amount_other',
                                     "street_address-{$this->_bltID}",
                                     "city-{$this->_bltID}",
                                     "state_province_id-{$this->_bltID}",
                                     "postal_code-{$this->_bltID}",
                                     "country_id-{$this->_bltID}" );
                foreach ( $values as $name => $value ) {
                    // skip amount field
                    if ( ! in_array( $name, $skipFields ) ) {
                        $this->_params[$name] = $value;
                    }
                }
                $this->set( 'getExpressCheckoutDetails', $this->_params );
            } else {
                $this->_params = $this->get( 'getExpressCheckoutDetails' );
            }
        } else {
            $this->_params = $this->controller->exportValues( 'Main' );

            if ( !empty( $this->_params["state_province_id-{$this->_bltID}"] ) ) {
                $this->_params["state_province-{$this->_bltID}"] =
                    CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] ); 
            }
            if ( ! empty( $this->_params["country_id-{$this->_bltID}"] ) ) {
                $this->_params["country-{$this->_bltID}"]        =
                    CRM_Core_PseudoConstant::countryIsoCode( $this->_params["country_id-{$this->_bltID}"] ); 
            }
            
            // if onbehalf-of-organization
            if ( CRM_Utils_Array::value( 'is_for_organization', $this->_params ) ) {
                if ( $this->_params['org_option'] && $this->_params['organization_id'] ) {
                    $this->_params['organization_name'] = 
                        CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $this->_params['organization_id'], 'sort_name');
                }
                $this->_params['location'][1]['address']['country_id'] = 
                    $this->_params['location'][1]['address']['country_state'][0];
                if ( !empty( $this->_params['location'][1]['address']['country_id'] ) ) {
                    $this->_params['location'][1]['address']['country'] = 
                        CRM_Core_PseudoConstant::countryIsoCode( $this->_params['location'][1]['address']['country_id'] ); 
                }
                $this->_params['location'][1]['address']['state_province_id'] = 
                    $this->_params['location'][1]['address']['country_state'][1];
                if ( !empty( $this->_params['location'][1]['address']['state_province_id'] ) ) {
                    $this->_params['location'][1]['address']['state_province'] = 
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params['location'][1]['address']['state_province_id'] );
                }
                unset($this->_params['location'][1]['address']['country_state']);
            }

            if ( isset( $this->_params['credit_card_exp_date'] ) ) {
                $this->_params['year'   ]        = $this->_params['credit_card_exp_date']['Y'];  
                $this->_params['month'  ]        = $this->_params['credit_card_exp_date']['M'];  
            }
            $this->_params['ip_address']     = $_SERVER['REMOTE_ADDR']; 
            // hack for safari
            if ( $this->_params['ip_address'] == '::1' ) {
                $this->_params['ip_address'] = '127.0.0.1';
            }
            $this->_params['amount'        ] = $this->get( 'amount' );
            
            if ( $this->_params['amount'] ) { 
                require_once 'CRM/Core/OptionGroup.php';
                $this->_params['amount_level'  ] = CRM_Core_OptionGroup::optionLabel( "civicrm_contribution_page.amount.{$this->_id}",
                                                                                      $this->_params['amount'] );
            }

            $this->_params['currencyID'    ] = $config->defaultCurrency;
            $this->_params['payment_action'] = 'Sale';
        }

        foreach ( array ( 'pcp_display_in_roll', 'pcp_roll_nickname', 'pcp_personal_note' ) as $val ) {
            if ( CRM_Utils_Array::value( $val, $this->_params ) ) {
                $this->assign( $val, $this->_params[$val]);
                $this->assign( 'pcpBlock', true);
            }
        }
        $this->_params['invoiceID'] = $this->get( 'invoiceID' );
        $this->set( 'params', $this->_params );
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assignToTemplate( );
        require_once 'CRM/Contribute/BAO/Premium.php';
        
        $params = $this->_params;                    
        $honor_block_is_active = $this->get( 'honor_block_is_active');
        // make sure we have values for it
        if ( $honor_block_is_active &&
             ( ( ! empty( $params["honor_first_name"] ) && ! empty( $params["honor_last_name"] ) ) ||
               ( ! empty( $params["honor_email"] ) ) ) ) {
            $this->assign('honor_block_is_active', $honor_block_is_active );
            $this->assign("honor_block_title",$this->_values['honor_block_title']);
          
            require_once "CRM/Core/PseudoConstant.php";
            $prefix = CRM_Core_PseudoConstant::individualPrefix();
            $honor  = CRM_Core_PseudoConstant::honor( );             
            $this->assign("honor_type",$honor[$params["honor_type_id"]]);
            $this->assign("honor_prefix",$prefix[$params["honor_prefix_id"]]);
            $this->assign("honor_first_name",$params["honor_first_name"]);
            $this->assign("honor_last_name",$params["honor_last_name"]);
            $this->assign("honor_email",$params["honor_email"]);
        }
        $this->assign('receiptFromEmail',$this->_values['receipt_from_email']);
        $amount_block_is_active = $this->get( 'amount_block_is_active');
        $this->assign('amount_block_is_active', $amount_block_is_active );

        if ( isset( $params['selectProduct'] ) && $params['selectProduct'] != 'no_thanks') {
            $option    = $params['options_'.$params['selectProduct']];
            $productID = $params['selectProduct']; 
            CRM_Contribute_BAO_Premium::buildPremiumBlock( $this , $this->_id, false,
                                                           $productID, $option);
            $this->set('productID',$productID);
            $this->set('option',$option);
        }
        $config =& CRM_Core_Config::singleton( );
        if ( in_array("CiviMember", $config->enableComponents) ) {
            if ( isset( $params['selectMembership'] ) &&
                 $params['selectMembership'] != 'no_thanks' ) {
                CRM_Member_BAO_Membership::buildMembershipBlock( $this,
                                                                 $this->_id,
                                                                 false,
                                                                 $params['selectMembership'],
                                                                 false, null,
                                                                 $this->_membershipContactID );
            } else {
                $this->assign('membershipBlock', false);
            }
        }
        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre' , true );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost', true );
        $this->_separateMembershipPayment = $this->get( 'separateMembershipPayment' );
        $this->assign( "is_separate_payment", $this->_separateMembershipPayment );
        
        if ( $this->_paymentProcessor['payment_processor_type'] == 'Google_Checkout' 
             && !$this->_params['is_pay_later']) {
            $this->_checkoutButtonName = $this->getButtonName( 'next', 'checkout' );
            $this->add('image',
                       $this->_checkoutButtonName,
                       $this->_paymentProcessor['url_button'],
                       array( 'class' => 'form-submit' ) );
            
            $this->addButtons(array(
                                    array ( 'type'      => 'back',
                                            'name'      => ts('<< Go Back')),
                                    )
                              );
        } else {
            if ( $this->_contributeMode == 'notify' || !$this->_values['is_monetary'] || 
                 $this->_amount <= 0.0              || $this->_params['is_pay_later'] ||
                 $this->_separateMembershipPayment ) {
                $contribButton = ts('Continue >>');
            } else {
                $contribButton = ts('Make Contribution');
            }
            $this->addButtons(array(
                                    array ( 'type'      => 'next',
                                            'name'      => $contribButton,
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                            'isDefault' => true,
                                            'js'        => array( 'onclick' => "return submitOnce(this,'" . $this->_name . "','" . ts('Processing') ."');" )
                                            ),
                                    array ( 'type'      => 'back',
                                            'name'      => ts('<< Go Back')
                                            )
                                    )
                              );
        }
        
        $defaults = array( );
        $options = array( );
        $fields = array( );
        require_once "CRM/Core/BAO/CustomGroup.php";
        $removeCustomFieldTypes = array ('Contribution');
        foreach ( $this->_fields as $name => $dontCare ) {
            $fields[$name] = 1;
        }
        $fields["state_province-{$this->_bltID}"] =
            $fields["country-{$this->_bltID}"] = $fields["email-{$this->_bltID}"] = 1;

        $contact =  $this->_params;
        foreach ($fields as $name => $dontCare ) {
            if ( isset( $contact[$name] ) ) {
                if ( substr( $name, 0, 7 ) == 'custom_' ) {
                    $id = substr( $name, 7 );
                    $defaults[$name] = CRM_Core_BAO_CustomField::getDefaultValue( $contact[$name],
                                                                                  $id,
                                                                                  $options );
                } else {
                    $defaults[$name] = $contact[$name];
                } 
            }
        }
        $this->setDefaults( $defaults );

        $this->freeze();

    }

    /**
     * overwrite action, since we are only showing elements in frozen mode
     * no help display needed
     * @return int
     * @access public
     */
    function getAction( ) 
    {
        if ( $this->_action & CRM_Core_Action::PREVIEW ) {
            return CRM_Core_Action::VIEW | CRM_Core_Action::PREVIEW;
        } else {
            return CRM_Core_Action::VIEW;
        }
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
        
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess( )
    {
        $config =& CRM_Core_Config::singleton( );
        require_once "CRM/Contact/BAO/Contact.php";

        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );

        // add a description field at the very beginning
        $this->_params['description'] = ts( 'Online Contribution' ) . ': ' . $this->_values['title'];

        // also add accounting code
        $this->_params['accountingCode'] = CRM_Utils_Array::value( 'accountingCode',
                                                                   $this->_values );

        $premiumParams = $membershipParams = $tempParams = $params = $this->_params;
        $now = date( 'YmdHis' );
        $fields = array( );
        
        // set email for primary location.
        $fields["email-Primary"] = 1;
        $params["email-Primary"] = $params["email-{$this->_bltID}"];
        
        // get the add to groups
        $addToGroups = array( );
        
        // now set the values for the billing location.
        foreach ( $this->_fields as $name => $value ) {
            $fields[$name] = 1;

            // get the add to groups for uf fields
            if ( CRM_Utils_Array::value('add_to_group_id',$value) ) {
                $addToGroups[$value['add_to_group_id']] = $value['add_to_group_id'];
            }
        }
        
        if ( ! array_key_exists( 'first_name', $fields ) ) {
            $nameFields = array( 'first_name', 'middle_name', 'last_name' );
            foreach ( $nameFields as $name ) {
                $fields[$name] = 1;
                if ( array_key_exists( "billing_$name", $params ) ) {
                    $params[$name] = $params["billing_{$name}"];
                    $params['preserveDBName'] = true;
                }
            }
        }
        
        // billing email address
        $fields["email-{$this->_bltID}"] = 1;
        
        //unset the billing parameters if it is pay later mode
        //to avoid creation of billing location
        if ( $params['is_pay_later'] ) {
            $billingFields = array( "email-{$this->_bltID}",
                                    "billing_first_name",
                                    "billing_middle_name",
                                    "billing_last_name",
                                    "street_address-{$this->_bltID}",
                                    "city-{$this->_bltID}",
                                    "state_province-{$this->_bltID}",
                                    "state_province_id-{$this->_bltID}",
                                    "postal_code-{$this->_bltID}",
                                    "country-{$this->_bltID}",
                                    "country_id-{$this->_bltID}"
                                    );

            foreach( $billingFields as $value ) {
                unset( $params[$value] );
                unset( $fields[$value] );
            }
        }

        // if onbehalf-of-organization contribution, take out
        // organization params in a separate variable, to make sure
        // normal behavior is continued. And use that variable to
        // process on-behalf-of functionality.
        if ( CRM_Utils_Array::value( 'is_for_organization', $this->_values ) ) {
            $behalfOrganization = array();
            foreach ( array('organization_name', 'organization_id', 'location', 'org_option') as $fld ) {
                $behalfOrganization[$fld] = $params[$fld];
                unset($params[$fld]);
            }
        }
        
        if ( ! isset( $contactID ) ) {
            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams($params, 'Individual');
            $ids = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual');

            // if we find more than one contact, use the first one
            $contact_id  = CRM_Utils_Array::value( 0, $ids );
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contact_id, $addToGroups );
            $this->set( 'contactID', $contactID );
        } else {
            $ctype = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $contactID, 'contact_type');
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contactID, $addToGroups,
                                                                         null, $ctype);
        }

        // If onbehalf-of-organization contribution / signup, add organization
        // and it's location.
        if ( isset( $params['is_for_organization'] ) && isset( $behalfOrganization['organization_name'] ) ) {
            self::processOnBehalfOrganization( $behalfOrganization, $contactID, $this->_values, $this->_params );
        }

        // lets store the contactID in the session
        // for things like tell a friend
        if ( ! $session->get( 'userID' ) ) {
            $session->set( 'transaction.userID', $contactID );
        } else {
            $session->set( 'transaction.userID', null );
        }
        
        // store the fact that this is a membership and membership type is selected
        $processMembership = false;
        if ( CRM_Utils_Array::value( 'selectMembership', $membershipParams ) &&
             $membershipParams['selectMembership'] != 'no_thanks' ) {
            $processMembership = true;
            $this->assign( 'membership_assign' , true );
            $this->set('membershipTypeID' , $this->_params['selectMembership']);
            if( $this->_action & CRM_Core_Action::PREVIEW ) {
                $membershipParams['is_test'] = 1;
            }
            if ( $this->_params['is_pay_later'] ) {
                $membershipParams['is_pay_later'] = 1;
            }
        }

   
        if ( $processMembership ) {
            require_once 'CRM/Core/Payment/Form.php';
            CRM_Core_Payment_Form::mapParams( $this->_bltID, $this->_params, $membershipParams, true );

            require_once 'CRM/Member/BAO/Membership.php';
            CRM_Member_BAO_Membership::postProcessMembership( $membershipParams, $contactID,
                                                              $this, $premiumParams );                       
        } else {
            // at this point we've created a contact and stored its address etc
            // all the payment processors expect the name and address to be in the 
            // so we copy stuff over to first_name etc. 
            $paymentParams      = $this->_params;
            $contributionTypeId = $this->_values['contribution_type_id'];
            
            require_once "CRM/Contribute/BAO/Contribution/Utils.php";
            CRM_Contribute_BAO_Contribution_Utils::processConfirm( $this, $paymentParams, 
                                                                   $premiumParams, $contactID, 
                                                                   $contributionTypeId, 
                                                                   'contribution' );
        }
    }
    
    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcessPremium( $premiumParams, $contribution )
    {
        // assigning Premium information to receipt tpl
        $selectProduct = CRM_Utils_Array::value( 'selectProduct', $premiumParams );
        if ( $selectProduct &&
             $selectProduct != 'no_thanks' ) {
            $startDate = $endDate = "";
            $this->assign('selectPremium',  true );
            require_once 'CRM/Contribute/DAO/Product.php';
            $productDAO =& new CRM_Contribute_DAO_Product();
            $productDAO->id = $selectProduct;
            $productDAO->find(true);
            $this->assign('product_name',  $productDAO->name );
            $this->assign('price', $productDAO->price);
            $this->assign('sku', $productDAO->sku);
            $this->assign('option',$premiumParams['options_'.$premiumParams['selectProduct']]);
            
            $periodType = $productDAO->period_type;
            
            if ( $periodType ) {
                $fixed_period_start_day = $productDAO->fixed_period_start_day;
                $duration_unit          = $productDAO->duration_unit;
                $duration_interval      = $productDAO->duration_interval;
                if ( $periodType == 'rolling' ) {
                    $startDate = date('Y-m-d');
                } else if ($periodType == 'fixed') {
                    if ( $fixed_period_start_day ) {
                        $date  = explode('-', date('Y-m-d') );
                        $month     = substr( $fixed_period_start_day, 0, strlen($fixed_period_start_day)-2);
                        $day       = substr( $fixed_period_start_day,-2)."<br>";
                        $year      = $date[0];
                        $startDate = $year.'-'.$month.'-'.$day;
                    } else {
                        $startDate = date('Y-m-d');
                    }
                }
                
                $date  = explode('-', $startDate );
                $year  = $date[0];
                $month = $date[1];
                $day   = $date[2];
                
                switch ( $duration_unit ) {
                case 'year' :
                    $year  = $year   + $duration_interval;
                    break;
                case 'month':
                    $month = $month  + $duration_interval;
                    break;
                case 'day':
                    $day   = $day    + $duration_interval;
                    break;
                case 'week':
                    $day   = $day    + ($duration_interval * 7);
                }
                $endDate = date('Y-m-d H:i:s',mktime($hour, $minute, $second, $month, $day, $year));
                $this->assign('start_date',$startDate);
                $this->assign('end_date',$endDate);
            }
            
            require_once 'CRM/Contribute/DAO/Premium.php';
            $dao = & new CRM_Contribute_DAO_Premium();
            $dao->entity_table = 'civicrm_contribution_page';
            $dao->entity_id    = $this->_id;
            $dao->find(true);
            $this->assign('contact_phone',$dao->premiums_contact_phone);
            $this->assign('contact_email',$dao->premiums_contact_email);
            
            //create Premium record
            require_once 'CRM/Utils/Date.php';
            $params = array(
                            'product_id'         => $premiumParams['selectProduct'],
                            'contribution_id'    => $contribution->id,
                            'product_option'     => $premiumParams['options_'.$premiumParams['selectProduct']],
                            'quantity'           => 1,
                            'start_date'         => CRM_Utils_Date::customFormat($startDate,'%Y%m%d'),
                            'end_date'           => CRM_Utils_Date::customFormat($endDate,'%Y%m%d'),
                            );
            
            require_once 'CRM/Contribute/BAO/Contribution.php';
            CRM_Contribute_BAO_Contribution::addPremium($params);
        }
    }

    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    static function processContribution( &$form, $params, $result, $contactID, $contributionType,
                                         $deductibleMode = true, $pending = false,
                                         $online = true ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        $honorCId = $recurringContributionID = null;
        if ( $online ) {
            if ( $form->get( 'honor_block_is_active' ) ) {
                $honorCId = $form->createHonorContact( );
            }
            
            $recurringContributionID = $form->processRecurringContribution( $params, $contactID );
        } else if ( ! $online && isset($params['honor_contact_id'] ) ) {
            $honorCId = $params['honor_contact_id'];
        }
        
        $config =& CRM_Core_Config::singleton( );
        if ( ! $online && isset($params['non_deductible_amount'] ) ) {
            $nonDeductibleAmount = $params['non_deductible_amount'];
        } else {
            $nonDeductibleAmount = $params['amount'];
        }
        if ( $online && $contributionType->is_deductible && $deductibleMode ) {
            $selectProduct = CRM_Utils_Array::value( 'selectProduct', $premiumParams );
            if ( $selectProduct &&
                 $selectProduct != 'no_thanks' ) {
                require_once 'CRM/Contribute/DAO/Product.php';
                $productDAO =& new CRM_Contribute_DAO_Product();
                $productDAO->id = $selectProduct;
                $productDAO->find(true);
                if( $params['amount'] < $productDAO->price ){
                    $nonDeductibleAmount = $params['amount'];
                } else {
                    $nonDeductibleAmount = $productDAO->price;
                }
            } else {
                $nonDeductibleAmount = '0.00';
            }
        }

        $now = date( 'YmdHis' );    
        $receiptDate = CRM_Utils_Array::value( 'receipt_date', $params );
        if ( $form->_values['is_email_receipt'] ) {
            $receiptDate = $now;
        }

        // check contribution Type
        // first create the contribution record
        $contribParams = array(
                               'contact_id'            => $contactID,
                               'contribution_type_id'  => $contributionType->id,
                               'contribution_page_id'  => $online ? $form->_id : null,
                               'receive_date'          => $now,
                               'non_deductible_amount' => $nonDeductibleAmount,
                               'total_amount'          => $params['amount'],
                               'amount_level'          => CRM_Utils_Array::value( 'amount_level', $params ),
                               'invoice_id'            => $params['invoiceID'],
                               'currency'              => $params['currencyID'],
                               'source'                =>
                               ( ! $online || $params['source'] ) ? $params['source'] : $params['description'],
                               'is_pay_later'          => CRM_Utils_Array::value( 'is_pay_later', $params, 0 ),
                               );
        if ( ! $online && isset($params['thankyou_date'] ) ) {
            $contribParams['thankyou_date'] = $params['thankyou_date'];
        }
        
        if ( ! $online || $form->_values['is_monetary'] ) {
            if ( ! $params['is_pay_later'] ) {
                $contribParams['payment_instrument_id'] = 1;
            }
        }
        
        if ( ! $pending && $result ) {
            $contribParams += array(
                                    'fee_amount'   => CRM_Utils_Array::value( 'fee_amount', $result ),
                                    'net_amount'   => CRM_Utils_Array::value( 'net_amount', $result, $params['amount'] ),
                                    'trxn_id'      => $result['trxn_id'],
                                    'receipt_date' => $receiptDate,
                                    );
        }
        
        if ( isset($honorCId)  ) {
            $contribParams["honor_contact_id"] = $honorCId;
            $contribParams["honor_type_id"]    = $params['honor_type_id'];
        }

        if ( $recurringContributionID ) {
            $contribParams['contribution_recur_id'] = $recurringContributionID;
        }

        $contribParams["contribution_status_id"] = $pending ? 2 : 1;

        if ( $form->_mode == 'test' ) {
            $contribParams["is_test"] = 1;
        }
        
        $ids = array( );
        if ( isset( $contribParams['invoice_id'] ) ) {
            $contribID = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_Contribution',
                                                      $contribParams['invoice_id'],
                                                      'id',
                                                      'invoice_id' );
            if ( isset( $contribID ) ) {
                $ids['contribution'] = $contribID;
                $contribParams['id'] = $contribID;
            }
        }
        foreach ( array ( 'pcp_display_in_roll', 'pcp_roll_nickname', 'pcp_personal_note' ) as $val ) {
            if ( CRM_Utils_Array::value( $val, $params ) ) {
                $contribParams[$val] = $params[$val];
            }
        }
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $contribution =& CRM_Contribute_BAO_Contribution::add( $contribParams, $ids );
        
        //handle pledge stuff.
        if ( !CRM_Utils_Array::value( 'separate_membership_payment', $form->_params ) &&
             CRM_Utils_Array::value('pledge_block_id', $form->_values ) && 
             ( CRM_Utils_Array::value('is_pledge', $form->_params ) ||
               CRM_Utils_Array::value('pledge_id', $form->_values ) ) ) {
                        
            if ( CRM_Utils_Array::value( 'pledge_id', $form->_values ) ) {
                
                //when user doing pledge payments.
                //update the schedule when payment(s) are made 
                
                require_once 'CRM/Pledge/BAO/Payment.php';
                foreach ( $form->_params['pledge_amount'] as $paymentId => $dontCare ) {
                    $pledgePaymentParams = array('id'              => $paymentId,
                                                 'contribution_id' => $contribution->id,
                                                 'status_id'       => $contribution->contribution_status_id
                                                 );
                    
                    CRM_Pledge_BAO_Payment::add( $pledgePaymentParams );
                }
                
                //update pledge status according to the new payment statuses
                CRM_Pledge_BAO_Payment::updatePledgePaymentStatus( $form->_values['pledge_id'] );
                
            } else {
                //when user creating pledge record.
                $pledgeParams                            = array( );
                $pledgeParams['contact_id'             ] = $contribution->contact_id;
                $pledgeParams['installment_amount'     ] = $contribution->total_amount;
                $pledgeParams['contribution_id'        ] = $contribution->id;
                $pledgeParams['contribution_page_id'   ] = $contribution->contribution_page_id;
                $pledgeParams['contribution_type_id'   ] = $contribution->contribution_type_id;
                $pledgeParams['frequency_interval'     ] = $params['pledge_frequency_interval'];
                $pledgeParams['installments'           ] = $params['pledge_installments'];
                $pledgeParams['frequency_unit'         ] = $params['pledge_frequency_unit'];
                $pledgeParams['frequency_day'          ] = 1;
                $pledgeParams['create_date'            ] = $pledgeParams['start_date'] = date( "Ymd" );
                $pledgeParams['scheduled_date'    ]['M'] = date("m"); 
                $pledgeParams['scheduled_date'    ]['d'] = date("d");
                $pledgeParams['scheduled_date'    ]['Y'] = date("Y");
                $pledgeParams['status_id'              ] = $contribution->contribution_status_id;
                $pledgeParams['max_reminders'          ] = $form->_values['max_reminders'];
                $pledgeParams['initial_reminder_day'   ] = $form->_values['initial_reminder_day'];
                $pledgeParams['additional_reminder_day'] = $form->_values['additional_reminder_day'];
                $pledgeParams['is_test'                ] = $contribution->is_test;
                $pledgeParams['acknowledge_date'       ] = date( 'Ymd' );
                
                require_once 'CRM/Pledge/BAO/Pledge.php';
                $pledge = CRM_Pledge_BAO_Pledge::create( $pledgeParams );
                
                $form->_params['pledge_id'] = $pledge->id;  
                
                //send acknowledgment email. only when pledge is created
                if ( $pledge->id ) {
                    
                    //build params to send acknowledgment.
                    $pledgeParams['id'                 ] = $pledge->id;
                    $pledgeParams['receipt_from_name'  ] = $form->_values['receipt_from_name'];
                    $pledgeParams['receipt_from_email' ] = $form->_values['receipt_from_email'];
                    
                    //scheduled amount will be same as installment_amount.
                    $pledgeParams['scheduled_amount'   ] = $pledgeParams['installment_amount'];
                    
                    //get total pledge amount.
                    $pledgeParams['total_pledge_amount'] = $pledge->amount;
                    
                    require_once 'CRM/Pledge/BAO/Pledge.php';
                    CRM_Pledge_BAO_Pledge::sendAcknowledgment( $form, $pledgeParams );
                }
            }
        }
        
        if ( $online ) {
            require_once 'CRM/Core/BAO/CustomValueTable.php';
            CRM_Core_BAO_CustomValueTable::postProcess( $form->_params,
                                                        CRM_Core_DAO::$_nullArray,
                                                        'civicrm_contribution',
                                                        $contribution->id,
                                                        'Contribution' );
            CRM_Core_Session::setStatus( $status );
        } else {
            //handle custom data.
            $params['contribution_id'] = $contribution->id;
            if ( CRM_Utils_Array::value( 'custom', $params ) &&
                 is_array( $params['custom'] ) &&
                 !is_a( $contribution, 'CRM_Core_Error') ) {
                require_once 'CRM/Core/BAO/CustomValueTable.php';
                CRM_Core_BAO_CustomValueTable::store( $params['custom'], 'civicrm_contribution', $contribution->id );
            }
        }
        
        require_once "CRM/Contribute/BAO/Contribution/Utils.php";
        CRM_Contribute_BAO_Contribution_Utils::createCMSUser( $params,
                                                              $contactID,
                                                              'email-' . $form->_bltID );

        // return if pending
        if ( $pending ) {
            return $contribution;
        }
 
        // next create the transaction record
        if ( ( ! $online || $form->_values['is_monetary'] ) && $result['trxn_id'] ) {
            $trxnParams = array(
                                'contribution_id'   => $contribution->id,
                                'trxn_date'         => $now,
                                'trxn_type'         => 'Debit',
                                'total_amount'      => $params['amount'],
                                'fee_amount'        => CRM_Utils_Array::value( 'fee_amount', $result ),
                                'net_amount'        => CRM_Utils_Array::value( 'net_amount', $result, $params['amount'] ),
                                'currency'          => $params['currencyID'],
                                'payment_processor' => $form->_paymentProcessor['payment_processor_type'],
                                'trxn_id'           => $result['trxn_id'],
                                );
            
            require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
            $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );
        }

        // create an activity record
        require_once 'CRM/Activity/BAO/Activity.php';
        CRM_Activity_BAO_Activity::addActivity( $contribution );

        $transaction->commit( ); 

        return $contribution;
    }

    /**
     * Create the recurring contribution record
     *
     */
    function processRecurringContribution( &$params, $contactID ) {
        // return if this page is not set for recurring
        // or the user has not chosen the recurring option
        if ( ! CRM_Utils_Array::value( 'is_recur', $this->_values ) ||
             ! CRM_Utils_Array::value( 'is_recur', $params ) ) {
            return null;
        }

        $recurParams = array( );

        $config =& CRM_Core_Config::singleton( );
        $recurParams['contact_id']         = $contactID;
        $recurParams['amount']             = $params['amount'];
        $recurParams['frequency_unit']     = $params['frequency_unit'];
        $recurParams['frequency_interval'] = $params['frequency_interval'];
        $recurParams['installments']       = $params['installments'];

        if( $this->_action & CRM_Core_Action::PREVIEW ) {
            $recurParams["is_test"] = 1;
        }
        
        $now = date( 'YmdHis' );
        $recurParams['start_date'] = $recurParams['create_date'] = $recurParams['modified_date'] = $now;
        $recurParams['invoice_id'] = $params['invoiceID'];
        $recurParams['contribution_status_id'] = 2;

        // we need to add a unique trxn_id to avoid a unique key error
        // in paypal IPN we reset this when paypal sends us the real trxn id, CRM-2991
        $recurParams['trxn_id'] = CRM_Utils_Array::value( 'trxn_id', $params, $params['invoiceID'] );


        $ids = array( ); 

        require_once 'CRM/Contribute/BAO/ContributionRecur.php';
        $recurring =& CRM_Contribute_BAO_ContributionRecur::add( $recurParams, $ids );
        if ( is_a( $recurring, 'CRM_Core_Error' ) ) {
                CRM_Core_Error::displaySessionError( $result );
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_Main_display=true' ) );
        }
        return $recurring->id;
    }


    /**
     * Create the Honor contact
     *
     * @return void
     * @access public
     */
    function createHonorContact(  ) {
        $params = $this->controller->exportValues( 'Main' );
       
        // return if we dont have enough information
        if ( empty( $params["honor_first_name"] ) &&
             empty( $params["honor_last_name" ] ) &&
             empty( $params["honor_email"] ) ) {
            return null;
        }
        
        //assign to template for email reciept
        $honor_block_is_active = $this->get( 'honor_block_is_active');
        
        $this->assign('honor_block_is_active', $honor_block_is_active );
        $this->assign("honor_block_title",$this->_values['honor_block_title']);
        
        require_once "CRM/Core/PseudoConstant.php";
        $prefix = CRM_Core_PseudoConstant::individualPrefix();
        $honorType = CRM_Core_PseudoConstant::honor( );
        $this->assign("honor_type",       $honorType[$params["honor_type_id"]]);
        $this->assign("honor_prefix",     $prefix[$params["honor_prefix_id"]]);
        $this->assign("honor_first_name", $params["honor_first_name"]);
        $this->assign("honor_last_name",  $params["honor_last_name"]);
        $this->assign("honor_email",      $params["honor_email"]);
        
        //create honoree contact
        require_once 'CRM/Contribute/BAO/Contribution.php';
        return CRM_Contribute_BAO_Contribution::createHonorContact( $params );
    }

    /**
     * Function to add on behalf of organization and it's location  
     *
     * @param $behalfOrganization array  array of organization info
     * @param $values             array  form values array
     * @param $contactID          int    individual contact id. One
     * who is doing the process of signup / contribution. 
     *
     * @return void
     * @access public
     */
    static function processOnBehalfOrganization( &$behalfOrganization, &$contactID, &$values, &$params ) {
        if ( $behalfOrganization['organization_id'] && $behalfOrganization['org_option'] ) {
            $orgID = $behalfOrganization['organization_id'];
            unset($behalfOrganization['organization_id']);
        }

        // formalities for creating / editing organization.
        $behalfOrganization['contact_type']                    = 'Organization';
        $behalfOrganization['location'][1]['is_primary']       = 1;
        $behalfOrganization['location'][1]['location_type_id'] = 
            CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_LocationType', 'Main', 'id', 'name' );
        
        // get the relationship type id
        require_once "CRM/Contact/DAO/RelationshipType.php";
        $relType =& new CRM_Contact_DAO_RelationshipType();
        $relType->name_a_b = "Employee of";
        $relType->find(true);
        $relTypeId = $relType->id;
        
        // keep relationship params ready
        $relParams['relationship_type_id']    = $relTypeId.'_a_b';
        $relParams['is_permission_a_b'   ]    = 1;
        $relParams['is_active'           ]    = 1;
        
        if ( ! $orgID ) {
            // check if matching organization contact exists
            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams($behalfOrganization, 'Organization');
            $dupeIDs      = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Organization', 'Strict');
            if ( count($dupeIDs) == 1 ) {
                $behalfOrganization['contact_id'] = $dupeIDs[0];
                // don't allow name edit
                unset($behalfOrganization['organization_name']);
            }
        } else {
            // if found permissioned related organization, allow location edit
            $behalfOrganization['contact_id'] = $orgID;
            // don't allow name edit
            unset($behalfOrganization['organization_name']);
        }

        // create organization, add location 
        $org = CRM_Contact_BAO_Contact::create( $behalfOrganization );
                
        // create relationship
        $relParams['contact_check'][$org->id] = 1;
        $cid = array( 'contact' => $contactID );
        $relationship = CRM_Contact_BAO_Relationship::create($relParams, $cid);
        
        // take a note of new/updated organiation contact-id.
        $orgID = $org->id;

        // if multiple match - send a duplicate alert
        if ( $dupeIDs && (count($dupeIDs) > 1) ) {
            $values['onbehalf_dupe_alert'] = 1;
            // required for IPN
            $params['onbehalf_dupe_alert'] = 1;
        }
        
        // make sure organization-contact-id is considered for recording
        // contribution/membership etc..
        if ( $contactID != $orgID ) {
            // take a note of contact-id, so we can send the
            // receipt to individual contact as well.

            // required for mailing/template display ..etc 
            $values['related_contact'] = $contactID;
            // required for IPN
            $params['related_contact'] = $contactID;
            
            // contribution / signup will be done using this
            // organization id.
            $contactID = $orgID;
        }
    }
}
