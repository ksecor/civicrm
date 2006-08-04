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

require_once 'CRM/Core/Page.php';

class CRM_Core_Page_File extends CRM_Core_Page {
  
    function run( ) {
        require_once 'CRM/Utils/Request.php';
        require_once 'CRM/Core/DAO.php';

        $eid         = CRM_Utils_Request::retrieve( 'eid', 'Positive', CRM_Core_DAO::$_nullObject, true );
        $id          = CRM_Utils_Request::retrieve( 'id' , 'Positive', CRM_Core_DAO::$_nullObject, true );
        $quest       = CRM_Utils_Request::retrieve( 'quest' , 'Positive', CRM_Core_DAO::$_nullObject );

        // make sure that the id (file_id) belongs to eid (contact_id)
        require_once 'CRM/Core/BAO/File.php';
        list( $path, $mimeType ) = CRM_Core_BAO_File::path( $id, $eid, 'civicrm_contact' ,$quest);
        if ( ! $path ) {
            CRM_Utils_System::statusBounce( 'Could not retrieve the file' );
        }

        $buffer = file_get_contents( $path );
        if ( ! $buffer ) {
            CRM_Utils_System::statusBounce( 'The file is either empty or you do not have permission to retrieve the file' );
        }
        CRM_Utils_System::download( basename( $path ), $mimeType, $buffer );
    }

}

?>