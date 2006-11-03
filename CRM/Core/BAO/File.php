<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
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
                   $entityTable = 'civicrm_contact' ,$quest = false  ) {
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
                if ($quest) {
                   $path =  $config->customFileUploadDir."Student".DIRECTORY_SEPARATOR.$entityID.DIRECTORY_SEPARATOR. $fileDAO->uri;
                }else {
                    $path = $config->customFileUploadDir . $fileDAO->uri;
                }
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
            CRM_Core_Error::statusBounce( ts( 'Could not move custom file to custom upload directory' ) );
            break;
        }
        // to get id's 
        $sql = "SELECT CF.id as fID ,CF.uri as uri, CEF.id as feID FROM civicrm_file as CF LEFT JOIN civicrm_entity_file as CEF ON (CEF.file_id = CF.id) WHERE ( CF.file_type_id =".$fileID." AND CEF.entity_table = '".$entityTable."' AND CEF.entity_id =".$entityId .")";
        
        $dao = new CRM_Core_DAO();
        $dao->query($sql);
        $dao->fetch();
       
        $mimeType = $_FILES['uploadFile']['type'];
        
        require_once "CRM/Core/DAO/File.php";
        $fileDAO =& new CRM_Core_DAO_File();
        if ( $dao->fID ) {
            $fileDAO->id = $dao->fID;
            unlink($directoryName .DIRECTORY_SEPARATOR.$dao->uri);
        }
        $fileDAO->uri               = $filename;
        $fileDAO->mime_type         = $mimeType;
        $fileDAO->file_type_id      = $fileID;
        $fileDAO->upload_date       = date('Ymdhis'); 
        $fileDAO->save();
    
        // need to add/update civicrm_entity_file
        require_once "CRM/Core/DAO/EntityFile.php";
        $entityFileDAO =& new CRM_Core_DAO_EntityFile();
        if ($dao->feID ) {
            $entityFileDAO->id =  $dao->feID;
        }
        $entityFileDAO->entity_table = $entityTable;
        $entityFileDAO->entity_id    = $entityId;
        $entityFileDAO->file_id      = $fileDAO->id;
        $entityFileDAO->save();
        
    }

    public function delete($fileID , $entityId, $entityTable = 'civicrm_contact') {
        require_once "CRM/Core/DAO/CustomValue.php";
        $customDAO =& new CRM_Core_DAO_CustomValue();
        $customDAO->entity_table = $entityTable; 
        $customDAO->file_id = $fileID;
        if ( $customDAO->find(true) ) {
            $customDAO->delete();
        }

        require_once "CRM/Core/DAO/EntityFile.php";
        $entityFileDAO =& new CRM_Core_DAO_EntityFile();
        $entityFileDAO->file_id = $fileID;
        $entityFileDAO->entity_table = $entityTable;
        $entityFileDAO->entity_id    = $entityId;
        
        if ( $entityFileDAO->find(true) ) {
            $entityFileDAO->delete();
        }

        require_once "CRM/Core/DAO/File.php";
        $fileDAO =& new CRM_Core_DAO_File();
        $fileDAO->id = $fileID;
        if ( $fileDAO->find(true) ) {
            $fileDAO->delete();
        }
    }
}

?>