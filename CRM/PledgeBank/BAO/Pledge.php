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
     * pledge id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_PledgeBank_BAO_Pledge object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $pledge = new CRM_PledgeBank_DAO_Pledge( );
        $pledge->copyValues( $params );
        if ( $pledge->find( true ) ) {
            CRM_Core_DAO::storeValues( $pledge, $defaults );
            return $pledge;
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
    static function add( &$params)
    {
        require_once 'CRM/Utils/Hook.php';
        
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Pledge', $params['id'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Pledge', null, $params ); 
        }
        
        $pledge =& new CRM_PledgeBank_DAO_Pledge( );
        
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
        $pledge     = & new CRM_PledgeBank_DAO_Pledge( );
        $pledge->id = $id; 
        
        if ( $pledge->find( true ) ) {
            $locBlockId = $pledge->loc_block_id;
            
            $result = $pledge->delete( );
            
            if ( ! is_null( $locBlockId ) ) {
                require_once 'CRM/Core/BAO/Location.php';
                CRM_Core_BAO_Location::deleteLocBlock( $locBlockId );
            }
            
            return $result;
        }
        
        return null;

    }

    /**
     * Function to get pledges Summary
     *
     * @return array Array of pledge summary values
     */
    static function getPledgeSummary( $admin = false )
    {
        //get only those pledges whose  
        //deadline should be current or future
        $pledgeSummary = array( );
        
        require_once 'CRM/Utils/Date.php';
        $now = CRM_Utils_Date::isoToMysql( date("Y-m-d") );
        $params = array( 1 => array( $now, 'Date' ) );
        
        $query = "SELECT count(id) as total_pledges
                  FROM   civicrm_pb_pledge
                  WHERE  civicrm_pb_pledge.is_active=1 AND civicrm_pb_pledge.deadline >= %1"; 
        
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        
        if ( $dao->fetch( ) ) {
            $pledgeSummary['total_pledges'] = $dao->total_pledges;
        }
        
        if ( empty( $pledgeSummary ) ||
             $dao->total_pledges == 0 ) {
            return $pledgeSummary;
        }
        
        $query = "
SELECT     civicrm_pb_pledge.id as id, civicrm_pb_pledge.creator_name as creator_name,
           civicrm_pb_pledge.creator_pledge_desc as creator_pledge_desc, 
           civicrm_pb_pledge.signers_limit as signers_limit, civicrm_pb_pledge.signer_description_text as signer_description_text, 
           civicrm_pb_pledge.signer_pledge_desc as signer_pledge_desc, civicrm_pb_pledge.deadline as deadline,
           civicrm_pb_pledge.is_active as is_active, civicrm_contact.display_name as display_name
FROM       civicrm_pb_pledge
LEFT JOIN  civicrm_contact ON ( civicrm_pb_pledge.creator_id = civicrm_contact.id )
WHERE      civicrm_pb_pledge.is_active = 1 AND civicrm_pb_pledge.deadline >= %1
GROUP BY   civicrm_pb_pledge.id
ORDER BY   civicrm_pb_pledge.created_date DESC
LIMIT      0, 10
";
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        
        $properties = array( 'creatorName'           => 'creator_name',
                             'creatorPledgeDesc'     => 'creator_pledge_desc',     'signersLimit'     => 'signers_limit', 
                             'signerDescriptionText' => 'signer_description_text', 'signerPledgeDesc' => 'signer_pledge_desc', 
                             'deadline'              => 'deadline',                'isActive'         => 'is_active', 
                             'displayName'           => 'display_name',           
                             );
        
        while ( $dao->fetch( ) ) {
            require_once 'CRM/Core/Config.php';
            $config = CRM_Core_Config::singleton();
            
            foreach ( $properties as $property => $name ) {
                $set = null;
                if ( $name == 'deadline'  ) {
                    $pledgeSummary['pledges'][$dao->id][$property] = 
                        CRM_Utils_Date::customFormat( $dao->$name,'%b %e, %Y', array( 'd' ) );
                } else if ( $name == 'is_active' ) {
                    if ( $dao->$name ) {
                        $set = 'Yes';
                    } else {
                        $set = 'No';
                    }
                    $pledgeSummary['pledges'][$dao->id][$property] = $set;
                } else {
                    $pledgeSummary['pledges'][$dao->id][$property] = $dao->$name;
                }
            }
            
            $pledgeSummary['pledges'][$dao->id]['title'] = ts( '%1 will %2 but only if %3 %4 will %5', 
                                                               array( 1 => $dao->creator_name, 
                                                                      2 => $dao->creator_pledge_desc,
                                                                      3 => $dao->signers_limit,
                                                                      4 => $dao->signer_description_text,
                                                                      5 => $dao->signer_pledge_desc ));
            
            //get the pledge status
            $pledgeSummary['pledges'][$dao->id]['status'] = self::getPledgeStatus( $dao->id ); 
            if ( $admin ) {
                $pledgeSummary['pledges'][$dao->id]['configure'] =
                    CRM_Utils_System::url( "civicrm/admin/pledge", "action=update&id={$dao->id}&reset=1" );
            }
        }
        
        return $pledgeSummary;
    }
    
    /**
     * Function to get pledge Status
     *
     * @param int $id  pledge id
     * @return string pledge status( Successful / Failed )
     */
    static function getPledgeStatus( $id )
    {
        $params = array( 1 => array( $id, 'Integer' ) );
        
        $query = "
SELECT     count( civicrm_pb_signer.id ) as signers, civicrm_pb_pledge.signers_limit as signer_limit
FROM       civicrm_pb_signer
LEFT JOIN  civicrm_pb_pledge ON ( civicrm_pb_signer.pledge_id = civicrm_pb_pledge.id AND 
                                  DATE(civicrm_pb_signer.signing_date) <= civicrm_pb_pledge.deadline )
WHERE      civicrm_pb_pledge.id = %1
GROUP BY   civicrm_pb_pledge.id
";
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch( ) ) {
            if ( $dao->signers >= $dao->signer_limit ) {
                return "Successful";
            }
        }
        
        return "Failed";
    }
    
}

