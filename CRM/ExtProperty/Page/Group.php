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

class CRM_ExtProperty_Page_Group extends CRM_Page {
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    static $_links = array(
                           CRM_Action::VIEW    => array(
                                                        'name'  => 'View',
                                                        'url'   => 'civicrm/extproperty/group',
                                                        'qs'    => 'action=view&id=%%id%%',
                                                        'title' => 'View Extended Property Group',
                                                        ),
                           CRM_Action::UPDATE  => array(
                                                        'name'  => 'Edit',
                                                        'url'   => 'civicrm/extproperty/group',
                                                        'qs'    => 'action=update&id=%%id%%',
                                                        'title' => 'Edit Extended Property Group'),
                           CRM_Action::DISABLE => array(
                                                        'name'  => 'Disable',
                                                        'url'   => 'civicrm/extproperty/group',
                                                        'qs'    => 'action=disable&id=%%id%%',
                                                        'title' => 'Disable Extended Property Group',
                                                        ),
                           CRM_Action::ENABLE  => array(
                                                        'name'  => 'Enable',
                                                        'url'   => 'civicrm/extproperty/group',
                                                        'qs'    => 'action=enable&id=%%id%%',
                                                        'title' => 'Enable Extended Property Group',
                                                        ),
                           CRM_Action::BROWSE  => array(
                                                        'name'  => 'List',
                                                        'url'   => 'civicrm/extproperty/field',
                                                        'qs'    => 'action=browse&gid=%%id%%',
                                                        'title' => 'List Extended Property Group Fields',
                                                        ),
                           );

    /**
     * class constructor
     */
    function __construct( $name, $title = null, $mode = null ) {
        parent::__construct( $name, $title, $mode );
    }

    function browse( $action = null ) {
        $group = new CRM_DAO_ExtPropertyGroup( );

        if ( $action == null ) {
            $action = array_sum( array_keys( self::$_links ) );
        }

        if ( $action & CRM_Action::DISABLE ) {
            $action -= CRM_Action::DISABLE;
        }
        if ( $action & CRM_Action::ENABLE ) {
            $action -= CRM_Action::ENABLE;
        }
        

        $values = array( );
        $group->find( );
        while ( $group->fetch( ) ) {
            $values[$group->id] = array( );
            $group->storeValues( $values[$group->id] );
            if ( $group->is_active ) {
                $newAction = $action + CRM_Action::DISABLE;
            } else {
                $newAction = $action + CRM_Action::ENABLE;
            }
            $values[$group->id]['action'] = CRM_Action::formLink( self::$_links, $newAction, array( 'id' => $group->id ) );
        }
        $this->assign( 'rows', $values );
    }

    function edit( $mode, $id = null ) {
        $controller = new CRM_Controller_Simple( 'CRM_ExtProperty_Form_Group', 'Extended Property Groups', $mode );

        // set the userContext stack
        $session = CRM_Session::singleton();
        $session->pushUserContext( CRM_System::url( 'civicrm/extproperty/group', 'reset=1&action=browse' ) );

        $controller->reset( );
        if ( $id ) {
            $controller->set( 'id', $id );
        }
        $controller->process( );
        $controller->run( );
    }

    function run( ) {
        $action = CRM_Request::retrieve( 'action', $this, false, 'browse' );
        $this->assign( 'action', $action );

        $id = CRM_Request::retrieve( 'id', $this, false, 0 );

        if ( $action & (CRM_Action::VIEW | CRM_Action::ADD | CRM_Action::UPDATE) ) {
            $this->edit( $action, $id );
        } else if ( $action & CRM_Action::DISABLE ) {
            CRM_BAO_ExtPropertyGroup::setIsActive( $id, 0 );
        } else if ( $action & CRM_Action::ENABLE ) {
            CRM_BAO_ExtPropertyGroup::setIsActive( $id, 1 );
        }

        $this->browse( );

        return parent::run( );
    }

}

?>