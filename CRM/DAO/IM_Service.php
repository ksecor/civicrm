<?php

require_once 'CRM/DAO/Base.php';

class CRM_DAO_IM_Service extends CRM_DAO_Base {

  // name of the IM service
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
                                   'name'         => array( self::TYPE_STRING),
                                   )
                             );
    }
    return $fields;
  }

}

?>
