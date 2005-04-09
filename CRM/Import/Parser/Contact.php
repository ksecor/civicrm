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
    static $_importableFields;

    protected $_mapperKeys;
    
    protected $_emailIndex;

    protected $_allEmails;
    /**
     * class constructor
     */
    function __construct( &$mapperKeys ) {
        $this->_mapperKeys =& $mapperKeys;
    }

    function init( ) {
        $fields =& self::importableFields( );
        foreach ( $fields as $name => &$field ) {
            $this->addField( $name, $field['title'], $field['type'] );
        }
        $this->setActiveFields( $this->_mapperKeys );

        $this->_emailIndex = -1;
        $index             = 0 ;
        foreach ( $this->_mapperKeys as $key ) {
            if ( $key == 'email' ) {
                $this->_emailIndex = $index;
                $this->_allEmails  = array( );
                break;
            }
            $index++;
        }
    }

    function process( &$values, $mode ) {
        if ( $mode == self::MODE_PREVIEW ) {
            return self::VALID;
        }

        $response = $this->setActiveFieldValues( $values );
        if ( $response != self::VALID ) {
            return $response;
        }

        if ( $this->_emailIndex >= 0 ) {
            $email = CRM_Array::value( $values, $this->_emailIndex );
            if ( $email ) {
                if ( CRM_Array::value( $email, $this->_allEmails ) ) {
                    return self::DUPLICATE;
                }
                $this->_allEmails[$email] = 1;
            }
        }

        return self::VALID;
    }

    function fini( ) {
    }

    function &importableFields( ) {
        if ( ! isset( self::$_importableFields ) ) {
            self::$_importableFields = array( );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Individual::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Location::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Address::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Phone::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Email::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_IM::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Contact::import( ) );
        }
        return self::$_importableFields;
    }
}

?>