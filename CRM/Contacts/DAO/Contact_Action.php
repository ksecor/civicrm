<?php

require_once 'CRM/DAO/Base.php';

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
				  'contact_id'           => array(self::TYPE_INT, self::NOT_NULL),
				  'action_category'      => array(self::TYPE_ENUM),
				  'callback'             => array(self::TYPE_STRING),
				  'action_id'            => array(self::TYPE_INT, self::NOT_NULL),
				  'action_date'          => array(self::TYPE_DATE | self::TYPE_TIME | self::TYPE_STRING),
				  'action_summary'       => array(self::TYPE_STRING),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

} // end of class CRM_Contacts_DAO_Contact_Action

?>
