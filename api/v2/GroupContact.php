<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'api/v2/utils.php';

/**
 * 
 *
 *
 *
 */
function civicrm_group_contact_get( &$params ) 
{
    if ( ! array_key_exists( 'contact_id', $params ) ) {
        return civicrm_create_error( ts( 'contact_id is a required field' ) );
    }

    $status = CRM_Utils_Array::value( 'status', $params, 'Added' );
    require_once 'CRM/Contact/BAO/GroupContact.php';
    $values =& CRM_Contact_BAO_GroupContact::getContactGroup( $params['contact_id'], $status, null, false, true );
    return $values;
}

/**
 * get list of contacts of particular group
 * 
 * This API will return you list of contacts of particular group
 * 'group_id' is required field
 *
 * @param  array   $params  array of values for group
 * 
 * @return array   either success message cotaining array of contact
 *                 values or error message is returned back in an
 *                 array
 * @access public
 */
function civicrm_contacts_group_get( &$params )
{
    if ( ! CRM_Utils_Array::value( 'group_id', $params ) ) {
        return civicrm_create_error( ts('group_id is required field' ) );
    }
    
    $returnProperties = array( );
    foreach( $params as $k => $v ) {
        if ( substr( $k, 0, 7 ) == 'return.' ) {
            $returnProperties[ substr( $k, 7 ) ] = $v;
        }
    }
    
    $status = CRM_Utils_Array::value( 'status',    $params, 'Added' );
    $sort   = CRM_Utils_Array::value( 'sort',      $params, null    );
    $offset = CRM_Utils_Array::value( 'offset',    $params, null    );
    $count  = CRM_Utils_Array::value( 'row_count', $params, null    );
    
    $group  = new CRM_Contact_BAO_Group( );
    $group->id = $params['group_id'];
    $group->find( true );
    
    $contacts = array();
    $contacts = CRM_Contact_BAO_GroupContact::getGroupContacts($group, $returnProperties, $status, $sort, $offset, $row_count);
    return civicrm_create_success( $contacts );
}

function civicrm_group_contact_add( &$params ) 
{
    return civicrm_group_contact_common( $params, 'add' );
}

function civicrm_group_contact_remove( &$params ) 
{
    return civicrm_group_contact_common( $params, 'remove' );
}

function civicrm_group_contact_common( &$params, $op = 'add' ) 
{
    $contactIDs = array( );
    $groupIDs = array( );
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 10 ) == 'contact_id' ) {
            $contactIDs[] = $v;
        } else if ( substr( $n, 0, 8 ) == 'group_id' ) {
            $groupIDs[] = $v;
        }
    }

    if ( empty( $contactIDs ) ) {
        return civicrm_create_error( ts( 'contact_id is a required field' ) );
    }

    if ( empty( $groupIDs ) ) {
        return civicrm_create_error( ts( 'group_id is a required field' ) );
    }

    $method     = CRM_Utils_Array::value( 'method'  , $params, 'API' );
    if ( $op == 'add' ) {
        $status     = CRM_Utils_Array::value( 'status'  , $params, 'Added'  );
    } else {
        $status     = CRM_Utils_Array::value( 'status'  , $params, 'Removed');
    }
    $tracking   = CRM_Utils_Array::value( 'tracking', $params );

    require_once 'CRM/Contact/BAO/GroupContact.php';
    $values = array( 'is_error' => 0 );
    if ( $op == 'add' ) {
        $values['total_count'] = $values['added'] = $values['not_added'] = 0;
        foreach ( $groupIDs as $groupID ) {
            list( $tc, $a, $na ) = 
                CRM_Contact_BAO_GroupContact::addContactsToGroup( $contactIDs, $groupID,
                                                                  $method, $status, $tracking );
            $values['total_count'] += $tc;
            $values['added']       += $a;
            $values['not_added']   += $na;
        }
    } else {
        $values['total_count'] = $values['removed'] = $values['not_removed'] = 0;
        foreach ( $groupIDs as $groupID ) {
            list( $tc, $r, $nr ) = 
                CRM_Contact_BAO_GroupContact::removeContactsFromGroup( $contactIDs, $groupID,
                                                                       $method, $status, $tracking );
            $values['total_count'] += $tc;
            $values['removed']     += $r;
            $values['not_removed'] += $nr;
        }
    }
    return $values;
}
