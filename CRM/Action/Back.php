<?php
// $Id: Back.class.php,v 1.1 2004/02/18 20:18:45 lobo Exp $

require_once 'HTML/QuickForm/Action/Back.php';

/**
 * The action for a 'next' button of wizard-type multipage form. 
 * 
 */
class CRM_Action_Back extends HTML_QuickForm_Action_Back {
  protected $_stateMachine;

  function CRM_Action_Back( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }

  function perform(&$page, $actionName) {
    $this->_stateMachine->getBackState( $page, $actionName );
  }

}

?>
