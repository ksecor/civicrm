<?php

require_once 'CRM/Contacts/DAO/DomainBase.php';

class CRM_Contacts_DAO_Saved_Search extends CRM_Contacts_DAO_DomainBase {

  public $name;
  public $description;
  public $query;

  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
			    parent::dbFields(),
			    array(
				  'name'        => array(self::TYPE_STRING, self::NOT_NULL),
				  'description' => array(self::TYPE_STRING),
				  'query'       => array(self::TYPE_TEXT, self::NOT_NULL),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

} // end of class CRM_Contacts_DAO_Saved_Search

?>
