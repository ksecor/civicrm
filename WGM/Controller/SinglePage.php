<?php

require_once 'WGM/Controller.php';

class WGM_Controller_SinglePage extends WGM_Controller {
  protected $_stateMachine = null;

  function __construct( $pageClass, $pageName, $modal = true ) {
    parent::__construct( $pageName, $modal );

    $this->_stateMachine = new WGM_StateMachine( $this );

    $params = array( $pageClass );

    $this->_stateMachine->addSequentialStates( $params );

    $this->addPages( $this->_stateMachine, $params );

    $this->addDefaultActions( );

  }

}

?>
