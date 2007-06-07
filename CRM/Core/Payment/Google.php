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
     * @param  string $mode the mode we are operating in (live or test) 
     * 
     * @return string the error message if any 
     * @public 
     */ 
    function checkConfig( $mode ) {
        $config =& CRM_Core_Config::singleton( );

        $error = array( );

        if ( empty( $this->_paymentProcessor['user_name'] ) ) {
            $error[] = ts( 'user_name is not set in the Administer CiviCRM &raquo; Payment Processor.' );
        }
        
        if ( empty( $this->_paymentProcessor['password'] ) ) {
            $error[] = ts( 'password is not set in the Administer CiviCRM &raquo; Payment Processor.' );
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

}

?>
