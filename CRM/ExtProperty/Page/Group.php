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

    function view( $groupId ) {
        $group = new CRM_DAO_ExtPropertyGroup( );
        $group->id = $groupId;
        if ( $group->find( true ) ) {
            $values = array( );
            $group->storeValues( $values );
            $this->assign( 'group', $values );
        }
        
        $this->browse( );
    }

    function browse( ) {
        $group = new CRM_DAO_ExtPropertyGroup( );

        $values = array( );
        $group->find( );
        while ( $group->fetch( ) ) {
            $values[$group->id] = array( );
            $group->storeValues( $values[$group->id] );
        }
        $this->assign( 'rows', $values );
    }

    function edit( $mode, $groupId = null ) {
        $controller = new CRM_Controller_Simple( 'CRM_ExtProperty_Form_Group', 'Extended Property Groups', $mode );

        // set the userContext stack
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();
        $session->pushUserContext( $config->httpBase . 'civicrm/extproperty/group&op=browse' );

        $controller->reset( );
        $controller->process( );
        $controller->run( );
    }

    function run( ) {
        $op = CRM_Request::retrieve( 'op', $this, false, 'browse' );
        $this->assign( 'op', $op );

        $groupId = CRM_Request::retrieve( 'groupId', $this, false, 0 );

        switch ( $op ) {
        case 'view':
            $this->view( $groupId );
            break;

        case 'edit':
            $this->edit( CRM_Form::MODE_UPDATE, $groupId );
            break;

        case 'add':
            $this->edit( CRM_Form::MODE_ADD );
            break;
        }

        $this->browse( );

        return parent::run( );
    }

}

?>