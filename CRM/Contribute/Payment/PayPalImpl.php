<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.6                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright CiviCRM LLC (c) 2004-2006                                  | 
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       | 
 | about the Affero General Public License or the licensing  of       | 
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   | 
 | http://www.civicrm.org/licensing/                                 | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@civicrm.org> 
 * @copyright CiviCRM LLC (c) 2004-2006 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Contribute/Payment.php';

class CRM_Contribute_Payment_PayPalImpl extends CRM_Contribute_Payment {
    const
        CHARSET  = 'iso-8859-1';
    
    /** 
     * We only need one instance of this object. So we use the singleton 
     * pattern and cache the instance in this variable 
     * 
     * @var object 
     * @static 
     */ 
    static private $_singleton = null; 

    static protected $_mode = null;

    /** 
     * Constructor 
     * 
     * @param string $mode the mode of operation: live or test
     *
     * @return void 
     */ 
    function __construct( $mode ) {
        require_once 'PayPal.php';
        require_once 'PayPal/Profile/API.php';

        $this->_mode = $mode;

        $config =& CRM_Core_Config::singleton( );

        if ( $config->paymentProcessor == 'PayPal_Standard' ) {
            return;
        }

        if ( $config->paymentUsername[$mode] ) {
            require_once 'PayPal/Profile/Handler/Array.php';
            $environment = ( $mode == 'test' ) ? 'sandbox' : 'live';
            $this->_handler =& ProfileHandler_Array::getInstance( 
                                                                 array(
                                                                       'username'        => $config->paymentUsername[$mode],
                                                                       'certificateFile' => null,
                                                                       'subject'         => $config->paymentSubject[$mode],
                                                                       'environment'     => $environment,
                                                                       )
                                                                 );
            if ( PayPal::isError( $handler ) ) {
                return self::error( $handler );
            }

            $pid            =  ProfileHandler::generateID( );
            $this->_profile =& APIProfile::getInstance( $pid, $this->_handler );

            if ( PayPal::isError( $this->_profile ) ) {
                return self::error( $this->_profile );
            }

            $this->_profile->setAPIPassword( $config->paymentPassword[$mode] );
            $this->_profile->setSignature  ( $config->paymentKey     [$mode] );
        } else {
            require_once 'PayPal/Profile/Handler/File.php';
            $this->_handler =& ProfileHandler_File::getInstance( array(
                                                                       'path' => $config->paymentCertPath[$mode],
                                                                       'charset' => self::CHARSET,
                                                                       )
                                                                 );
        
            if ( PayPal::isError( $handler ) ) {
                return self::error( $handler );
            }
        
            $this->_profile =& APIProfile::getInstance( $config->paymentKey[$mode], $this->_handler );

            if ( PayPal::isError( $this->_profile ) ) {
                return self::error( $this->_profile );
            }

            $this->_profile->setAPIPassword( $config->paymentPassword[$mode] );
        }

        $this->_caller =& PayPal::getCallerServices( $this->_profile );

        if ( PayPal::isError( $this->_caller ) ) {
            $ret = self::error( $this->_caller );
            $this->_caller = null;
            return $ret;
        }
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
            self::$_singleton =& new CRM_Contribute_Payment_PaypalImpl( $mode );
        } 
        return self::$_singleton; 
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
        
        $address                  =& $payer->getAddress    ( );
        $params['street_address'] =  $address->getStreet1  ( );
        $params['supplemental_address_1'] = $address->getStreet2( );
        $params['city']        =  $address->getCityName ( );
        $params['state_province'] = $address->getStateOrProvince( );
        $params['postal_code'] = $address->getPostalCode( );
        $params['country']     =  $address->getCountry  ( );
        
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
        if ( ! $this->_caller ) {
            return self::error( );
        }

        $orderTotal =& PayPal::getType( 'BasicAmountType' );  
  
        if ( PayPal::isError( $orderTotal ) ) {  
            return self::error( $orderTotal );  
        }  
  
        $orderTotal->setattr( 'currencyID', $params['currencyID'] );  
        $orderTotal->setval( $params['amount'], self::CHARSET  );  

        $payerName =& PayPal::getType( 'PersonNameType' );

        if ( PayPal::isError( $payerName ) ) {
            return self::error( $payerName );
        }

        $payerName->setLastName  ( $params['last_name'  ], self::CHARSET  );
        $payerName->setMiddleName( $params['middle_name'], self::CHARSET  );
        $payerName->setFirstName ( $params['first_name' ], self::CHARSET  );
        $address =& PayPal::getType('AddressType');

        if (PayPal::isError($address)) {
            return self::error( $address );
        }

        $address->setStreet1        ( $params['street_address'], self::CHARSET );
        $address->setCityName       ( $params['city']          , self::CHARSET );
        $address->setStateOrProvince( $params['state_province'], self::CHARSET );
        $address->setPostalCode     ( $params['postal_code']   , self::CHARSET );
        $address->setCountry        ( $params['country']       , self::CHARSET );
        $cardOwner =& PayPal::getType( 'PayerInfoType' );

        if ( PayPal::isError( $cardOwner ) ) {
            return self::error( $cardOwner );
        }

        $cardOwner->setPayer( $params['email'] );
        $cardOwner->setAddress( $address );
        $cardOwner->setPayerCountry( $params['country'], self::CHARSET  );
        $cardOwner->setPayerName( $payerName );
        $creditCard =& PayPal::getType( 'CreditCardDetailsType' );

        if ( PayPal::isError( $creditCard ) ) {
            return self::error( $creditCard );
        }

        $creditCard->setCardOwner( $cardOwner );
        $creditCard->setCVV2            ( $params['cvv2']              , self::CHARSET  );
        $creditCard->setExpYear         ( $params['year' ]             , self::CHARSET  );
        $creditCard->setExpMonth        ( $params['month']             , self::CHARSET  );
        $creditCard->setCreditCardNumber( $params['credit_card_number'], self::CHARSET  );
        $creditCard->setCreditCardType  ( $params['credit_card_type']  , self::CHARSET  );
        $doDirectPaymentRequestDetails =& PayPal::getType( 'DoDirectPaymentRequestDetailsType' );

        $paymentDetails =& PayPal::getType( 'PaymentDetailsType' );

        if ( PayPal::isError( $paymentDetails ) ) {
            return self::error( $paymentDetails );
        }

        $paymentDetails->setOrderTotal($orderTotal);
        $paymentDetails->setInvoiceID( $params['invoiceID'], self::CHARSET );

        $shipToAddress = $address;
        $shipToAddress->setName( $params['first_name' ] . ' ' . $params['last_name'] );
        $paymentDetails->setShipToAddress( $shipToAddress );

        if ( PayPal::isError( $doDirectPaymentRequestDetails ) ) {
            return self::error( $doDirectPaymentRequestDetails );
        }

        $doDirectPaymentRequestDetails->setCreditCard    ( $creditCard     );
        $doDirectPaymentRequestDetails->setPaymentDetails( $paymentDetails );
        $doDirectPaymentRequestDetails->setIPAddress     ( $params['ip_address'    ], self::CHARSET  );
        $doDirectPaymentRequestDetails->setPaymentAction ( $params['payment_action'], self::CHARSET  );
        $doDirectPayment =& PayPal::getType( 'DoDirectPaymentRequestType' );

        if ( PayPal::isError( $doDirectPayment ) ) {
            return self::error( $doDirectPayment );
        }

        $doDirectPayment->setDoDirectPaymentRequestDetails( $doDirectPaymentRequestDetails );

        $result = $this->_caller->DoDirectPayment( $doDirectPayment );

        if ( PayPal::isError( $result ) ) { 
            return self::error( $result );
        }

        /* Check for application errors */
        $result =& self::checkResult( $result );
        if ( is_a( $result, 'CRM_Core_Error' ) ) {  
            return $result;  
        }

        /* Success */
        $params['trxn_id']        = $result->TransactionID;
        $params['gross_amount'  ] = self::getAmount( $result->Amount );
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
        $config =& CRM_Core_Config::singleton( );

        $error = array( );

        if ( $config->paymentProcessor == 'PayPal_Standard' ) {
            if ( empty( $config->paymentUsername[$mode] ) ) {
                if ( $mode == 'live' ) {
                    $error[] = ts( '%1 is not set in the config file.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_USERNAME') );
                } else {
                    $error[] = ts( '%1 is not set in the config file.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_USERNAME') );
                }
            }
        }

        if ( $config->paymentProcessor == 'PayPal' ) {
            if ( empty( $config->paymentCertPath[$mode] ) ) {
                if ( $mode == 'live' ) {
                    $error[] = ts( '%1 is not set in the config file.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_CERT_PATH') );
                } else {
                    $error[] = ts( '%1 is not set in the config file.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_CERT_PATH') );
                }
            }
        }

        if ( $config->paymentProcessor != 'PayPal_Standard' ) {
            if ( empty( $config->paymentKey[$mode] ) ) {
                if ( $mode == 'live' ) {
                    $error[] = ts( '%1 is not set in the config file.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_KEY') ); 
                } else {
                    $error[] = ts( '%1 is not set in the config file.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_KEY') ); 
                }
            }
            
            if ( empty( $config->paymentPassword[$mode] ) ) {
                if ( $mode == 'live' ) {
                    $error[] = ts( '%1 is not set in the config file.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_PASSWORD') );
                } else {
                    $error[] = ts( '%1 is not set in the config file.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_PASSWORD') );
                }
            }
        }

        if ( ! empty( $error ) ) {
            return implode( ' ', $error );
        } else {
            return null;
        }
    }

    function doTransferCheckout( &$params ) {
        $config =& CRM_Core_Config::singleton( );

        $notifyURL = $config->userFrameworkResourceURL . "extern/ipn.php?reset=1&contactID={$params['contactID']}&contributionID={$params['contributionID']}&contributionTypeID={$params['contributionTypeID']}";
        $returnURL = CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_ThankYou_display=1', true, null, false );
        $cancelURL = CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_Main_display=1&cancel=1', true, null, false );

        $paypalParams =
            array( 'cmd'                => 'xclick',
                   'business'           => $config->paymentUsername[$this->_mode],
                   'notify_url'         => $notifyURL,
                   'amount'             => $params['amount'],
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


        $uri = '';
        foreach ( $paypalParams as $key => $value ) {
            $value = urlencode( $value );
            if ( $key == 'return' ||
                 $key == 'cancel_return' ||
                 $key == 'notify_url' ) {
                $value = str_replace( '%2F', '/', $value );
            }
            $uri .= "&{$key}={$value}";
        }

        $uri = substr( $uri, 1 );
        $url = ( $this->_mode == 'test' ) ? $config->paymentPayPalExpressTestUrl : $config->paymentPayPalExpressUrl;
        $paypalURL = "https://{$url}/xclick/$uri";

        CRM_Utils_System::redirect( $paypalURL );
    }

}

?>
