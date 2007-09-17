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

class CRM_Bridge_OG_CiviCRM {

    static function groupContact( $groupID, $contactIDs, $op ) {
        require_once 'CRM/Bridge/OG/Utils.php';
        $ogID = CRM_Bridge_OG_Utils::ogID( $groupID, true );

        require_once 'api/UFGroup.php';
        foreach ( $contactIDs as $contactID ) {
            $drupalID = crm_uf_get_uf_id( $contactID );
            if ( $drupalID ) {
                if ( $op == 'add' ) {
                    og_save_subscription( $ogID, $drupalID, array( 'is_active' => 1 ) );
                } else {
                    og_delete_subscription( $ogID, $drupalID );
                }
            }
        }
    }
}

?>
