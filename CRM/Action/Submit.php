<?php
// $Id: Submit.class.php,v 1.1 2004/02/18 20:18:45 lobo Exp $

require_once 'HTML/QuickForm/Action/Submit.php';

/**
 * The action for a 'next' button of wizard-type multipage form. 
 * 
 */
class CRM_Action_Submit extends HTML_QuickForm_Action_Submit {
  protected $_stateMachine;

  function CRM_Action_Submit( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }

  function perform(&$page, $actionName) {
    parent::perform($page, $actionName);
  }

}

?>
