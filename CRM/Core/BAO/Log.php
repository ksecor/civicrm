<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/Log.php';

/**
 * BAO object for crm_log table
 */

class CRM_Core_BAO_Log extends CRM_Core_DAO_Log {

    static function &lastModified( $id, $table = 'civicrm_contact' ) {
        require_once 'CRM/Core/DAO/Log.php';

        $log =& new CRM_Core_DAO_Log( );
        
        $log->entity_table = $table;
        $log->entity_id    = $id;
        $log->orderBy( 'modified_date desc' );
        $log->limit( 1 );
        if ( $log->find( true ) ) {
            list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $log->modified_id );
            return array( 'id'    => $log->modified_id,
                          'name'  => $displayName,
                          'image' => $contactImage,
                          'date'  => $dao->modified_date );
        }
        return null;
    }

}

?>