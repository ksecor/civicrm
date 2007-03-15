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
 * @copyright CiviCRM LLC (c) 2004-2007 
 * $Id$ 
 * 
 */ 


require_once('CRM/Core/Payment/Google.php');
require_once('Google/library/googlecart.php');
require_once('Google/library/googleitem.php');

class CRM_Contribute_Payment_Google extends CRM_Core_Payment_Google { 
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
        parent::__construct( $mode );
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

    /**  
     * Sets appropriate parameters for checking out to google
     *  
     * @param array $params  name value pair of contribution datat
     *  
     * @return void  
     * @access public 
     *  
     */  
    function doCheckout( &$params ) {
        $config =& CRM_Core_Config::singleton( );

        $url = ( $this->_mode == 'test' ) ? $config->googleCheckoutTestUrl : $config->googleCheckoutUrl;
        $url = 'https://' . $url . '/cws/v2/Merchant/' . $config->merchantID[$this->_mode] . '/checkout';
        
        //Create a new shopping cart object
        $merchant_id  = $config->merchantID[$this->_mode];  // Merchant ID
        $merchant_key = $config->paymentKey[$this->_mode];  // Merchant Key
        $server_type  = ( $this->_mode == 'test' ) ? 'sandbox' : '';
        
        $cart =  new GoogleCart($merchant_id, $merchant_key, $server_type); 
        $item1 = new GoogleItem($params['item_name'],'', 1, $params['amount']);
        $cart->AddItem($item1);

        $privateData = "contactID={$params['contactID']},contributionID={$params['contributionID']},contributionTypeID={$params['contributionTypeID']},invoiceID={$params['invoiceID']}";
        if ( $params['selectMembership'] &&  $params['selectMembership'] != 'no_thanks' ) {
            $privateData .= ",membershipTypeID={$params['selectMembership']}";
        }
        
        $cart->SetMerchantPrivateData($privateData);

        $returnURL = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                            "_qf_ThankYou_display=1&qfKey={$params['qfKey']}",
                                            true, null, false );
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
            CRM_Core_Error::fatal( ts( 'Invalid response code received from Google Checkout: %1', array(1 => $request->getResponseCode())) );
        }
        CRM_Utils_System::redirect( $request->getResponseHeader( 'location' ) );
        exit( );
    }

}

?>
