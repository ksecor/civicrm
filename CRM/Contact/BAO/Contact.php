<?php

require_once 'CRM/Contacts/DAO/Contact.php';

require_once 'CRM/Contacts/BAO/Base.php';

/**
 * rare case where because of inheritance etc, we actually store a reference
 * to the dao object rather than inherit from it
 */

class CRM_Contacts_BAO_Contact extends CRM_Contacts_BAO_Base 
{
  
  protected $_contactDAO;
  
  function __construct()
  {
    parent::__construct();
    _contactDAO = new CRM_Contacts_DAO_Contact();
  }
  
}

?>
