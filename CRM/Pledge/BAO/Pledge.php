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

require_once 'CRM/Pledge/DAO/Pledge.php';

class CRM_Pledge_BAO_Pledge extends CRM_Pledge_DAO_Pledge 
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
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
     * @return object CRM_Pledge_BAO_Pledge object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $pledge = new CRM_Pledge_DAO_Pledge( );
        $pledge->copyValues( $params );
        if ( $pledge->find( true ) ) {
            CRM_Core_DAO::storeValues( $pledge, $defaults );
            return $pledge;
        }
        return null;
    }
    
    /**
     * function to add pledge
     *
     * @param array $params reference array contains the values submitted by the form
     *
     * @access public
     * @static 
     * @return object
     */
    static function add( &$params)
    {
        require_once 'CRM/Utils/Hook.php';
        
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Pledge', $params['id'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Pledge', null, $params ); 
        }
        
        $pledge =& new CRM_Pledge_DAO_Pledge( );
        
        $pledge->copyValues( $params );
        $result = $pledge->save( );
        
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            CRM_Utils_Hook::post( 'edit', 'Pledge', $pledge->id, $pledge );
        } else {
            CRM_Utils_Hook::post( 'create', 'Pledge', $pledge->id, $pledge );
        }
        
        return $result;
    }
    
    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Pledge_BAO_Pledge|null the found object or null
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) 
    {
        $pledge =& new CRM_Pledge_BAO_Pledge( );
        $pledge->copyValues( $params );
        
        if ( $pledge->find(true) ) {
            $ids['pledge'] = $pledge->id;
            CRM_Core_DAO::storeValues( $pledge, $values );
            return $pledge;
        }
        
        return null;
    }
    
    /**
     * takes an associative array and creates a pledge object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Pledge_BAO_Pledge object 
     * @access public
     * @static
     */
    static function &create( &$params ) 
    { 
        require_once 'CRM/Utils/Date.php';
        //FIXME: a cludgy hack to fix the dates to MySQL format
        $dateFields = array( 'start_date', 'create_date', 'acknowledge_date', 'modified_date', 'cancel_date', 'end_date' );
        foreach ($dateFields as $df) {
            if (isset($params[$df])) {
                $params[$df] = CRM_Utils_Date::isoToMysql($params[$df]);
            }
        }
       
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        $pledge = self::add( $params );
        if ( is_a( $pledge, 'CRM_Core_Error') ) {
            $pledge->rollback( );
            return $pledge;
        }
        
        $params['id'] = $pledge->id;
        
        //building payment params
        $paymentParams = array( );
        $paymentParams['pledge_id'] = $params['id'];
        $paymentParams['pledge_status_id'] = $params['status_id'];
        foreach (array('amount', 'installments', 'scheduled_date', 'frequency_unit') as $key) {
            $paymentParams[$key] = $params[$key];
        }
        
        
        require_once 'CRM/Contribute/PseudoConstant.php';
        $paymentParams['status_id'] = array_search( 'Pending', 
                                                    CRM_Contribute_PseudoConstant::contributionStatus());
        $paymentParams['scheduled_amount'] = $params['eachPaymentAmount'];
     
        require_once 'CRM/Pledge/BAO/Payment.php';
        CRM_Pledge_BAO_Payment::create( $paymentParams );
        
        $transaction->commit( );
        
        return $pledge;
   }
    
    /**
     * Function to delete the pledge
     *
     * @param int $id  pledge id
     *
     * @access public
     * @static
     *
     */
    static function deletePledge( $id )
    { 
        CRM_Utils_Hook::pre( 'delete', 'Pledge', $id, CRM_Core_DAO::$_nullArray );

        //check for no Completed Payment records with the pledge
        require_once 'CRM/Pledge/DAO/Payment.php';
        $payment = new CRM_Pledge_DAO_Payment( );
        $payment->pledge_id = $id;
        $payment->find( );

        require_once 'CRM/Contribute/PseudoConstant.php';
        while ( $payment->fetch( ) ) {
            if ($payment->status_id == array_search( 'Completed', CRM_Contribute_PseudoConstant::contributionStatus())) {
                CRM_Core_Session::setStatus( ts( 'This pledge can not be deleted because there are payment records (with status completed) linked to it.' ) );
                
                return;
            }
        }
                
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        $results = null;
        $dao     = new CRM_Pledge_DAO_Pledge( );
        $dao->id = $id;
        $results = $dao->delete( );
        
        $transaction->commit( );
        
        CRM_Utils_Hook::post( 'delete', 'Pledge', $dao->id, $dao );
        
        return $results;
    }
 
    /**
     * function to get the amount details date wise.
     */
    function getTotalAmountAndCount( $status = null, $startDate = null, $endDate = null ) 
    {
        $where = array( );
        //get all status
        require_once 'CRM/Contribute/PseudoConstant.php';
        $allStatus = CRM_Contribute_PseudoConstant::contributionStatus( );
        $statusId = array_search( $status, $allStatus);
        
        switch ( $status ) {
        case 'Completed':
            $where[] = 'status_id = '. $statusId;
            break;
            
        case 'Cancelled':
            $where[] = 'status_id = '. $statusId;
            break;

        case 'In Progress':
            $where[] = 'status_id = '. $statusId;
            break;

        case 'Pending':
            $where[] = 'status_id = '. $statusId;
            break;
        }
        
        if ( $startDate ) {
            $where[] = "create_date >= '" . CRM_Utils_Type::escape( $startDate, 'Timestamp' ) . "'";
        }
        if ( $endDate ) {
            $where[] = "create_date <= '" . CRM_Utils_Type::escape( $endDate, 'Timestamp' ) . "'";
        }
        
        $whereCond = implode( ' AND ', $where );
        
        $query = "
SELECT sum( amount ) as pledge_amount, count( id ) as pledge_count
FROM   civicrm_pledge
WHERE  $whereCond AND is_test=0
";
        $start = substr( $startDate, 0, 8 );
        $end   = substr( $endDate, 0, 8 );
       
        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        if ( $dao->fetch( ) ) {
            $pledge_amount = array( 'pledge_amount' => $dao->pledge_amount,
                                    'pledge_count'  => $dao->pledge_count,
                                    'purl'          => CRM_Utils_System::url( 'civicrm/pledge/search',
                                                                              "reset=1&force=1&pstatus={$statusId}&pstart={$start}&pend=$end&test=0"));
        }
        
        $where = array( );
        switch ( $status ) {
        case 'Completed':
            $select = 'sum( total_amount ) as received_pledge , count( cd.id ) as received_count';
            $where[] = 'status_id = ' .$statusId. ' AND cp.contribution_id = cd.id AND cd.is_test=0';
            $queryDate = 'receive_date';
            $from = ' civicrm_contribution cd, civicrm_pledge_payment cp';
            break;
            
        case 'Cancelled':
            $select = 'sum( total_amount ) as received_pledge , count( cd.id ) as received_count';
            $where[] = 'status_id = ' .$statusId. ' AND cp.contribution_id = cd.id AND cd.is_test=0';
            $queryDate = 'receive_date';
            $from = ' civicrm_contribution cd, civicrm_pledge_payment cp';
            break;

        case 'Pending':
            $select = 'sum( scheduled_amount )as received_pledge , count( cp.id ) as received_count';
            $where[] = 'status_id = ' . $statusId;
            $queryDate = 'scheduled_date';
            $from = ' civicrm_pledge_payment cp';
            break;

        case 'Overdue':
            $select = 'sum( scheduled_amount ) as received_pledge , count( cp.id ) as received_count';
            $where[] = 'status_id = ' . $statusId;
            $queryDate = 'scheduled_date';
            $from = ' civicrm_pledge_payment cp';
            break;
        }
        
        if ( $startDate ) {
            $where[] = " $queryDate >= '" . CRM_Utils_Type::escape( $startDate, 'Timestamp' ) . "'";
        }
        if ( $endDate ) {
            $where[] = " $queryDate <= '" . CRM_Utils_Type::escape( $endDate, 'Timestamp' ) . "'";
        }
        
        $whereCond = implode( ' AND ', $where );
        
        $query = "
SELECT $select
FROM $from
WHERE  $whereCond 
";
        if ( $select ) {
            $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            if ( $dao->fetch( ) ) {
                return array_merge( $pledge_amount, array( 'received_amount' => $dao->received_pledge,
                                                           'received_count'  => $dao->received_count,
                                                           'url'             => CRM_Utils_System::url( 'civicrm/pledge/search',
                                                                                                       "reset=1&force=1&status={$statusId}&start={$start}&end=$end&test=0")));
            } 
        }else {
            return $pledge_amount;
        }
        return null;
    }
}

