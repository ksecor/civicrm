<?php

require_once 'CRM/Import/Base.php';

class CRM_Contacts_Import_Base extends CRM_Import_Base {
  function __construct() {
  }

  function getStaticFields() {
    static $fields;
    
    if ( ! isset( $fields ) ) {
      $fields = array(
                      array( 'First Name' , 'first_name' , CRM_Type::T_STRING, false ),
                      array( 'Middle Name', 'middle_name', CRM_Type::T_STRING, false ),
                      array( 'Last Name'  , 'last_name  ', CRM_Type::T_STRING, false ),
                      array( 'Prefix'     , 'prefix'     , CRM_Type::T_STRING, false ),
                      array( 'Suffix'     , 'suffix'     , CRM_Type::T_STRING, false ),
                      array( 'Job Title'  , 'job_title'  , CRM_Type::T_STRING, false ),
                      array( 'Street'     , 'street'     , CRM_Type::T_STRING, false ),
                      array( 'Supplemental Address', 'supplemental_address' CRM_Type::T_STRING, false ),
                      array( 'City'       , 'city'       , CRM_Type::T_STRING, false ),
                      array( 'State'      , 'state_province', CRM_Type::T_STRING, false ),
                      array( 'Country'    , 'country'    , CRM_Type::T_STRING, false ),
                      array( 'Postal Code', 'postal_code', CRM_Type::T_STRING, false ),
                      array( 'Email'      , 'email'      , CRM_Type::T_STRING, false ),
                      array( 'Phone'      , 'phone_1'    , CRM_Type::T_STRING, false ),
                      array( 'Mobile'     , 'phone_2'    , CRM_Type::T_STRING, false ),
                      array( 'Fax'        , 'phone_3'    , CRM_Type::T_STRING, false ),
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
    $elements = CRM_String::explodeLine( $line, $seperator, true );
    
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