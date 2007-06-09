<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Payment/GoogleIPN.php';

class CRM_Contribute_Payment_GoogleIPN extends CRM_Core_Payment_GoogleIPN {

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
            self::$_singleton =& new CRM_Contribute_Payment_GoogleIPN( $mode );
        } 
        return self::$_singleton; 
    } 

    /**  
     * The function gets called when a new order takes place. And does all the preliminary operations.
     *  
     * @param xml   $dataRoot    response send by google in xml format
     * @param array $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     * @access public 
     *  
     */  
    function newOrderNotify( $dataRoot, $privateData ) {
        parent::newOrderNotify( $dataRoot, $privateData, 'contribute' );
    }
    
    /**  
     * The function gets called when the state(CHARGED, CANCELLED..) changes for an order. 
     * And the operations are performed based on the state.
     *  
     * @param string $status     status of the transaction send by google
     * @param array  $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     * @access public 
     *  
     */  
    function orderStateChange( $status, $dataRoot ) {
        parent::orderStateChange($status, $dataRoot, 'contribute' );
    }
    
}

?>
