<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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

class CRM_Bridge_OG_OG {

    static function og( &$params, $op ) {
        require_once 'CRM/Bridge/OG/Utils.php';

        $contactID = CRM_Bridge_OG_Utils::contactID( $params['uf_id'] );
        if ( ! $contactID ) {
            CRM_Core_Error::fatal( );
        }

        // get the group id of this OG
        $groupID   = CRM_Bridge_OG_Utils::groupID( CRM_Bridge_OG_Utils::ogSyncName( $params['og_id'] ),
                                                   null, true );
        
        $groupParams = array( 'contact_id' => $contactID,
                              'group_id'   => $groupID  );

        require_once 'api/v2/GroupContact.php';
        if ( $op == 'add' ) {
            $groupParams['status'] = $params['is_active'] ? 'Added' : 'Pending';
            civicrm_group_contact_add( $groupParams );
        } else {
            $groupParams['status'] = 'Removed';
            civicrm_group_contact_remove( $groupParams );
        }

        if ( $params['is_admin'] !== null ) {
            // get the group ID of the acl group
            $groupID   = CRM_Bridge_OG_Utils::groupID( CRM_Bridge_OG_Utils::ogSyncACLName( $params['og_id'] ),
                                                       null, true );
            
            $groupParams = array( 'contact_id' => $contactID,
                                  'group_id'   => $groupID  ,
                                  'status'     => $params['is_admin'] ? 'Added' : 'Removed' );
            
            if ( $params['is_admin'] ) {
                civicrm_group_contact_add( $groupParams );
            } else {
                civicrm_group_contact_remove( $groupParams );
            }
        }
    }

}

?>
