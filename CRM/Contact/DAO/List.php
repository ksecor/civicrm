<?php

require_once 'CRM/Contacts/DAO/DomainBase.php';

class CRM_Contacts_DAO_List extends CRM_Contacts_DAO_DomainBase {

  public $iname;
  public $name;
  public $description;
  public $list_type;
  public $saved_search_id;

  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
			    parent::dbFields(),
			    array(
				  'iname'           => array(CRM_Type::T_STRING, self::NOT_NULL),
				  'name'            => array(CRM_Type::T_STRING, self::NOT_NULL),
				  'description'     => array(CRM_Type::T_STRING),
				  'list_type'       => array(CRM_Type::T_ENUM, self::NOT_NULL),
				  'saved_search_id' => array(CRM_Type::T_INT),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields


  function links() {
    static $links;
    if ( $links === null ) {
      $links = array_merge(parent::links(),
			   array('saved_search_id'      => 'crm_saved_search:id')
			   );
    }
    return $links;
  } // end of method links()


} // end of class CRM_Contacts_DAO_List

?>
