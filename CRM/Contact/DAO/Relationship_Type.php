<?php

require_once 'CRM/Contact/DAO/DomainBase.php';

class CRM_Contact_DAO_Relationship_Type extends CRM_Contact_DAO_DomainBase {

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
				  'name'         => array(CRM_Type::T_STRING),
				  'description'  => array(CRM_Type::T_STRING),
				  'direction'    => array(CRM_Type::T_ENUM),
				  'contact_type' => array(CRM_Type::T_ENUM),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

} // end of class CRM_Contact_DAO_Relationship_Type

?>
