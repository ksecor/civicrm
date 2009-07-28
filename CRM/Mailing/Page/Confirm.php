<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/


/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/Page.php';

class CRM_Mailing_Page_Confirm extends CRM_Core_Page 
{
    function run( ) {
        require_once 'CRM/Utils/Request.php';
        $contact_id   = CRM_Utils_Request::retrieve( 'cid', 'Integer', CRM_Core_DAO::$_nullObject );
        $subscribe_id = CRM_Utils_Request::retrieve( 'sid', 'Integer', CRM_Core_DAO::$_nullObject );
        $hash         = CRM_Utils_Request::retrieve( 'h'  , 'String' , CRM_Core_DAO::$_nullObject );
        
        if ( ! $contact_id   ||
             ! $subscribe_id ||
             ! $hash ) {
            CRM_Core_Error::fatal( ts( "Missing input parameters" ) );
        }

        require_once 'CRM/Mailing/Event/BAO/Confirm.php';
        $result = CRM_Mailing_Event_BAO_Confirm::confirm( $contact_id, $subscribe_id, $hash );
        if ( $result === false ) {
            $this->assign( 'success', $result );
        } else {
            $this->assign( 'success', true    );
            $this->assign( 'group'  , $result );
        }

	require_once 'CRM/Contact/BAO/Contact/Location.php';
        list( $displayName, $email ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $contact_id );
        $this->assign( 'display_name', $displayName);
        $this->assign( 'email'       , $email );

        parent::run();
    }
}

