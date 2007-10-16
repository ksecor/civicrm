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
                if ( ! empty( $this->_params["state_province_id-{$this->_bltID}"] ) ) {
                    $this->_params["state_province-{$this->_bltID}"] =
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] ); 
                }
                if ( ! empty( $this->_params['country_id'] ) ) {
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
                                     'first_name', 'middle_name', 'last_name',
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
            require_once 'CRM/Core/OptionGroup.php';
            $this->_params['amount_level'  ] = CRM_Core_OptionGroup::optionLabel( "civicrm_contribution_page.amount.{$this->_id}",
                                                                                  $this->_params['amount'] );

            $this->_params['currencyID'    ] = $config->defaultCurrency;
            $this->_params['payment_action'] = 'Sale';
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
                                                                 $params['selectMembership'] );
            }
        }
        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );
        

        if ( $this->_paymentProcessor['payment_processor_type'] == 'Google_Checkout') {
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
            if ( $this->_contributeMode == 'notify' || ! $this->_values['is_monetary'] || $this->_amount <= 0.0 ) {
                $contribButton = ts('Continue >>');
            } else {
                $contribButton = ts('Make Contribution');
            }
            $this->addButtons(array(
                                    array ( 'type'      => 'next',
                                            'name'      => $contribButton,
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                            'isDefault' => true,
                                            'js'        => array( 'onclick' => "return submitOnce(this,'Confirm','" . ts('Processing') ."');" )
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
        $this->_params['description'] = ts( 'Online Contribution:' ) . ' ' . $this->_values['title'];

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
                }
            }
        }
        
        // also add location name to the array
        $params["location_name-{$this->_bltID}"] =
            CRM_Utils_Array::value( 'billing_first_name' , $params ) . ' ' .
            CRM_Utils_Array::value( 'billing_middle_name', $params ) . ' ' .
            CRM_Utils_Array::value( 'billing_last_name'  , $params );
        $params["location_name-{$this->_bltID}"] = trim( $params["location_name-{$this->_bltID}"] );
        $fields["location_name-{$this->_bltID}"] = 1;
        $fields["email-{$this->_bltID}"] = 1;
 
        if ( ! isset( $contactID ) ) {
            // make a copy of params so we dont destroy our params
            // (since we pass this by reference)
            require_once 'api/crm.php';
            $ids = CRM_Core_BAO_UFGroup::findContact( $params );
            $contactsIDs = explode( ',', $ids );
            
            // if we find more than one contact, use the first one
            $contact_id  = CRM_Utils_Array::value( 0, $contactsIDs );
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contact_id, $addToGroups );
            $this->set( 'contactID', $contactID );
        } else {
            $ctype = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $contactID, 'contact_type');
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contactID, $addToGroups,
                                                                         null, $ctype);
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
            CRM_Contribute_BAO_Contribution::processConfirm( $this, $paymentParams, 
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
        }

        $config =& CRM_Core_Config::singleton( );
        $nonDeductibleAmount = $params['amount'];
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
        if ( ! $online && $form->_values['is_email_receipt'] ) {
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
                               'source'                => ! $online || $params['source'] ?
                               $params['source'] : 
                               $params['description'],
                               );

        if ( ! $online || $form->_values['is_monetary'] ) {
            $contribParams['payment_instrument_id'] = 1;
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

        if( $form->_action & CRM_Core_Action::PREVIEW ) {
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
             
        $contribution =& CRM_Contribute_BAO_Contribution::add( $contribParams, $ids );

        if ( $online ) {
            // process the custom data that is submitted or that came via the url
            // format custom data
            $customData = array( );
            foreach ( $form->_params as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,$value, 'Contribution');
                }
            }
            
            if ( ! empty($customData) ) {
                foreach ( $customData as $customValue) {
                    $cvParams = array(
                                      'entity_table'    => 'civicrm_contribution', 
                                      'entity_id'       => $contribution->id,
                                      'value'           => $customValue['value'],
                                      'type'            => $customValue['type'],
                                      'custom_field_id' => $customValue['custom_field_id'],
                                      'file_id'         => $customValue['file_id'],
                                      );
                    
                    if ($customValue['id']) {
                        $cvParams['id'] = $customValue['id'];
                    }
                    CRM_Core_BAO_CustomValue::create($cvParams);
                }
            }
        }

        if ( CRM_Utils_Array::value( 'cms_create_account', $params ) ) {
            require_once "CRM/Core/BAO/CMSUser.php";
            if ( ! CRM_Core_BAO_CMSUser::create( $params, 'email-' . $form->_bltID ) ) {
                CRM_Core_Error::statusBounce( ts('Your profile is not saved and Account is not created.') );
            }
        }

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

        // also create an activity history record
        require_once 'CRM/Utils/Money.php';
        $params = array( 'source_contact_id' => $contactID,
                         'source_record_id'  => $contribution->id,
                         'activity_type_id'  => CRM_Core_OptionGroup::getValue( 'activity_type',
                                                                                'CiviContribute Online Contribution',
                                                                                'name' ),
                         'module'            => 'CiviContribute', 
                         'callback'          => 'CRM_Contribute_Page_Contribution::details',
                         'subject'           =>
                         CRM_Utils_Money::format($params['amount']). ' - ' . $form->_values['title'] . ' (online)',
                         'activity_date_time'=> $now,
                         'is_test'           => $contribution->is_test
                        );

        require_once 'api/v2/Activity.php';
        if ( is_a( civicrm_activity_create($params), 'CRM_Core_Error' ) ) { 
            CRM_Core_Error::fatal( "Could not create a system record" );
        }

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
        $recurParams['start_date'] = $recurParams['create_date'] = $now;
        $recurParams['invoice_id'] = $params['invoiceID'];
        $recurParams['contribution_status_id'] = 2;

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
        return CRM_Contribute_BAO_Contribution::createHonorContact( $params );
    }

}

?>
