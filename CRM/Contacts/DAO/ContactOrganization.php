<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_ContactOrganization extends CRM_Contacts_DAO_ContactBase {

  /**
   * name and other related things for the organization
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
  public $race;
  public $marital_status;
  public $occupation;
  public $annual_income;
  public $is_deceased;

  function __construct() {
    parent::__construct();
  }

}

?>
?>