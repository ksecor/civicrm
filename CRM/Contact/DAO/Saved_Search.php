<?php

require_once 'CRM/Contact/DAO/DomainBase.php';

class CRM_Contact_DAO_Saved_Search extends CRM_Contact_DAO_DomainBase {

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
				  'name'        => array(CRM_Type::T_STRING, self::NOT_NULL),
				  'description' => array(CRM_Type::T_STRING),
				  'query'       => array(CRM_Type::T_TEXT, self::NOT_NULL),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

} // end of class CRM_Contact_DAO_Saved_Search

?>
