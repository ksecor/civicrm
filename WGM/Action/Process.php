<?php

class WGM_Action_Process extends HTML_QuickForm_Action {
  protected $_stateMachine;

  function WGM_Action_Process( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }
  
  function perform( &$page, $actionName ) {
    $this->_stateMachine->reset( );

    $this->_stateMachine->returnToURL( );
  }

}

?>
