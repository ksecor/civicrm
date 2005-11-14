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
        PPD_FILE = '01b74fec70d1773ea72cbefd7e9aad12',
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

        $this->_handler =& ProfileHandler_File::getInstance( array(
                                                                   'path' => '',
                                                                   'charset' => self::CHARSET,
                                                                   )
                                                             );
        
        if ( Services_PayPal::isError( $handler ) ) {
            CRM_Core_Error::debug( 'v', $handler->getMessage() );
            exit;
        }

        $this->_profile =& APIProfile::getInstance( self::PPD_FILE, $this->_handler );

        if ( Services_PayPal::isError( $this->_profile ) ) {
            CRM_Core_Error::debug( 'v', $this->_profile->getMessage() );
            exit;
        }

        $this->_profile->setAPIPassword('Social!Source@');

        $this->_caller =& Services_PayPal::getCallerServices( $this->_profile );

        if ( Services_PayPal::isError( $this->_caller ) ) {
            CRM_Core_Error::debug( 'v', $this->_caller->getMessage() );
            exit;
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

        $orderTotal =& Services_PayPal::getType( 'BasicAmountType' );

        if ( Services_PayPal::isError( $orderTotal ) ) {
            CRM_Core_Error::debug( 'v', $orderTotal );
            exit;
        }

        $orderTotal->setattr('currencyID', $params['currencyID'] );
        $orderTotal->setval( $params['amount'], self::CHARSET );
        $setExpressCheckoutRequestDetails =& Services_PayPal::getType( 'SetExpressCheckoutRequestDetailsType' );

        if ( Services_PayPal::isError( $setExpressCheckoutRequestDetails ) ) {
            CRM_Core_Error::debug( 'v', $setExpressCheckoutRequestDetails );
            exit;
        }

        $setExpressCheckoutRequestDetails->setCancelURL ( $params['cancelURL'], self::CHARSET  );
        $setExpressCheckoutRequestDetails->setReturnURL ( $params['returnURL'], self::CHARSET  );
        $setExpressCheckoutRequestDetails->setOrderTotal( $orderTotal );
        $setExpressCheckoutRequestDetails->setNoShipping( 1 );
        $setExpressCheckout =& Services_PayPal::getType ( 'SetExpressCheckoutRequestType' );

        if ( Services_PayPal::isError( $setExpressCheckout ) ) {
            CRM_Core_Error::debug( 'v', $setExpressCheckout );
            exit;
        }

        $setExpressCheckout->setSetExpressCheckoutRequestDetails( $setExpressCheckoutRequestDetails );

        $result = $this->_caller->SetExpressCheckout( $setExpressCheckout );

        if (Services_PayPal::isError( $result  ) ) { 
            CRM_Core_Error::debug( 'v', $result );
            exit;
        } else {
            /* Success, extract the token and return it */
            return $result->getToken( );
        }
    }

    function getExpressCheckoutDetails( $token ) {
        $getExpressCheckoutDetails =& Services_PayPal::getType('GetExpressCheckoutDetailsRequestType');

        if ( Services_PayPal::isError( $getExpressCheckoutDetails ) ) {
            CRM_Core_Error::debug( 'v', $getExpressCheckoutDetails );
            exit;
        }

        $getExpressCheckoutDetails->setToken( $token, self::CHARSET );

        $result = $this->_caller->GetExpressCheckoutDetails( $getExpressCheckoutDetails );

        if ( Services_PayPal::isError( $result ) ) { 
            CRM_Core_Error::debug( 'v', $result );
        } else {
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
            $params['street']      =  $address->getStreet1  ( );
            $params['supplemental_address_1'] = $address->getStreet2( );
            $params['city']        =  $address->getCityName ( );
            $params['state_province'] = $address->getStateOrProvince( );
            $params['country']     =  $address->getCountry  ( );

            return $params;
        }
    }

    function doExpressCheckout( &$params ) {
        $orderTotal =& Services_PayPal::getType( 'BasicAmountType' ); 
 
        if ( Services_PayPal::isError( $orderTotal ) ) { 
            CRM_Core_Error::debug( 'v', $orderTotal ); 
            exit; 
        } 
 
        $orderTotal->setattr('currencyID', $params['currencyID'] ); 
        $orderTotal->setval( $params['amount'], self::CHARSET ); 
        $paymentDetails =& Services_PayPal::getType( 'SetExpressCheckoutRequestDetailsType' ); 
        
        if ( Services_PayPal::isError( $paymentDetails ) ) {
            CRM_Core_Error::debug( 'v', $paymentDetails );
            exit;
        }

        $paymentDetails->setOrderTotal( $orderTotal );
        $doExpressCheckoutPaymentRequestDetails =& Services_PayPal::getType( 'DoExpressCheckoutPaymentRequestDetailsType' );

        if ( Services_PayPal::isError( $doExpressCheckoutPaymentRequestDetails ) ) {
            CRM_Core_Error::debug( 'v', $doExpressCheckoutPaymentRequestDetails );
            exit;
        }

        $doExpressCheckoutPaymentRequestDetails->setPaymentDetails( $paymentDetails );
        $doExpressCheckoutPaymentRequestDetails->setPayerID       ( $params['payer_id']      , self::CHARSET  );
        $doExpressCheckoutPaymentRequestDetails->setToken         ( $params['token']         , self::CHARSET  );
        $doExpressCheckoutPaymentRequestDetails->setPaymentAction ( $params['payment_action'], self::CHARSET  );
        $doExpressCheckoutPayment =& Services_PayPal::getType( 'DoExpressCheckoutPaymentRequestType' );

        if ( Services_PayPal::isError( $doExpressCheckoutPayment ) ) {
            CRM_Core_Error::debug( 'v', $doExpressCheckoutPayment );
            exit;
        }

        $doExpressCheckoutPayment->setDoExpressCheckoutPaymentRequestDetails( $doExpressCheckoutPaymentRequestDetails );

        $result = $this->_caller->DoExpressCheckoutPayment( $doExpressCheckoutPayment );

        if ( Services_PayPal::isError( $result ) ) { 
            CRM_Core_Error::debug( 'v', $result );
        } else {
            /* Success */
            $details     =& $result->getDoExpressCheckoutPaymentResponseDetails( );

            $params = array( );
            $paymentInfo =& $details->getPaymentInfo( );

            $params['transaction_id'] = $paymentInfo->TransactionID;
            $params['payment_type'  ] = $paymentInfo->PaymentType;
            $params['payment_date'  ] = $paymentInfo->PaymentDate;
            $params['gross_amount'  ] = self::getAmount( $paymentInfo->GrossAmount );
            $params['fee_amount'    ] = self::getAmount( $paymentInfo->FeeAmount    );
            $params['settle_amount' ] = self::getAmount( $paymentInfo->SettleAmount );
            $params['payment_status'] = $paymentInfo->PaymentStatus;
            $params['pending_reason'] = $paymentInfo->PendingReason;
            
            return $params;
        }

    }

    function getAmount( &$amount ) {
        return $amount->_value;
    }

    function doDirectPayment( &$params ) {
        $orderTotal =& Services_PayPal::getType( 'BasicAmountType' );  
  
        if ( Services_PayPal::isError( $orderTotal ) ) {  
            CRM_Core_Error::debug( 'v', $orderTotal );  
            exit;  
        }  
  
        $orderTotal->setattr( 'currencyID', $params['currencyID'] );  
        $orderTotal->setval( $params['amount'], self::CHARSET  );  
        $paymentDetails =& Services_PayPal::getType( 'PaymentDetailsType' );

        if ( Services_PayPal::isError( $paymentDetails ) ) {
            CRM_Core_Error::debug( 'v', $paymentDetails );
            exit;
        }

        $paymentDetails->setOrderTotal($orderTotal);
        $payerName =& Services_PayPal::getType( 'PersonNameType' );

        if ( Services_PayPal::isError( $payerName ) ) {
            CRM_Core_Error::debug( 'v', $payerName );
            exit;
        }

        $payerName->setLastName  ( $params['last_name'  ], self::CHARSET  );
        $payerName->setMiddleName( $params['middle_name'], self::CHARSET  );
        $payerName->setFirstName ( $params['first_name' ], self::CHARSET  );
        $address =& Services_PayPal::getType('AddressType');

        if (Services_PayPal::isError($address)) {
            CRM_Core_Error::debug( 'v', $address );
            exit;
        }

        $address->setStreet1        ( $params['street1']       , self::CHARSET );
        $address->setCityName       ( $params['city']          , self::CHARSET );
        $address->setStateOrProvince( $params['state_province'], self::CHARSET );
        $address->setPostalCode     ( $params['postal_code']   , self::CHARSET );
        $address->setCountry        ( $params['country']       , self::CHARSET );
        $cardOwner =& Services_PayPal::getType( 'PayerInfoType' );

        if ( Services_PayPal::isError( $cardOwner ) ) {
            CRM_Core_Error::debug( 'v', $cardOwner );
            exit;
        }

        $cardOwner->setAddress( $address );
        $cardOwner->setPayerCountry( $params['country'], self::CHARSET  );
        $cardOwner->setPayerName( $payerName );
        $creditCard =& Services_PayPal::getType( 'CreditCardDetailsType' );

        if ( Services_PayPal::isError( $creditCard ) ) {
            CRM_Core_Error::debug( 'v', $creditCard );
            exit;
        }

        $creditCard->setCardOwner( $cardOwner );
        $creditCard->setCVV2            ( $params['cvv2']              , self::CHARSET  );
        $creditCard->setExpYear         ( $params['year' ]             , self::CHARSET  );
        $creditCard->setExpMonth        ( $params['month']             , self::CHARSET  );
        $creditCard->setCreditCardNumber( $params['credit_card_number'], self::CHARSET  );
        $creditCard->setCreditCardType  ( $params['credit_card_type']  , self::CHARSET  );
        $doDirectPaymentRequestDetails =& Services_PayPal::getType( 'DoDirectPaymentRequestDetailsType' );

        if ( Services_PayPal::isError( $doDirectPaymentRequestDetails ) ) {
            CRM_Core_Error::debug( 'v', $doDirectPaymentRequestDetails );
            exit;
        }

        $doDirectPaymentRequestDetails->setCreditCard    ( $creditCard     );
        $doDirectPaymentRequestDetails->setPaymentDetails( $paymentDetails );
        $doDirectPaymentRequestDetails->setIPAddress     ( $params['ip_address'    ], self::CHARSET  );
        $doDirectPaymentRequestDetails->setPaymentAction ( $params['payment_action'], self::CHARSET  );
        $doDirectPayment =& Services_PayPal::getType( 'DoDirectPaymentRequestType' );

        if ( Services_PayPal::isError( $doDirectPayment ) ) {
            CRM_Core_Error::debug( 'v', $doDirectPayment );
            exit;
        }

        $doDirectPayment->setDoDirectPaymentRequestDetails( $doDirectPaymentRequestDetails );

        $result = $this->_caller->DoDirectPayment( $doDirectPayment );

        if ( Services_PayPal::isError( $result ) ) { 
            CRM_Core_Error::debug( 'v', $result );
        } else {
            /* Success */
            CRM_Core_Error::debug( 'v', $result );
        }

    }

}

?>
