<?php

require_once 'CRM/DAO/Base.php';

class CRM_Contact_DAO_Base extends CRM_DAO_Base {

  /*
   * organization this record belong to
   * @var int
   */
  public $domain_id;

  function __construct() {
    parent::__construct();
  }

  function links( ) {
    static $links = null;

    if ( $links === null ) {
      $links = array( 'domain_id'  => 'crm_domain:id' );
    }
    return $links;
  }


  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                            parent::dbFields(),
                            array(
                                  'domain_id'    => array( CRM_Type::T_INT, self::NOT_NULL ),
                                  )
                            );
    }
    return $fields;
  }

}

?>