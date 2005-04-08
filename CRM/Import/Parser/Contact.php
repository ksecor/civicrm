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

require_once 'CRM/Import/Parser.php';

/**
 * class to parse contact csv files
 */
class CRM_Import_Parser_Contact extends CRM_Import_Parser {

    function init( ) {
        $fields =& $this->fields( );
        foreach ( $fields as $name => &$field ) {
            $this->addField( $field['name'], $field['title'], $field['type'] );
        }
    }

    function process( &$fields ) {
    }

    function fini( ) {
    }

    function &fields( ) {
        if ( ! isset( $this->_fields ) ) {
            $this->_fields = array( );
            $this->_fields = array_merge($this->_fields,
                                         CRM_Contact_DAO_Contact::import( ) );
            $this->_fields = array_merge($this->_fields,
                                         CRM_Contact_DAO_Individual::import( ) );
            $this->_fields = array_merge($this->_fields,
                                         CRM_Contact_DAO_Location::import( ) );
            $this->_fields = array_merge($this->_fields,
                                         CRM_Contact_DAO_Phone::import( ) );
            $this->_fields = array_merge($this->_fields,
                                         CRM_Contact_DAO_Email::import( ) );
            $this->_fields = array_merge($this->_fields,
                                         CRM_Contact_DAO_IM::import( ) );
        }
        return $this->_fields;
    }
}

?>