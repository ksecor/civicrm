<?php

require_once 'CRM/DAO/Base.php';

class CRM_Contacts_DAO_ContactBase extends CRM_DAO_Base {

  /*
   * organization this record belong to
   * @var int
   */
  public $domain_id;

  /*
   * FK link to uuid in contact table
   * @var int
   */
  public $contact_uuid;

  /*
   * FK link to rid in contact table
   * @var int
   */
  public $contact_rid;

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links = null;

    if ( $links === null ) {
      $links = array( 'contact_uuid' => 'Contact:uuid',
                      'contact_rid'  => 'Contact:rid' );
      array_merge( $this->_links, $links );
    }

    parent::links();
  }

}

?>
?>