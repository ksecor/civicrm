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

require_once 'CRM/Page.php';

class CRM_Contact_Page_Note {

    /**
     * class constructor
     */
    function __construct( ) {
    }

    static function common( ) {
        $note = new CRM_DAO_Note( );
        $note->table_name = 'crm_contact';
        $note->table_id   = $page->getContactId( );

        $note->orderBy( 'modified_date desc' );

        $values = array( );
        $count  = 0;
        while ( $note->fetch( ) && $count < CRM_Contact_BAO_Note::MAX_NOTES ) {
            $values[$note->id] = array( );
            $note->storeValues( $values[$note->id] );
            $count++;
        }
        $page->assign( 'notes', $values );
    }

    static function view( $page, $noteId ) {
        $note = new CRM_DAO_Note( );
        $note->id = $noteId;
        if ( $note->find( true ) ) {
            $values = array( );
            $note->storeValues( $values );
            $page->assign( 'note', $values );
        }
        
        self::browse( $page );
    }

    static function browse( $page ) {
        $note = new CRM_DAO_Note( );
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

    static function edit( ) {
    }

    static function add( ) {
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();

        $controller = new CRM_Controller_Simple( 'CRM_Note_Form_Note', 'Contact Notes', CRM_Form::MODE_ADD );
        $controller->process( );
        $controller->run( );
    }

    static function run( $page ) {
        $contactId = $page->getContactId( );

        $op = CRM_Request::retrieve( 'op', $page, false, 'browse' );
        $page->assign( 'op', $op );

        switch ( $op ) {
        case 'view':
            $noteId = $_GET['nid'];
            self::view( $page, $noteId );

        case 'edit':
            $noteId = $_GET['nid'];
            self::edit( $page, $noteId );

        case 'add':
            self::add( $page );
        }

        self::browse( $page );
    }

}

?>