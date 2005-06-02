<?php

$GLOBALS['_CRM_CONTACTS_IMPORT_BASE']['fields'] = '';

require_once 'CRM/Utils/String.php';
require_once 'CRM/Import/Base.php';

class CRM_Contacts_Import_Base extends CRM_Import_Base {
  function CRM_Contacts_Import_Base() {
  }

  function getStaticFields() {
    
    
    if ( ! isset( $GLOBALS['_CRM_CONTACTS_IMPORT_BASE']['fields'] ) ) {
      $GLOBALS['_CRM_CONTACTS_IMPORT_BASE']['fields'] = array(
                      array( ts('First Name')          , 'first_name'          , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Middle Name')         , 'middle_name'         , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Last Name')           , 'last_name  '         , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Prefix')              , 'prefix'              , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Suffix')              , 'suffix'              , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Job Title')           , 'job_title'           , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Street')              , 'street'              , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Supplemental Address'), 'supplemental_address', CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('City')                , 'city'                , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('State')               , 'state_province'      , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Country')             , 'country'             , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Postal Code')         , 'postal_code'         , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Email')               , 'email'               , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Phone')               , 'phone_1'             , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Mobile')              , 'phone_2'             , CRM_UTILS_TYPE_T_STRING, false ),
                      array( ts('Fax')                 , 'phone_3'             , CRM_UTILS_TYPE_T_STRING, false ),
                      );
      for ( $i = 0; $i < count($GLOBALS['_CRM_CONTACTS_IMPORT_BASE']['fields']); $i++ ) {
        $this->addField( $GLOBALS['_CRM_CONTACTS_IMPORT_BASE']['fields'][$i][0],
                         $GLOBALS['_CRM_CONTACTS_IMPORT_BASE']['fields'][$i][1],
                         $GLOBALS['_CRM_CONTACTS_IMPORT_BASE']['fields'][$i][2],
                         $GLOBALS['_CRM_CONTACTS_IMPORT_BASE']['fields'][$i][3] );
      }
    }

    return $this->_fields;

  }

  function init() {
  }

  function process( $line ) {
    $elements = CRM_Utils_String::explodeLine( $line, $seperator, true );
    
    $returnCode = $this->setActiveFields( $elements );
    if ( $returnCode & CRM_CONTACTS_IMPORT_BASE_VALID) {
      $returnCode = $this->process( $line );
    }
    return $returnCode;
  }

  function fini() {
  }

}

?>
