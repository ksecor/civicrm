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

abstract class CRM_Page_Basic extends CRM_Page {
    
    /**
     * define all the abstract functions here
     */

    /**
     * name of the BAO to perform various DB manipulations
     *
     * @return string
     * @access public
     */
    abstract function getBAOName( );
    
    /**
     * array of action links
     *
     * @return array (reference)
     * @access public
     */
    abstract function &links( );

    /**
     * name of the form class
     *
     * @return string
     * @access public
     */
    abstract function formClass( );

    /**
     * name of the form
     *
     * @return string
     * @access public
     */
    abstract function formName( );

    /**
     * userContext to pop back to
     *
     * @return string
     * @access public
     */
    abstract function userContext( );

    /**
     * class constructor
     *
     * @param string $name  name of the page
     * @param string $title title of the page
     * @param int    $mode  mode of the page
     *
     * @return CRM_Page
     */
    function __construct( $name, $title = null, $mode = null ) {
        parent::__construct($name, $title, $mode);
    }

    function run( ) {
        $action = CRM_Request::retrieve( 'action', $this, false, 'browse' );
        $this->assign( 'action', $action );

        $id = CRM_Request::retrieve( 'id', $this, false, 0 );

        if ( $action & (CRM_Action::VIEW | CRM_Action::ADD | CRM_Action::UPDATE) ) {
            $this->edit($action, $id );
        } else if ( $action & CRM_Action::DISABLE ) {
            eval( $this->getBAOName( ) . "::setIsActive( $id, 0 );" );
        } else if ( $action & CRM_Action::ENABLE ) {
            eval( $this->getBAOName( ) . "::setIsActive( $id, 1 );" );
        }

        $this->browse( );

        return parent::run( );
    }

    function browse( $action = null ) {
        $links =& $this->links( );
        if ( $action == null ) {
            $action = array_sum( array_keys( $links ) );
        }

        if ( $action & CRM_Action::DISABLE ) {
            $action -= CRM_Action::DISABLE;
        }
        if ( $action & CRM_Action::ENABLE ) {
            $action -= CRM_Action::ENABLE;
        }

        eval( '$object = new ' . $this->getBAOName( ) . '( );' );

        $values = array( );
        $object->find( );
        while ( $object->fetch( ) ) {
            $values[$object->id] = array( );
            $object->storeValues( $values[$object->id] );
            if ( isset( $object->is_active ) {
                if ( $object->is_active ) {
                    $newAction = $action + CRM_Action::DISABLE;
                } else {
                    $newAction = $action + CRM_Action::ENABLE;
                }
            }
            $values[$object->id]['action'] = CRM_Action::formLink( $links, $newAction, array( 'id' => $object->id ) );
        }
        $this->assign( 'rows', $values );
    }

    function edit( $mode, $id = null ) 
    {
        $controller = new CRM_Controller_Simple( $this->formClass( ), $this->formName( ), $mode );

       // set the userContext stack
        $session = CRM_Session::singleton();
        $session->pushUserContext( CRM_System::url( $this->userContext( ), 'reset=1&action=browse' ) );
        
        $controller->reset( );
        if ($id) {
            $controller->set( 'id'   , $id );
        }
        $controller->process( );
        $controller->run( );
    }

}

?>