<?php

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
    $this->value     = null;
  }

  /**
   * the value is in string format. convert the value to the type of this field
   * and set the field value with the appropriate type
   */
  function setValue( $value ) {
    $this->value = null;
    $this->value = CRM_Type::format( $value, $this->type );
  }

}

?>