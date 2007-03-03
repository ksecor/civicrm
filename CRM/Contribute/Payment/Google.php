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
 * @author Donald A. Lobo <lobo@civicrm.org> 
 * @copyright CiviCRM LLC (c) 2004-2007 
 * $Id$ 
 * 
 */ 


require_once('Google/library/googlecart.php');
require_once('Google/library/googleitem.php');

class CRM_Contribute_Payment_Google { 
    const
        CHARSET  = 'utf-8';
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
        $this->_mode = $mode;
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
            self::$_singleton =& new CRM_Contribute_Payment_Google( $mode );
        } 
        return self::$_singleton; 
    } 

    function doCheckout( &$params ) {
        $config =& CRM_Core_Config::singleton( );
        //print_r($params);
        //print_r($config);

        $url = ( $this->_mode == 'test' ) ? $config->paymentPayPalExpressTestUrl : $config->paymentPayPalExpressUrl;
        $url = 'https://' . $url . '/cws/v2/Merchant/' . $config->paymentUsername[$this->_mode] . '/checkout';
        
        //Create a new shopping cart object
        $merchant_id  = $config->paymentUsername[$this->_mode];  //Your Merchant ID
        $merchant_key = $config->paymentKey[$this->_mode];  //Your Merchant Key
        $server_type  = "sandbox"; //provide provision for live
        
        $cart =  new GoogleCart($merchant_id, $merchant_key, $server_type); 
        $item1 = new GoogleItem('Help Support CiviCRM','', 1, $params['amount']); //generalize title
        $cart->AddItem($item1);

        $privateData = "module=contribute,contactID={$params['contactID']},contributionID={$params['contributionID']},contributionTypeID={$params['contributionTypeID']},mode={$this->_mode}";
        if ( $params['selectMembership'] &&  $params['selectMembership'] != 'no_thanks' ) {
            $privateData .= ",membershipTypeID={$params['selectMembership']}";
        }
        
        $cart->SetMerchantPrivateData($privateData);
        
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
            CRM_Core_Error::fatal( ts( 'Invalid response code received from Google Checkout: ' . $request->getResponseCode( )) );
        }
        CRM_Utils_System::redirect( $request->getResponseHeader( 'location' ) );
        exit( );
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

        if ( empty( $config->paymentUsername[$mode] ) ) {
            if ( $mode == 'live' ) {
                $error[] = ts('%1 is not set in the Administer CiviCRM &raquo; Global Settings &raquo; Payment Processor.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_USERNAME (Merchant ID)'));
            } else {
                $error[] = ts('%1 is not set in the Administer CiviCRM &raquo; Global Settings &raquo; Payment Processor.', array(1 => 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_USERNAME (Merchant ID)'));
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
