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
  }

  function links() {
    static $links = null;

    if ( $links === null ) {
      $links = array( 'domain_id' => 'Domain:id' );
      array_merge( $this->_links, self::$links );
    }
  }

}

?>