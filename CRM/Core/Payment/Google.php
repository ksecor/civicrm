<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/
 
/** 
 * 
 * @package CRM 
 * @copyright CiviCRM LLC (c) 2004-2009 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Core/Payment.php';

class CRM_Core_Payment_Google extends CRM_Core_Payment { 
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
     * This function checks to see if we have the right config values 
     * 
     * @return string the error message if any 
     * @public 
     */ 
    function checkConfig( ) {
        $config =& CRM_Core_Config::singleton( );

        $error = array( );

        if ( empty( $this->_paymentProcessor['user_name'] ) ) {
            $error[] = ts( 'User Name is not set in the Administer CiviCRM &raquo; Payment Processor.' );
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

    function doDirectPayment( &$params ) {
        CRM_Core_Error::fatal( ts( 'This function is not implemented' ) );
    }

    /**  
     * Sets appropriate parameters for checking out to google
     *  
     * @param array $params  name value pair of contribution datat
     *  
     * @return void  
     * @access public 
     *  
     */  
    function doTransferCheckout( &$params, $component ) {
        $component = strtolower( $component );
        
        $url = 
            $this->_paymentProcessor['url_site'] .
            'cws/v2/Merchant/' . 
            $this->_paymentProcessor['user_name'] .
            '/checkout';
        
        //Create a new shopping cart object
        $merchant_id  = $this->_paymentProcessor['user_name'];   // Merchant ID
        $merchant_key = $this->_paymentProcessor['password'];    // Merchant Key
        $server_type  = ( $this->_mode == 'test' ) ? 'sandbox' : '';
        
        $cart  = new GoogleCart($merchant_id, $merchant_key, $server_type); 
        $item1 = new GoogleItem($params['item_name'],'', 1, $params['amount'], $params['currencyID']);
        $cart->AddItem($item1);

        if ( $component == "event" ) {
            $privateData = "contactID={$params['contactID']},contributionID={$params['contributionID']},contributionTypeID={$params['contributionTypeID']},eventID={$params['eventID']},participantID={$params['participantID']},invoiceID={$params['invoiceID']}";
        } elseif ( $component == "contribute" ) {
            $privateData = "contactID={$params['contactID']},contributionID={$params['contributionID']},contributionTypeID={$params['contributionTypeID']},invoiceID={$params['invoiceID']}";

            $membershipID = CRM_Utils_Array::value( 'membershipID', $params );
            if ( $membershipID ) {
                $privateData .= ",membershipID=$membershipID";
            }

            $relatedContactID = CRM_Utils_Array::value( 'related_contact', $params );
            if ( $relatedContactID ) {
                $privateData .= ",relatedContactID=$relatedContactID";

                $onBehalfDupeAlert = CRM_Utils_Array::value( 'onbehalf_dupe_alert', $params );
                if ( $onBehalfDupeAlert ) {
                    $privateData .= ",onBehalfDupeAlert=$onBehalfDupeAlert";
                }
            }
        }
        
        $cart->SetMerchantPrivateData($privateData);
        
        if ( $component == "event" ) {
            $returnURL = CRM_Utils_System::url( 'civicrm/event/register',
                                                "_qf_ThankYou_display=1&qfKey={$params['qfKey']}", 
                                                true, null, false );
        } elseif ( $component == "contribute" ) {
            $returnURL = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                                "_qf_ThankYou_display=1&qfKey={$params['qfKey']}",
                                                true, null, false );
        }

        $cart->SetContinueShoppingUrl( $returnURL );

        $cartVal      = base64_encode($cart->GetXML());
        $signatureVal = base64_encode($cart->CalcHmacSha1($cart->GetXML()));
        
        $googleParams = array('cart'      => $cartVal,
                              'signature' => $signatureVal );
        
        require_once 'HTTP/Request.php';
        $params = array( 'method' => HTTP_REQUEST_METHOD_POST,
                         'allowRedirects' => false );
        $request =& new HTTP_Request( $url, $params );
        foreach ( $googleParams as $key => $value ) {
            $request->addPostData($key, $value);
        }

        $result = $request->sendRequest( );

        if ( PEAR::isError( $result ) ) {
            CRM_Core_Error::fatal( $result->getMessage( ) );
        }
        
        if ( $request->getResponseCode( ) != 302 ) {
            CRM_Core_Error::fatal( ts( 'Invalid response code received from Google Checkout: %1', 
                                       array(1 => $request->getResponseCode())) );
        }
        CRM_Utils_System::redirect( $request->getResponseHeader( 'location' ) );

        exit( );
    }

    /**
     * hash_call: Function to perform the API call to PayPal using API signature
     * @paymentProcessor is the array of payment processor settings value.
     * @searchParamsnvpStr is the array of search params.
     * returns an associtive array containing the response from the server.
     */
    function invokeAPI( $paymentProcessor, $searchParams ) {
        $merchantID  = $paymentProcessor['user_name'];
        $merchantKey = $paymentProcessor['password'];
        $siteURL     = rtrim(str_replace('https://', '', $paymentProcessor['url_site']), '/');
        
        $url = "https://{$merchantID}:{$merchantKey}@{$siteURL}/api/checkout/v2/reports/Merchant/{$merchantID}";
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<notification-history-request xmlns="http://checkout.google.com/schema/2">
    <start-time>' . $searchParams['start'] . '</start-time>
    <end-time>'   . $searchParams['end']   . '</end-time>
    <notification-types>
        <notification-type>risk-information</notification-type>
        <notification-type>charge-amount</notification-type>
    </notification-types>
</notification-history-request>';

        // Add the below to $xml for specific requests
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        
        //getting response from server
        $xmlResponse = curl_exec( $ch );

        // strip slashes if needed
        if (get_magic_quotes_gpc()) {
            $xmlResponse = stripslashes($xmlResponse);
        }

        if ( curl_errno( $ch ) ) {
            $e =& CRM_Core_Error::singleton( );
            $e->push( curl_errno( $ch ),
                      0, null,
                      curl_error( $ch ) );
            return $e;
        } else {
			curl_close($ch);
        }

        return $xmlResponse;
   }

    function processAPIContribution( $xmlResponse, $mapper ) {
        require_once 'Google/library/xml-processing/xmlparser.php';

        // Retrieve the root and data from the xml response
        $xmlParser = new XmlParser($xmlResponse);
        $root      = $xmlParser->GetRoot();
        $data      = $xmlParser->GetData();

        $chargedNotification =& $data[$root]['notifications']['charge-amount-notification'];
        $details             =& $data[$root]['notifications']['risk-information-notification'];

        // store all successfully charged transaction numbers
        $chargedAccounts = array();
        foreach ( $chargedNotification as $info ) {
            $chargedAccounts[$info['google-order-number']['VALUE']] = $info['total-charge-amount'];
        }
        
        require_once 'CRM/Contribute/DAO/Contribution.php';
        require_once 'CRM/Dedupe/Finder.php';
        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Contribute/BAO/Contribution.php';

        foreach ( $details as $detail ) {
            if ( array_key_exists($detail['google-order-number']['VALUE'], $chargedAccounts) ) {
                $params = array( );
                foreach ( $detail['risk-information']['billing-address'] as $field => $info ) {
                    if ( CRM_Utils_Array::value( $field, $mapper['location'] ) ) {
                        $params['location'][1]['address'][$mapper['location'][$field]] = $info['VALUE'];
                    } else if ( CRM_Utils_Array::value( $field, $mapper['contact'] ) ) {
                        $params[$mapper['contact'][$field]] = $info['VALUE'];
                    } else if ( CRM_Utils_Array::value( $field, $mapper['transaction'] ) ) {
                        $params[$mapper['transaction'][$field]] = $info['VALUE'];
                    }
                }

                if ( CRM_Utils_Array::value( 'google-order-number', $mapper['transaction'] ) ) {
                    $params[$mapper['transaction']['google-order-number']] = $detail['google-order-number']['VALUE'];
                }

                if ( CRM_Utils_Array::value( 'total-charge-amount', $mapper['transaction'] ) ) {
                    $params[$mapper['transaction']['total-charge-amount']] 
                        = $chargedAccounts[$detail['google-order-number']['VALUE']]['VALUE'];
                    $params['currency'] = $chargedAccounts[$detail['google-order-number']['VALUE']]['currency'];
                }

                if ( empty($params) ) {
                    continue;
                }
                CRM_Core_Error::debug( '$params', $params );
                
                if ( isset( $params['trxn_id'] ) ) {
                    // return if transaction already processed.
                    $contribution =& new CRM_Contribute_DAO_Contribution();
                    $contribution->trxn_id = $params['trxn_id'];
                    if ( $contribution->find(true) ) {
                        continue;
                    }
                } else {
                    $params['trxn_id'] = md5( uniqid( rand( ), true ) );
                }
                
                // fill default params
                $params['contact_type']  = 'Individual';
                
                if ( array_key_exists( 'location', $params ) ) {
                    $params['location'][1]['is_primary']        = 1;
                    $params['location'][1]['location_type_id']  = 
                        CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_LocationType', 'Billing', 'id', 'name' );
                    
                    if ( isset($params['email']) ) {
                        $params['location'][1]['email'][1]['email'] = $params['email'];
                        unset($params['email']);
                    }
                }
                
                // === add contact using dedupe rule ===
                $dedupeParams = CRM_Dedupe_Finder::formatParams ($params      , 'Individual');
                $dupeIds      = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual');
                // if we find more than one contact, use the first one
                if ( CRM_Utils_Array::value( 0, $dupeIds ) ) {
                    $params['contact_id'] = $dupeIds[0];
                }
                $contact = CRM_Contact_BAO_Contact::create( $params );
                if ( ! $contact->id ) {
                    continue;
                }
                
                // === create contribution ===
                $contribution =& CRM_Contribute_BAO_Contribution::create( $params,
                                                                          CRM_Core_DAO::$_nullArray );
            }
        }
    }

}
