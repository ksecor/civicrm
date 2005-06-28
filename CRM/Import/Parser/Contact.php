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

    protected $_mapperKeys;
    
    protected $_emailIndex;
    protected $_firstNameIndex;
    protected $_lastNameIndex;

    protected $_allEmails;

    protected $_phoneIndex;

    /**
     * class constructor
     */
    function __construct( &$mapperKeys ) {
        parent::__construct();
        $this->_mapperKeys =& $mapperKeys;
    }

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function init( ) {
        $fields =& CRM_Contact_BAO_Contact::importableFields( );
        foreach ($fields as $name => $field) {
            $this->addField( $name, $field['title'], $field['type'], $field['headerPattern'], $field['dataPattern'] );
        }

        $this->setActiveFields( $this->_mapperKeys );
        
        $this->_phoneIndex = -1;
        $this->_emailIndex = -1;
        $this->_firstNameIndex = -1;
        $this->_lastNameIndex = -1;

        $index             = 0 ;
        foreach ( $this->_mapperKeys as $key ) {
            if ( $key == 'email' ) {
                $this->_emailIndex = $index;
                $this->_allEmails  = array( );
            }
            if ( $key == 'phone' ) {
                $this->_phoneIndex = $index;
            }
            if ( $key == 'first_name' ) {
                $this->_firstNameIndex = $index;
            }
            if ( $key == 'last_name' ) { 
                $this->_lastNameIndex = $index;
            }
            $index++;
        }
    }

    /**
     * handle the values in mapField mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean
     * @access public
     */
    function mapField( &$values ) {
        return self::VALID;
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
//         return self::VALID;
        return $this->summary($values);
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
//         if ( $response != self::VALID ) {
//             return $response;
//         }
        if ( $this->_firstNameIndex < 0 || $this->_lastNameIndex < 0) {
            $noFirstLast = true;
        } else {
            $noFirstLast = ! CRM_Utils_Array::value($this->_firstNameIndex, $values) &&
                        ! CRM_Utils_Array::value($this->_lastNameIndex, $values);
        }
        if ( $this->_emailIndex >= 0 ) {
            /* If we don't have the required fields, bail */
            if ($noFirstLast && ! CRM_Utils_Array::value('email', $values)) {
                array_unshift($values, ts('Missing required fields'));
                return self::ERROR;
            }
            $email = CRM_Utils_Array::value( $this->_emailIndex, $values );
            if ( $email ) {
                /* If the email address isn't valid, bail */
                if (! CRM_Utils_Rule::email($email)) {
                    array_unshift($values, ts('Invalid Email address'));
                    return self::ERROR;
                }
                /* If it's a dupe, bail */
                if ( $dupe = CRM_Utils_Array::value( $email, $this->_allEmails ) ) {
                    array_unshift($values, ts('Email address conflicts with record %1', array(1 => $dupe)));
                    return self::CONFLICT;
                }

                /* otherwise, count it and move on */
                $this->_allEmails[$email] = $this->_lineCount;
            }
        } else if ($noFirstLast) {
            array_unshift($values, ts('Missing required fields'));
            return self::ERROR;
        }

//  Block removed due to bug CRM-150, internationalization/wew.
//
//         if ( $this->_phone_index >= 0) { 
//             $phone = CRM_Utils_Array::value( $this->_phoneIndex, $values );
//             if ($phone) {
//                 if (! CRM_Utils_Rule::phone($phone)) {
//                     $values[] = ts('Invalid phone number');
//                     return self::ERROR;
//                 }
//             }
//         }

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
        if ( is_a($newContact = crm_create_contact( $params, 'Individual' ), CRM_Core_Error) ) {
            $urls = array( );
            $base = CRM_Utils_System::baseURL() . '/';
            
            foreach ($newContact->_errors[0]['params'] as $id) {
                $urls[] = $base . CRM_Utils_System::url('civicrm/contact/view',
                'reset=1&cid=' . $id, false);
            }
            
            $url_string = implode("\n", $urls);
            array_unshift($values, $url_string); 
            
            return self::DUPLICATE;
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

}

?>
