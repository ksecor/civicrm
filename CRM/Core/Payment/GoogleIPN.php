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

abstract class CRM_Core_Payment_GoogleIPN {

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    /**
     * mode of operation: live or test
     *
     * @var object
     * @static
     */
    static protected $_mode = null;
    
    /** 
     * Constructor 
     * 
     * @param string $mode the mode of operation: live or test
     *
     * @return void 
     */ 
    function __construct( $mode, &$paymentProcessor ) {
        $this->_mode = $mode;

        $this->_paymentProcessor = $paymentProcessor;
    }

    /**  
     * The function gets called when a new order takes place.
     *  
     * @param xml   $dataRoot    response send by google in xml format
     * @param array $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     * @abstract  
     *  
     */  
    abstract function newOrderNotify($dataRoot, $privateData);
    
    /**  
     * The function gets called when the state(CHARGED, CANCELLED..) changes for an order
     *  
     * @param string $status      status of the transaction send by google
     * @param array  $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     * @abstract  
     *  
     */  
    abstract function orderStateChange($status, $dataRoot);

    /**  
     * singleton function used to manage this object  
     *  
     * @param string $mode the mode of operation: live or test
     *  
     * @return object  
     * @static  
     */  
    static function &singleton( $mode = 'test', $component, &$paymentProcessor ) {
        if ( self::$_singleton === null ) {
            $config       =& CRM_Core_Config::singleton( );
            $paymentClass = "CRM_{$component}_" . $paymentProcessor['class_name'] . "IPN";
            
            $classPath = str_replace( '_', '/', $paymentClass ) . '.php';
            require_once($classPath);
            self::$_singleton = eval( 'return ' . $paymentClass . '::singleton( $mode, $paymentProcessor );' );
        }
        return self::$_singleton;
    }
    
    /**  
     * The function retrieves the amount the contribution is for, based on the order-no google sends
     *  
     * @param int $orderNo <order-total> send by google
     *  
     * @return amount  
     * @access public 
     */  
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

    /**  
     * The function returns the component(Event/Contribute..), given the google-order-no and merchant-private-data
     *  
     * @param xml     $xml_response   response send by google in xml format
     * @param array   $privateData    contains the name value pair of <merchant-private-data>
     * @param int     $orderNo        <order-total> send by google
     * @param string  $root           root of xml-response
     *  
     * @return component/module  
     * @static  
     */  
    static function getModule($xml_response, $privateData, $orderNo, $root) {
        require_once 'CRM/Contribute/DAO/Contribution.php';
        
        if ($root == 'new-order-notification') {
            $contributionID   = $privateData['contributionID'];
            $contribution     =& new CRM_Contribute_DAO_Contribution( );
            $contribution->id = $contributionID;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
                echo "Failure: Could not find contribution record for $contributionID<p>";
                return;
            }
            if (stristr($contribution->source, 'Online Contribution')) {
                return 'Contribute';
            } elseif (stristr($contribution->source, 'Online Event Registration')) {
                return 'Event';
            }
        } else {
            $contribution =& new CRM_Contribute_DAO_Contribution( );
            $contribution->invoice_id = $orderNo;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
                echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
                return;
            }
            if (stristr($contribution->source, 'Online Contribution')) {
                return 'Contribute';
            } elseif (stristr($contribution->source, 'Online Event Registration')) {
                return 'Event';
            }
        }
        
        CRM_Core_Error::debug_log_message( "Could not find the module or component (Contribute/Event)" );
        CRM_Core_Error::debug_log_message( "Contribution ID received in private data: {$privateData['contributionID']}" );
        CRM_Core_Error::debug_log_message( "Invoice ID received in private data: {$privateData['invoiceID']}" );
        CRM_Core_Error::debug_log_message( "Google oredr No: $orderNo" );
        exit();
    }

    /**  
     * The function returns the mode(test, live..), given the google-order-no and merchant-private-data
     *  
     * @param xml     $xml_response   response send by google in xml format
     * @param array   $privateData    contains the name value pair of <merchant-private-data>
     * @param int     $orderNo        <order-total> send by google
     * @param string  $root           root of xml-response
     *  
     * @return mode  
     * @static  
     */  
    static function getMode($xml_response, $privateData, $orderNo, $root) {
        require_once 'CRM/Contribute/DAO/Contribution.php';
        
        if ($root == 'new-order-notification') {
            $contributionID   = $privateData['contributionID'];
            $contribution     =& new CRM_Contribute_DAO_Contribution( );
            $contribution->id = $contributionID;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
                echo "Failure: Could not find contribution record for $contributionID<p>";
                return;
            }
            return $contribution->is_test;
        } else {
            $contribution =& new CRM_Contribute_DAO_Contribution( );
            $contribution->invoice_id = $orderNo;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
                echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
                return;
            }
            return $contribution->is_test;
        }
    }
    
    /**
     * This method is handles the response that will be invoked (from extern/googleNotify) every time
     * a notification or request is sent by the Google Server.
     *
     */
    static function main( ) 
    {
        require_once('Google/library/googleresponse.php');
        require_once('Google/library/googlemerchantcalculations.php');
        require_once('Google/library/googleresult.php');
        require_once('Google/library/xml-processing/xmlparser.php');
        
        //require_once('CRM/Core/Payment/GoogleIPN.php');

        $config =& CRM_Core_Config::singleton();
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
        $privateData = $privateData ? self::stringToArray($privateData) : '';
        
        $mode   = self::getMode($xml_response, $privateData, $orderNo, $root);
        $mode   = $mode ? 'test' : 'live';
        
        $module = self::getModule($xml_response, $privateData, $orderNo, $root);
        
        $ipn    =& self::singleton( $mode, $module );
        
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
    }


    /**
     * In case the XML API contains multiple open tags
     * with the same value, then invoke this function and
     * perform a foreach on the resultant array.
     * This takes care of cases when there is only one unique tag
     * or multiple tags.
     * Examples of this are "anonymous-address", "merchant-code-string"
     * from the merchant-calculations-callback API
     */
    static function get_arr_result($child_node) {
        $result = array();
        if(isset($child_node)) {
            if(self::is_associative_array($child_node)) {
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
    
    /**
     * Returns true if a given variable represents an associative array 
     */
    static function is_associative_array( $var ) {
        return is_array( $var ) && !is_numeric( implode( '', array_keys( $var ) ) );
    }
    
    /**
     * Converts the comma separated name-value pairs in <merchant-private-data> 
     * to an array of name-value pairs.
     */
    static function stringToArray($str) {
        $vars = $labels = array();
        $labels = explode(',', $str);
        foreach ($labels as $label) {
            $terms = explode('=', $label);
            $vars[$terms[0]] = $terms[1];
        }
        return $vars;
    }
}

?>
