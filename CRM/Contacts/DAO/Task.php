<?php

require_once 'CRM/DAO/Base.php';

class CRM_Contacts_DAO_Task extends CRM_DAO_Base {
  public $target_contact_id;
  public $assigned_contact_id;
  public $time_started;
  public $time_completed;
  public $description;

  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
			    parent::dbFields(),
			    array(
				  'target_contact_id'    => array(self::TYPE_INT, self::NOT_NULL),
				  'assigned_contact_id'  => array(self::TYPE_INT, self::NOT_NULL),
				  'time_started'         => array(self::TYPE_DATE | self::TYPE_TIME | self::TYPE_STRING),
				  'time_ended'           => array(self::TYPE_DATE | self::TYPE_TIME | self::TYPE_STRING),
				  'description'          => array(self::TYPE_STRING),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

  function links() {
    static $links;
    if($links === null) {
      $links = array_merge(parent::links(),
			   array('target_contact_id'   => 'crm_contact:id',
				 'assigned_contact_id' => 'crm_contact:id',
				 )
			   );
    }
    return $links;
  } // end of method links()



} // end of class CRM_Contacts_DAO_Task

?>
