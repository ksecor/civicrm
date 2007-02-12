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

require_once 'CRM/Core/Payment/PayPalImpl.php';

class CRM_Event_Payment_PayPalImpl extends CRM_Core_Payment_PayPalImpl {
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
     * 
     * @return object 
     * @static 
     * 
     */ 
    static function &singleton( $mode ) {
        if (self::$_singleton === null ) { 
            self::$_singleton =& new CRM_Event_Payment_PaypalImpl( $mode );
        } 
        return self::$_singleton; 
    } 

    function doTransferCheckout( &$params ) {
        $config =& CRM_Core_Config::singleton( );
        
        $notifyURL = $config->userFrameworkResourceURL . "extern/ipn.php?reset=1&module=event&contactID={$params['contactID']}&contributionID={$params['contributionID']}&contributionTypeID={$params['contributionTypeID']}&eventID={$params['eventID']}";
        
        $returnURL = CRM_Utils_System::url( 'civicrm/event/register', '_qf_ThankYou_display=1', true, null, false );
        $cancelURL = CRM_Utils_System::url( 'civicrm/event/register', '_qf_Register_display=1&cancel=1', true, null, false );
        
        $paypalParams =
            array( 'business'           => $config->paymentUsername[$this->_mode],
                   'notify_url'         => $notifyURL,
                   'item_name'          => $params['item_name'],
                   'quantity'           => 1,
                   'undefined_quantity' => 0,
                   'cancel_return'      => $cancelURL,
                   'no_note'            => 1,
                   'no_shipping'        => 1,
                   'return'             => $returnURL,
                   'rm'                 => 1,
                   'currency_code'      => $params['currencyID'],
                   'invoice'            => $params['invoiceID'] );
        
        // if recurring donations, add a few more items
        if ( ! empty( $params['is_recur'] ) ) {
            if ( $params['contributionRecurID'] ) {
                $notifyURL .= "&contributionRecurID={$params['contributionRecurID']}&contributionPageID={$params['contributionPageID']}";
                $paypalParams['notify_url'] = $notifyURL;
            } else {
                CRM_Core_Error::fatal( ts( 'Recurring contribution, but no database id' ) );
            }

            $paypalParams +=
                array( 'cmd'                => '_xclick-subscriptions',
                       'a3'                 => $params['amount'],
                       'p3'                 => $params['frequency_interval'],
                       't3'                 => ucfirst( substr( $params['frequency_unit'], 0, 1 ) ),
                       'src'                => 1,
                       'sra'                => 1,
                       'srt'                => ( $params['installments'] > 0 ) ? $params['installments'] : null,
                       'no_note'            => 1,
                       'modify'             => 0,
                       );
        } else {
            $paypalParams +=
                array( 'cmd'                => 'xclick',
                       'amount'             => $params['amount'],
                       );
        }

        $uri = '';
        foreach ( $paypalParams as $key => $value ) {
            if ( $value === null ) {
                continue;
            }

            $value = urlencode( $value );
            if ( $key == 'return' ||
                 $key == 'cancel_return' ||
                 $key == 'notify_url' ) {
                $value = str_replace( '%2F', '/', $value );
            }
            $uri .= "&{$key}={$value}";
        }

        $uri = substr( $uri, 1 );
        $url = ( $this->_mode == 'test' ) ? $config->paymentPayPalExpressTestUrl : $config->paymentPayPalExpressUrl;
        $sub = empty( $params['is_recur'] ) ? 'xclick' : 'subscriptions';
        $paypalURL = "https://{$url}/{$sub}/$uri";

        // CRM_Core_Error::debug( 'paypalParams', $paypalParams );
        // CRM_Core_Error::debug( 'paypalURL'   , $paypalURL );

        CRM_Core_Error::debug_var( 'paypalParams', $paypalParams );
        CRM_Core_Error::debug_var( 'paypalURL'   , $paypalURL );

        CRM_Utils_System::redirect( $paypalURL );

    }

}

?>
