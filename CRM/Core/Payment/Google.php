<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007 
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
                                                false, null, false );
        } elseif ( $component == "contribute" ) {
            $returnURL = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                                "_qf_ThankYou_display=1&qfKey={$params['qfKey']}",
                                                false, null, false );
        }

        $cart->SetContinueShoppingUrl( $returnURL );

        $cartVal      = base64_encode($cart->GetXML());
        $signatureVal = base64_encode($cart->CalcHmacSha1($cart->GetXML()));
        
        $googleParams = array('cart'      => $cartVal,
                              'signature' => $signatureVal );
        
        require_once 'HTTP/Request.php';
        $params = array( 'method' => HTTP_REQUEST_METHOD_POST,
                         'allowRedirects' => false );
        $request =& new HTTP_Request( $url, $params )
;
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
}


