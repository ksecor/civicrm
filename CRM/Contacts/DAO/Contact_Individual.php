<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_Contact_Individual extends CRM_Contacts_DAO_ContactBase {

  /**
   * name and salutation of individual
   * @var string
   */
  public $first_name;
  public $middle_name;
  public $last_name;

  public $prefix;
  public $suffix;
  public $job_title;

  /**
   * how do we address this person in a formal letter
   * @var enum
   */
  public $greeting_type;
  
  /**
   * This allows the user to override an automatically generated greeting
   * @var string
   */
  public $custom_greeting;
  
  /**
   * List of demographic fields for the individual
   */
  public $gender;
  public $birth_date;
  public $is_deceased;

  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'first_name'   => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'last_name'    => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'middle_name'  => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'prefix'       => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'suffix'       => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'job_title'  => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'greeting_type'   => array( self::TYPE_ENUM, self::NOT_NULL ),
                                   'custom_greeting' => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'gender'       => array( self::TYPE_ENUM, self::NOT_NULL ),
                                   'birth_date'   => array( self::TYPE_DATE, self::NOT_NULL ),
				   'is_deceased'  => array( self::TYPE_BOOLEAN, null )
                                   )
                             );
    }
    return $fields;
  }
  
}

?>
