<?php
/**
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


require_once 'CRM/Config.php';
require_once 'CRM/Session.php';
require_once 'CRM/State.php' ;
require_once 'CRM/String.php';

class CRM_StateMachine {
    protected $_controller;

    protected $_states;

    static $_statesDescriptionArray = null;

    protected $_mode = null;

    protected $_wizardName = null;

    function CRM_StateMachine( $controller ) {
        $this->_controller = $controller;

        $this->_states = array( );
    }

    public function getWizardName() {
        return $this->_wizardName;
    }

    public function setWizardName($name) {
        $this->_wizardName = $name;
    }

    function getNextState( &$page, $actionName ) {
        // save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();

        $pageName =  $page->getAttribute('name');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        $data['valid'][$pageName]  = $page->validate();
    
        // Modal form and page is invalid: don't go further
        if ($page->controller->isModal() && !$data['valid'][$pageName]) {
            return $page->handle('display');
        }

        $state =& $this->_states[$pageName];
    
        // we dont know anything about this state, major error
        // TODO: fix error condition here
        if ( empty($state) ) {
            return $page->handle('display');
        }
    
        // the page is valid, process it before we jump to the next state
        $page->postProcess( );

        $state->handleNextState( $page );
    }
  
    function getBackState( &$page, $actionName ) {
        // save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();
        $pageName =  $page->getAttribute('name');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        // we don't check validation status here, 'jump' handler should 
        if (!$page->controller->isModal()) { 
            $data['valid'][$pageName]  = $page->validate(); 
        } 
    
        $state =& $this->_states[$pageName];

        // we dont know anything about this state, major error
        // TODO: fix error condition here
        if ( empty($state) ) {
            return $page->handle('display');
        }
    
        $state->handleBackState( $page );
    }

    function addState( $iname, $name, $type, $prev, $next ) {
        $this->_states[$name] =& new CRM_State( $iname, $name, $type, $prev, $next, $this );
    }

    function isValidStateName( $name ) {
        if ( array_key_exists( $name, $this->_states ) ) {
            return true;
        } else {
            return false;
        }
    }

    function find( $name ) {
        if ( array_key_exists( $name, $this->_states ) ) {
            return $this->_states[$name];
        } else {
            return null;
        }
    }

    function validate( &$data ) {
        foreach ( $this->_states as $name => $value ) {
            if ( $this->_states[$name]->_type & CRM_State::COND ) {
                $this->_states[$name]->validate( $data );
            }
        }
    }

    function invalidate( &$data ) {
        foreach ( $this->_states as $name => $value ) {
            if ( $this->_states[$name]->_type & CRM_State::COND ) {
                $this->_states[$name]->invalidate( $data );
            }
        }
    }

    function getStates( $type ) {
        $states = array( );

        foreach ( $this->_states as $name => $value ) {
            if ( $this->_states[$name]->_type & $type ) {
                $states[] = $value;
            }
        }
        return $states;
    }
  
    /**
     * This is usually overridden to return a specialized state list. But it will
     * also return the state array saved in this base class after calling addSequentialStates().
     *
     */
    function getStatesDescription() {
        return $this->_statesDescriptionArray;
    }
  
    function navigationLabels( &$labels ) {
        $states = $this->getStates( CRM_State::START );

        // assume only 1 start state for now
        $state = $states[0];
        while ( $state != null ) {
            $labels[ $state->getIName( ) ] = $state->getName( );
            $state = $state->getNextState( );
        }
    }

    function getNavigationLabels() {
        $labels = array();
        $this->navigationLabels($labels);
        return $labels;
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
    function addSequentialStates( &$states ) {
        $this->_statesDescriptionArray = $states;
        $numStates = count( $states );
        
        for ( $i = 0; $i < $numStates ; $i++ ) {
            $iname    = CRM_String::getClassName( $states[$i] );

            $classPath = str_replace( '_', '/', $states[$i] ) . '.php';
            require_once($classPath);
            $name = eval( sprintf( "return %s::getDisplayName( );", $states[$i] ) );

            if ( $numStates == 1 ) {
                $prev = $next = null;
                $type = CRM_State::START | CRM_State::FINISH;
            } else if ( $i == 0 ) {
                // start state
                $prev = null;
                $next = CRM_String::getClassName( $states[$i + 1] );
                $type = CRM_State::START;
            } else if ( $i == $numStates - 1 ) {
                // finish state
                $prev = CRM_String::getClassName( $states[$i - 1] );
                $next = null;
                $type = CRM_State::FINISH;
            } else {
                // in between simple state
                $prev = CRM_String::getClassName( $states[$i - 1] );
                $next = CRM_String::getClassName( $states[$i + 1] ); 
                $type = CRM_State::SIMPLE;
            }
      
            $this->addState( $iname, $name, $type, $prev, $next );
        }
    }
  
    function reset( ) {
        $this->_controller->reset( );
    }

    function getMode( ) {
        return $this->_mode;
    }

    // this function should actually go in a superclass of action
    // since we dont have a superclass, putting it here 
    function returnToURL( ) {
        $session = CRM_Session::singleton( );
        $config  = CRM_Config::singleton( );

        $returnURL = $session->popReturnURL( );
        if ( empty( $returnURL ) ) {
            $returnURL = $config->mainMenu;
        }

        header( "Location: $returnURL" );
        exit();
    }
  
    function setContent(&$content) {
        $this->_controller->setContent($content);
    }
  
    function &getContent() {
        return $this->_controller->getContent();
    }

}

?>
