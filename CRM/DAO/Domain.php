<?php

require_once 'CRM/DAO/Base.php';

class CRM_DAO_Domain extends CRM_DAO_Base {

  /*
   * name of the domain / organization
   * @var string
   */
  public $name;

  /*
   * has this record been deleted
   * @var boolean
   */
  public $is_deleted;

  /*
   * date and time of creation of this record. Since all records are revisioned, any
   * modifications would be in a seperate record
   * @var object
   */
  public $created;

  function __construct() {
  }

}

?>