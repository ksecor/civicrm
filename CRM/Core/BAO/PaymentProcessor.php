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

require_once 'CRM/Core/DAO/PaymentProcessor.php';


class CRM_Core_BAO_PaymentProcessor extends CRM_Core_DAO_PaymentProcessor {

    /**
     * static holder for the default payment processor
     */
    static $_defaultPaymentProcessor = null;


    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Core_BAO_LocaationType object on success, null otherwise
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $paymentProcessor =& new CRM_Core_DAO_PaymentProcessor( );
        $paymentProcessor->copyValues( $params );
        if ( $paymentProcessor->find( true ) ) {
            CRM_Core_DAO::storeValues( $paymentProcessor, $defaults );
            return $paymentProcessor;
        }
        return null;
    }
    
    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * 
     * @access public
     * @static
     */
    static function setIsActive( $id, $is_active ) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_PaymentProcessor', $id, 'is_active', $is_active );
    }
    
    /**
     * retrieve the default payment processor
     * 
     * @param NULL
     * 
     * @return object           The default payment processor object on success,
     *                          null otherwise
     * @static
     * @access public
     */
    static function &getDefault( ) {
        if (self::$_defaultPaymentProcessor == null) {
            $params = array( 'is_default' => 1,
                             'domain_id'  => CRM_Core_Config::domainID( ));
            $defaults = array();
            self::$_defaultPaymentProcessor = self::retrieve($params, $defaults);
        }
        return self::$_defaultPaymentProcessor;
    }
    
    /**
     * Function to delete payment processor
     * 
     * @param  int  $paymentProcessorId     ID of the processor to be deleted.
     * 
     * @access public
     * @static
     */
    static function del($paymentProcessorId) 
    {
    }

    static function getPayment( $paymentProcessorID, $mode ) {
        $dao =& new CRM_Core_DAO_PaymentProcessor( );
        
        $dao->id        = $paymentProcessorID;
        $dao->is_active = 1;
        $dao->domain_id = CRM_Core_Config::domainID( );
        if ( ! $dao->find( true ) ) {
            CRM_Core_Error::fatal( ts( 'Could not retrieve payment processor details' ) );
        }

        if ( $mode == 'test' ) {
            $testDAO =& new CRM_Core_DAO_PaymentProcessor( );
            $testDAO->name      = $dao->name;
            $testDAO->is_active = 1;
            $testDAO->is_test   = 1;
            $testDAO->domain_id = $dao->domain_id;
            if ( ! $testDAO->find( true ) ) {
                CRM_Core_Error::fatal( ts( 'Could not retrieve payment processor details' ) );
            }
            return self::buildPayment( $testDAO );
        } else {
            return self::buildPayment( $dao );
        }
    }

    static function buildPayment( $dao ) {

        $fields = array( 'name', 'payment_processor_type', 'user_name', 'password',
                         'signature', 'url_site', 'url_button', 'subject',
                         'class_name', 'is_recur', 'billing_mode');
        $result = array( );
        foreach ( $fields as $name ) {
            $result[$name] = $dao->$name;
        }
        return $result;
    }

}
?>
