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

require_once 'CRM/Core/Payment/GoogleIPN.php';

class CRM_Event_Payment_GoogleIPN extends CRM_Core_Payment_GoogleIPN {
    /** 
     * We only need one instance of this object. So we use the singleton 
     * pattern and cache the instance in this variable 
     * 
     * @var object 
     * @static 
     */ 
    static private $_singleton = null; 
    
    /** 
     * Constructor 
     * 
     * @param string $mode the mode of operation: live or test
     *
     * @return void 
     */ 
    function __construct( $mode ) {
        parent::__construct( $mode );
    }

    /** 
     * singleton function used to manage this object 
     * 
     * @param string $mode the mode of operation: live or test
     * 
     * @return object 
     * @static 
     * 
     */ 
    static function &singleton( $mode ) {
        if (self::$_singleton === null ) { 
            self::$_singleton =& new CRM_Event_Payment_GoogleIPN( $mode );
        } 
        return self::$_singleton; 
    } 

    /**  
     * The function gets called when a new order takes place. And does all the preliminary operations.
     *  
     * @param xml   $dataRoot    response send by google in xml format
     * @param array $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     * @access public 
     *  
     */  
    function newOrderNotify($dataRoot, $privateData) {
        $contactID          = $privateData['contactID'];
        $contributionID     = $privateData['contributionID'];
        $eventID            = $privateData['eventID'];

        // make sure contact exists and is valid
        require_once 'CRM/Contact/DAO/Contact.php';
        $contact =& new CRM_Contact_DAO_Contact( );
        $contact->id = $contactID;
        if ( ! $contact->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contact record: $contactID" );
            echo "Failure: Could not find contact record: $contactID<p>";
            return;
        }
        
        // make sure contribution exists and is valid
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->id = $contributionID;
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
            echo "Failure: Could not find contribution record for $contributionID<p>";
            return;
        }

        // make sure event exists and is valid
        require_once 'CRM/Event/DAO/Event.php';
        $event =& new CRM_Event_DAO_Event( );
        $event->id = $eventID;
        if ( ! $event->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find event: $eventID" );
            echo "Failure: Could not find event: $eventID<p>";
            return;
        }
        
        // make sure the invoice is valid and matches what we have in the contribution record
        $invoice = $privateData['invoiceID'];
        if ( $contribution->invoice_id != $invoice ) {
            CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
            echo "Failure: Invoice values dont match between database and IPN request<p>";
            return;
        } else {
            // lets replace invoice-id with google-order-number because thats what is common and unique 
            // in subsequent calls or notifications send by google.
            $contribution->invoice_id = $dataRoot['google-order-number']['VALUE'];
        }
        
        $amount =  $dataRoot['order-total']['VALUE'];
        
        if ( $contribution->total_amount != $amount ) {
            CRM_Core_Error::debug_log_message( "Amount values dont match between database and IPN request" );
            echo "Failure: Amount values dont match between database and IPN request<p>";
            return;
        }
        
        // ok we are done with error checking, now let the real work begin
        // update the contact record with the name and address
        $params = array( );
        $lookup = array( 'first_name'     => 'contact-name',
                         'last_name'      => 'last_name' , // not available with google (every thing in contact-name)
                         'street_address' => 'address1',
                         'city'           => 'city',
                         'state'          => 'region',
                         'postal_code'    => 'postal-code',
                         'country'        => 'country-code' );
        foreach ( $lookup as $name => $googleName ) {
            $value = $dataRoot['buyer-billing-address'][$googleName]['VALUE'];
            if ( $value ) {
                $params[$name] = $value;
            } else {
                $params[$name] = null;
            }
        }
        
        if ( ! empty( $params ) ) {
            // update contact record
            $idParams = array( 'id' => $contactID, 'contact_id' => $contactID );
//             $ids = $defaults = array( );
            require_once "CRM/Contact/BAO/Contact.php";
//             CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
//             $contact = CRM_Contact_BAO_Contact::createFlat($params, $ids );
            $contact = CRM_Contact_BAO_Contact::createFlat($params, $idParams );
        }
        
        // lets keep this the same
        $contribution->receive_date = CRM_Utils_Date::isoToMysql($contribution->receive_date); 
        
        // check if contribution is already completed, if so we ignore this ipn
        if ( $contribution->contribution_status_id == 1 ) {
            CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
            echo "Success: Contribution has already been handled<p>";
            return;
        }
        
        // Since trxn_id hasn't got any use here, lets make use of it by passing the eventID to next level. 
        // And change trxn_id to google-order-number before finishing db update
        if ( $eventID ) {
            $contribution->trxn_id = "eid" . $eventID;
        }
        
        $contribution->save( );
    }


    /**  
     * The function gets called when the state(CHARGED, CANCELLED..) changes for an order. 
     * And the operations are performed based on the state.
     *  
     * @param string $status     status of the transaction send by google
     * @param array  $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     * @access public 
     *  
     */  
    function orderStateChange($status, $dataRoot) {
        $orderNo = $dataRoot['google-order-number']['VALUE'];
        
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->invoice_id = $orderNo;
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
            echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
            return;
        }
        
        if ( $status == 'PAYMENT_DECLINED' || $status == 'CANCELLED_BY_GOOGLE' || $status == 'CANCELLED' ) {        
            $contribution->contribution_status_id = 4;
            $contribution->trxn_id = $orderNo;
            $contribution->save( );
            CRM_Core_DAO::transaction( 'COMMIT' );
            CRM_Core_Error::debug_log_message( "Setting contribution status to failed" );
            echo "Success: Setting contribution status to failed<p>";
            return;
        }
        
        require_once 'CRM/Contribute/DAO/ContributionType.php';
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->id = $contribution->contribution_type_id;
        if ( ! $contributionType->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution type record: $contributionTypeID" );
            echo "Failure: Could not find contribution type record for $contributionTypeID<p>";
            return;
        }
        
        // lets start since payment has been made
        $now       = date( 'YmdHis' );
        $contactID = $contribution->contact_id;
        $amount    = $contribution->total_amount;
        $eventID   = $contribution->trxn_id; 
        $eventID   = (int)str_replace('eid', "", $eventID);
        
        $eventParams = array( 'id' => $eventID );
        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::retrieve( $eventParams, $values['event'] );
        
        $eventParams = array( 'event_id' => $eventID );
        require_once 'CRM/Event/BAO/EventPage.php';
        CRM_Event_BAO_EventPage::retrieve( $eventParams, $values['page'] );

        $contribution->receive_date = CRM_Utils_Date::isoToMysql($contribution->receive_date); 
        
        $contribution->contribution_status_id  = 1;
        $contribution->source                  = ts( 'Online Event Registration:' ) . ' ' . $values['event']['title'];
        //     $contribution->is_test    = $privateData['test'] ? 1 : 0; //since this is done before checkout
        $contribution->fee_amount = $dataRoot['fee_amount']['VALUE']; //not available
        $contribution->net_amount = $dataRoot['net_amount']['VALUE']; //not available
        $contribution->trxn_id    = $dataRoot['google-order-number']['VALUE']; // storing google-order-no
        
        if ( $values['page']['is_email_confirm'] ) {
            $contribution->receipt_date = $now;
        }
        
        CRM_Core_DAO::transaction( 'BEGIN' );
        
        $contribution->save( );
        
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton( );
        
        // next create the transaction record
        $trxnParams = array(
                            'entity_table'      => 'civicrm_contribution',
                            'entity_id'         => $contribution->id,
                            'trxn_date'         => $now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $amount,
                            'fee_amount'        => $contribution->fee_amount,
                            'net_amount'        => $contribution->net_amount,
                            'currency'          => $contribution->currency,
                            'payment_processor' => $config->paymentProcessor,
                            'trxn_id'           => $contribution->trxn_id,
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );
        
        //create participant record
        require_once 'CRM/Event/BAO/Participant.php';
        
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
                                   'event_id'      => $eventID,
                                   'status_id'     => 1,
                                   'role_id'       => $roleID,
                                   'register_date' => $now,
                                   'source'        => ts( 'Online Event Registration:' ) . ' ' . $values['event']['title'],
                                   'event_level'   => $contribution->amount_level,
                                   'is_test'       => $contribution->is_test
                                   );
        
        $ids = array();
        $participant = CRM_Event_BAO_Participant::add($participantParams, $ids);

        // also create an activity history record
        CRM_Event_BAO_Participant::setActivityHistory( $participant );
        
        CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
        CRM_Core_DAO::transaction( 'COMMIT' );
        
        // add the new contribution values
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'title', $values['event']['title']);
        $template->assign( 'amount' , $amount );
        $template->assign( 'trxn_id', $contribution->trxn_id );
        $template->assign( 'receive_date', 
                           CRM_Utils_Date::mysqlToIso( $contribution->receive_date ) );
        $template->assign( 'contributeMode', 'notify' );
        $template->assign( 'action', $contribution->is_test ? 1024 : 1 );
        $template->assign( 'receipt_text', $values['receipt_text'] );
        $template->assign( 'is_monetary', 1 );

        require_once 'CRM/Utils/Address.php';
        $template->assign( 'address', CRM_Utils_Address::format( $params ) );
        
        require_once "CRM/Event/BAO/EventPage.php";
        CRM_Event_BAO_EventPage::sendMail( $contactID, $values['page'] );
        
        echo "Success: Database updated<p>";
    }
}

?>
