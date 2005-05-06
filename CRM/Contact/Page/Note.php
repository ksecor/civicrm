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

class CRM_Contact_Page_Note {

    static function view( $page, $noteId ) {
        $note = new CRM_Core_DAO_Note( );
        $note->id = $noteId;
        if ( $note->find( true ) ) {
            $values = array( );
            $note->storeValues( $values );
            $page->assign( 'note', $values );
        }
        
        self::browse( $page );
    }

    static function browse( $page ) {
        $note = new CRM_Core_DAO_Note( );
        $note->table_name = 'crm_contact';
        $note->table_id   = $page->getContactId( );

        $note->orderBy( 'modified_date desc' );

        $values = array( );
        $note->find( );
        while ( $note->fetch( ) ) {
            $values[$note->id] = array( );
            $note->storeValues( $values[$note->id] );
        }
        $page->assign( 'notes', $values );
    }

    static function edit( $page, $mode, $noteId = null ) {
        $controller = new CRM_Core_Controller_Simple( 'CRM_Note_Form_Note', 'Contact Notes', $mode );
        $controller->setEmbedded( true );

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/note', 'action=browse' ) );

        $controller->reset( );
        $controller->set( 'tableName', 'crm_contact' );
        $controller->set( 'tableId'  , $page->getContactId( ) );
        $controller->set( 'noteId'   , $noteId );

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
        }

        self::browse( $page );
    }

}

?>