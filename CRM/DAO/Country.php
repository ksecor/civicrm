<?php

require_once 'CRM/DAO/Base.php';

class CRM_DAO_Country extends CRM_DAO_Base {

  /*
   * name of the country
   * @var string
   */
  public $name;


  /*
   * iso code of the country
   * @var string
   */
  public $iso_code;

  /*
   * country_code the national prefix of this country which is needed when dialing from
   * another country to this country
   * @var string
   */
  public $country_code;

  /*
   * the international dialing prefix for this country. i.e. how do u dial another country
   * from within this country
   * @var string
   */
  public $idd_prefix;

  /*
   * the national dialing prefix for this country. i.e. how do u dial another area code
   * from within this country
   * @var string
   */
  public $ndd_prefix;

  function __construct() {
    parent::$_construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                             parent::dbFields(),
                             array(
                                   'name'         => array( CRM_Type::T_STRING, self::NOT_NULL ),
                                   'iso_code'     => array( CRM_Type::T_STRING, null ),
                                   'country_code' => array( CRM_Type::T_STRING, null ),
                                   'idd_prefix'   => array( CRM_Type::T_STRING, null ),
                                   'ndd_prefix'   => array( CRM_Type::T_STRING, null ),
                                   )
                             );
    }
    return $fields;
  }

}

?>