<?php

require_once 'CRM/Base.php';
require_once 'CRM/Controller/Simple.php';

require_once 'CRM/DAO/Domain.php';

require_once 'CRM/Contact/BAO/Contact_Organization.php';

class CRM_Contact_Organization extends CRM_Base {
  
  protected $_controller;

  function __construct() {
    parent::__construct();
  }

  function run( $mode, $id = 0 ) {
    $session = CRM_Session::singleton();
    $config  = CRM_Config::singleton();

    // store the return url. Note that this is typically computed by the framework at runtime
    // based on multiple things (typically where the link was clicked from / http_referer
    // since we are just starting and figuring out navigation, we are hard coding it here
    $session->pushUserContext( $config->httpBase . "crm/contact/add_org?reset=1" );

    $this->_controller = new CRM_Controller_Simple( 'CRM_Contact_Form_Organization', 'Contact Organization Page', $mode );

    $this->_controller->process();
    $this->_controller->run();
  }

    function display() {
        return $this->_controller->getContent();
    }

}

?>