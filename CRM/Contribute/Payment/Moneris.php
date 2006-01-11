<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.3                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright (c) 2005 Donald A. Lobo                                  | 
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
 * @author Alan Dixon
 * @copyright Donald A. Lobo (c) 2005 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Contribute/Payment.php';

class CRM_Contribute_Payment_Moneris extends CRM_Contribute_Payment { 
    const
        CHARSET  = 'UFT-8'; # (not used, implicit in the API, might need to convert?)
         
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
        require_once 'Services/mpgClasses.php'; // require moneris supplied api library
        $config =& CRM_Core_Config::singleton( ); // get merchant data from config
        $this->_profile['mode'] = $mode; // live or test
        $this->_profile['storeid'] = $config->paymentKey[$mode];
        $this->_profile['apitoken'] = $config->paymentPassword[$mode];
        if ('CAD' != $config->currencyID) {
          return self::error(); // Configuration error: default currency must be CAD
        }
    }

    /** 
     * singleton function used to manage this object 
     * 
     * @param string $mode the mode of operation: live or test
 
     * @return object 
     * @static 
     * 
     */ 
    static function &singleton( $mode ) {
        if (self::$_singleton === null ) { 
            self::$_singleton =& new CRM_Contribute_Payment_Moneris( $mode );
        } 
        return self::$_singleton; 
    } 


    function doDirectPayment( &$params ) {
      # make sure i've been called correctly ...
      if ( ! $this->_profile ) {
          return self::error( );
      }
      if ($params['currencyID'] != 'CAD') {
         return self::error();
      }

      /* unused params: cvv not yet implemented, payment action ingored (should test for 'Sale' value?)
      [cvv2] => 000
      [ip_address] => 192.168.0.103
      [payment_action] => Sale
      [contact_type] => Individual
      [geo_coord_id] => 1 */
      # this code based on Moneris example code #
      ## create an mpgCustInfo object
      $mpgCustInfo = new mpgCustInfo();
      ## call set methods of the mpgCustinfo object
      $mpgCustInfo->setEmail($params['email']);
      ## get text representations of province/country to send to moneris for billing info #
      $province = CRM_Core_PseudoConstant::stateProvince( $params['state_province_id'] );
      $country = CRM_Core_PseudoConstant::country($params['country_id']);
      $billing = array( first_name => $params['first_name'],
         last_name => $params['last_name'],
         address => $params['street_address'],
         city => $params['city'],
         province => $province,
         postal_code => $params['postal_code'],
         country => $country);
      $mpgCustInfo->setBilling($billing);
      $my_orderid = $params['invoiceID']; // set orderid as invoiceID to help match things up with Moneris later
      $expiry_string = sprintf('%04d%02d',$params['year'],$params['month']);
      $txnArray=array(type=>'purchase',
         order_id=>$my_orderid,
         amount=> sprintf('%01.2f',$params['amount']),
         pan=> $params['credit_card_number'],
         expdate=>substr($expiry_string,2,4),
         crypt_type=>'7',
         cust_id=>$params['contact_id']);
      ## create a transaction object passing the hash created above
      $mpgTxn = new mpgTransaction($txnArray);
  
      ## use the setCustInfo method of mpgTransaction object to
      ## set the customer info (level 3 data) for this transaction
      $mpgTxn->setCustInfo($mpgCustInfo);
  
      ## create a mpgRequest object passing the transaction object 
      $mpgRequest = new mpgRequest($mpgTxn);
  
      ## create mpgHttpsPost object which does an https post ## 
      // [extra parameter added to library by AD] 
      $isProduction = ($this->_profile['mode'] == 'live');
      $mpgHttpPost = new mpgHttpsPost($this->_profile['storeid'],$this->_profile['apitoken'],$mpgRequest,$isProduction);
      ## get an mpgResponse object ##
      $mpgResponse=$mpgHttpPost->getMpgResponse();
      $params['trxn_result_code'] = $mpgResponse->getResponseCode(); 

      if ( self::isError( $mpgResponse ) ) {
          return self::error( $mpgResponse );
      }

      /* Check for application errors */
      $result =& self::checkResult( $mpgResponse );
      if ( is_a( $result, 'CRM_Core_Error' ) ) {
          return $result;
      }

      /* Success */
      $params['trxn_result_code']        = (integer) $mpgResponse->getResponseCode();  
      // todo: above assignment seems to be ignored, not getting stored in the civicrm_financial_trxn table
      $params['trxn_id']        = $mpgResponse->getTxnNumber();
      $params['gross_amount'  ] = $mpgResponse->getTransAmount();
      return $params;

    }

    function isError( &$response) {
      $responseCode = $response->getResponseCode();
      if (null === $responseCode) return true;
      if (($responseCode >= 0) && ($responseCode < 50))
        return false;
      return true;
    }

    function &checkResult( &$response ) { // ignore for now, more elaborate error handling later.
        return $response;

        $errors = $response->getErrors( );
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
            $e->push( $error->getResponseCode( ),
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

        if ( empty( $config->paymentKey[$mode] ) ) {
            if ( $mode == 'live' ) {
                $error[] = ts( "CIVICRM_PAYMENT_KEY is not set in the config file." ); 
            } else {
                $error[] = ts( "CIVICRM_PAYMENT_TEST_KEY is not set in the config file." ); 
            }
        }
        
        if ( empty( $config->paymentPassword[$mode] ) ) {
            if ( $mode == 'live' ) {
                $error[] = ts( "CIVICRM_PAYMENT_PASSWORD is not set in the config file." );
            } else {
                $error[] = ts( "CIVICRM_PAYMENT_TEST_PASSWORD is not set in the config file." );
            }
        }

        if ( ! empty( $error ) ) {
            return implode( ' ', $error );
        } else {
            return null;
        }
    }

}

?>
