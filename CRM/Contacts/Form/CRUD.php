<?php

require_once 'CRM/Form.php';

class CRM_Contacts_Form_CRUD extends CRM_Form {
  
  function __construct( $name, $state, $mode = self::MODE_NONE ) {
    parent::__construct( $name, $state, $mode );
  }

  function buildQuickForm() {
    $this->add('text', 'first_name', 'First Name', CRM_Form::ATTR_TEXT, true);
    $this->add('text', 'last_name' , 'Last Name' , CRM_Form::ATTR_TEXT, true);

    $this->add('text', 'address_line_1', 'Address Line 1', CRM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'address_line_2', 'Address Line 2',  CRM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'city'   , 'City'   , CRM_Form::ATTR_TEXT);
    $this->add('text', 'state'  , 'State'  , CRM_Form::ATTR_TEXT);
    $this->add('text', 'zipcode', 'Zipcode', CRM_Form::ATTR_TEXT);

    $this->add('text', 'email', 'Email', CRM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'telephone_no_home'    , 'Telephone No - Home'    , CRM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'telephone_no_work'    , 'Telephone No - Work'    , CRM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'telephone_no_cellular', 'Telephone No - Cellular', CRM_Form::ATTR_TEXT_LARGE);
    
    $this->addDefaultButtons( array( 1 => array( 'next'   , 'Save'    , true  ),
                                     2 => array( 'reset'  , 'Reset'   , false ),
                                     3 => array( 'cancel' , 'Cancel'  , false ) ) );

    if ( $this->_mode == self::MODE_VIEW || self::MODE_UPDATE ) {
      $this->setDefaults();
    }

  }

  function setDefaults() {
    $defaults = array();

    $defaults['first_name'] = 'Dave';
    $defaults['last_name' ] = 'Greenberg';
    $defaults['email'     ] = 'dgg@blackhole.net';
    $defaults['telephone_no_home'] = '1-800-555-1212';

    $this->setDefaults( $defaults );
  }

  function addRules( ) {
    $this->addRule( 'email', t(' should be a valid well formed email address.'), 'email' );
    $this->addRule( 'telephone_no_home', t( ' should be a valid phone number.'), 'phoneNumber' );
  }

  function postProcess() {
    $content = '<pre>' . print_r($_POST, TRUE) . '</pre>';
    CRM_Utils::debug( 'Content', $content );
  }

}

?>
