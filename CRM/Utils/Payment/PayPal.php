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
                                                                   'charset' => 'iso-8859-1',
                                                                   )
                                                             );
        
        if ( Services_PayPal::isError( $handler ) ) {
            CRM_Core_Error::debug( 'v', $handler->getMessage() );
            exit;
        }

        $this->_profile =& APIProfile::getInstance('7faf450cac153ccfe841c3e436b31d0a', $this->_handler);

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

    function expressCheckOut( &$params ) {

        $orderTotal =& Services_PayPal::getType( 'BasicAmountType' );

        if ( Services_PayPal::isError( $orderTotal ) ) {
            CRM_Core_Error::debug( 'v', $orderTotal );
            exit;
        }

        $orderTotal->setattr('currencyID', $params['currencyID'] );
        $orderTotal->setval( $params['amount'], 'iso-8859-1');
        $setExpressCheckoutRequestDetails =& Services_PayPal::getType( 'SetExpressCheckoutRequestDetailsType' );

        if ( Services_PayPal::isError( $setExpressCheckoutRequestDetails ) ) {
            CRM_Core_Error::debug( 'v', $setExpressCheckoutRequestDetails );
            exit;
        }

        $setExpressCheckoutRequestDetails->setCancelURL ( $params['cancelURL'], 'iso-8859-1' );
        $setExpressCheckoutRequestDetails->setReturnURL ( $params['returnURL'], 'iso-8859-1' );
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

        $getExpressCheckoutDetails->setToken( $token, 'iso-8859-1');

        $result = $this->_caller->GetExpressCheckoutDetails( $getExpressCheckoutDetails );

        if ( Services_PayPal::isError( $result ) ) { 
            CRM_Core_Error::debug( 'v', $result );
        } else {
            /* Success */
            $detail                =& $result->getGetExpressCheckoutDetailsResponseDetails( );
            $payer                 =& $detail->getPayerInfo ( );

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

}
