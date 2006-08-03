<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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

        if ( $entityFileDAO->find( true ) ) {
            require_once 'CRM/Core/DAO/File.php'; 
            $fileDAO =& new CRM_Core_DAO_File( );
            $fileDAO->id = $fileID;
            if ( $fileDAO->find( true ) ) {
                $config =& CRM_Core_Config::singleton( );
                $path = $config->customFileUploadDir . $fileDAO->uri;
                if ( file_exists( $path ) && is_readable( $path ) ) {
                    return array( $path, $fileDAO->mime_type );
                }
            }
        }

        return array( null, null );
    }

    
    public function filePostProcess($data ,$fileID ,$entityTable, $entityId ,$entitySubtype) {
        require_once 'CRM/Core/DAO/File.php';
        $config = & CRM_Core_Config::singleton();
        
        $path = explode( '/', $data );
        $filename = $path[count($path) - 1];
        
        // rename this file to go into the secure directory
        $directoryName = $config->customFileUploadDir.$entitySubtype.DIRECTORY_SEPARATOR.$entityId;
        require_once "CRM/Utils/File.php";
        CRM_Utils_File::createDir($directoryName);
        if ( ! rename( $data, $directoryName .DIRECTORY_SEPARATOR. $filename ) ) {
            CRM_Utils_System::statusBounce( ts( 'Could not move custom file to custom upload directory' ) );
            break;
        }
        
        $mimeType = $_FILES['uploadFile']['type'];
        
        require_once 'CRM/Core/DAO/EntityFile.php'; 
        $entityFileDAO =& new CRM_Core_DAO_EntityFile();
        $fileDAO =& new CRM_Core_DAO_File();
        $entityFileDAO->entity_table = $entityTable;
        $entityFileDAO->entity_id    = $entityId;
        if ( $entityFileDAO->find(true) ) {
            $fileDAO->id = $entityFileDAO->file_id;
            $fileDAO->find(true);
            if ($fileDAO->file_type_id !=$fileID ) {
                $fileDAO =& new CRM_Core_DAO_File();
            }
        }
        
        $fileDAO->uri               = $filename;
        $fileDAO->mime_type         = $mimeType;
        $fileDAO->file_type_id      = $fileID;
        $fileDAO->upload_date       = date('Ymdhis'); 
        $fileDAO->save();
    
        // need to add/update civicrm_entity_file
        
        $entityFileDAO =& new CRM_Core_DAO_EntityFile();
        
        $entityFileDAO->entity_table = $entityTable;
        $entityFileDAO->entity_id    = $entityId;
        $entityFileDAO->file_id      = $fileDAO->id;
        $entityFileDAO->find(true);
        $entityFileDAO->save();
        
    }


}

?>