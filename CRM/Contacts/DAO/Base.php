<?php

require_once 'CRM/DAO/Base.php';

class CRM_Contacts_DAO_Base extends CRM_DAO_Base {

  /*
   * organization this record belong to
   * @var int
   */
  public $domain_id;

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

  function links( ) {
    static $links = null;

    if ( $links === null ) {
      $links = array( 'created_by' => 'Contact:id',
                      'domain_id'  => 'Domain:id' );
    }
    return $links;
  }


  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
                            parent::dbFields(),
                            array(
                                  'domain_id'    => array( self::TYPE_INT, self::NOT_NULL ),
                                  'created'      => array( self::TYPE_TIMESTAMP, null ),
                                  'created_by'   => array( self::TYPE_INT, self::NOT_NULL ),
                                  )
                            );
    }
    return $fields;
  }

}

?>