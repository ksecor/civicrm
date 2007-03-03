<?php

/**
 * Copyright (C) 2006 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

 /* This is the response handler code that will be invoked every time
  * a notification or request is sent by the Google Server
  *
  * To allow this code to receive responses, the url for this file
  * must be set on the seller page under Settings->Integration as the
  * "API Callback URL'
  * Order processing commands can be sent automatically by placing these
  * commands appropriately
  *
  * To use this code for merchant-calculated feedback, this url must be
  * set also as the merchant-calculations-url when the cart is posted
  * Depending on your calculations for shipping, taxes, coupons and gift
  * certificates update parts of the code as required
  *
  */

  //  chdir("..");
session_start( );
//print_r($_SESSION);
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

$config =& CRM_Core_Config::singleton();

require_once('Google/library/googleresponse.php');
require_once('Google/library/googlemerchantcalculations.php');
require_once('Google/library/googleresult.php');

//define('RESPONSE_HANDLER_LOG_FILE', 'googlemessage.log');
define('RESPONSE_HANDLER_LOG_FILE', $config->uploadDir . 'CiviCRM.log');

//Setup the log file
if (!$message_log = fopen(RESPONSE_HANDLER_LOG_FILE, "a")) {
    error_func("Cannot open " . RESPONSE_HANDLER_LOG_FILE . " file.\n", 0);
    exit(1);
 }

// Retrieve the XML sent in the HTTP POST request to the ResponseHandler
$xml_response = $HTTP_RAW_POST_DATA;
if (get_magic_quotes_gpc()) {
    $xml_response = stripslashes($xml_response);
 }
$headers = getallheaders();
fwrite($message_log, sprintf("\n\r%s:- %s\n",date("D M j G:i:s T Y"),
                             $xml_response));

// Create new response object
$merchant_id  = '559999327053114';         //Your Merchant ID
$merchant_key = 'R2zv2g60-A7GXKJYl0nR0g';  //Your Merchant Key
$server_type  = "sandbox";                 //provision for live

$response = new GoogleResponse($merchant_id, $merchant_key,
                               $xml_response, $server_type);
$root = $response->root;
$data = $response->data;
fwrite($message_log, sprintf("\n\r%s:- %s\n",date("D M j G:i:s T Y"),
                             $response->root));

//Use the following two lines to log the associative array storing the XML data
//$result = print_r($data,true);
//fwrite($message_log, sprintf("\n\r%s:- %s\n",date("D M j G:i:s T Y"),$result));

//Check status and take appropriate action
$status = $response->HttpAuthentication($headers);

/* Commands to send the various order processing APIs
 * Send charge order : $response->SendChargeOrder($data[$root]
 *    ['google-order-number']['VALUE'], <amount>, $message_log);
 * Send proces order : $response->SendProcessOrder($data[$root]
 *    ['google-order-number']['VALUE'], $message_log);
 * Send deliver order: $response->SendDeliverOrder($data[$root]
 *    ['google-order-number']['VALUE'], <carrier>, <tracking-number>,
 *    <send_mail>, $message_log);
 * Send archive order: $response->SendArchiveOrder($data[$root]
 *    ['google-order-number']['VALUE'], $message_log);
 *
 */

switch ($root) {
 case "request-received": {
     break;
 }
 case "error": {
     break;
 }
 case "diagnosis": {
     break;
 }
 case "checkout-redirect": {
     break;
 }
 case "merchant-calculation-callback": {
     break;
 }
 case "new-order-notification": {
     $response->SendAck();
     
     $privateData = $data[$root]['shopping-cart']['merchant-private-data']['VALUE'];
     $privateData = stringToArray($privateData);
     
     CRM_Core_Error::debug_log_message( "\n private data preserved in new-order-notification: contactID=" . $privateData['contactID'] . '\n');
     //$_SESSION['contactID'] = $privateData['contactID'];
     //main($privateData, $data[$root]);
     break;
 }
 case "order-state-change-notification": {
     $response->SendAck();
     $new_financial_state = $data[$root]['new-financial-order-state']['VALUE'];
     $new_fulfillment_order = $data[$root]['new-fulfillment-order-state']['VALUE'];

     //CRM_Core_Error::debug_log_message( "\n private data preserved in order-state-change: contactID=" . $_SESSION['contactID'] . '\n');
     
     switch($new_financial_state) {
     case 'REVIEWING': {
         break;
     }
     case 'CHARGEABLE': {
//          $response->SendChargeOrder($data[$root]['google-order-number']['VALUE'], 
//                                     0.5, $message_log);
//          $response->SendProcessOrder($data[$root]['google-order-number']['VALUE'], 
//                                      $message_log);
         break;
     }
     case 'CHARGING': {
         break;
     }
     case 'CHARGED': {
         break;
        }
     case 'PAYMENT_DECLINED': {
         break;
     }
     case 'CANCELLED': {
         break;
     }
     case 'CANCELLED_BY_GOOGLE': {
         //$response->SendBuyerMessage($data[$root]['google-order-number']['VALUE'],
         //    "Sorry, your order is cancelled by Google", true, $message_log);
         break;
     }
     default:
         break;
      }
     
     switch($new_fulfillment_order) {
     case 'NEW': {
         break;
     }
     case 'PROCESSING': {
         break;
     }
     case 'DELIVERED': {
         break;
     }
     case 'WILL_NOT_DELIVER': {
         break;
     }
     default:
         break;
     }
 }
 case "charge-amount-notification": {
     $response->SendAck();
     //$response->SendDeliverOrder($data[$root]['google-order-number']['VALUE'], 
     //    <carrier>, <tracking-number>, <send-email>, $message_log);
     //$response->SendArchiveOrder($data[$root]['google-order-number']['VALUE'], 
     //    $message_log);

//      $response->SendDeliverOrder($data[$root]['google-order-number']['VALUE'], 
//                                  'UPS', 'Z9842W69871281267', "false", $message_log);
     break;
 }
 case "chargeback-amount-notification": {
     $response->SendAck();
     break;
 }
 case "refund-amount-notification": {
     $response->SendAck();
     break;
 }
 case "risk-information-notification": {
     $response->SendAck();
     break;
 }
 default: {
     break;
 }
 }
/* In case the XML API contains multiple open tags
 with the same value, then invoke this function and
 perform a foreach on the resultant array.
 This takes care of cases when there is only one unique tag
 or multiple tags.
 Examples of this are "anonymous-address", "merchant-code-string"
 from the merchant-calculations-callback API
*/
function get_arr_result($child_node) {
    $result = array();
    if(isset($child_node)) {
        if(is_associative_array($child_node)) {
            $result[] = $child_node;
        }
        else {
            foreach($child_node as $curr_node){
                $result[] = $curr_node;
            }
        }
    }
    return $result;
}

/* Returns true if a given variable represents an associative array */
function is_associative_array( $var ) {
    return is_array( $var ) && !is_numeric( implode( '', array_keys( $var ) ) );
}

function stringToArray($str) {
    $vars = $labels = array();
    $labels = explode(',', $str);
    foreach ($labels as $label) {
        $terms = explode('=', $label);
        $vars[$terms[0]] = $terms[1];
    }
    return $vars;
}

function main($params, $dataRoot) {
    $contactID          = $params['contactID'];
    $contributionID     = $params['contributionID'];
    $contributionTypeID = $params['contributionTypeID'];
    
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
    
    // make sure contribution type exists and is valid
    require_once 'CRM/Contribute/DAO/ContributionType.php';
    $contributionType =& new CRM_Contribute_DAO_ContributionType( );
    $contributionType->id = $contributionTypeID;
    if ( ! $contributionType->find( true ) ) {
        CRM_Core_Error::debug_log_message( "Could not find contribution type record: $contributionTypeID" );
        echo "Failure: Could not find contribution type record for $contributionTypeID<p>";
        return;
    }
    return single( $contactID, $contribution, $contributionType, false, false, $dataRoot );
}

function single( $contactID, &$contribution, &$contributionType, $recur = false, $first = false, $dataRoot ) {
    //$store = null;

    $privateData = $dataRoot['shopping-cart']['merchant-private-data']['VALUE'];
    $privateData = stringToArray($privateData);
    
    $membershipTypeID   = $privateData['membershipTypeID'];
    
    // make sure the invoice is valid and matches what we have in the contribution record
    $invoice             = $privateData['invoiceID'];
    if ( $contribution->invoice_id != $invoice ) {
        CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
        echo "Failure: Invoice values dont match between database and IPN request<p>";
        return;
    }
    
    $now = date( 'YmdHis' );
    $amount =  $dataRoot['order-total'];
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
        $value = $dataRoot['buyer-billing-address'][$googleName];
        if ( $value ) {
            $params[$name] = $value;
        } else {
            $params[$name] = null;
        }
    }
    
    if ( ! empty( $params ) ) {
        // update contact record
        $idParams = array( 'id'         => $contactID, 
                           'contact'    => $contactID );
        $ids = $defaults = array( );
        require_once "CRM/Contact/BAO/Contact.php";
        CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
        $contact = CRM_Contact_BAO_Contact::createFlat($params, $ids );
    }
    
    // lets keep this the same
    $contribution->receive_date = CRM_Utils_Date::isoToMysql($contribution->receive_date); 
    
//     $status = self::retrieve( 'payment_status', 'String', 'POST', true );
//     if ( $status == 'Denied' || $status == 'Failed' || $status == 'Voided' ) {
//         $contribution->contribution_status_id = 4;
//         $contribution->save( );
//         CRM_Core_DAO::transaction( 'COMMIT' );
//         CRM_Core_Error::debug_log_message( "Setting contribution status to failed" );
//         echo "Success: Setting contribution status to failed<p>";
//         return;
//     } else if ( $status == 'Pending' ) {
//         CRM_Core_Error::debug_log_message( "returning since contribution status is pending" );
        
//         echo "Success: Returning since contribution status is pending<p>";
//         return;
//     } else if ( $status == 'Refunded' || $status == 'Reversed' ) {
//         $contribution->contribution_status_id = 3;
//         $contribution->cancel_date = $now;
//         $contribution->cancel_reason = self::retrieve( 'ReasonCode', 'String', 'POST', false );
//         $contribution->save( );
//         CRM_Core_DAO::transaction( 'COMMIT' );
//         CRM_Core_Error::debug_log_message( "Setting contribution status to cancelled" );
//         echo "Success: Setting contribution status to cancelled<p>";
//         return;
//     } else if ( $status != 'Completed' ) {
//         // we dont handle this as yet
//         CRM_Core_Error::debug_log_message( "returning since contribution status: $status is not handled" );
//         echo "Failure: contribution status $status is not handled<p>";
//         return;
//     }
    
    // check if contribution is already completed, if so we ignore this ipn
    if ( $contribution->contribution_status_id == 1 ) {
        CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
        echo "Success: Contribution has already been handled<p>";
        return;
    }
    
    require_once 'CRM/Contribute/BAO/ContributionPage.php';
    CRM_Contribute_BAO_ContributionPage::setValues( $contribution->contribution_page_id, $values );
    
    $contribution->contribution_status_id  = 1;
    $contribution->source                  = ts( 'Online Contribution:' ) . ' ' . $values['title'];
    $contribution->is_test    = $privateData['test'] ? 1 : 0;
    $contribution->fee_amount = $dataRoot['fee_amount']; //not available
    $contribution->net_amount = $dataRoot['net_amount']; //not available
    $contribution->trxn_id    = $dataRoot['trnx_id'];    //not available
    
    if ( $values['is_email_receipt'] ) {
        $contribution->receipt_date = $now;
    }
    
    CRM_Core_DAO::transaction( 'BEGIN' );
    
    $contribution->save( );
    
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
    
    // get the title of the contribution page
    $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                          $contribution->contribution_page_id,
                                          'title' );
    
    require_once 'CRM/Utils/Money.php';
    $formattedAmount = CRM_Utils_Money::format($amount);
    
    // also create an activity history record
    $ahParams = array('entity_table'     => 'civicrm_contact', 
                      'entity_id'        => $contactID, 
                      'activity_type'    => $contributionType->name,
                      'module'           => 'CiviContribute', 
                      'callback'         => 'CRM_Contribute_Page_Contribution::details',
                      'activity_id'      => $contribution->id, 
                      'activity_summary' => "$formattedAmount - $title (online)",
                      'activity_date'    => $now,
                      );
    
    require_once 'api/History.php';
    if ( is_a( crm_create_activity_history($ahParams), 'CRM_Core_Error' ) ) { 
        CRM_Core_Error::debug_log_message( "error in updating activity" );
    }
    
    // create membership record
    if ($membershipTypeID) {
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign('membership_assign' , true );
        
        require_once 'CRM/Member/BAO/Membership.php';
        require_once 'CRM/Member/DAO/MembershipLog.php';
        require_once 'CRM/Member/BAO/MembershipType.php';
        $membershipDetails = CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $membershipTypeID );
        $template->assign('membership_name',$membershipDetails['name']);
        
        $minimumFee = $membershipDetails['minimum_fee'];
        $template->assign('membership_amount'  , $minimumFee);
        
        if ($currentMembership = CRM_Member_BAO_Membership::getContactMembership($contactID,  $membershipTypeID)) {
            if ( ! $currentMembership['is_current_member'] ) {
                $dao = &new CRM_Member_DAO_Membership();
                $dates = CRM_Member_BAO_MembershipType::getRenewalDatesForMembershipType( $currentMembership['id']);
                $currentMembership['start_date'] = CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                $currentMembership['end_date']   = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d');
                $currentMembership['source']     = self::retrieve( 'item_name', 'String', 'POST', false );
                $dao->copyValues($currentMembership);
                $membership = $dao->save();
                
                //insert log here 
                $dao = new CRM_Member_DAO_MembershipLog();
                $dao->membership_id = $membership->id;
                $dao->status_id     = $membership->status_id;
                $dao->start_date    = CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                $dao->end_date      = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'); 
                $dao->modified_id   = $contactID;
                $dao->modified_date = date('Ymd');
                $dao->save();
                
                $template->assign('mem_start_date', CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                $template->assign('mem_end_date',   CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                
            } else {
                $dao = &new CRM_Member_DAO_Membership();
                $dao->id = $currentMembership['id'];
                $dao->find(true); 
                $membership = $dao ;
                
                //insert log here 
                $dates = CRM_Member_BAO_MembershipType::getRenewalDatesForMembershipType( $membership->id);
                $dao = new CRM_Member_DAO_MembershipLog();
                $dao->membership_id = $membership->id;
                $dao->status_id     = $membership->status_id;
                $dao->start_date    = CRM_Utils_Date::customFormat($dates['log_start_date'],'%Y%m%d');
                $dao->end_date      = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'); 
                $dao->modified_id   = $contactID;
                $dao->modified_date = date('Ymd');
                $dao->save();
                
                $template->assign('mem_start_date', CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                $template->assign('mem_end_date',   CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                
            }
        } else {
            require_once 'CRM/Member/BAO/MembershipStatus.php';
            $memParams = array();
            $memParams['contact_id']             = $contactID;
            $memParams['membership_type_id']     = $membershipTypeID;
            $dates = CRM_Member_BAO_MembershipType::getDatesForMembershipType($membershipTypeID);
            
            $memParams['join_date']     = CRM_Utils_Date::customFormat($dates['join_date'],'%Y%m%d');
            $memParams['start_date']    = CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
            $memParams['end_date']      = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d');
            $memParams['reminder_date'] = CRM_Utils_Date::customFormat($dates['reminder_date'],'%Y%m%d'); 
            $memParams['source'  ]      = CRM_Utils_Request::retrieve( 'item_name', 'String', $store,
                                                                       false, 0, 'POST' );
            $status = CRM_Member_BAO_MembershipStatus::getMembershipStatusByDate( CRM_Utils_Date::customFormat($dates['start_date'],'%Y-%m-%d'),CRM_Utils_Date::customFormat($dates['end_date'],'%Y-%m-%d'),CRM_Utils_Date::customFormat($dates['join_date'],'%Y-%m-%d')) ;
            
            $memParams['status_id']   = $status['id'];
            $memParams['is_override'] = false;
            $dao = &new CRM_Member_DAO_Membership();
            $dao->copyValues($memParams);
            $membership = $dao->save();
            $template->assign('mem_start_date',  CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
            $template->assign('mem_end_date', CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
        }
        require_once 'CRM/Member/DAO/MembershipBlock.php';
        $dao = & new CRM_Member_DAO_MembershipBlock();
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id = $contribution->contribution_page_id; 
        $dao->is_active = 1;
        if ( $dao->find(true) ) {
            $membershipBlock   = array(); 
            CRM_Core_DAO::storeValues($dao, $membershipBlock );
            $template->assign( 'membershipBlock' , $membershipBlock );
        }
    }
    
    require_once 'CRM/Contribute/BAO/ContributionPage.php';
    
    CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
    CRM_Core_DAO::transaction( 'COMMIT' );
    
    // add the new contribution values
    $template =& CRM_Core_Smarty::singleton( );
    $template->assign( 'title', $values['title']);
    $template->assign( 'amount' , $amount );
    $template->assign( 'trxn_id', $contribution->trxn_id );
    $template->assign( 'receive_date', 
                       CRM_Utils_Date::mysqlToIso( $contribution->receive_date ) );
    $template->assign( 'contributeMode', 'notify' );
    $template->assign( 'action', $contribution->is_test ? 1024 : 1 );
    $template->assign( 'receipt_text', $values['receipt_text'] );
    $template->assign( 'is_monetary', 1 );
    $template->assign( 'is_recur', $recur );

    require_once 'CRM/Utils/Address.php';
    $template->assign( 'address', CRM_Utils_Address::format( $params ) );
    
    CRM_Contribute_BAO_ContributionPage::sendMail( $contactID, $values );
    
    echo "Success: Database updated<p>";
}

?>