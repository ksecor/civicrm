<?php 
 
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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
 * @author Alan Dixon
 * @copyright CiviCRM LLC (c) 2004-2007 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Core/Payment.php';

class CRM_Core_Payment_IATS extends CRM_Core_Payment { 
    const
        CHARSET  = 'UFT-8'; # (not used, implicit in the API, might need to convert?)
    const
        CURRENCIES = 'CAD';         
//        CURRENCIES = 'CAD,USD';         
    /** 
     * Constructor 
     *
     * @param string $mode the mode of operation: live or test
     * 
     * @return void 
     */ 
    function __construct( $mode, &$paymentProcessor ) {
        $this->_paymentProcessor = $paymentProcessor;

        require_once 'Services/IATS/iatslink.php'; // require IATS supplied api library
        $config =& CRM_Core_Config::singleton( ); // get merchant data from config
        $this->_profile['mode'] = $mode; // live or test
        $this->_profile['webserver'] = parse_url($this->_paymentProcessor['url_site'],PHP_URL_HOST);
        $currencyID = $config->defaultCurrency;
        if (!in_array($currencyID,explode(',',self::CURRENCIES))) {
          // Configuration error: default currency must be in CURRENCIES const
          return self::error('Invalid configuration:'.$currencyID.', you must use one of '.self::CURRENCIES.' with IATS'); 
        }
    }

    function doDirectPayment( &$params ) {
      /* $result = '';
      foreach($params as $key => $value) {
        $result .= "<strong>$key</strong>: $value<br />";
      } 
      return self::error($result); */
      # make sure i've been called correctly ...
      if ( ! $this->_profile ) {
          return self::error('Unexpected error, missing profile');
      }
      if (!in_array($params['currencyID'],explode(',',self::CURRENCIES))) {
         return self::error('Invalid currency selection, must be one of '.self::CURRENCIES);
      }
      $canDollar = ($params['currencyID'] == 'CAD');  //define currency type
      # AgentCode  = $this->_paymentProcessor['signature'];
      # Password = $this->_paymentProcessor['password' ];
      # beginning of modified sample code from IATS php api 
      $iatslink1 = new iatslink;
      
      $iatslink1->setTestMode($this->_profile['mode'] == 'live');
      $iatslink1->setWebServer($this->_profile['webserver']);
    
      # return self::error($this->_profile['webserver']);
      $iatslink1->setInvoiceNumber($params['invoiceID']); // Put your invoice # here
      // $iatslink1->setCardType("VISA");
      // If CardType is not set, iatslink will find the cardType
      $iatslink1->setCardType($params['credit_card_type']);
      $iatslink1->setCardNumber($params['credit_card_number']);
      $iatslink1->setCVV2($params['cvv2']);
      $expiry_string = sprintf('%02d/%02d',$params['month'],($params['year'] % 100));
      $iatslink1->setCardExpiry($expiry_string);
      $amount = sprintf('%01.2f',$params['amount']);
      $iatslink1->setDollarAmount($amount);    //sell
        //$iatslink1->setDollarAmount(-1.15); //refund
      
      
      if ($canDollar == true) { 
        //Fields setting for Canadian credit card processing
        $AgentCode  = $this->_paymentProcessor['signature'];
        $Password = $this->_paymentProcessor['password' ];
        $CardHolderName = $params['billing_first_name'].' '.$params['billing_last_name'];
        $iatslink1->setAgentCode($AgentCode);
        $iatslink1->setPassword($Password);
        $iatslink1->setCardholderName($CardHolderName);
      }   
      else {    
        //Fields setting for US credit card processing.
        die('USD not implemented!');
        $iatslink1->setAgentCode("UTES01");
        $iatslink1->setPassword("UTES01");      
        $iatslink1->setFirstName("TEST");
        $iatslink1->setLastName("WRONG CARD");
        $iatslink1->setStreetAddress("101ST 101AVE");
        $iatslink1->setCity("VAN");
        $iatslink1->setState("BC");
        $iatslink1->setZipCode("V1V 1V1");
      }
      // go! ... uses curl to post and retrieve values
      // after various data integrity tests 
      $iatslink1->processCreditCard();
        
      if ($iatslink1->getStatus() == 1) {  
        // this just means we got some kind of answer, not necessarily approved
        $result = $iatslink1->getAuthorizationResult();
        # return self::error($result);
        $result = explode(':',$result,2);
        $trxn_result = trim($result[0]);
        $trxn_id = trim($result[1]);
        if ($trxn_result == 'OK') {
          $params['trxn_id']        = $trxn_id.':'.time();
          $params['gross_amount'  ] = $amount;
          return $params;
        }
        else {
          return self::error($trxn_id);
        }
      }
      else {
       return self::error($iatslink1->getError());
      }
    }

    function &error( $error = null ) {
        $e =& CRM_Core_Error::singleton( );
        if ( is_object($error) ) {
            $e->push( $error->getResponseCode( ),
                      0, null,
                      $error->getMessage( ) );
        } elseif ($error && is_numeric($error) ) {
            $e->push( $error,
                      0, null,
                      $this->errorString($error) );
        } elseif ( is_string($error) ) {
            $e->push( 9002,
                      0, null,
                      $error );
        } else {
            $e->push( 9001, 0, null, "Unknown System Error." );
        }
        return $e;
    }

    function errorString($error_id) {
      $errors = array(
        1 => 'Agent Code has not been set up on the authorization system.',
        2 => 'Unable to process transaction. Verify and re-enter credit card information.',
        3 => 'Charge card expired.',
        4 => 'Incorrect expiration date.',
        5 => 'Invalid transaction. Verify and re-enter credit card information.',
        6 => 'Transaction not supported by institution.',
        7 => 'Lost or stolen card.',
        8 => 'Invalid card status.',
        9 => 'Restricted card status. Usually on corporate cards restricted to specific sales.',
        10 => 'Error. Please verify and re-enter credit card information.',
        11 => 'General decline code, may have different reasons for each card type. Please have your client call customer service.',
        14 => 'This means that the credit card is over the limit.',
        15 => 'Decline code, may have different reasons for each card type. Please have your client call customer service.',
        16 => 'Invalid charge card number. Verify and re-enter credit card information.',
        17 => 'Unable to authorize transaction. Verify card information with customer and re-enter. Could be invalid name or expiry date.',
        18 => 'Card not supported by institution.',
        19 => 'Incorrect CVV2.',
        22 => 'Bank Timeout. Bank lines may be down or busy. Re-try transaction later.',
        23 => 'System error. Re-try transaction later.',
        24 => 'Charge card expired.',
        25 => 'Capture card. Reported lost or stolen.',
        27 => 'System error, please re-enter transaction.',
        29 => 'Rejected by Ticketmaster.',
        31 => 'Manual reject code ',
        39 => 'Contact Ticketmaster 1-888-955-5455 ',
        40 => 'Card not supported by Ticketmaster. Invalid cc number.',
        41 => 'Invalid Expiry date ',
        100 => 'Authorization system down. DO NOT REPROCESS.',
      );
      return ' <strong>'.$errors[(integer) $error_id].'</strong>';

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
        
        if ( empty( $this->_paymentProcessor['signature'] ) ) {
            $error[] = ts( 'Agent Code is not set in the Administer CiviCRM &raquo; Payment Processor.' );
        }
            
        if ( empty( $this->_paymentProcessor['password'] ) ) {
            $error[] = ts( 'Password is not set in the Administer CiviCRM &raquo; Payment Processor.' );
        }

        if ( ! empty( $error ) ) {
            return implode( '<p>', $error );
        } else {
            return null;
        }
    }

}


