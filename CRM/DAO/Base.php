<?php

require_once 'CRM/DAO.php';

class CRM_DAO_Base extends CRM_DAO {

  const
    TYPE_INT       =   1,
    TYPE_STRING    =   2,
    TYPE_ENUM      =   2,
    TYPE_DATE      =   4,
    TYPE_TIME      =   8,
    TYPE_BOOLEAN   =  16,
    TYPE_TEXT      =  32,
    TYPE_BLOB      =  64,
    TYPE_TIMESTAMP = 256,

    NOT_NULL       =  1,
    IS_NULL        =  2,

    DB_DAO_NOTNULL = 128;

  /*
   * auto incremented id
   * @var int
   */
  public $id;

  function __construct() {
    $this->links();

    $this->__table = $this->getTableName();
  }

  function links( ) {
    return null;
  }

  function dbFields() {
    return array( 'id' => array( self::TYPE_STRING, self::NOT_NULL ) );
  }

  function table() {
    $fields = $this->dbFields();
    $table = array();
    foreach ( $fields as $name => $value ) {
      $table[$name] = $value[0];
      if ( $value[1] === self::NOT_NULL ) {
        $table[$name] += self::DB_DAO_NOTNULL;
      }
    }

    // set the links
    $this->setLinks();

    return $table;
  }

  function getTableName() {
    $name = strtolower( get_class( $this ) );
    
    // eliminate the early part of the class name till the BAO or DAO sign
    // this should potentially be replaced with preg_match / preg_replace
    $tableName = strstr( $name, 'bao_' );
    if ( ! $tableName ) {
      $tableName = strstr( $name, 'dao_' );
    }

    if ( $tableName ) {
      // replace dao with crm
      $tableName = 'crm' . substr( $tableName, 3 );
      return $tableName;
    }
    return null;
  }

}

?>