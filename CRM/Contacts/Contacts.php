<?php

require_once 'CRM/Base.php';
require_once 'CRM/Controller/SinglePage.php';

class CRM_Contacts_Contacts extends CRM_Base {
  
  protected $_controller;

  function __construct() {
    parent::__construct();
  }

  function run() {
    $mode = CRM_Form::MODE_VIEW;
    $this->_controller = new CRM_Controller_SinglePage( 'CRM_Contacts_Form_CRUD', 'Contact CRUD Page', $mode );

    $this->_controller->process();
    $this->_controller->run();
  }

  function display() {
    return $this->_controller->getContent();
  }

}

?>