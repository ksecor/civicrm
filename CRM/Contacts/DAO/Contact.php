<?php

require_once 'CRM/Contacts/DAO/Base.php';

class CRM_Contacts_DAO_Contact extends CRM_Contacts_DAO_Base {

  /**
   * what type of contact is this, avoids doing a lookup in multiple tables
   * @var enum
   */
  public $contact_type;

  /**
   * cache a composition of the first and last name to speed up sorting
   * @var string
   */
  public $sort_name;

  /**
   * where did we originally get the data for this context
   * should this be actually an FK to another more comprehensive table
   * @var string
   */
  public $source;

  /**
   * what mode of communication does the user prefer
   * @var enum
   */
  public $preferred_communication_method;

  /**
   * various boolean operators to comply with the
   * local / state / federal laws.
   * @var boolean
   */
  public $do_not_phone;
  public $do_not_email;
  public $do_not_mail;

  /**
   * random 64 bit number created at "insertion" time to give
   * the user some random value that is not monotonically increasing like
   * the id / uuid. Makes things a bit difficult to crack
   * @var int
   */
  public $hash;

  function __construct() {
    parent::__construct();
  }

}

?>