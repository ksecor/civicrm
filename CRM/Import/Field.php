<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/


require_once 'CRM/Type.php';

class CRM_Import_Fields {
  
  /**#@+
   * @access protected
   * @var string
   */

  /**
   * display name of the field
   */
  protected $_name;

  /**
   * name of the variable that matches the above
   * typically this should be a Table.FieldName except
   * for dynamic fields, where it will potentially be more complex
   */
  protected $_fieldName;

  /**
   * type of field
   * @var enum
   */
  protected $_type;

  /**
   * is this field required
   * @var boolean
   */
  protected $_required;

  /**
   * data to be carried for use by a derived class
   * @var object
   */
  protected $_payload;

  /**
   * value of this field
   * @var object
   */
  protected $_value;

  function __construct( $name, $fieldName, $type = CRM_Type::INTEGER, $required = false, $payload = null, $active = false ) {
    $this->_name      = $name;
    $this->_fieldName = $fieldName;
    $this->_type      = $type;
    $this->_required  = $required;
    $this->_payload   = $payload;

    $this->_value     = null;
  }

  function resetValue( ) {
    $this->_value     = null;
  }

  /**
   * the value is in string format. convert the value to the type of this field
   * and set the field value with the appropriate type
   */
  function setValue( $value ) {
    $this->_value = $value;
  }

  function validate( ) {
    if ( $this->_value === null ) {
      return true;
    }

    if ( $this->_value === null ) {
      return false;
    }

    return true;
  }

}

?>