<?php

require_once 'CRM/DAO/Base.php';

class CRM_Contacts_DAO_Base extends CRM_DAO_Base {

  /*
   * organization this record belong to
   * @var int
   */
  public $domain_id;

  /*
   * unique identifier for all time (a uuid has multiple records with different id's
   * and different revision numbers.
   * @var int
   */
  public $uuid;

  /*
   * the latest revision id. Note that the revision id is increasing, but not necessarily
   * monotonically. This way we can roll back to a consistent state if needed
   * @var int
   */
  public $rid;

  /*
   * is this the latest revision? This enables us to update a record with one insert and one
   * update and a query need only include rid_latest = true. A signficant optimization over
   * keeping the latest revision id
   * @var boolean
   */
  public $latest_rev;

  /*
   * has this record been deleted
   * @var boolean
   */
  public $is_deleted;

  /*
   * date and time of creation of this record. Since all records are revisioned, any
   * modifications would be in a seperate record
   * @var object
   */
  public $created;

  /*
   * id of the contact who created this record
   * @var int
   */
  public $created_by;

  function __construct() {
    parent::__construct();
  }

  function links() {
    static $links = null;

    if ( $links === null ) {
      $links = array( 'created_by' => 'Contact:id',
                      'domain_id'  => 'Domain:id' );
      array_merge( $this->_links, $links );
    }

    parent::links();
  }

}

?>