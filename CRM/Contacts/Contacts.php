<?php

require_once 'CRM/Base.php';
require_once 'CRM/Controller/SinglePage.php';

require_once 'CRM/DAO/Domain.php';

require_once 'CRM/Contacts/BAO/Contact_Individual.php';

class CRM_Contacts_Contacts extends CRM_Base {
  
  protected $_controller;

  function __construct() {
    parent::__construct();
  }

  function run( $mode, $id = 0 ) {
    $this->_controller = new CRM_Controller_SinglePage( 'CRM_Contacts_Form_CRUD', 'Contact CRUD Page', $mode );

    $this->_controller->process();
    $this->_controller->run();

    $contact    = new CRM_Contacts_BAO_Contact_Individual();

    $contact->domain_id = 1;
    $contact->find();
    while ( $contact->fetch() ) {
      CRM_Utils::debug( 'contactInd', $contact );
    }

    /**
    $contact = new CRM_Contacts_BAO_Contact_Individual();
    $contact->contact_type = 'Individual';
    $contact->sort_name    = 'Donald Lobo';
    $contact->hash         = 9876543;
    $contact->domain_id    = 1;
    $contact->first_name   = 'Donald';
    $contact->last_name    = 'Lobo';
    $contact->insert();
    **/
  }

  function display() {
    return $this->_controller->getContent();
  }

}

?>