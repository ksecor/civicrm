<?php

class CRM_QuickForm_Action_Done extends HTML_QuickForm_Action {
  protected $_stateMachine;

  function CRM_QuickForm_Action_Done( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }

  // this is basically a self submit, so 
  // validate the page and call process
  // when we are done processing, we basically bail :)
  function perform( &$page, $actionName ) {
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

    // the page is valid, process it before we jump to the next state
    $page->postProcess( );

    // ok so we are done now, pop stack and jump back to where we came from
    $this->_stateMachine->reset( );
    $this->_stateMachine->returnToURL( );
  }

}

?>
