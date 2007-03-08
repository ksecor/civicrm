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

abstract class CRM_Core_Payment_GoogleIPN {

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

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
    function __construct( $mode ) {
        $this->_mode = $mode;
    }

    /**  
     * The function gets called when a new order takes place.
     *  
     * @param xml   $dataRoot    response send by google in xml format
     * @param array $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     * @abstract  
     *  
     */  
    abstract function newOrderNotify($dataRoot, $privateData);
    
    /**  
     * The function gets called when the state(CHARGED, CANCELLED..) changes for an order
     *  
     * @param string $status      status of the transaction send by google
     * @param array  $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     * @abstract  
     *  
     */  
    abstract function orderStateChange($status, $dataRoot);

    /**  
     * singleton function used to manage this object  
     *  
     * @param string $mode the mode of operation: live or test
     *  
     * @return object  
     * @static  
     */  
    static function &singleton( $mode = 'test', $component ) {
        if ( self::$_singleton === null ) {
            $config       =& CRM_Core_Config::singleton( );
            $paymentClass = "CRM_{$component}_" . $config->paymentFile . "IPN";
            
            $classPath = str_replace( '_', '/', $paymentClass ) . '.php';
            require_once($classPath);
            self::$_singleton = eval( 'return ' . $paymentClass . '::singleton( $mode );' );
        }
        return self::$_singleton;
    }
    
    /**  
     * The function retrieves the amount the contribution is for, based on the order-no google sends
     *  
     * @param int $orderNo <order-total> send by google
     *  
     * @return amount  
     * @access public 
     */  
    function getAmount($orderNo) {
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->invoice_id = $orderNo;
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
            echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
            return;
        }
        return $contribution->total_amount;
    }

    /**  
     * The function returns the component(Event/Contribute..), given the google-order-no and merchant-private-data
     *  
     * @param xml     $xml_response   response send by google in xml format
     * @param array   $privateData    contains the name value pair of <merchant-private-data>
     * @param int     $orderNo        <order-total> send by google
     * @param string  $root           root of xml-response
     *  
     * @return component/module  
     * @static  
     */  
    static function getModule($xml_response, $privateData, $orderNo, $root) {
        require_once 'CRM/Contribute/DAO/Contribution.php';
        
        if ($root == 'new-order-notification') {
            $contributionID   = $privateData['contributionID'];
            $contribution     =& new CRM_Contribute_DAO_Contribution( );
            $contribution->id = $contributionID;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
                echo "Failure: Could not find contribution record for $contributionID<p>";
                return;
            }
            if (stristr($contribution->source, 'Online Contribution')) {
                return 'Contribute';
            } elseif (stristr($contribution->source, 'Online Event Registration')) {
                return 'Event';
            }
        } else {
            $contribution =& new CRM_Contribute_DAO_Contribution( );
            $contribution->invoice_id = $orderNo;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
                echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
                return;
            }
            if (stristr($contribution->source, 'Online Contribution')) {
                return 'Contribute';
            } elseif (stristr($contribution->source, 'Online Event Registration')) {
                return 'Event';
            }
        }
        
        CRM_Core_Error::debug_log_message( "Could not find the module or component (Contribute/Event)" );
        CRM_Core_Error::debug_log_message( "Contribution ID received in private data: {$privateData['contributionID']}" );
        CRM_Core_Error::debug_log_message( "Invoice ID received in private data: {$privateData['invoiceID']}" );
        CRM_Core_Error::debug_log_message( "Google oredr No: $orderNo" );
        exit();
    }

    /**  
     * The function returns the mode(test, live..), given the google-order-no and merchant-private-data
     *  
     * @param xml     $xml_response   response send by google in xml format
     * @param array   $privateData    contains the name value pair of <merchant-private-data>
     * @param int     $orderNo        <order-total> send by google
     * @param string  $root           root of xml-response
     *  
     * @return mode  
     * @static  
     */  
    static function getMode($xml_response, $privateData, $orderNo, $root) {
        require_once 'CRM/Contribute/DAO/Contribution.php';
        
        if ($root == 'new-order-notification') {
            $contributionID   = $privateData['contributionID'];
            $contribution     =& new CRM_Contribute_DAO_Contribution( );
            $contribution->id = $contributionID;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
                echo "Failure: Could not find contribution record for $contributionID<p>";
                return;
            }
            return $contribution->is_test;
        } else {
            $contribution =& new CRM_Contribute_DAO_Contribution( );
            $contribution->invoice_id = $orderNo;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
                echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
                return;
            }
            return $contribution->is_test;
        }
    }

}

?>
