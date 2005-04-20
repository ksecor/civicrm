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

class CRM_Admin_Page_LocationType {
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    static $_links = array(
                           CRM_Action::UPDATE  => array(
                                                        'name'  => 'Edit',
                                                        'url'   => 'admin/contact/locType',
                                                        'qs'    => 'action=update&id=%%id%%',
                                                        'title' => 'Edit Location Type'),
                           CRM_Action::DISABLE => array(
                                                        'name'  => 'Disable',
                                                        'url'   => 'admin/contact/locType',
                                                        'qs'    => 'action=disable&id=%%id%%',
                                                        'title' => 'Disable Location Type',
                                                        ),
                           CRM_Action::ENABLE  => array(
                                                        'name'  => 'Enable',
                                                        'url'   => 'admin/contact/locType',
                                                        'qs'    => 'action=enable&id=%%id%%',
                                                        'title' => 'Enable Location Type',
                                                        ),
                           );

    /**
     * class constructor
     *
     * @return CRM_Page
     */
    function __construct( ) {

    }

    function run($page) {
        $action = CRM_Request::retrieve( 'action', $page, false, 'browse' );
        $page->assign( 'action', $action );

        $id = CRM_Request::retrieve( 'id', $page, false, 0 );

        if ( $action & (CRM_Action::VIEW | CRM_Action::ADD | CRM_Action::UPDATE) ) {
            self::edit($action, $id );
        } else if ( $action & CRM_Action::DISABLE ) {
            CRM_BAO_LocationType::setIsActive( $id, 0 );
        } else if ( $action & CRM_Action::ENABLE ) {
            CRM_BAO_LocationType::setIsActive( $id, 1 );
        }

        self::browse($page);

    }

    static function browse( $page, $action = null ) {
        $locationType = new CRM_Contact_DAO_LocationType( );

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
        $locationType->find( );
        while ( $locationType->fetch( ) ) {
            $values[$locationType->id] = array( );
            $locationType->storeValues( $values[$locationType->id] );
            if ( $locationType->is_active ) {
                $newAction = $action + CRM_Action::DISABLE;
            } else {
                $newAction = $action + CRM_Action::ENABLE;
            }
            $values[$locationType->id]['action'] = CRM_Action::formLink( self::$_links, $newAction, array( 'id' => $locationType->id ) );
        }
        $page->assign( 'rows', $values );
    }



    static function edit( $mode, $id = null ) 
    {
        
        $controller = new CRM_Controller_Simple( 'CRM_Admin_Form_LocationType', 'Location Types', $mode );

       // set the userContext stack
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();

        $session->pushUserContext( CRM_System::url( 'admin/contact/locType', 'reset=1&action=browse' ) );
        
        $controller->reset( );
        if ($id) {
            $controller->set( 'id'   , $id );
        }
        $controller->process( );
        $controller->run( );
    }

}

?>