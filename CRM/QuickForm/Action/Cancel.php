<?php

class CRM_QuickForm_Action_Cancel extends HTML_QuickForm_Action {
  protected $_stateMachine;

  function CRM_QuickForm_Action_Cancel( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }
  
  function perform( &$page, $actionName ) {
    // cancel needs to be smarter than this at times
    $this->_stateMachine->reset( );

    $this->_stateMachine->returnToURL( );
  }

}

?>
