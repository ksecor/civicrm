<?php

require_once 'CRM/DAO/Base.php';

class CRM_DAO_Mobile_Provider extends CRM_DAO_Base {

  // name of the mobile provider
  public $name;

  function __construct() {
    parent::$_construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'name'         => array( CRM_Type::T_STRING),
                                   )
                             );
    }
    return $fields;
  }

}

?>
