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
     newOrderNotify($data[$root]);

     break;
 }
 case "order-state-change-notification": {
     $response->SendAck();
     $new_financial_state = $data[$root]['new-financial-order-state']['VALUE'];
     $new_fulfillment_order = $data[$root]['new-fulfillment-order-state']['VALUE'];

     switch($new_financial_state) {
     case 'REVIEWING': {
         break;
     }
     case 'CHARGEABLE': {
         $orderNo = $data[$root]['google-order-number']['VALUE'];
         $amount = getAmount($orderNo);
         if ($amount) {
             $response->SendChargeOrder($data[$root]['google-order-number']['VALUE'], 
                                        $amount, $message_log);
             $response->SendProcessOrder($data[$root]['google-order-number']['VALUE'], 
                                         $message_log);
         }
         break;
     }
     case 'CHARGING': {
         break;
     }
     case 'CHARGED': {
         orderStateChange('CHARGED', $data[$root]);
         break;
        }
     case 'PAYMENT_DECLINED': {
         orderStateChange('PAYMENT_DECLINED', $data[$root]);
         break;
     }
     case 'CANCELLED': {
         orderStateChange('CANCELLED', $data[$root]);
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

function newOrderNotify($dataRoot) {
    $params = $dataRoot['shopping-cart']['merchant-private-data']['VALUE'];
    $params = stringToArray($params);
    
    $contactID          = $params['contactID'];
    $contributionID     = $params['contributionID'];
    
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
    
    // make sure the invoice is valid and matches what we have in the contribution record
    $invoice = $params['invoiceID'];
    if ( $contribution->invoice_id != $invoice ) {
        CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
        echo "Failure: Invoice values dont match between database and IPN request<p>";
        return;
    } else {
        // lets replace invoice-id with google-order-number because thats what is common and unique in subsequent call or notification send by google.
        $contribution->invoice_id = $dataRoot['google-order-number']['VALUE'];
    }
    
    $now = date( 'YmdHis' );
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
        $idParams = array( 'id'         => $contactID, 
                           'contact'    => $contactID );
        $ids = $defaults = array( );
        require_once "CRM/Contact/BAO/Contact.php";
        CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
        $contact = CRM_Contact_BAO_Contact::createFlat($params, $ids );
    }
    
    // lets keep this the same
    $contribution->receive_date = CRM_Utils_Date::isoToMysql($contribution->receive_date); 

    // check if contribution is already completed, if so we ignore this ipn
    if ( $contribution->contribution_status_id == 1 ) {
        CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
        echo "Success: Contribution has already been handled<p>";
        return;
    }
    
    $contribution->save( );
}
function getAmount($orderNo) {
    require_once 'CRM/Contribute/DAO/Contribution.php';
    $contribution =& new CRM_Contribute_DAO_Contribution( );
    $contribution->invoice_id = $orderNo;
    if ( ! $contribution->find( true ) ) {
        CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
        echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
        return;
    }
    return $contribution->total_amount;
}
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

    if ( $status == 'PAYMENT_DECLINED' || $status == 'CANCELLED_BY_GOOGLE' || $status == 'CANCELLED' ) {        $contribution->contribution_status_id = 4;
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
    $now = date( 'YmdHis' );
    $amount = $contribution->total_amount;

    require_once 'CRM/Contribute/BAO/ContributionPage.php';
    CRM_Contribute_BAO_ContributionPage::setValues( $contribution->contribution_page_id, $values );
    
    $contribution->contribution_status_id  = 1;
    $contribution->source                  = ts( 'Online Contribution:' ) . ' ' . $values['title'];
    //$contribution->is_test    = $privateData['test'] ? 1 : 0; //since this is done before checkout
    $contribution->fee_amount = $dataRoot['fee_amount']['VALUE']; //not available
    $contribution->net_amount = $dataRoot['net_amount']['VALUE']; //not available
    $contribution->trxn_id    = $dataRoot['trnx_id']['VALUE'];    //not available
    
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

    //need to update membership record.
    CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
    CRM_Core_DAO::transaction( 'COMMIT' );
}
?>