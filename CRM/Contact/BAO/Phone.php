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

require_once 'CRM/Contact/DAO/Phone.php';

class CRM_Contact_BAO_Phone extends CRM_Contact_DAO_Phone {
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $phoneId
     *
     * @return object CRM_Contact_BAO_Phone object
     * @access public
     * @static
     */
    static function add( &$params, $locationId, $phoneId ) {
        if ( ! self::dataExists( $params, $locationId, $phoneId ) ) {
            return null;
        }

        $phone = new CRM_Contact_DAO_Phone();
        $phone->location_id        = $params['location'][$locationId]['id'];
        $phone->phone              = $params['location'][$locationId]['phone'][$phoneId]['phone'];
        $phone->phone_type         = $params['location'][$locationId]['phone'][$phoneId]['phone_type'];
        $phone->is_primary         = CRM_Array::value( 'is_primary', $params['location'][$locationId]['phone'][$phoneId], false );
        $phone->mobile_provider_id = CRM_Array::value( 'mobile_provider_id', $params['location'][$locationId]['phone'][$phoneId] );
        return $phone->save( );
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $phoneId
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params, $locationId, $phoneId ) {
        // return if no data present
        if ( ! array_key_exists( 'phone' , $params['location'][$locationId] ) ||
             ! array_key_exists( $phoneId, $params['location'][$locationId]['phone'] ) ) {
            return false;
        }

        if ( ! empty( $params['location'][$locationId]['phone'][$phoneId]['phone'] ) ) {
            return true;
        }

        return false;
    }

}

?>