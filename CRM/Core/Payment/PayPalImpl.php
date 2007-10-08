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

require_once 'CRM/Core/Payment.php';

class CRM_Core_Payment_PayPalImpl extends CRM_Core_Payment {
    const
        CHARSET  = 'iso-8859-1';
    
    protected $_mode = null;
    
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

        if ( $this->_paymentProcessor['payment_processor_type'] == 'PayPal_Standard' ) {
            return;
        }

        if ( ! $this->_paymentProcessor['user_name'] ) {
            CRM_Core_Error::fatal( ts( 'Could not find user name for payment processor' ) );
        }
    }

    /**
     * express checkout code. Check PayPal documentation for more information
     * @param  array $params assoc array of input parameters for this transaction 
     * 
     * @return array the result in an nice formatted array (or an error object) 
     * @public
     */
    function setExpressCheckOut( &$params ) {
        if ( ! $this->_caller ) {
            return self::error( );
        }

        $orderTotal =& PayPal::getType( 'BasicAmountType' );

        if ( PayPal::isError( $orderTotal ) ) {
            return self::error( $orderTotal );
        }

        $orderTotal->setattr('currencyID', $params['currencyID'] );
        $orderTotal->setval( $params['amount'], self::CHARSET );
        $setExpressCheckoutRequestDetails =& PayPal::getType( 'SetExpressCheckoutRequestDetailsType' );

        if ( PayPal::isError( $setExpressCheckoutRequestDetails ) ) {
            return self::error( $setExpressCheckoutRequestDetails );
        }

        $setExpressCheckoutRequestDetails->setCancelURL ( $params['cancelURL'], self::CHARSET  );
        $setExpressCheckoutRequestDetails->setReturnURL ( $params['returnURL'], self::CHARSET  );
        $setExpressCheckoutRequestDetails->setInvoiceID ( $params['invoiceID'], self::CHARSET  );
        $setExpressCheckoutRequestDetails->setOrderTotal( $orderTotal );
        $setExpressCheckout =& PayPal::getType ( 'SetExpressCheckoutRequestType' );

        if ( PayPal::isError( $setExpressCheckout ) ) {
            return self::error( $setExpressCheckout );
        }

        $setExpressCheckout->setSetExpressCheckoutRequestDetails( $setExpressCheckoutRequestDetails );

        $result = $this->_caller->SetExpressCheckout( $setExpressCheckout );

        if (PayPal::isError( $result  ) ) { 
            return self::error( $result );
        }

        $result =& self::checkResult( $result );
        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            return $result;
        }

        /* Success, extract the token and return it */
        return $result->getToken( );
    }

    /**
     * get details from paypal. Check PayPal documentation for more information
     *
     * @param  string $token the key associated with this transaction
     * 
     * @return array the result in an nice formatted array (or an error object) 
     * @public
     */
    function getExpressCheckoutDetails( $token ) {
        if ( ! $this->_caller ) {
            return self::error( );
        }

        $getExpressCheckoutDetails =& PayPal::getType('GetExpressCheckoutDetailsRequestType');

        if ( PayPal::isError( $getExpressCheckoutDetails ) ) {
            return self::error( $getExpressCheckoutDetails );
        }

        $getExpressCheckoutDetails->setToken( $token, self::CHARSET );

        $result = $this->_caller->GetExpressCheckoutDetails( $getExpressCheckoutDetails );

        if ( PayPal::isError( $result ) ) { 
            return self::error( $result );
        }

        /* Success */
        $detail                =& $result->getGetExpressCheckoutDetailsResponseDetails( );

        $params                 =  array( );
        $params['token']        =  $result->Token;
        
        $payer                  =& $detail->getPayerInfo ( );
        $params['payer'       ] =  $payer->Payer;
        $params['payer_id'    ] =  $payer->PayerID;
        $params['payer_status'] =  $payer->PayerStatus;
        
        $name                  =& $payer->getPayerName  ( );
        $params['first_name' ] =  $name->getFirstName   ( );
        $params['middle_name'] =  $name->getMiddleName  ( );
        $params['last_name'  ] =  $name->getLastName    ( );
        
        $address                          =& $payer->getAddress    ( );
        $params['street_address']         =  $address->getStreet1  ( );
        $params['supplemental_address_1'] =  $address->getStreet2( );
        $params['city']                   =  $address->getCityName ( );
        $params['state_province']         =  $address->getStateOrProvince( );
        $params['postal_code']            =  $address->getPostalCode( );
        $params['country']                =  $address->getCountry  ( );
        
        return $params;
    }

    /**
     * do the express checkout at paypal. Check PayPal documentation for more information
     *
     * @param  string $token the key associated with this transaction
     * 
     * @return array the result in an nice formatted array (or an error object) 
     * @public
     */
    function doExpressCheckout( &$params ) {
        if ( ! $this->_caller ) {
            return self::error( );
        }

        $orderTotal =& PayPal::getType( 'BasicAmountType' ); 
 
        if ( PayPal::isError( $orderTotal ) ) { 
            return self::error( $orderTotal ); 
        } 
 
        $orderTotal->setattr('currencyID', $params['currencyID'] ); 
        $orderTotal->setval( $params['amount'], self::CHARSET ); 
        $paymentDetails =& PayPal::getType( 'SetExpressCheckoutRequestDetailsType' ); 
        
        if ( PayPal::isError( $paymentDetails ) ) {
            return self::error( $paymentDetails );
        }

        $paymentDetails->setOrderTotal( $orderTotal );
        $paymentDetails->setInvoiceID( $params['invoiceID'], self::CHARSET );
        $doExpressCheckoutPaymentRequestDetails =& PayPal::getType( 'DoExpressCheckoutPaymentRequestDetailsType' );

        if ( PayPal::isError( $doExpressCheckoutPaymentRequestDetails ) ) {
            return self::error( $doExpressCheckoutPaymentRequestDetails );
        }

        $doExpressCheckoutPaymentRequestDetails->setPaymentDetails( $paymentDetails );
        $doExpressCheckoutPaymentRequestDetails->setPayerID       ( $params['payer_id']      , self::CHARSET  );
        $doExpressCheckoutPaymentRequestDetails->setToken         ( $params['token']         , self::CHARSET  );
        $doExpressCheckoutPaymentRequestDetails->setPaymentAction ( $params['payment_action'], self::CHARSET  );
        $doExpressCheckoutPayment =& PayPal::getType( 'DoExpressCheckoutPaymentRequestType' );

        if ( PayPal::isError( $doExpressCheckoutPayment ) ) {
            return self::error( $doExpressCheckoutPayment );
        }

        $doExpressCheckoutPayment->setDoExpressCheckoutPaymentRequestDetails( $doExpressCheckoutPaymentRequestDetails );

        $result = $this->_caller->DoExpressCheckoutPayment( $doExpressCheckoutPayment );

        if ( PayPal::isError( $result ) ) { 
            return self::error( $result );
        }

        $result =& self::checkResult( $result ); 
        if ( is_a( $result, 'CRM_Core_Error' ) ) { 
            return $result; 
        } 

        /* Success */
        $details     =& $result->getDoExpressCheckoutPaymentResponseDetails( );
        
        $paymentInfo =& $details->getPaymentInfo( );
        
        $params['trxn_id']        = $paymentInfo->TransactionID;
        $params['gross_amount'  ] = self::getAmount( $paymentInfo->GrossAmount );
        $params['fee_amount'    ] = self::getAmount( $paymentInfo->FeeAmount    );
        $params['net_amount'    ] = self::getAmount( $paymentInfo->SettleAmount );
        if ( $params['net_amount'] == 0 && $params['fee_amount'] != 0 ) {
            $params['net_amount'] = $params['gross_amount'] - $params['fee_amount'];
        }
        $params['payment_status'] = $paymentInfo->PaymentStatus;
        $params['pending_reason'] = $paymentInfo->PendingReason;
        
        return $params;
    }

    /**
     * extract the value from the paypal amount structure
     *
     * @param Object $amount the paypal amount type
     *
     * @return string the amount value
     * @public
     */
    function getAmount( &$amount ) {
        return $amount->_value;
    }

    function initialize( &$args, $method ) {
        $args['user'     ] = $this->_paymentProcessor['user_name' ];
        $args['pwd'      ] = $this->_paymentProcessor['password'  ];
        $args['version'  ] = 3.0;
        $args['signature'] = $this->_paymentProcessor['signature' ];
        $args['subject'  ] = $this->_paymentProcessor['subject'   ];
        $args['method'   ] = $method;
    }

    /**
     * This function collects all the information from a web/api form and invokes
     * the relevant payment processor specific functions to perform the transaction
     *
     * @param  array $params assoc array of input parameters for this transaction
     *
     * @return array the result in an nice formatted array (or an error object)
     * @public
     */
    function doDirectPayment( &$params ) {
        $args = array( );

        $this->initialize( $args, 'DoDirectPayment' );
        $args['paymentAction']  = $params['payment_action'];
        $args['amt']            = $params['amount'];
        $args['currencyCode']   = $params['currencyID'];
        $args['invnum']         = $params['invoiceID'];
        $args['ipaddress']      = $params['ip_address'];
        $args['creditCardType'] = $params['credit_card_type'];
        $args['acct']           = $params['credit_card_number'];
        $args['expDate']        = sprintf( '%02d', $params['month'] ) . $params['year'];
        $args['cvv2']           = $params['cvv2'];
        $args['firstName']      = $params['first_name'];
        $args['lastName']       = $params['last_name'];
        $args['email']          = $params['email'];
        $args['street']         = $params['street_address'];
        $args['city']           = $params['city'];
        $args['state']          = $params['state_province'];
        $args['countryCode']    = $params['country'];
        $args['zip']            = $params['postal_code'];

        $result = $this->invokeNVPAPI( $args );

        if ( is_a( $result, 'CRM_Core_Error' ) ) {  
            return $result;  
        }

        if ( strtolower( $result['ACK'] ) != 'success' ) {
            $e =& CRM_Core_Error::singleton( );
            $e->push( $result['L_ERRORCODE0'],
                      0, null,
                      "{$result['L_SHORTMESSAGE0']} {$result['L_LONGMESSAGE0']}" );
            return $e;
        }

        /* Success */
        $params['trxn_id']        = $result['TRANSACTIONID'];
        $params['gross_amount'  ] = $result['AMT'];
        return $params;
    }

    /**
     * helper function to check the result and construct an error packet 
     * if needed
     *
     * @param Object an object returned by the paypal SDK
     *
     * @return Object the same object if not an error, else a CRM_Core_Error object
     * @public
     */
    function &checkResult( &$result ) {
        $errors = $result->getErrors( );
        if ( empty( $errors ) ) {
            return $result;
        }

        $e =& CRM_Core_Error::singleton( );
        if ( is_a( $errors, 'ErrorType' ) ) {
                $e->push( $errors->getErrorCode( ), 
                          0, null, 
                          $errors->getShortMessage( ) . ' ' . $errors->getLongMessage( ) ); 
        } else {
            foreach ( $errors as $error ) { 
                $e->push( $error->getErrorCode( ), 
                          0, null, 
                          $error->getShortMessage( ) . ' ' . $error->getLongMessage( ) ); 
            } 
        }
        return $e;
    }

    /**
     * create a CiviCRM error object and return
     *
     * @param Object a PEAR_Error object
     *
     * @return Object a CiviCRM Error object
     * @public
     */
    function &error( $error = null ) {
        $e =& CRM_Core_Error::singleton( );
        if ( $error ) {
            $e->push( $error->getCode( ),
                      0, null,
                      $error->getMessage( ) );
        } else {
            $e->push( 9001, 0, null, "Unknown System Error." );
        }
        return $e;
    }

    /** 
     * This function checks to see if we have the right config values 
     * 
     * @param  string $mode the mode we are operating in (live or test) 
     * 
     * @return string the error message if any 
     * @public 
     */ 
    function checkConfig( $mode ) {
        $error = array( );
        if ( $this->_paymentProcessor['payment_processor_type'] == 'PayPal_Standard' ||
             $this->_paymentProcessor['payment_processor_type'] == 'PayPal' ) {
            if ( empty( $this->_paymentProcessor['user_name'] ) ) {
                $error[] = ts( 'User Name is not set in the Administer CiviCRM &raquo; Payment Processor.' );
            }
        }

        if ( $this->_paymentProcessor['payment_processor_type'] != 'PayPal_Standard' ) {
            if ( empty( $this->_paymentProcessor['signature'] ) ) {
                $error[] = ts( 'Signature is not set in the Administer CiviCRM &raquo; Payment Processor.' );
            }
            
            if ( empty( $this->_paymentProcessor['password'] ) ) {
                $error[] = ts( 'Password is not set in the Administer CiviCRM &raquo; Payment Processor.' );
            }
        }
    
        if ( ! empty( $error ) ) {
            return implode( '<p>', $error );
        } else {
            return null;
        }
    }

    function doTransferCheckout( &$params, $component = 'contribute' ) {
        $config =& CRM_Core_Config::singleton( );

        if ( $component != 'contribute' && $component != 'event' ) {
            CRM_Core_Error::fatal( ts( 'Component is invalid' ) );
        }
        
        $notifyURL = $config->userFrameworkResourceURL . "extern/ipn.php?reset=1&contactID={$params['contactID']}&contributionID={$params['contributionID']}&contributionTypeID={$params['contributionTypeID']}&module={$component}";

        if ( $component == 'event' ) {
            $notifyURL .= "&eventID={$params['eventID']}";
        } else {
            $selectMembership = CRM_Utils_Array::value( 'selectMembership', $params );
            if ( $selectMembership &&
                 $selectMembership != 'no_thanks' ) {
                $notifyURL .= "&membershipTypeID=$selectMembership";
            }
        }

        $url    = ( $component == 'event' ) ? 'civicrm/event/register' : 'civicrm/contribute/transact';
        $cancel = ( $component == 'event' ) ? '_qf_Register_display'   : '_qf_Main_display';
        $returnURL = CRM_Utils_System::url( $url,
                                            "_qf_ThankYou_display=1&qfKey={$params['qfKey']}",
                                            true, null, false );
        $cancelURL = CRM_Utils_System::url( $url,
                                            "$cancel=1&cancel=1&qfKey={$params['qfKey']}",
                                            true, null, false );
        
        $paypalParams =
            array( 'business'           => $this->_paymentProcessor['user_name'],
                   'notify_url'         => $notifyURL,
                   'item_name'          => $params['item_name'],
                   'quantity'           => 1,
                   'undefined_quantity' => 0,
                   'cancel_return'      => $cancelURL,
                   'no_note'            => 1,
                   'no_shipping'        => 1,
                   'return'             => $returnURL,
                   'rm'                 => 1,
                   'currency_code'      => $params['currencyID'],
                   'invoice'            => $params['invoiceID'] );

        // if recurring donations, add a few more items
        if ( ! empty( $params['is_recur'] ) ) {
            if ( $params['contributionRecurID'] ) {
                $notifyURL .= "&contributionRecurID={$params['contributionRecurID']}&contributionPageID={$params['contributionPageID']}";
                $paypalParams['notify_url'] = $notifyURL;
            } else {
                CRM_Core_Error::fatal( ts( 'Recurring contribution, but no database id' ) );
            }
            
            $paypalParams +=
                array( 'cmd'                => '_xclick-subscriptions',
                       'a3'                 => $params['amount'],
                       'p3'                 => $params['frequency_interval'],
                       't3'                 => ucfirst( substr( $params['frequency_unit'], 0, 1 ) ),
                       'src'                => 1,
                       'sra'                => 1,
                       'srt'                => ( $params['installments'] > 0 ) ? $params['installments'] : null,
                       'no_note'            => 1,
                       'modify'             => 0,
                       );
        } else {
            $paypalParams +=
                array( 'cmd'                => '_xclick',
                       'amount'             => $params['amount'],
                       );
        }
        
        $uri = '';
        foreach ( $paypalParams as $key => $value ) {
            if ( $value === null ) {
                continue;
            }

            $value = urlencode( $value );
            if ( $key == 'return' ||
                 $key == 'cancel_return' ||
                 $key == 'notify_url' ) {
                $value = str_replace( '%2F', '/', $value );
            }
            $uri .= "&{$key}={$value}";
        }

        $uri = substr( $uri, 1 );
        $url = $this->_paymentProcessor['url_site'];
        $sub = empty( $params['is_recur'] ) ? 'xclick' : 'subscriptions';
        $paypalURL = "{$url}{$sub}/$uri";

        CRM_Utils_System::redirect( $paypalURL );
    }

    /**
     * hash_call: Function to perform the API call to PayPal using API signature
     * @methodName is name of API  method.
     * @nvpStr is nvp string.
     * returns an associtive array containing the response from the server.
     */
    function invokeNVPAPI( $args ) {

        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_paymentProcessor['url_site'] . 'nvp' );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $p = array( );
        foreach ( $args as $n => $v ) {
            $p[] = "$n=" . urlencode( $v );
        }

        //NVPRequest for submitting to server
        $nvpreq = implode( '&', $p );

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        //getting response from server
        $response = curl_exec( $ch );

        //converting NVPResponse to an Associative Array
        $result = $this->deformatNVP( $response );

        if ( curl_errno( $ch ) ) {
            $e =& CRM_Core_Error::singleton( );
            $e->push( curl_errno( $ch ),
                      0, null,
                      curl_error( $ch ) );
            return $e;
        } else {
			curl_close($ch);
        }

        return $result;
    }

    /** This function will take NVPString and convert it to an Associative Array and it will decode the response.
     * It is usefull to search for a particular key and displaying arrays.
     * @nvpstr is NVPString.
     * @nvpArray is Associative Array.
     */

    function deformatNVP( $str )
    {
        $result = array();

        while ( strlen( $str ) ) {
            // postion of key
            $keyPos = strpos( $str, '=' );

            // position of value
            $valPos = strpos( $str, '&' ) ? strpos( $str, '&' ): strlen( $str );

            /*getting the Key and Value values and storing in a Associative Array*/
            $key = substr( $str, 0, $keyPos );
            $val = substr( $str, $keyPos+1, $valPos - $keyPos - 1 );

            //decoding the respose
            $result[ urldecode( $key ) ] = urldecode( $val );
            $str = substr( $str, $valPos + 1, strlen( $str ) );
        }

        return $result;
    }

}

?>
