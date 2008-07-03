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
        $contribution =& new CRM_Contribute_BAO_Contribution( );
        
        $pledge->copyValues( $params );
        
        if ( $pledge->find(true) ) {
            $ids['pledge'] = $pledge->id;
            CRM_Core_DAO::storeValues( $pledge, $values );
        }
        
        $contribution->contact_id = $pledge->contact_id;

        if ( $contribution->find(true) ) {
            CRM_Core_DAO::storeValues( $contribution, $values );
            array_merge($pledge, $contribution);
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
    static function del( $id )
    { 
        $pledge     = & new CRM_Pledge_DAO_Pledge( );
        $pledge->id = $id; 

        //we need to add more checks before deleting pledges
        
        //$result = $pledge->delete( );

        return $result;
    }
 
    /**
     * function to get the amount details date wise.
     */
    function getTotalAmountAndCount( $status = null, $startDate = null, $endDate = null ) 
    {
        $where = array( );
        switch ( $status ) {
        case 'Valid':
            $where[] = 'status_id = 1';
            break;
            
        case 'Cancelled':
            $where[] = 'status_id = 3';
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
SELECT sum( amount ) as total_amount, count( id ) as total_count
FROM   civicrm_pledge
WHERE  $whereCond AND is_test=0
";
        
        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        if ( $dao->fetch( ) ) {
            return array( 'amount' => $dao->total_amount,
                          'count'  => $dao->total_count );
        }
        return null;
    }
}

