<?php

require_once 'CRM/Controller.php';
require_once 'CRM/Form.php';

class CRM_Controller_SinglePage extends CRM_Controller {
  protected $_stateMachine = null;

  function __construct( $pageClass, $pageName, $formMode, $modal = true ) {
    parent::__construct( $pageName, $modal );

    $this->_stateMachine = new CRM_StateMachine( $this );

    $params = array( $pageClass );

    $this->_stateMachine->addSequentialStates( $params );

    $this->addPages( $this->_stateMachine, $params, $formMode );

    $this->addDefaultActions( );

  }

}

?>
