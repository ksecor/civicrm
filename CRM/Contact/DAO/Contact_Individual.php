<?php

require_once 'CRM/Contact/DAO/ContactBase.php';

/**
 * This is a dataobject class for Contact individual table.
 */
class CRM_Contact_DAO_Contact_Individual extends CRM_Contact_DAO_ContactBase 
{

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

  /**
   * This the constructor of the class
   */
  function __construct() 
  {
    parent::__construct();
  }
  
  /**
   * This function is used to create the array of the feilds from Contact Individual table.
   * @return array array contains the feilds of the table
   */
  function dbFields() 
  {
    static $fields;
    if ($fields === null) {
      $fields = array_merge(
			    parent::dbFields(),
                             array(
                                   'first_name'   => array(CRM_Type::T_STRING, self::NOT_NULL),
                                   'last_name'    => array(CRM_Type::T_STRING, self::NOT_NULL),
                                   'middle_name'  => array(CRM_Type::T_STRING, self::NOT_NULL),
                                   'prefix'       => array(CRM_Type::T_STRING, self::NOT_NULL),
                                   'suffix'       => array(CRM_Type::T_STRING, self::NOT_NULL),
                                   'job_title'    => array(CRM_Type::T_STRING, self::NOT_NULL),
                                   'greeting_type'   => array(CRM_Type::T_ENUM, self::NOT_NULL),
                                   'custom_greeting' => array(CRM_Type::T_STRING, self::NOT_NULL),
                                   'gender'       => array(CRM_Type::T_ENUM, self::NOT_NULL),
                                   'birth_date'   => array(CRM_Type::T_DATE, self::NOT_NULL),
				   'is_deceased'  => array(CRM_Type::T_BOOLEAN, null)
                                   )
                             );
    }
    return $fields;
  }
  
}

?>
