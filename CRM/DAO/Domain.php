<?php

require_once 'CRM/DAO/Base.php';

class CRM_DAO_Domain extends CRM_DAO_Base {

  /*
   * name of the domain / organization
   * @var string
   */
  public $name;

  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'name'        => array( self::TYPE_STRING, self::NOT_NULL ),
                                   )
                             );
    }
    return $fields;
  }

}

?>
