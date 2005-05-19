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

require_once 'CRM/Contact/DAO/Email.php';

/**
 * BAO object for crm_email table
 */
class CRM_Contact_BAO_Email extends CRM_Contact_DAO_Email {
    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $ids            the array that holds all the db ids
     * @param int    $locationId
     * @param int    $emailId
     * @param bool   $isPrimary      Has any previous entry been marked as isPrimary?
     *
     * @return object CRM_Contact_BAO_Email object
     * @access public
     * @static
     */
    static function add( &$params, &$ids, $locationId, $emailId, &$isPrimary ) {
        // if no data and we are not updating an exisiting record
        if ( ! self::dataExists( $params, $locationId, $emailId, $ids ) ) {
            return null;
        }

        $email = new CRM_Contact_DAO_Email();
        $email->location_id = $params['location'][$locationId]['id'];
        $email->email       = $params['location'][$locationId]['email'][$emailId]['email'];

        // set this object to be the value of isPrimary and make sure no one else can be isPrimary
        $email->is_primary  = $isPrimary;
        $isPrimary          = false;

        $email->id = CRM_Utils_Array::value( $emailId, $ids['location'][$locationId]['email'] );
        if ( empty( $email->email ) ) {
            $email->delete( );
            return null;
        } else {
            return $email->save( );
        }
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $emailId
     * @param array  $ids            the array that holds all the db ids
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params, $locationId, $emailId, &$ids) {
        if ( CRM_Utils_Array::value( $emailId, $ids['location'][$locationId]['email'] )) {
            return true;
        }

        return CRM_Contact_BAO_Block::dataExists('email', array( 'email' ), $params, $locationId, $emailId );
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     * @param int   $blockCount    number of blocks to fetch
     *
     * @return void
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids, $blockCount = 0 ) {
        $email = new CRM_Contact_BAO_Email( );
        return CRM_Contact_BAO_Block::getValues( $email, 'email', $params, $values, $ids, $blockCount );
    }
}

?>