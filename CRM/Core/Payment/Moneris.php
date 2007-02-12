<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.7                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright CiviCRM LLC (c) 2004-2007                                  | 
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
 * @author Alan Dixon
 * @copyright CiviCRM LLC (c) 2004-2007 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Core/Payment.php';

class CRM_Core_Payment_Moneris extends CRM_Core_Payment { 
    const
        CHARSET  = 'UFT-8'; # (not used, implicit in the API, might need to convert?)
         
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
      # add a recurring payment if requested
      if ($params['is_recur'] && $params['installments'] > 1) {
        // ------ Recur Variables
        $recurUnit = $params['frequency_unit'];
        $recurInterval = $params['frequency_interval'];
        $next = time();
        $day = 60 * 60 * 24;
        switch($recurUnit) {
          case 'day': $next += $recurInterval * $day; break;
          case 'week': $next += $recurInterval * $day * 7; break;
          case 'month': 
              $date = getdate(); 
              $date['mon'] += $recurInterval;
              while ($date['mon'] > 12) {
                $date['mon'] -= 12;
                $date['year'] += 1;
              }
              $next = mktime($date['hours'],$date['minutes'],$date['seconds'],$date['mon'],$date['mday'],$date['year']);
              break;
          case 'year':
              $date = getdate(); 
              $date['year'] += 1;
              $next = mktime($date['hours'],$date['minutes'],$date['seconds'],$date['mon'],$date['mday'],$date['year']);
              break;
          default: die('Unexpected error!');
        } 
        $startDate = date("Y/m/d",$next); // next payment in moneris required format
        $numRecurs = $params['installments'] - 1;
        #$startNow = 'true'; -- setting start now to false will mean the main transaction doesn't happen!
        $recurAmount = sprintf('%01.2f',$params['amount']);
        // ------ Create an array with the recur variables
        $recurArray = array(recur_unit=>$recurUnit, // (day | week | month)
                start_date=>$startDate, // yyyy/mm/dd
                num_recurs=>$numRecurs,
                start_now=>'true',
                period => $recurInterval,
                recur_amount=> $recurAmount
                );
        $mpgRecur = new mpgRecur($recurArray);
        // set the Recur Object to mpgRecur
        $mpgTxn->setRecur($mpgRecur);
      } 
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
      if (is_null($responseCode)) return true;
      if ('null' == $responseCode) return true;
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

        if ( ! empty( $error ) ) {
            return implode( ' ', $error );
        } else {
            return null;
        }
    }

}

?>
