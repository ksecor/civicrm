<?php

require_once 'WGM/Form.php';

class WGM_Contacts_Form_Edit extends WGM_Form {
  
  function __construct( $name, $state ) {
    parent::__construct( $name, $state );
  }

  function buildQuickForm() {
    $this->add('text', 'first_name', 'First Name', WGM_Form::ATTR_TEXT, true);
    $this->add('text', 'last_name' , 'Last Name' , WGM_Form::ATTR_TEXT, true);

    $this->add('text', 'address_line_1', 'Address Line 1', WGM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'address_line_2', 'Address Line 2',  WGM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'city'   , 'City'   , WGM_Form::ATTR_TEXT);
    $this->add('text', 'state'  , 'State'  , WGM_Form::ATTR_TEXT);
    $this->add('text', 'zipcode', 'Zipcode', WGM_Form::ATTR_TEXT);

    $this->add('text', 'email', 'Email', WGM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'telephone_no_home'    , 'Telephone No - Home'    , WGM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'telephone_no_work'    , 'Telephone No - Work'    , WGM_Form::ATTR_TEXT_LARGE);
    $this->add('text', 'telephone_no_cellular', 'Telephone No - Cellular', WGM_Form::ATTR_TEXT_LARGE);
    
    $this->addDefaultButtons( array( 1 => array( 'next'   , 'Continue', true  ),
                                     2 => array( 'reset'  , 'Reset'   , false ),
                                     3 => array( 'cancel' , 'Cancel'  , false ) ) );

  }

  function addRules( ) {
    $this->addRule( 'email', t(' should be a valid well formed email address.'), 'email' );
    $this->addRule( 'telephone_no_home', t( ' should be a valid phone number.'), 'phoneNumber' );
  }

  function postProcess() {
    $content = '<pre>' . print_r($_POST, TRUE) . '</pre>';
    WGM_Utils::debug( 'Content', $content );
  }

}

?>
