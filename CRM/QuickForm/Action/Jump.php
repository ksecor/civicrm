<?php
// $Id: Jump.class.php,v 1.1 2004/02/18 20:18:45 lobo Exp $

require_once 'HTML/QuickForm/Action/Jump.php';

/**
 * The action for a 'next' button of wizard-type multipage form. 
 * 
 */
class CRM_QuickForm_Action_Jump extends HTML_QuickForm_Action_Jump {
  protected $_stateMachine;

  function CRM_QuickForm_Action_Jump( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }

  function perform(&$page, $actionName) {
    parent::perform($page, $actionName);
  }

}

?>
