<?php

require_once 'WGM/Base.php';
require_once 'WGM/Controller/SinglePage.php';

class WGM_Contacts_Contacts extends WGM_Base {
  
  protected $_controller;

  function __construct() {
    parent::__construct();
  }

  function run() {
    $this->_controller = new WGM_Controller_SinglePage( 'WGM_Contacts_Form_Edit', 'Contact Edit Page' );

    $this->_controller->process();
    $this->_controller->run();
  }

  function display() {
    return $this->_controller->getContent();
  }

}

?>