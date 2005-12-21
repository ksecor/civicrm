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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionBase.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Contribute_Form_Contribution_Confirm extends CRM_Contribute_Form_ContributionBase {

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
            $nullObject = null;
            // rfp == redirect from paypal
            $rfp = CRM_Utils_Request::retrieve( 'rfp', $nullObject, false, null, 'GET' );
            if ( $rfp ) {
                require_once 'CRM/Utils/Payment.php'; 
                $payment =& CRM_Utils_Payment::singleton( $this->_mode );
                $this->_params = $payment->getExpressCheckoutDetails( $this->get( 'token' ) );

                // set a few other parameters for PayPal
                $this->_params['token']          = $this->get( 'token' );

                $this->_params['amount'        ] = $this->get( 'amount' );
                $this->_params['currencyID'    ] = $config->defaultCurrency;
                $this->_params['payment_action'] = 'Sale';
                $this->_params['email'         ] = $this->controller->exportValue( 'Main', 'email' );

                $this->set( 'getExpressCheckoutDetails', $this->_params );
            } else {
                $this->_params = $this->get( 'getExpressCheckoutDetails' );
            }
        } else {
            $this->_params = $this->controller->exportValues( 'Main' );

            $this->_params['state_province'] = CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params['state_province_id'] ); 
            $this->_params['country']        = CRM_Core_PseudoConstant::countryIsoCode( $this->_params['country_id'] ); 
            $this->_params['year'   ]        = $this->_params['credit_card_exp_date']['Y'];  
            $this->_params['month'  ]        = $this->_params['credit_card_exp_date']['M'];  
            $this->_params['ip_address']     = $_SERVER['REMOTE_ADDR']; 

            $this->_params['amount'        ] = $this->get( 'amount' );
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

        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Make Contribution'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'back',
                                        'name'      => ts('<< Go Back') ),
                                )
                          );

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
        $defaults = array();
        return $defaults;
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        $contactID = $this->get( 'contactID' );
        if ( ! $contactID ) {
            // make a copy of params so we dont destroy our params
            // (since we pass this by reference)
            $params = $this->_params;

            // so now we have a confirmed financial transaction
            // lets create or update a contact first
            require_once 'api/crm.php';
            $contact_id = CRM_Core_BAO_UFGroup::findContact( $params );
            $contact = null;
            if ( $contact_id ) {
                $contact =& crm_get_contact( array( 'contact_id' => $contact_id ) );
            }

            if ( $this->_action != 1024 ) { // no db transactions during preview
                $ids = array( );
                if ( ! $contact || ! is_a( $contact, 'CRM_Contact_BAO_Contact' ) ) {
                    $contact =& CRM_Contact_BAO_Contact::createFlat( $params, $ids );
                } else {
                    // need to fix and unify all contact creation
                    $idParams = array( 'id' => $contact_id, 'contact_id' => $contact_id );
                    $defaults = array( );
                    CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
                    $contact =& CRM_Contact_BAO_Contact::createFlat( $params, $ids );
                }
                
                if ( is_a( $contact, 'CRM_Core_Error' ) ) {
                    CRM_Core_Error::fatal( "Failed creating contact for contributor" );
                }

                $contactID = $contact->id;
            } else {
                $contactID = 1;
            }

            $this->set( 'contactID', $contactID );
        }

        require_once 'CRM/Utils/Payment.php';
        $payment =& CRM_Utils_Payment::singleton( $this->_mode );

        if ( $this->_contributeMode == 'express' ) {
            $result =& $payment->doExpressCheckout( $this->_params );
        } else {
            $result =& $payment->doDirectPayment( $this->_params );
        }

        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            CRM_Core_Error::displaySessionError( $result );
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_Main_display=true' ) );
        }

        $now = date( 'YmdHis' );

        $this->_params = array_merge( $this->_params, $result );
        $this->_params['receive_date'] = $now;
        $this->set( 'params', $this->_params );
        $this->assign( 'trxn_id', $result['trxn_id'] );
        $this->assign( 'receive_date',
                       CRM_Utils_Date::mysqlToIso( $this->_params['receive_date']) );

        // result has all the stuff we need
        // lets archive it to a financial transaction
        $config =& CRM_Core_Config::singleton( );

        $receiptDate = null;
        if ( $this->_values['is_email_receipt'] ) {
            $receiptDate = $now;
        }

        if ( $this->_action != 1024 ) { // no db transactions during preview
            CRM_Core_DAO::transaction( 'BEGIN' );

            $contributionType =& new CRM_Contribute_DAO_ContributionType( );
            $contributionType->id = $this->_values['contribution_type_id'];
            if ( ! $contributionType->find( true ) ) {
                CRM_Core_Error::fatal( "Could not find a system table" );
            }
            
            if ( $contributionType->is_deductible ) {
                $nonDeductibeAmount = $result['gross_amount'];
            } else {
                $nonDeductibeAmount = 0.00;
            }
            
            // check contribution Type
            // first create the contribution record
            $params = array(
                            'contact_id'            => $contactID,
                            'contribution_type_id'  => $contributionType->id,
                            'payment_instrument_id' => 1,
                            'receive_date'          => $now,
                            'non_deductible_amount' => $nonDeductibeAmount,
                            'total_amount'          => $result['gross_amount'],
                            'fee_amount'            => CRM_Utils_Array::value( 'fee_amount', $result, 0 ),
                            'net_amount'            => CRM_Utils_Array::value( 'net_amount', $result, 0 ),
                            'trxn_id'               => $result['trxn_id'],
                            'invoice_id'            => $this->_params['invoiceID'],
                            'currency'              => $this->_params['currencyID'],
                            'receipt_date'          => $receiptDate,
                            'source'                => ts( 'Online Contribution:' ) . ' ' . $this->_values['title'],
                            );
            
            $ids = array( );
            $contribution =& CRM_Contribute_BAO_Contribution::add( $params, $ids );
            
            // next create the transaction record
            $params = array(
                            'entity_table'      => 'civicrm_contribution',
                            'entity_id'         => $contribution->id,
                            'trxn_date'         => $now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $result['gross_amount'],
                            'fee_amount'        => CRM_Utils_Array::value( 'fee_amount', $result, 0 ),
                            'net_amount'        => CRM_Utils_Array::value( 'net_amount', $result, 0 ),
                            'currency'          => $this->_params['currencyID'],
                            'payment_processor' => $config->paymentProcessor,
                            'trxn_id'           => $result['trxn_id'],
                            );
            
            require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
            $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $params );
            
            // also create an activity history record
            $params = array('entity_table'     => 'civicrm_contact', 
                            'entity_id'        => $contactID, 
                            'activity_type'    => $contributionType->name,
                            'module'           => 'CiviContribute', 
                            'callback'         => 'CRM_Contribute_Page_Contribution::details',
                            'activity_id'      => $contribution->id, 
                            'activity_summary' => 'Online - $' . $this->_params['amount'],
                            'activity_date'    => $now,
                            );
            if ( is_a( crm_create_activity_history($params), 'CRM_Core_Error' ) ) { 
                CRM_Core_Error::fatal( "Could not create a system record" );
            }

            CRM_Core_DAO::transaction( 'COMMIT' );
        }

        // finally send an email receipt
        if ( $this->_values['is_email_receipt'] ) {
            if ( $this->_action != 1024 ) {
                list( $displayName, $email ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID );
            } else {
                list( $displayName, $email ) = array( $this->get( 'name' ), $this->_params['email'] );
            }

            $template =& CRM_Core_Smarty::singleton( );
            $subject = trim( $template->fetch( 'CRM/Contribute/Form/Contribution/ReceiptSubject.tpl' ) );
            $message = $template->fetch( 'CRM/Contribute/Form/Contribution/ReceiptMessage.tpl' );
            
            $this->_values['receipt_from_email'] = $config->paymentResponseEmail;

            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $this->_values['receipt_from_email'],
                                  $displayName,
                                  $email,
                                  $subject,
                                  $message,
                                  $this->_values['cc_receipt'],
                                  $this->_values['bcc_receipt']
                                  );
        }
    }
}

?>
