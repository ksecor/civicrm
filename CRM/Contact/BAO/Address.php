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

require_once 'CRM/Contact/DAO/Address.php';

class CRM_Contact_BAO_Address extends CRM_Contact_DAO_Address {
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
     * @param array  $locationId     
     *
     * @return object CRM_Contact_BAO_Address object
     * @access public
     * @static
     */
    static function add( &$params, $locationId ) {
        if ( ! self::dataExists( $params, $locationId ) ) {
            return null;
        }

        $address = new CRM_Contact_BAO_Address( );
        
        $address->location_id       = $params['location'][$locationId]['id'];
        $address->copyValues( $params['location'][$locationId]['address'] );
 
        // hack for now, should fix db soon
        $address->geo_coord_id = 1;
        $address->county_id    = 1;

        return $address->save( );
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $locationId     
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params, $locationId ) {
        // return if no data present
        if ( ! array_key_exists( 'address' , $params['location'][$locationId] ) ) {
            return false;
        }

        foreach ( $params['location'][$locationId]['address'] as $name => $value ) {
            if ( ! empty( $value ) ) {
                return true;
            }
        }

        return false;
    }
}

?>