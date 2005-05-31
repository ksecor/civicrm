<?php

require_once 'CRM/Import/Base.php';

class CRM_Contacts_Import_Base extends CRM_Import_Base {
  function __construct() {
  }

  function getStaticFields() {
    static $fields;
    
    if ( ! isset( $fields ) ) {
      $fields = array(
                      array( ts('First Name')          , 'first_name'          , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Middle Name')         , 'middle_name'         , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Last Name')           , 'last_name  '         , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Prefix')              , 'prefix'              , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Suffix')              , 'suffix'              , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Job Title')           , 'job_title'           , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Street')              , 'street'              , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Supplemental Address'), 'supplemental_address', CRM_Utils_Type::T_STRING, false ),
                      array( ts('City')                , 'city'                , CRM_Utils_Type::T_STRING, false ),
                      array( ts('State')               , 'state_province'      , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Country')             , 'country'             , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Postal Code')         , 'postal_code'         , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Email')               , 'email'               , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Phone')               , 'phone_1'             , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Mobile')              , 'phone_2'             , CRM_Utils_Type::T_STRING, false ),
                      array( ts('Fax')                 , 'phone_3'             , CRM_Utils_Type::T_STRING, false ),
                      );
      for ( $i = 0; $i < count($fields); $i++ ) {
        $this->addField( $fields[$i][0],
                         $fields[$i][1],
                         $fields[$i][2],
                         $fields[$i][3] );
      }
    }

    return $this->_fields;

  }

  function init() {
  }

  function process( $line ) {
    $elements = CRM_Utils_String::explodeLine( $line, $seperator, true );
    
    $returnCode = $this->setActiveFields( $elements );
    if ( $returnCode & self::VALID ) {
      $returnCode = $this->process( $line );
    }
    return $returnCode;
  }

  function fini() {
  }

}

?>
