<?php

require_once 'HTML/QuickForm/Action/Next.php';

class WGM_Action_Next extends HTML_QuickForm_Action_Next {
  protected $_stateMachine;

  function WGM_Action_Next( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }

  function perform(&$page, $actionName) {
    $this->_stateMachine->getNextState( $page, $actionName );
  }

}

?>
