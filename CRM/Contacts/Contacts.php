<?php

require_once 'CRM/Base.php';
require_once 'CRM/Controller/SinglePage.php';

require_once 'CRM/Contacts/DAO/Contact.php';
require_once 'CRM/Contacts/DAO/Contact_Individual.php';

class CRM_Contacts_Contacts extends CRM_Base {
  
  protected $_controller;

  function __construct() {
    parent::__construct();
  }

  function run( $mode, $id = 0 ) {
    $this->_controller = new CRM_Controller_SinglePage( 'CRM_Contacts_Form_CRUD', 'Contact CRUD Page', $mode );

    $this->_controller->process();
    $this->_controller->run();

    $contact               = new CRM_Contacts_DAO_Contact();
    $contact->contact_type = 'Individual';
    $contact->sort_name    = 'Donald Lobo';
    $contact->hash         = 9876543;
    $contact->domain_id    = 1;
    $contact->insert();

    $contactInd = new CRM_Contacts_DAO_Contact_Individual();
    $contactInd->first_name = 'Donald';
    $contactInd->last_name  = 'Lobo';
    $contactInd->contact_id = $contact->id;
    $contactInd->insert();

  }

  function display() {
    return $this->_controller->getContent();
  }

}

?>