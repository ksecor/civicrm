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

require_once 'CRM/Core/DAO/File.php';

/**
 * BAO object for crm_log table
 */

class CRM_Core_BAO_File extends CRM_Core_DAO_File {

    function path( $fileID,
                   $entityID,
                   $entityTable = 'civicrm_contact' ) {
        require_once 'CRM/Core/DAO/EntityFile.php'; 
        
        $entityFileDAO =& new CRM_Core_DAO_EntityFile();
        $entityFileDAO->entity_table = $entityTable;
        $entityFileDAO->entity_id    = $entityID;
        $entityFileDAO->file_id      = $fileID;
        
        if ( $$entityFileDAO->find( true ) ) {
            require_once 'CRM/Core/DAO/File.php'; 
            $fileDAO =& new CRM_Core_DAO_File( );
            $fileDAO->id = $fileID;
            if ( $fileDAO->fetch( ) ) {
                $config =& CRM_Core_Config::singleton( );
                $path = $config->customFileUploadDir . $fileDAO->uri;
                if ( file_exists( $path ) && is_readable( $path ) ) {
                    return array( $path, $fileDAO->mime_type );
                }
            }
        }

        return array( null, null );
    }

}

?>