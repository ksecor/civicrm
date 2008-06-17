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

require_once 'CRM/PledgeBank/DAO/Pledge.php';

class CRM_PledgeBank_BAO_Pledge extends CRM_PledgeBank_DAO_Pledge 
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
     * contact_id. We'll tweak this function to be more full featured over a period
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
        $event  = new CRM_Pledge_DAO_Pledge( );
        $event->copyValues( $params );
        if ( $event->find( true ) ) {
            CRM_Core_DAO::storeValues( $event, $defaults );
            return $event;
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
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_PledgeBank_DAO_Pledge', $id, 'is_active', $is_active );
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
    static function add( &$params, &$ids)
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

    }

}

