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

class CRM_ExtProperty_Page_Group extends CRM_Page {

    /**
     * class constructor
     */
    function __construct( ) {
    }

    static function view( $page, $groupId ) {
        $group = new CRM_DAO_ExtPropertyGroup( );
        $group->id = $groupId;
        if ( $group->find( true ) ) {
            $values = array( );
            $group->storeValues( $values );
            $page->assign( 'group', $values );
        }
        
        self::browse( $page );
    }

    static function browse( $page ) {
        $group = new CRM_DAO_ExtPropertyGroup( );

        $values = array( );
        $group->find( );
        while ( $group->fetch( ) ) {
            $values[$group->id] = array( );
            $group->storeValues( $values[$group->id] );
        }
        $page->assign( 'groups', $values );
    }

    static function edit( $page, $mode, $groupId = null ) {
        $controller = new CRM_Controller_Simple( 'CRM_ExtProperty_Form_Group', 'Extended Property Groups', $mode );

        // set the userContext stack
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();
        $session->pushUserContext( $config->httpBase . 'civicrm/extproperty/group&op=browse' );

        if ( ! $groupId ) {
            $groupId = $controller->get( 'groupId' );
        }

        $controller->reset( );
        $controller->set( 'groupId'   , $groupId );

        $controller->process( );
        $controller->run( );
    }

    static function run( $page ) {
        $contactId = $page->getContactId( );
        $page->assign( 'contactId', $contactId );

        $op = CRM_Request::retrieve( 'op', $page, false, 'browse' );
        $page->assign( 'op', $op );

        switch ( $op ) {
        case 'view':
            $groupId = $_GET['gid'];
            self::view( $page, $groupId );
            break;

        case 'edit':
            $groupId = $_GET['gid'];
            self::edit( $page, CRM_Form::MODE_UPDATE, $groupId );
            break;

        case 'add':
            self::edit( $page, CRM_Form::MODE_ADD );
            break;
        }

        self::browse( $page );
    }

}

?>