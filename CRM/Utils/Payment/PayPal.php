<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.1                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright (c) 2005 Social Source Foundation                        | 
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
 | at http://www.openngo.org/faqs/licensing.html                      | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Social Source Foundation (c) 2005 
 * $Id$ 
 * 
 */ 

class CRM_Utils_Payment_PayPal {
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

    /** 
     * Constructor 
     * 
     * @return void 
     */ 
    function __construct( ) {
        require_once 'Services/PayPal.php';
        require_once 'Services/PayPal/Profile/Handler/File.php';
        require_once 'Services/PayPal/Profile/API.php';

        $config =& CRM_Core_Config::singleton( );
        $this->_handler =& ProfileHandler_File::getInstance( array(
                                                                   'path' => $config->paymentCertPath,
                                                                   'charset' => self::CHARSET,
                                                                   )
                                                             );
        
        if ( Services_PayPal::isError( $handler ) ) {
            return self::error( $handler );
        }

        $this->_profile =& APIProfile::getInstance( $config->paymentKey, $this->_handler );

        if ( Services_PayPal::isError( $this->_profile ) ) {
            return self::error( $this->_profile );
        }

        $this->_profile->setAPIPassword( $config->paymentPassword );

        $this->_caller =& Services_PayPal::getCallerServices( $this->_profile );

        if ( Services_PayPal::isError( $this->_caller ) ) {
            $ret = self::error( $this->_caller );
            $this->_caller = null;
            return $ret;
        }
    }

    /** 
     * singleton function used to manage this object 
     * 
     * @param string the key to permit session scope's 
     * 
     * @return object 
     * @static 
     * 
     */ 
    static function &singleton( ) {
        if (self::$_singleton === null ) { 
            self::$_singleton =& new CRM_Utils_Payment_Paypal( );
        } 
        return self::$_singleton; 
    } 

    function setExpressCheckOut( &$params ) {
        if ( ! $this->_caller ) {
            return self::error( );
        }

        $orderTotal =& Services_PayPal::getType( 'BasicAmountType' );

        if ( Services_PayPal::isError( $orderTotal ) ) {
            return self::error( $orderTotal );
        }

        $orderTotal->setattr('currencyID', $params['currencyID'] );
        $orderTotal->setval( $params['amount'], self::CHARSET );
        $setExpressCheckoutRequestDetails =& Services_PayPal::getType( 'SetExpressCheckoutRequestDetailsType' );

        if ( Services_PayPal::isError( $setExpressCheckoutRequestDetails ) ) {
            return self::error( $setExpressCheckoutRequestDetails );
        }

        $setExpressCheckoutRequestDetails->setCancelURL ( $params['cancelURL'], self::CHARSET  );
        $setExpressCheckoutRequestDetails->setReturnURL ( $params['returnURL'], self::CHARSET  );
        $setExpressCheckoutRequestDetails->setOrderTotal( $orderTotal );
        $setExpressCheckout =& Services_PayPal::getType ( 'SetExpressCheckoutRequestType' );

        if ( Services_PayPal::isError( $setExpressCheckout ) ) {
            return self::error( $setExpressCheckout );
        }

        $setExpressCheckout->setSetExpressCheckoutRequestDetails( $setExpressCheckoutRequestDetails );

        $result = $this->_caller->SetExpressCheckout( $setExpressCheckout );

        if (Services_PayPal::isError( $result  ) ) { 
            return self::error( $result );
        }

        $result =& self::checkResult( $result );
        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            return $result;
        }

        /* Success, extract the token and return it */
        return $result->getToken( );
    }

    function getExpressCheckoutDetails( $token ) {
        if ( ! $this->_caller ) {
            return self::error( );
        }

        $getExpressCheckoutDetails =& Services_PayPal::getType('GetExpressCheckoutDetailsRequestType');

        if ( Services_PayPal::isError( $getExpressCheckoutDetails ) ) {
            return self::error( $getExpressCheckoutDetails );
        }

        $getExpressCheckoutDetails->setToken( $token, self::CHARSET );

        $result = $this->_caller->GetExpressCheckoutDetails( $getExpressCheckoutDetails );

        if ( Services_PayPal::isError( $result ) ) { 
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
        
        $address               =& $payer->getAddress    ( );
        $params['street1']     =  $address->getStreet1  ( );
        $params['supplemental_address_1'] = $address->getStreet2( );
        $params['city']        =  $address->getCityName ( );
        $params['state_province'] = $address->getStateOrProvince( );
        $params['country']     =  $address->getCountry  ( );
        
        return $params;
    }

    function doExpressCheckout( &$params ) {
        if ( ! $this->_caller ) {
            return self::error( );
        }

        $orderTotal =& Services_PayPal::getType( 'BasicAmountType' ); 
 
        if ( Services_PayPal::isError( $orderTotal ) ) { 
            return self::error( $orderTotal ); 
        } 
 
        $orderTotal->setattr('currencyID', $params['currencyID'] ); 
        $orderTotal->setval( $params['amount'], self::CHARSET ); 
        $paymentDetails =& Services_PayPal::getType( 'SetExpressCheckoutRequestDetailsType' ); 
        
        if ( Services_PayPal::isError( $paymentDetails ) ) {
            return self::error( $paymentDetails );
        }

        $paymentDetails->setOrderTotal( $orderTotal );
        $doExpressCheckoutPaymentRequestDetails =& Services_PayPal::getType( 'DoExpressCheckoutPaymentRequestDetailsType' );

        if ( Services_PayPal::isError( $doExpressCheckoutPaymentRequestDetails ) ) {
            return self::error( $doExpressCheckoutPaymentRequestDetails );
        }

        $doExpressCheckoutPaymentRequestDetails->setPaymentDetails( $paymentDetails );
        $doExpressCheckoutPaymentRequestDetails->setPayerID       ( $params['payer_id']      , self::CHARSET  );
        $doExpressCheckoutPaymentRequestDetails->setToken         ( $params['token']         , self::CHARSET  );
        $doExpressCheckoutPaymentRequestDetails->setPaymentAction ( $params['payment_action'], self::CHARSET  );
        $doExpressCheckoutPayment =& Services_PayPal::getType( 'DoExpressCheckoutPaymentRequestType' );

        if ( Services_PayPal::isError( $doExpressCheckoutPayment ) ) {
            return self::error( $doExpressCheckoutPayment );
        }

        $doExpressCheckoutPayment->setDoExpressCheckoutPaymentRequestDetails( $doExpressCheckoutPaymentRequestDetails );

        $result = $this->_caller->DoExpressCheckoutPayment( $doExpressCheckoutPayment );

        if ( Services_PayPal::isError( $result ) ) { 
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
        $params['payment_status'] = $paymentInfo->PaymentStatus;
        $params['pending_reason'] = $paymentInfo->PendingReason;
        
        return $params;
    }

    function getAmount( &$amount ) {
        return $amount->_value;
    }

    function doDirectPayment( &$params ) {
        if ( ! $this->_caller ) {
            return self::error( );
        }

        $orderTotal =& Services_PayPal::getType( 'BasicAmountType' );  
  
        if ( Services_PayPal::isError( $orderTotal ) ) {  
            return self::error( $orderTotal );  
        }  
  
        $orderTotal->setattr( 'currencyID', $params['currencyID'] );  
        $orderTotal->setval( $params['amount'], self::CHARSET  );  
        $paymentDetails =& Services_PayPal::getType( 'PaymentDetailsType' );

        if ( Services_PayPal::isError( $paymentDetails ) ) {
            return self::error( $paymentDetails );
        }

        $paymentDetails->setOrderTotal($orderTotal);
        $payerName =& Services_PayPal::getType( 'PersonNameType' );

        if ( Services_PayPal::isError( $payerName ) ) {
            return self::error( $payerName );
        }

        $payerName->setLastName  ( $params['last_name'  ], self::CHARSET  );
        $payerName->setMiddleName( $params['middle_name'], self::CHARSET  );
        $payerName->setFirstName ( $params['first_name' ], self::CHARSET  );
        $address =& Services_PayPal::getType('AddressType');

        if (Services_PayPal::isError($address)) {
            return self::error( $address );
        }

        $address->setStreet1        ( $params['street1']       , self::CHARSET );
        $address->setCityName       ( $params['city']          , self::CHARSET );
        $address->setStateOrProvince( $params['state_province'], self::CHARSET );
        $address->setPostalCode     ( $params['postal_code']   , self::CHARSET );
        $address->setCountry        ( $params['country']       , self::CHARSET );
        $cardOwner =& Services_PayPal::getType( 'PayerInfoType' );

        if ( Services_PayPal::isError( $cardOwner ) ) {
            return self::error( $cardOwner );
        }

        $cardOwner->setAddress( $address );
        $cardOwner->setPayerCountry( $params['country'], self::CHARSET  );
        $cardOwner->setPayerName( $payerName );
        $creditCard =& Services_PayPal::getType( 'CreditCardDetailsType' );

        if ( Services_PayPal::isError( $creditCard ) ) {
            return self::error( $creditCard );
        }

        $creditCard->setCardOwner( $cardOwner );
        $creditCard->setCVV2            ( $params['cvv2']              , self::CHARSET  );
        $creditCard->setExpYear         ( $params['year' ]             , self::CHARSET  );
        $creditCard->setExpMonth        ( $params['month']             , self::CHARSET  );
        $creditCard->setCreditCardNumber( $params['credit_card_number'], self::CHARSET  );
        $creditCard->setCreditCardType  ( $params['credit_card_type']  , self::CHARSET  );
        $doDirectPaymentRequestDetails =& Services_PayPal::getType( 'DoDirectPaymentRequestDetailsType' );

        if ( Services_PayPal::isError( $doDirectPaymentRequestDetails ) ) {
            return self::error( $doDirectPaymentRequestDetails );
        }

        $doDirectPaymentRequestDetails->setCreditCard    ( $creditCard     );
        $doDirectPaymentRequestDetails->setPaymentDetails( $paymentDetails );
        $doDirectPaymentRequestDetails->setIPAddress     ( $params['ip_address'    ], self::CHARSET  );
        $doDirectPaymentRequestDetails->setPaymentAction ( $params['payment_action'], self::CHARSET  );
        $doDirectPayment =& Services_PayPal::getType( 'DoDirectPaymentRequestType' );

        if ( Services_PayPal::isError( $doDirectPayment ) ) {
            return self::error( $doDirectPayment );
        }

        $doDirectPayment->setDoDirectPaymentRequestDetails( $doDirectPaymentRequestDetails );

        $result = $this->_caller->DoDirectPayment( $doDirectPayment );

        if ( Services_PayPal::isError( $result ) ) { 
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
}

?>