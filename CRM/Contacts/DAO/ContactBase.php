<?php

require_once 'CRM/DAO/Base.php';

class CRM_Contacts_DAO_ContactBase extends CRM_DAO_Base {

  /*
   * FK link to uuid in contact table
   * @var int
   */
  public $contact_id;

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links = null;

    if ( $links === null ) {
      $links = array( 'contact_id'  => 'crm_contact:id'  );
    }
    return $links;
  }


  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'contact_id'   => array( self::TYPE_INT, self::NOT_NULL ),
                                   )
                             );
    }
    return $fields;
  }


}

?>