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

abstract class CRM_Core_Page_Basic extends CRM_Core_Page {
    
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
     * name of the edit form class
     *
     * @return string
     * @access public
     */
    abstract function editForm( );

    /**
     * name of the form
     *
     * @return string
     * @access public
     */
    abstract function editName( );

    /**
     * userContext to pop back to
     *
     * @param int $mode mode that we are in
     *
     * @return string
     * @access public
     */
    abstract function userContext( $mode = null );

    /**
     * function to get userContext params
     *
     * @param int $mode mode that we are in
     *
     * @return string
     * @access public
     */
    function userContextParams( $mode = null ) {
        return 'reset=1&action=browse';
    }

    /**
     * allows the derived class to add some more state variables to
     * the controller. By default does nothing, and hence is abstract
     *
     * @param CRM_Core_Controller $controller the controller object
     *
     * @return void
     * @access public
     */
    function addValues( $controller ) {
    }

    /**
     * class constructor
     *
     * @param string $name  name of the page
     * @param string $title title of the page
     * @param int    $mode  mode of the page
     *
     * @return CRM_Core_Page
     */
    function __construct( $name, $title = null, $mode = null ) {
        parent::__construct($name, $title, $mode);
    }

    function run( ) {
        $action = CRM_Utils_Request::retrieve( 'action', $this, false, 'browse' );
        $this->assign( 'action', $action );

        $id  = CRM_Utils_Request::retrieve( 'id', $this, false, 0 );

        if ( $action & (CRM_Core_Action::VIEW | CRM_Core_Action::ADD | CRM_Core_Action::UPDATE) ) {
            $this->edit($action, $id );
        } else if ( $action & CRM_Core_Action::DELETE ) {
            $this->delete($action, $id );
        } else if ( $action & CRM_Core_Action::DISABLE ) {
            eval( $this->getBAOName( ) . "::setIsActive( $id, 0 );" );
        } else if ( $action & CRM_Core_Action::ENABLE ) {
            eval( $this->getBAOName( ) . "::setIsActive( $id, 1 );" );
        } else if ( $action & CRM_Core_Action::DELETE ) {
            eval( $this->getBAOName( ) . "::del( $id );" );
        }

        $this->browse( );

        return parent::run( );
    }

    function browse( $action = null ) {
        $links =& $this->links( );
        if ( $action == null ) {
            $action = array_sum( array_keys( $links ) );
        }

        if ( $action & CRM_Core_Action::DISABLE ) {
            $action -= CRM_Core_Action::DISABLE;
        }
        if ( $action & CRM_Core_Action::ENABLE ) {
            $action -= CRM_Core_Action::ENABLE;
        }

        eval( '$object = new ' . $this->getBAOName( ) . '( );' );

        $values = array( );

        /*
         * lets make sure we get the stuff sorted by name if it exists
         */
        $fields =& $object->fields( );
        if ( CRM_Utils_Array::value( 'name', $fields ) ) {
            $object->orderBy ( 'name asc' );
        } else if ( CRM_Utils_Array::value( 'title', $fields ) ) {
            $object->orderBy ( 'title asc' );
        }  else if ( CRM_Utils_Array::value( 'label', $fields ) ) {
            $object->orderBy ( 'label asc' );
        }

        $object->find( );
        while ( $object->fetch( ) ) {
            $values[$object->id] = array( );
            $object->storeValues( $values[$object->id] );
            $newAction = self::action( $object, $action, $values[$object->id], $links );
        }
        $this->assign( 'rows', $values );
    }

    /**
     * Given an object, get the actions that can be associated with this
     * object. Check the is_active and is_required flags to display valid
     * actions
     *
     * @param CRM_Core_DAO $object the object being considered
     * @param int     $action the base set of actions
     * @param array   $values the array of values that we send to the template
     * @param array   $links  the array of links
     *
     * @return void
     * @access private
     */
    function action( $object, $action, &$values, &$links ) {
        $values['class'] = '';
        if ( array_key_exists( 'is_reserved', $object ) && $object->is_reserved ) {
            $newAction = 0;
            $values['action'] = '';
            $values['class'] = 'reserved';
        } else if ( array_key_exists( 'is_active', $object ) ) {
            if ( $object->is_active ) {
                $newAction = $action + CRM_Core_Action::DISABLE;
            } else {
                $newAction = $action + CRM_Core_Action::ENABLE;
            }
            $values['action'] = CRM_Core_Action::formLink( $links, $newAction, array( 'id' => $object->id ) );
        } else {
            $values['action'] = CRM_Core_Action::formLink( $links, $action, array( 'id' => $object->id ) );
        }
    }

    function edit( $mode, $id = null ) 
    {
        $controller = new CRM_Core_Controller_Simple( $this->editForm( ), $this->editName( ), $mode );

       // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url( $this->userContext( $mode ), $this->userContextParams( $mode ) ) );
        
        $controller->reset( );
        if ( $id ) {
            $controller->set( 'id'   , $id );
        }
        $controller->set( 'BAOName', $this->getBAOName( ) );
        $this->addValues( $controller );
        $controller->setEmbedded( true );
        $controller->process( );
        $controller->run( );
    }


    function delete( $mode, $id = null )
    {
        $controller = new CRM_Core_Controller_Simple( $this->deleteForm( ), $this->deleteName( ), $mode );

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url( $this->userContext( $mode ),
                                                          $this->userContextParams( $mode ) ) );

        $controller->reset( );
        if ( $id ) {
            $controller->set( 'id'   , $id );
        }
        $this->addValues( $controller );
        $controller->setEmbedded( true );
        $controller->process( );
        $controller->run( );
    }

}

?>