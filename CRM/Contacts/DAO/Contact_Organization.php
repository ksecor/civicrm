<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_Contact_Organization extends CRM_Contacts_DAO_ContactBase {

  /**
   * name and other related things for the organization
   * @var string
   */
  public $organization_name;
  public $legal_name;
  public $nick_name;
  public $sic_code;

  /**
   * FK to who the primary contact for the organization is
   * @var int
   */
  public $primary_contact_id;

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links;
    if ( $links === null ) {
      $links = array_merge( parent::links(),
                            array( 'primary_contact_id' => 'Contact:id' ) );
    }
    return $links;
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'organization_name'   => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'legal_name'    => array( self::TYPE_STRING, null ),
                                   'nick_name'  => array( self::TYPE_STRING, null ), 
                                   'sic_code'  => array( self::TYPE_STRING, null ),
                                   'primary_contact_id' => array( self::TYPE_INT, null ),
                                   )
                             );
    }
    return $fields;
  }


}

?>