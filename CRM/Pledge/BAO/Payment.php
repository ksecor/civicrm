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

require_once 'CRM/Pledge/DAO/Payment.php';

class CRM_Pledge_BAO_Payment extends CRM_Pledge_DAO_Payment
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Function to get pledge payment details
     *  
     * @param int $pledgeId pledge id
     *
     * @return array associated array of pledge payment details
     * @static
     */
    static function getPledgePayments( $pledgeId )
    {
        $query = "
SELECT civicrm_pledge_payment.id id, scheduled_amount, scheduled_date, reminder_date, reminder_count,
        total_amount, receive_date, civicrm_option_value.name as status
FROM civicrm_pledge_payment
LEFT JOIN civicrm_contribution ON civicrm_pledge_payment.contribution_id = civicrm_contribution.id
LEFT JOIN civicrm_option_group ON ( civicrm_option_group.name = 'contribution_status' )
LEFT JOIN civicrm_option_value ON ( civicrm_pledge_payment.status_id = civicrm_option_value.value
AND civicrm_option_group.id = civicrm_option_value.option_group_id )
WHERE pledge_id = %1
";

        $params[1] = array( $pledgeId, 'Integer' );
        $payment = CRM_Core_DAO::executeQuery( $query, $params );

        $paymentDetails = array( );
        while ( $payment->fetch( ) ) {
            $paymentDetails[$payment->id]['scheduled_amount'] = $payment->scheduled_amount;
            $paymentDetails[$payment->id]['scheduled_date'  ] = $payment->scheduled_date;
            $paymentDetails[$payment->id]['reminder_date'   ] = $payment->reminder_date;
            $paymentDetails[$payment->id]['reminder_count'  ] = $payment->reminder_count;
            $paymentDetails[$payment->id]['total_amount'    ] = $payment->total_amount;
            $paymentDetails[$payment->id]['receive_date'    ] = $payment->receive_date;
            $paymentDetails[$payment->id]['status'          ] = $payment->status;
            $paymentDetails[$payment->id]['id'              ] = $payment->id;
        }
        
        return $paymentDetails;
    }

    /**
     * Add pledge payment
     *
     * @param array $params associate array of field
     *
     * @return pledge payment id 
     * @static
     */
    static function add( $params )
    {
        require_once 'CRM/Pledge/DAO/Payment.php';
        $payment =& new CRM_Pledge_DAO_Payment( );
        $payment->copyValues( $params );
        $result = $payment->save( );

        return $result;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * pledge id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Pledge_BAO_Payment object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $payment =& new CRM_Pledge_DAO_Payment;
        $payment->copyValues( $params );
        if ( $payment->find( true ) ) {
            CRM_Core_DAO::storeValues( $payment, $defaults );
            return $payment;
        }
        return null;
    }
    

}

