<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_Contact_Household extends CRM_Contacts_DAO_ContactBase {

  /**
   * name and other related things for the household
   * @var string
   */
  public $household_name;
  public $nick_name;

  /**
   * FK to who the primary contact for the organization is
   * @var int
   */
  public $primary_contact_id;

  /**
   * various booleans that determine how to contact this household vs
   * contacting individual members of the household
   * @var boolean
   */
  public $phone_to_household;
  public $email_to_household;
  public $postal_to_household;

  /**
   * annual income of household
   * @var int
   */
  public $annual_income;

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links;
    if ( $links === null ) {
      $links = array_merge( parent::links(),
                            array( 'primary_contact_id' => 'crm_contact:id' ) );
    }
    return $links;
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'household_name'    => array( self::TYPE_STRING, null ),
                                   'nick_name'  => array( self::TYPE_STRING, null ),
                                   'primary_contact_id' => array( self::TYPE_INT, null ),
                                   'phone_to_household' => array( self::TYPE_BOOLEAN, null ),
                                   'email_to_household' => array( self::TYPE_BOOLEAN, null ),
                                   'mail_to_household' => array( self::TYPE_BOOLEAN, null ),
                                   )
                             );
    }
    return $fields;
  }

}

?>