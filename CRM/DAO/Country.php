<?php

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

}

?>