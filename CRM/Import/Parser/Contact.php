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

require_once 'api/crm.php';

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

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function init( ) {
        $fields =& self::importableFields( );
        // does not work for php4 - we shld revert back to this one after we stop developing for php4        
        //foreach ( $fields as $name => &$field ) {
        foreach ($fields as $name => $field) {
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

    /**
     * handle the values in preview mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function preview( &$values ) {
        return self::VALID;
    }

    /**
     * handle the values in summary mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function summary( &$values ) {
        $response = $this->setActiveFieldValues( $values );
        if ( $response != self::VALID ) {
            return $response;
        }

        if ( $this->_emailIndex >= 0 ) {
            $email = CRM_Utils_Array::value( $values, $this->_emailIndex );
            if ( $email ) {
                if ( CRM_Utils_Array::value( $email, $this->_allEmails ) ) {
                    return self::DUPLICATE;
                }
                $this->_allEmails[$email] = 1;
            }
        }

        return self::VALID;
    }

    /**
     * handle the values in import mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function import( &$values ) {
        // first make sure this is a valid line
        $response = $this->summary( $values );
        if ( $response != self::VALID ) {
            return $response;
        }

        $params =& $this->getActiveFieldParams( );
        
        $params['location_type_id'] = 1;

        //if ( crm_create_contact( $params, 'Individual' ) instanceof CRM_Core_Error ) {
        if ( is_a(crm_create_contact( $params, 'Individual' ), CRM_Core_Error) ) {
            return self::ERROR;
        }
        return self::VALID;
    }
    
    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function fini( ) {
    }

    /**
     * combine all the importable fields from the lower levels object
     *
     * The ordering is important, since currently we do not have a weight
     * scheme. Adding weight is super important and should be done in the
     * next week or so, before this can be called complete.
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( ) {
        if ( ! isset( self::$_importableFields ) ) {
            self::$_importableFields = array();
            
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   array('' => array( 'title' => ts('-do not import-'))) );
            
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Individual::import( ) );
            /*
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Location::import( ) );
            */
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Address::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Phone::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Email::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_IM::import( true ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Contact::import( ) );
        }
        return self::$_importableFields;
    }
}

?>
