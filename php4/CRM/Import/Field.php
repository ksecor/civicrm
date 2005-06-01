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



require_once 'CRM/Utils/Rule.php';
require_once 'CRM/Utils/Type.php';

class CRM_Import_Field {
  
    /**#@+
     * @access protected
     * @var string
     */

    /**
     * name of the field
     */
    var $_name;

    /**
     * title of the field to be used in display
     */
    var $_title;

    /**
     * type of field
     * @var enum
     */
    var $_type;

    /**
     * is this field required
     * @var boolean
     */
    var $_required;

    /**
     * data to be carried for use by a derived class
     * @var object
     */
    var $_payload;

    /**
     * value of this field
     * @var object
     */
    var $_value;

    function CRM_Import_Field( $name, $title, $type = CRM_UTILS_TYPE_T_INT, $required = false, $payload = null, $active = false ) {
        $this->_name      = $name;
        $this->_title     = $title;
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
        //  echo $this->_value."===========<br>";
        $message = '';

        if ( $this->_value === null ) {
            return true;
        }
        /*        
        if ( $this->_name == 'phone' ) {
           return CRM_Utils_Rule::phone( $this->_value );
        }
        
        if ( $this->_name == 'email' ) {
            return CRM_Utils_Rule::email( $this->_value );
        }
        */

        if ( $this->_name == 'phone' ) {
            if( CRM_Utils_Rule::phone( $this->_value ) ) {
                if ( $this->_name == 'email' ) {
                    if( CRM_Utils_Rule::email( $this->_value )) {
                        return true;
                    }
                }
            } 
        } else if ( $this->_name == 'email' ) {
            if(CRM_Utils_Rule::email( $this->_value )) {
                return true;
            } 
        }

        return true;
    }

}

?>