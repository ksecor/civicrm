<?php

require_once 'CRM/Contacts/DAO/Base.php';

class CRM_Contacts_DAO_Context extends CRM_Base {

  /*
   * organization this record belong to
   * @var int
   */
  public $domain_id;

  /*
   * name of the context
   * @var string
   */
  public $name;

  /*
   * description of the context
   * @var string
   */
  public $description;

  function __construct() {
    parent::$_construct();
  }

  function links() {
    static $links = null;

    if ( $links === null ) {
      $links = array( 'domain_id'  => 'Domain:id' );
      array_merge( $this->_links, $links );
    }

    parent::links();
  }

}

?>