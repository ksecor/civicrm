<?php

require_once 'HTML/QuickForm/Action/Next.php';

class CRM_Action_Next extends HTML_QuickForm_Action_Next {
  protected $_stateMachine;

  function CRM_Action_Next( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }

  function perform(&$page, $actionName) {
    $this->_stateMachine->getNextState( $page, $actionName );
  }

}

?>
