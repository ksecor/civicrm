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

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */



require_once 'CRM/Contact/DAO/Contact.php';

require_once 'CRM/Contact/DAO/Individual.php';

require_once 'CRM/Contact/DAO/Location.php';

require_once 'CRM/Contact/DAO/Address.php';

require_once 'CRM/Contact/DAO/Phone.php';

require_once 'CRM/Contact/DAO/IM.php';

require_once 'CRM/Contact/DAO/Email.php';

class CRM_Contact_BAO_Individual extends CRM_Contact_DAO_Individual 
{
    
    protected $_contactDAO;
    
    /**
     * This is a contructor of the class.
     */
    function __construct() 
    {
        parent::__construct();
        
        $this->_contactDAO = new CRM_Contact_DAO_Contact();
    }
    
    /**
     * This function sets the values in the form.
     */
    function setContactValues() 
    {
        $fields  =& $this->_contactDAO->fields();
        foreach ($fields as $fieldName => $dontCare) {
            $this->_contactDAO->$fieldName = isset($this->$fieldName) ? $this->$fieldName : null;
        }
    }
    
    function fillContactValues() 
    {
        $fields  =& $this->_contactDAO->fields();
        $tableName = $this->_contactDAO->tableName();
        
        foreach ($fields as $fieldName => $dontCare) {
            $selectFieldName = $tableName . '_' . $fieldName;
            $this->_contactDAO->$fieldName = isset($this->$selectFieldName) ? $this->$selectFieldName : null;
        }
    }
    
    function getContactValues() 
    {
        $fields  =& $this->_contactDAO->fields();
        
        foreach ($fields as $fieldName => $dontCare) {
            $this->$fieldName = $this->_contactDAO->$fieldName;
        }
    }
    
    function insertContact() 
    {
        $this->setContactValues();
        
        $this->_contactDAO->insert();
        
        /* above insertion triggers setting the contact_id */
        $this->contact_id = $this->_contactDAO->id;
    }

    function count($countWhat = false,$whereAddOnly = false) {
        $this->setContactValues();
        $this->joinAdd( $this->_contactDAO );
        return parent::count($countWhat, $whereAddOnly);
    }

    function find($get = false) 
    {
        $this->setContactValues();
        $this->joinAdd( );
        $this->whereAdd( );
        $this->selectAdd();
        parent::find($get);
        
        if ($get) {
            $this->fillContactValues();
        }
        
    }
    
    function fetch() 
    {
        $result = parent::fetch();
        if ($result) {
            $this->fillContactValues();
        }
        return $result;
    }

    static function &getAttributes( ) {
        static $attrs;
        if ( ! isset( $attrs ) ) {
            $fields =& self::fields( );
            foreach ( $fields as $name => $field ) {
                if ( $field['type'] == CRM_Type::T_STRING ) {
                    $maxLength = CRM_Array::value( 'maxlength', $field );
                    $size      = CRM_Array::value( 'size'     , $field );
                    if ( $maxLength || $size ) {
                        $attrs[$name] = array( );
                        if ( $maxLength ) {
                            $attrs[$name]['maxlength'] = $maxLength;
                        }
                        if ( $size ) {
                            $attrs[$name]['size'] = $size;
                        }
                    }
                }
            }
        }
        return $attrs;
    }

    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contact_BAO_Individual object
     * @access public
     * @static
     */
    static function add( &$params ) {
        $individual = new CRM_Contact_BAO_Individual( );

        $individual->copyValues( $params );

        // fix gender and date
        $individual->gender = $params['gender']['gender'];

        $date = CRM_Array::value( 'birth_date', $params );
        if ( $date ) {
            $date['M'] = ( $date['M'] < 10 ) ? '0' . $date['M'] : $date['M'];
            $date['d'] = ( $date['d'] < 10 ) ? '0' . $date['d'] : $date['d'];
            $individual->birth_date = $date['Y'] . $date['M'] . $date['d'];
        }

        $id = CRM_Array::value( 'individual_id', $params );
        if ( $id ) {
            $individual->id = $id;
        }
        return $individual->save( );
    }
    
}

?>
