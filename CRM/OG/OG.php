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

class CRM_OG_OG {

    static function update( &$params ) {
        self::common( $params, 'add' );
    }

    static function delete( &$params ) {
        self::common( $params, 'remove' );
    }


    static function common( &$params, $op ) {
        $contactID = self::getContactID( $params['uf_id'] );

        // get the group id of this OG
        $groupID   = self::getGroupID( "OG Sync Group: {$params['og_id']}" );
        
        $groupParams = array( 'contact_id' => $contactID,
                              'group_id'   => $groupID  );

        require_once 'api/v2/GroupContact.php';
        if ( $op == 'add' ) {
            $groupParams['status'] = $params['is_active'] ? 'Added' : 'Pending';
            civicrm_group_contact_add( $groupParams );
        } else {
            $groupParams['status'] = 'Removed';
            civicrm_group_contact_add( $groupParams );
        }

        if ( isset( $params['is_admin'] ) &&
             $params['is_admin'] !== null ) {
            // get the group ID of the acl group
            $groupID   = self::getGroupID( "OG Sync ACL Group: {$params['og_id']}" );
            
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

    static function getContactID( $ufID ) {
        require_once 'api/UFGroup.php';
        $contactID = crm_uf_get_match_id( $ufID );
        if ( $contactID ) {
            return $contactID;
        }

        // else create a contact for this user
        $user = user_load( array( 'uid' => $ufID ) );
        $params = array( 'contact_type' => 'Individual',
                         'email'        => $user->mail, );

        require_once 'api/v2/Contact.php';
        $contact = civicrm_contact_add( $params );
        if ( $values['is_error'] ) {
            CRM_Core_Error::fatal( 'Please file an issue with the backtrace' );
        }
        return $values['contact_id'];
    }

    static function getGroupID( $source ) {
        $query  = "
SELECT id
  FROM civicrm_group
 WHERE source = %1";
        $params = array( 1 => array( $params['source'], 'String' ) );
                         
        return CRM_Core_DAO::singleValueQuery( $query, $params );
    }

}

?>
