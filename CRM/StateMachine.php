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

require_once 'CRM/Config.php';
require_once 'CRM/Session.php';
require_once 'CRM/State.php' ;
require_once 'CRM/String.php';

class CRM_StateMachine {

    /**
     * the controller of this state machine
     * @var object
     */
    protected $_controller;

    /**
     * the list of states that belong to this state machine
     * @var array
     */
    protected $_states;

    /**
     * the list of pages that belong to this state machine. Note
     * that a state and a form have a 1 <-> 1 relationship. so u
     * can always derive one from the other
     * @var array
     */
    protected $_pages;

    /**
     * the mode that the state machine is operating in
     * @var int
     */
    protected $_mode = null;

    /**
     * The display name for this machine
     * @var string
     */
    protected $_name = null;

    /**
     * class constructor
     *
     * @param object $controller the controller for this state machine
     *
     * @return object
     * @access public
     */
    function __construct( &$controller ) {
        $this->_controller =& $controller;

        $this->_states = array( );
    }

    /**
     * getter for name
     *
     * @return string
     * @access public
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * setter for name
     *
     * @param string
     *
     * @return void
     * @access public
     */
    public function setName($name) {
        $this->_name = $name;
    }

    /**
     * do a state transition jump. Currently only supported types are
     * Next and Back. The other actions (Cancel, Done, Submit etc) do
     * not need the state machine to figure out where to go
     *
     * @param  object    $page       CRM_Form the current form-page
     * @param  string    $actionName Current action name, as one Action object can serve multiple actions
     * @param  string    $type       The type of transition being requested (Next or Back)
     *
     * @return void
     * @access public
     */
    function perform( &$page, $actionName, $type = 'Next' ) {
        // save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();

        $pageName =  $page->getAttribute('name');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        $data['valid'][$pageName]  = $page->validate();
    
        // if we are going to the next state
        // Modal form and page is invalid: don't go further
        if ($type == 'Next' && $page->controller->isModal() && !$data['valid'][$pageName]) {
            return $page->handle('display');
        }

        $state =& $this->_states[$pageName];

        // dont know how or why we landed here so abort and display
        // current page
        if ( empty($state) ) {
            return $page->handle('display');
        }
    
        // the page is valid, process it if we are jumping to the next state
        if ( $type == 'Next' ) {
            $page->postProcess( );
            $state->handleNextState( $page );
        } else {
            $state->handleBackState( $page );
        }
    }

    /**
     * helper function to add a State to the state machine
     *
     * @param string $iname the internal name
     * @param string $name  the display name
     * @param int    $type  the type of state (START|FINISH|SIMPLE)
     * @param object $prev  the previous page if any
     * @param object $next  the next page if any
     *
     * @return void
     * @access public
     */
    function addState( $iname, $name, $type, $prev, $next ) {
        $this->_states[$name] =& new CRM_State( $iname, $name, $type, $prev, $next, $this );
    }

    /**
     * Given a name find the corresponding state
     *
     * @param string $name the state name
     * 
     * @return object the state object
     * @access public
     */
    function find( $name ) {
        if ( array_key_exists( $name, $this->_states ) ) {
            return $this->_states[$name];
        } else {
            return null;
        }
    }

    /**
     * return the list of state objects
     *
     * @return array array of states in the state machine
     * @access public
     */
    function getStates() {
        return $this->_states;
    }

    /**
     * return the list of form objects
     *
     * @return array array of pages in the state machine
     * @access public
     */
    function getPages() {
        return $this->_pages;
    }

    /**
     * addSequentialStates: meta level function to create a simple
     * wizard for a state machine that is completely sequential.
     *
     * @access public
     *
     * @param array $states states is an array of arrays. Each element
     * of the top level array describes a state. Each state description
     * includes the name, the display name and the class name
     *
     * @return void
     */
    function addSequentialStates( &$pages ) {
        $this->_pages =& $pages;
        $numPages = count( $pages );
        
        for ( $i = 0; $i < $numPages ; $i++ ) {
            $iname    = CRM_String::getClassName( $pages[$i] );

            $classPath = str_replace( '_', '/', $pages[$i] ) . '.php';
            require_once($classPath);
            // $name = eval( sprintf( "return %s::getDisplayName( );", $pages[$i] ) );
            $name = $iname;

            if ( $numPages == 1 ) {
                $prev = $next = null;
                $type = CRM_State::START | CRM_State::FINISH;
            } else if ( $i == 0 ) {
                // start state
                $prev = null;
                $next = CRM_String::getClassName( $pages[$i + 1] );
                $type = CRM_State::START;
            } else if ( $i == $numPages - 1 ) {
                // finish state
                $prev = CRM_String::getClassName( $pages[$i - 1] );
                $next = null;
                $type = CRM_State::FINISH;
            } else {
                // in between simple state
                $prev = CRM_String::getClassName( $pages[$i - 1] );
                $next = CRM_String::getClassName( $pages[$i + 1] ); 
                $type = CRM_State::SIMPLE;
            }
      
            $this->addState( $iname, $name, $type, $prev, $next );
        }
    }

    /**
     * reset the state machine
     *
     * @return void
     * @access public
     */
    function reset( ) {
        $this->_controller->reset( );
    }

    /**
     * getter for mode
     *
     * @return int
     * @access public
     */
    function getMode( ) {
        return $this->_mode;
    }

    /**
     * setter for content
     *
     * @param string $content the content generated by this state machine
     *
     * @return void
     * @access public
     */
    function setContent(&$content) {
        $this->_controller->setContent($content);
    }
  
    /**
     * getter for content
     *
     * @return string
     * @access public
     */
    function &getContent() {
        return $this->_controller->getContent();
    }

}

?>
