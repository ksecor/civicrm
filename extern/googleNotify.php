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

session_start( );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

$config =& CRM_Core_Config::singleton();

$config->userFramework          = 'Soap';
$config->userFrameworkClass     = 'CRM_Utils_System_Soap';
$config->userHookClass          = 'CRM_Utils_Hook_Soap';

require_once('Google/library/googleresponse.php');
require_once('Google/library/googlemerchantcalculations.php');
require_once('Google/library/googleresult.php');
require_once('Google/library/xml-processing/xmlparser.php');

require_once('CRM/Core/Payment/GoogleIPN.php');

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

// Retrieve the root and data from the xml response
$xmlParser = new XmlParser($xml_response);
$root      = $xmlParser->GetRoot();
$data      = $xmlParser->GetData();

$orderNo   = $data[$root]['google-order-number']['VALUE'];

// lets retrieve the private-data
$privateData = $data[$root]['shopping-cart']['merchant-private-data']['VALUE'];
$privateData = $privateData ? stringToArray($privateData) : '';

$mode   = CRM_Core_Payment_GoogleIPN::getMode($xml_response, $privateData, $orderNo, $root);
$mode   = $mode ? 'test' : 'live';

$module = CRM_Core_Payment_GoogleIPN::getModule($xml_response, $privateData, $orderNo, $root);

$ipn    =& CRM_Core_Payment_GoogleIPN::singleton( $mode, $module );

// Create new response object
$merchant_id  = $config->merchantID[$mode];  //Your Merchant ID
$merchant_key = $config->paymentKey[$mode];  //Your Merchant Key
$server_type  = ($mode == 'test') ? "sandbox" : '';

$response = new GoogleResponse($merchant_id, $merchant_key,
                               $xml_response, $server_type);
fwrite($message_log, sprintf("\n\r%s:- %s\n",date("D M j G:i:s T Y"),
                             $response->root));

//Check status and take appropriate action
$status = $response->HttpAuthentication($headers);

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
     $ipn->newOrderNotify($data[$root], $privateData);
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
         $amount = $ipn->getAmount($orderNo);
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
         $ipn->orderStateChange('CHARGED', $data[$root]);
         break;
     }
     case 'PAYMENT_DECLINED': {
         $ipn->orderStateChange('PAYMENT_DECLINED', $data[$root]);
         break;
     }
     case 'CANCELLED': {
         $ipn->orderStateChange('CANCELLED', $data[$root]);
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
     //      $response->SendDeliverOrder($data[$root]['google-order-number']['VALUE'], 
     //                                  <carrier>, <tracking-number>, <send-email>, $message_log);
     //      $response->SendArchiveOrder($data[$root]['google-order-number']['VALUE'], 
     //                                  $message_log);
     
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

/* Converts the comma separated name-value pairs in <merchant-private-data> 
 * to an array of name-value pairs.
 */
function stringToArray($str) {
    $vars = $labels = array();
    $labels = explode(',', $str);
    foreach ($labels as $label) {
        $terms = explode('=', $label);
        $vars[$terms[0]] = $terms[1];
    }
    return $vars;
}

?>