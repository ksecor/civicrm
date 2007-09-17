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

class CRM_OG_Utils {

    static function contactID( $ufID ) {
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
            CRM_Core_Error::fatal( );
        }
        return $values['contact_id'];
    }

    static function groupID( $source, $title = null, $abort = false ) {
        $query  = "
SELECT id
  FROM civicrm_group
 WHERE source = %1";
        $params = array( 1 => array( $source, 'String' ) );

        if ( $title ) {
            $query .= " OR title = %2";
            $params[2] = array( $title, 'String' );
        }
                         
        $groupID = CRM_Core_DAO::singleValueQuery( $query, $params );
        if ( $abort &&
             ! $groupID ) {
            CRM_Core_Error::fatal( );
        }

        return $groupID;
    }

}

?>
