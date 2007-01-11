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
class CRM_Event_Form_Registration_Confirm extends CRM_Event_Form_Registration
{
    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        $config =& CRM_Core_Config::singleton( );
        parent::preProcess( );

        if ( $this->_contributeMode == 'express' ) {
            // rfp == redirect from paypal
            $rfp = CRM_Utils_Request::retrieve( 'rfp', 'Boolean',
                                                CRM_Core_DAO::$_nullObject, false, null, 'GET' );
            if ( $rfp ) {
                require_once 'CRM/Contribute/Payment.php'; 
                $payment =& CRM_Contribute_Payment::singleton( $this->_mode );
                $this->_params = $payment->getExpressCheckoutDetails( $this->get( 'token' ) );

                // fix state and country id if present
                if ( CRM_Utils_Array::value( 'state_province', $this->_params ) ) {
                    $states = CRM_Core_PseudoConstant::stateProvinceAbbreviation();
                    $states = array_flip( $states );
                    $this->_params['state_province_id'] = CRM_Utils_Array::value( $this->_params['state_province'], $states );
                }
                if ( CRM_Utils_Array::value( 'country', $this->_params ) ) {
                    $states = CRM_Core_PseudoConstant::countryIsoCode();
                    $states = array_flip( $states );
                    $this->_params['country_id'] = CRM_Utils_Array::value( $this->_params['country'], $states );
                }

                // set a few other parameters for PayPal
                $this->_params['token']          = $this->get( 'token' );
                $this->_params['amount'        ] = $this->get( 'amount' );
                $this->_params['currencyID'    ] = $config->defaultCurrency;
                $this->_params['payment_action'] = 'Sale';
                
                // also merge all the other values from the profile fields
                $values = $this->controller->exportValues( 'Register' );
                $skipFields = array( 'amount', 'first_name', 'middle_name', 'last_name',
                                     'street_address', 'city', 'state_province_id', 'postal_code',
                                     'country_id' );
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
            $this->_params = $this->controller->exportValues( 'Register' );

            if ( isset( $this->_params['state_province_id'] ) ) {
                $this->_params['state_province'] = CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params['state_province_id'] ); 
            }
            if ( isset( $this->_params['country_id'] ) ) {
                $this->_params['country']        = CRM_Core_PseudoConstant::countryIsoCode( $this->_params['country_id'] ); 
            }
            if ( isset( $this->_params['credit_card_exp_date'] ) ) {
                $this->_params['year'   ]        = $this->_params['credit_card_exp_date']['Y'];  
                $this->_params['month'  ]        = $this->_params['credit_card_exp_date']['M'];  
            }
            $this->_params['ip_address']     = $_SERVER['REMOTE_ADDR']; 

            $this->_params['amount'        ] = $this->get( 'amount' );
            //$this->_params['amount_level'  ] = $this->get( 'amount_level' );
            $this->_params['currencyID'    ] = $config->defaultCurrency;
            $this->_params['payment_action'] = 'Sale';
        }

        $this->_params['invoiceID'] = $this->get( 'invoiceID' );
        $this->set( 'params', $this->_params );
        //CRM_Core_Error::debug_log_message( "confirm prepro" );
        //print_r($this->_params);
    }

    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $eventPage = array( );
        $params = array( 'event_id' => $this->_id );
        $confirm =  $this->get('registrationValue');
        $this->assign('confirm',$confirm);
                   
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
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once "CRM/Contact/BAO/Contact.php";

        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        $now = date( 'YmdHis' );
        
        $params = $this->_params;
        $fields = array( );

        //$this->fixLocationFields();
        //below fixing of locations block can be built into a method
        foreach ( $this->_fields as $name => $dontCare ) {
            $fields[$name] = 1;
        }
        $fields['first_name'] = $fields['last_name'] = 1;
        $fields['street_address-Primary'] = $fields['supplemental_address_1-Primary'] = $fields['city-Primary'] = 1;
        $fields['postal_code-Primary'] = 1;
        $fields['state_province-Primary'] = $fields['country-Primary'] = $fields['email-Primary'] = 1;
        
        $fixLocationFields = array( 'street_address', 'supplemental_address_1', 
                                    'city', 'state_province', 'postal_code', 'country', 'email' );
        foreach ( $fixLocationFields as $name ) {
            if ( array_key_exists( $name, $params ) ) {
                $params["{$name}-Primary"] = $params[$name];
                unset( $params[$name] );
            }
        } // end of fix locations
        
        if ($contactID) {
            $ctype = CRM_Core_DAO::getFieldValue("CRM_Contact_DAO_Contact", $contactID, "contact_type");
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contactID,null,null,$ctype);
        } else {
            // finding contact record based on duplicate match 
            $dupeParams = array();
            $dupeVars = array('first_name', 'last_name', 'email'); //use dupe match dao
            foreach ( $dupeVars as $name ) {
                if ( $this->_params[$name] ) {
                    $dupeParams[$name] = $this->_params[$name];
                }
            }
            require_once 'api/crm.php';
            $ids = CRM_Core_BAO_UFGroup::findContact( $dupeParams );
            $contactsIDs = explode( ',', $ids );
            
            // if we find more than one contact, use the first one
            $contact_id  = $contactsIDs[0];
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contact_id );
            $this->set( 'contactID', $contactID );
        }

        require_once 'CRM/Contribute/Payment.php';
        $payment =& CRM_Contribute_Payment::singleton( $this->_mode );
        
        if ( $this->_contributeMode == 'express' ) {
            $result =& $payment->doExpressCheckout( $this->_params );
        }
//         print_r($this->_params);
//         print_r($result);
        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            CRM_Core_Error::displaySessionError( $result );
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/admin/event/register', '_qf_Main_display=true' ) );
        }
        
        if ( $result ) {
            $this->_params = array_merge( $this->_params, $result );
        }

        $this->_params['receive_date'] = $now;
        $this->set( 'params', $this->_params );
        $this->assign( 'trxn_id', $result['trxn_id'] );
        $this->assign( 'receive_date', CRM_Utils_Date::mysqlToIso( $this->_params['receive_date']) );
        
        // insert participant record
        $participant  =& $this->addPartcipant( $this->_params, $result, $contactID );
        
        // if paid event add a contribution record
        $contribution =& $this->processContribution( $this->_params, $result, $contactID );
        
        // insert activity record
    }//end of function
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Confirm Event Registration');
    }

    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    public function addPartcipant( $params, $result, $contactID ) 
    {
        $participantParams = array('contact_id'    => $contactID,
                                   'event_id'      => $this->_id,
                                   'status_id'     => 1,
                                   'role_id'       => null, // (need to change) participation_role option_value where is_default is true;
                                   'register_date' => date( 'YmdHis' ),
                                   'source'        => ts( 'Online Event Registration' ),
                                   'event_level'   => null // (need to hv value)
                                   );
    }

    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    public function processContribution( $params, $result, $contactID ) 
    {
        CRM_Core_DAO::transaction( 'BEGIN' );

        $config =& CRM_Core_Config::singleton( );
        $now         = date( 'YmdHis' );
        $receiptDate = null;
        $pending     = ($result['payment_status'] != "Completed") ? true : false ;
        
        if ( $this->_values['is_email_receipt'] ) {
            $receiptDate = $now ;
        }
        
        $contribParams = array(
                               'contact_id'            => $contactID,
                               'contribution_type_id'  => $this->_values['event']['contribution_type_id'],
                               //'contribution_page_id'  => $this->_id,
                               'payment_instrument_id' => 1,
                               'receive_date'          => $now,
                               //'non_deductible_amount' => $nonDeductibleAmount,
                               'total_amount'          => $params['amount'],
                               //'amount_level'          => $params['amount_level'],
                               'invoice_id'            => $params['invoiceID'],
                               'currency'              => $params['currencyID'],
                               'source'                => ts( 'Online Event Registration:' ) . ' ' . $this->_values['event']['title']
                               );
        
        if ( !$pending ) {
            $contribParams += array(
                                    'fee_amount'   => CRM_Utils_Array::value( 'fee_amount', $result ),
                                    'net_amount'   => CRM_Utils_Array::value( 'net_amount', $result, $params['amount'] ),
                                    'trxn_id'      => $result['trxn_id'],
                                    'receipt_date' => $receiptDate,
                                    );
        }

        $contribParams["contribution_status_id"] = $pending ? 2 : 1;
        $contribParams["is_test"] = 1; // record test contribution
        
        $ids = array( );
        //require_once 'CRM/Contribute/BAO/Contribution.php';
        $contribution =& CRM_Contribute_BAO_Contribution::add( $contribParams, $ids );
        
        // return if pending
        if ( $pending ) {
            return $contribution;
        }
        
        // next create the transaction record
        //if ( $this->_values['is_monetary'] ) {
        $trxnParams = array(
                            'entity_table'      => 'civicrm_contribution',
                            'entity_id'         => $contribution->id,
                            'trxn_date'         => $now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $params['amount'],
                            'fee_amount'        => CRM_Utils_Array::value( 'fee_amount', $result ),
                            'net_amount'        => CRM_Utils_Array::value( 'net_amount', $result, $params['amount'] ),
                            'currency'          => $params['currencyID'],
                            'payment_processor' => $config->paymentProcessor,
                            'trxn_id'           => $result['trxn_id'],
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );
        //}
        CRM_Core_DAO::transaction( 'COMMIT' );
        
        return $contribution;
    }
}
?>
