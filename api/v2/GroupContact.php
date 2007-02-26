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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
 | http://civicrm.org/licensing/                                      |
 +--------------------------------------------------------------------+
*/

/**
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'api/v2/utils.php';

function civicrm_group_contact_add( &$params ) {
    return civicrm_group_contact_common( $params, 'add' );
}

function civicrm_group_contact_remove( &$params ) {
    return civicrm_group_contact_common( $params, 'remove' );
}

function civicrm_group_contact_common( &$params, $op = 'add' ) {
    $contactIDs = array( );
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 10 ) == 'contact_id' ) {
            $contactIDs[] = $v;
        }
    }
    if ( empty( $contactIDs ) ) {
        return civicrm_create_error( ts( 'contact_id is a required field' ) );
    }

    $groupID = CRM_Utils_Array::value( 'group_id', $params );
    if ( ! $groupID ) {
        return civicrm_create_error( ts( 'group_id is a required field' ) );
    }

    $method     = CRM_Utils_Array::value( 'method'  , $params, 'API v2' );
    if ( $op == 'add' ) {
        $status     = CRM_Utils_Array::value( 'status'  , $params, 'Added'  );
    } else {
        $status     = CRM_Utils_Array::value( 'status'  , $params, 'Removed');
    }
    $tracking   = CRM_Utils_Array::value( 'tracking', $params );

    require_once 'CRM/Contact/BAO/GroupContact.php';
    $values = array( 'is_error' = 0 );
    if ( $op == 'add' ) {
        list( $values['total_count'], $values['added'], $values['not_added'] ) = 
            CRM_Contact_BAO_GroupContact::addContactsToGroup( $contactIDs, $groupID,
                                                              $method, $status, $tracking );
    } else {
        list( $values['total_count'], $values['removed'], $values['not_removed'] ) = 
            CRM_Contact_BAO_GroupContact::removeContactsFromGroup( $contactIDs, $groupID,
                                                                   $method, $status, $tracking );
    }
    return $values;
}
