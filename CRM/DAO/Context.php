<?php

class CRM_DAO_Context extends CRM_DAO_Base {

  /*
   * name of the context (home / work / summer vacation retreat etc)
   * @var string
   */
  public $name;

  /*
   * description of the context
   * @var boolean
   */
  public $description;

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links;
    if ( $links == null ) {
      $links = array( 'domain_id' => 'crm_domain:id' );
    }
    return $links;
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'name'        => array( CRM_Type::T_STRING, self::NOT_NULL ),
                                   'description' => array( CRM_Type::T_STRING, null ),
                                   )
                             );
    }
    return $fields;
  }

}

?>