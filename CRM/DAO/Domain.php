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

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = arrray_merge(
                             parent::dbFields(),
                             array(
                                   'name'        => array( self::TYPE_STRING, self::NOT_NULL ),
                                   'is_deleted'  => array( self::TYPE_BOOLEAN, null ),
                                   'created'     => array( self::TYPE_TIMESTAMP, self::NOT_NULL ),
                                   )
                             );
    }
    return $fields;
  }

}

?>