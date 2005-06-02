<?php


require_once 'CRM/Utils/String.php';
require_once 'CRM/Import/Base.php';

class CRM_Contacts_Import_Summary extends CRM_Import_Base {
  var $_selectFields = null;

  var $_fieldIndex   = null;

  var $_rows         = null;

  var $_maxFields    = 0;

  function CRM_Contacts_Import_Summary() {
    parent::CRM_Import_Base();
  }

  function init() {
    $this->_rows = array();
    $this->_maxFields = 0;
  }


  function process($line) {
    $elements = CRM_Utils_String::explode( $line, CRM_UTILS_STRING_COMMA, true );

    if ( $this->_maxFields > count($elements) ) {
      $this->_maxFields = count($elements);
    }

    $this->_rows[] = $elements;

    if ( count($this->_rows) == 5 ) {
      return CRM_CONTACTS_IMPORT_SUMMARY_STOP;
    }
    return CRM_CONTACTS_IMPORT_SUMMARY_VALID;
  }

  function fini() {
    $this->_allFields    = array();
    $this->_selectFields = array();

    for ( $i = 0; $i < $this->_maxFields; $i++ ) {
      
    }

  }
  
}

?>