<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_Relationship_Types extends CRM_Contacts_DAO_Base {


  public $name;
  public $description;
  public $direction;
  public $contact_type;

  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
			    parent::dbFields(),
			    array(
				  'name'         => array(self::TYPE_STRING),
				  'description'  => array(self::TYPE_STRING),
				  'direction'    => array(self::TYPE_ENUM),
				  'contact_type' => array(self::TYPE_ENUM),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

} // end of class CRM_Contacts_DAO_Relationship_Types

?>
