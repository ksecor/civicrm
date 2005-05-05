<?php

require_once 'CRM/Import/Base.php';

class CRM_Contacts_Import_Summary extends CRM_Import_Base {
  protected $_selectFields = null;

  protected $_fieldIndex   = null;

  protected $_rows         = null;

  protected $_maxFields    = 0;

  function __construct() {
    parent::__construct();
  }

  function init() {
    $this->_rows = array();
    $this->_maxFields = 0;
  }


  function process($line) {
    $elements = CRM_Utils_String::explode( $line, CRM_Utils_String::COMMA, true );

    if ( $this->_maxFields > count($elements) ) {
      $this->_maxFields = count($elements);
    }

    $this->_rows[] = $elements;

    if ( count($this->_rows) == 5 ) {
      return self::STOP;
    }
    return self::VALID;
  }

  function fini() {
    $this->_allFields    = array();
    $this->_selectFields = array();

    for ( $i = 0; $i < $this->_maxFields; $i++ ) {
      
    }

  }
  
}

?>