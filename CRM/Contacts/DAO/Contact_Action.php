<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_Contact_Action extends CRM_Contacts_DAO_ContactBase {

  public $action_category;
  public $callback;
  public $action_id;
  public $action_date;
  public $action_summary;

  function __construct() {
    parent::__construct();
  }


  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
			    parent::dbFields(),
			    array(
				  'action_category'      => array(CRM_Type::T_ENUM),
				  'callback'             => array(CRM_Type::T_STRING),
				  'action_id'            => array(CRM_Type::T_INT, self::NOT_NULL),
				  'action_date'          => array(CRM_Type::T_DATE | CRM_Type::T_TIME | CRM_Type::T_STRING),
				  'action_summary'       => array(CRM_Type::T_STRING),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

} // end of class CRM_Contacts_DAO_Contact_Action

?>
