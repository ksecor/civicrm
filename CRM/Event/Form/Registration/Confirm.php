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
        parent::preProcess( );

        $config =& CRM_Core_Config::singleton( );
        if ( $this->_contributeMode == 'express' ) {
            // rfp == redirect from paypal
            $rfp = CRM_Utils_Request::retrieve( 'rfp', 'Boolean',
                                                CRM_Core_DAO::$_nullObject, false, null, 'GET' );
            if ( $rfp ) {
                //require_once 'CRM/Contribute/Payment.php'; 
                require_once 'CRM/Core/Payment.php'; 
                $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event' );
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
                $this->_params['amount_level'  ] = $this->get( 'amount_level' );
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
            if ( $this->_values['event']['is_monetary'] ) {
                $this->_params['ip_address']     = $_SERVER['REMOTE_ADDR']; 
                
                $this->_params['amount'        ] = $this->get( 'amount' );
                $this->_params['amount_level'  ] = $this->get( 'amount_level' );
                $this->_params['currencyID'    ] = $config->defaultCurrency;
                $this->_params['payment_action'] = 'Sale';
            }
        }
        
        if ( $this->_values['event']['is_monetary'] ) {
            $this->_params['invoiceID'] = $this->get( 'invoiceID' );
        }
        $this->set( 'params', $this->_params );
    }

    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->assignToTemplate( );
        
        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );

        $contribButton = ts('Make Contribution');
        if ( $this->_contributeMode == 'notify' ) {
            $contribButton = ts('Continue');
        }
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => $contribButton,
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true,
                                        'js'        => array( 'onclick' => "return submitOnce(this,'Confirm','" . ts('Processing') ."');" ) ),
                                array ( 'type'      => 'back',
                                        'name'      => ts('<< Go Back')),
                                )
                          );

        
        $defaults = array( );
        $fields = array( );
        foreach ( $this->_fields as $name => $dontCare ) {
            $fields[$name] = 1;
        }
        $fields['state_province'] = $fields['country'] = $fields['email'] = 1;
        foreach ($fields as $name => $dontCare ) {
            if ( $this->_params[$name] ) {
                    $defaults[$name] = $this->_params[$name];
            }
        }
        $this->setDefaults( $defaults );
        
        $this->freeze();
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Event/BAO/Participant.php';

        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        $now = date( 'YmdHis' );
        
        $params = $this->_params;
        $fields = array( );

        $this->fixLocationFields( $params, $fields );
        
        $contactID =& $this->updateContactFields( $contactID, $params, $fields );
        
        // required only if paid event
        if ( $this->_values['event']['is_monetary'] ) {
            require_once 'CRM/Core/Payment.php';
            $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event' );

            switch ( $this->_contributeMode ) {
            case 'express':
                $result =& $payment->doExpressCheckout( $this->_params );
                break;
            case 'notify':
                $this->_params['contactID'] = $contactID;
                $this->_params['eventID']   = $this->_id;
                
                $contribution =& $this->processContribution( $this->_params, null, $contactID, true );
                $this->_params['contributionID'    ] = $contribution->id;
                $this->_params['contributionTypeID'] = $contributionType->id;
                $this->_params['item_name'         ] = ts( 'Online Event Registration:' ) . ' ' . $this->_values['event']['title'];
                $this->_params['receive_date'      ] = $now;
                
//                 $participant  =& $this->addParticipant( $this->_params, $contactID );
//                 CRM_Event_BAO_Participant::setActivityHistory( $participant );
                
                $result =& $payment->doTransferCheckout( $this->_params );
                break;
            default   :
                $result =& $payment->doDirectPayment( $this->_params );
                break;
            }
            
            if ( is_a( $result, 'CRM_Core_Error' ) ) {
                CRM_Core_Error::displaySessionError( $result );
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/admin/event/register', '_qf_Main_display=true' ) );
            }
            
            if ( $result ) {
                $this->_params = array_merge( $this->_params, $result );
            }
            
            // transactionID & receive date required while building email template
            $this->assign( 'trxn_id', $result['trxn_id'] );
            $this->assign( 'receive_date', CRM_Utils_Date::mysqlToIso( $this->_params['receive_date']) );
            
            $this->_params['receive_date'] = $now;
            
            // if paid event add a contribution record
            $contribution =& $this->processContribution( $this->_params, $result, $contactID );
        }
        
        $this->set( 'params', $this->_params );
        
        // insert participant record
        $participant  =& $this->addParticipant( $this->_params, $contactID );
        
        // insert activity record
        CRM_Event_BAO_Participant::setActivityHistory( $participant );
        
        require_once "CRM/Event/BAO/EventPage.php";
        CRM_Event_BAO_EventPage::sendMail( $contactID, $this->_values['event_page'] );

    }//end of function
    
    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    public function addParticipant( $params, $contactID ) 
    {
        CRM_Core_DAO::transaction( 'BEGIN' );

        $domainID = CRM_Core_Config::domainID( );
        $groupName = "participant_role";
        $query = "
SELECT  v.label as label ,v.value as value
FROM   civicrm_option_value v, 
       civicrm_option_group g 
WHERE  v.option_group_id = g.id 
  AND  g.domain_id       = $domainID 
  AND  g.name            = %1 
  AND  v.is_active       = 1  
  AND  g.is_active       = 1  
";
        $p = array( 1 => array( $groupName , 'String' ) );

        $dao =& CRM_Core_DAO::executeQuery( $query, $p );
        if ( $dao->fetch( ) ) {
            $roleID = $dao->value;
        }
        
        $participantParams = array('contact_id'    => $contactID,
                                   'event_id'      => $this->_id,
                                   'status_id'     => 1,
                                   'role_id'       => $roleID,
                                   'register_date' => date( 'YmdHis' ),
                                   'source'        => ts( 'Online Event Registration:' ) . ' ' . $this->_values['event']['title'],
                                   'event_level'   => $params['amount_level']
                                   );
        
        if( $this->_action & CRM_Core_Action::PREVIEW ) {
            $participantParams['is_test'] = 1;
        }
        
        $ids = array();
        $participant = CRM_Event_BAO_Participant::add($participantParams, $ids);
        
        CRM_Core_DAO::transaction( 'COMMIT' );

        return $participant;
    }

    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    public function processContribution( $params, $result, $contactID, $pending = false ) 
    {
        CRM_Core_DAO::transaction( 'BEGIN' );

        $config =& CRM_Core_Config::singleton( );
        $now         = date( 'YmdHis' );
        $receiptDate = null;
        
        if ( $this->_values['event_page']['is_email_confirm'] ) {
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
                               'amount_level'          => $params['amount_level'],
                               'invoice_id'            => $params['invoiceID'],
                               'currency'              => $params['currencyID'],
                               'source'                => ts( 'Online Event Registration:' ) . ' ' . $this->_values['event']['title']
                               );
        
        if ( ! $pending && $result ) {
            $contribParams += array(
                                    'fee_amount'   => CRM_Utils_Array::value( 'fee_amount', $result ),
                                    'net_amount'   => CRM_Utils_Array::value( 'net_amount', $result, $params['amount'] ),
                                    'trxn_id'      => $result['trxn_id'],
                                    'receipt_date' => $receiptDate,
                                    );
        }

        $contribParams["contribution_status_id"] = $pending ? 2 : 1;

        if( $this->_action & CRM_Core_Action::PREVIEW ) {
            $contribParams["is_test"] = 1;
        }
        
        $ids = array( );
        $contribution =& CRM_Contribute_BAO_Contribution::add( $contribParams, $ids );
       
        // return if pending
        if ( $pending ) {
            return $contribution;
        }
        
        // next create the transaction record
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

        CRM_Core_DAO::transaction( 'COMMIT' );
        
        return $contribution;
    }
    
    /**
     * Fix the Location Fields
     *
     * @return void
     * @access public
     */
    public function fixLocationFields( &$params, &$fields ) 
    {
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
        }
    }
    
    /**
     * function to update contact fields
     *
     * @return void
     * @access public
     */
    public function updateContactFields( $contactID, $params, $fields ) 
    {
        require_once "CRM/Contact/BAO/Contact.php";

        if ($contactID) {
            $ctype = CRM_Core_DAO::getFieldValue("CRM_Contact_DAO_Contact", $contactID, "contact_type");
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contactID, null, null,$ctype);
        } else {
            // finding contact record based on duplicate match 
            require_once 'api/crm.php';
            $ids = CRM_Core_BAO_UFGroup::findContact( $params );
            $contactsIDs = explode( ',', $ids );
            
            // if we find more than one contact, use the first one
            $contact_id  = $contactsIDs[0];
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contact_id );
            $this->set( 'contactID', $contactID );
        }

        return $contactID;
    }

}
?>
