<?php

require_once 'CRM/DAO.php';

class CRM_DAO_Base extends CRM_DAO {

  /*
   * auto incremented id
   * @var int
   */
  public $id;

  /*
   * Array of FK relationships to other tables
   * Need to figure and settle on a naming convention
   * @var array
   */
  public $_links;

  function __construct() {
    $this->_links = null;
    
    $this->links();
  }

}

?>