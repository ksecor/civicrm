<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_Contact_List extends CRM_Contacts_DAO_ContactBase {

  public $list_id;

  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
			    parent::dbFields(),
			    array(
				  'list_id' => array(self::TYPE_INT, self::NOT_NULL),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields


  function links() {
    static $links;
    if ( $links === null ) {
      $links = array_merge(parent::links(),
			   array('list_id'      => 'crm_list:id')
			   );
    }
    return $links;
  } // end of method links()


} // end of class CRM_Contacts_DAO_Contact_List

?>
