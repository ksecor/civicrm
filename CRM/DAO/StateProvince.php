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

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links = null;

    if ( $links === null ) {
      $links = array( 'country_id' => 'Country:id' );
      array_merge( $this->_links, self::$links );
    }

  }

}

?>