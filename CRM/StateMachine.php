<?php

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
    $page->process( );

    $state->getNextState( $page );
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
    
    $state->getBackState( $page );
  }

  function addState( $name, $displayName, $type, $prev, $next ) {
    $this->_states[$name] =& new CRM_State( $name, $displayName, $type, $prev, $next, $this );
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
    $states = $this->getStates( CRM_State::INITIAL );

    // assume only 1 start state for now
    $state = $states[0];
    while ( $state != null ) {
      $labels[ $state->getName( ) ] = $state->getDisplayName( );
      $state = $state->getNextStateName( );
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
      $name    = CRM_String::getClassName( $states[$i] );

      $classPath = str_replace( '_', '/', $states[$i] ) . '.php';
      require_once($classPath);
      $display = eval( sprintf( "return %s::getDisplayName( );", $states[$i] ) );

      if ( $i == 0 ) {
        // initial state
        $prev = null;
        // StateMachine has only one state!
        if ( $numStates == 1 ) {
          $next = null;
          $type = CRM_State::INITIAL | CRM_State::SFINAL | CRM_State::SEQ | CRM_State::PRESENT;
        } else {
          $next = CRM_String::getClassName( $states[$i + 1] );
          $type = CRM_State::INITIAL | CRM_State::SEQ | CRM_State::PRESENT;
        }
      } else if ( $i == $numStates - 1 ) {
        // final state
        $prev = CRM_String::getClassName( $states[$i - 1] );
        $next = null;
        $type = CRM_State::SFINAL | CRM_State::SEQ | CRM_State::PRESENT;
      } else {
        // intermediate state
        $prev = CRM_String::getClassName( $states[$i - 1] );
        $next = CRM_String::getClassName( $states[$i + 1] ); 
        $type = CRM_State::SEQ | CRM_State::PRESENT;
      }
      
      $this->addState( $name, $display, $type, $prev, $next );
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
    $session = CRM_Session::instance( );
    $config  = CRM_Config::instance( );

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
