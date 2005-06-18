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
 * Page for displaying list of Meetings
 */
class CRM_Contact_Page_Meeting 
{

    static function view( $page, $meetingId ) {
        $meeting =& new CRM_Core_DAO_Meeting( );
        $meeting->id = $meetingId;
        if ( $meeting->find( true ) ) {
            $values = array( );
            CRM_Core_DAO::storeValues( $meeting, $values );
            $page->assign( 'meeting', $values );
        }
        
        self::browse( $page );
    }

    static function browse( $page ) {
        $meeting =& new CRM_Core_DAO_Meeting( );
        //$meeting->entity_table = 'crm_contact';
        //$meeting->entity_id    = $page->getContactId( );

        //$meeting->orderBy( 'modified_date desc' );

        $values = array( );
        $meeting->find( );
        while ( $meeting->fetch( ) ) {
            $values[$meeting->id] = array( );
            CRM_Core_DAO::storeValues( $meeting, $values[$meeting->id] );
        }
        $page->assign( 'meetings', $values );
    }

    static function edit( $page, $mode, $meetingId = null ) {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Activity_Form_Meeting', 'Contact Meetings', $mode );
        $controller->setEmbedded( true );

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/meeting', 'action=browse' ) );

        $controller->reset( );
        $controller->set( 'entityTable', 'crm_contact' );
        $controller->set( 'entityId'   , $page->getContactId( ) );
        $controller->set( 'meetingId'     , $meetingId );

        $controller->process( );
        $controller->run( );
    }

    static function run( $page ) {
        $contactId = $page->getContactId( );
        $page->assign( 'contactId', $contactId );

        $action = CRM_Utils_Request::retrieve( 'action', $page, false, 'browse' );
        $page->assign( 'action', $action );

        $nid = CRM_Utils_Request::retrieve( 'nid', $page, false, 0 );

        if ( $action & CRM_Core_Action::VIEW ) {
            self::view( $page, $nid );
        } else if ( $action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD ) ) {
            self::edit( $page, $action, $nid );
        } else if ( $action & CRM_Core_Action::DELETE ) {
            self::delete( $nid );
        }

        self::browse( $page );
    }

}
?>
