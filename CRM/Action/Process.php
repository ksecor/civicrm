<?php

class CRM_Action_Process extends HTML_QuickForm_Action {
  protected $_stateMachine;

  function CRM_Action_Process( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }
  
  function perform( &$page, $actionName ) {
    $this->_stateMachine->reset( );

    $this->_stateMachine->returnToURL( );
  }

}

?>
