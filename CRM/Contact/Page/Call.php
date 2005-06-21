<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying list of Call
 */
class CRM_Contact_Page_Call 
{

    static function edit( $page, $mode, $callId = null ) {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Activity_Form_Call', 'Contact Calls', $mode );
        $controller->setEmbedded( true );

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/activity', 'action=browse&history=' ) );

        $controller->reset( );

        $controller->process( );
        $controller->run( );
    }

    static function run( $page ) {
        $contactId = $page->getContactId( );
        $page->assign( 'contactId', $contactId );

        $action = CRM_Utils_Request::retrieve( 'action', $page, false, 'browse' );
        $page->assign( 'action', $action );

        $id = CRM_Utils_Request::retrieve( 'id', $page, false, 0 );
        
        $status = CRM_Utils_Request::retrieve( 'status', $page, false, 0 );
        
        if ( $action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::VIEW) ) {
            self::edit( $page, $action, $id );
        } else if ( $action & CRM_Core_Action::DELETE ) {
            self::delete( $id );
        }
    }

}
?>
