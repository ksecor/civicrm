<?php

class CRM_DAO_StateProvince extends CRM_Base {

  /*
   * auto incremented id
   * @var int
   */
  public $id;

  /*
   * name of the state / province
   * @var string
   */
  public $name;

  /*
   * abbreviation for this state / province
   * @var string
   */
  public $abbreviation;

  /*
   * FK to the country this state/provice belongs to
   * @var int
   */
  public $country_id;

  static $_links;

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links;
    if ( $links === null ) {
      $links = array( 'country_id' => 'crm_country:id' );
    }
    return $links;
  }

  
  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'name'         => array( CRM_Type::T_STRING, self::NOT_NULL ),
                                   'abbreviation' => array( CRM_Type::T_BOOLEAN, null ),
                                   'country_id'   => array( CRM_Type::T_TIMESTAMP, self::NOT_NULL ),
                                   )
                             );
    }
    return $fields;
  }

}

?>