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

require_once 'CRM/Contact/DAO/Location.php';

class CRM_Contact_BAO_Location extends CRM_Contact_DAO_Location {
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
     * @return object CRM_Contact_BAO_Location object
     * @access public
     * @static
     */
    static function add( &$params, $locationId ) {
        $dataExists = self::dataExists( $params, $locationId );
        if ( ! $dataExists ) {
            return null;
        }
        
        $location = new CRM_Contact_BAO_Location( );
        
        $location->contact_id       = $params['contact_id'];
        $location->is_primary       = CRM_Array::value( 'is_primary', $params['location'][$locationId] );
        $location->location_type_id = CRM_Array::value( 'location_type_id', $params['location'][$locationId] );

        $location->id = CRM_Array::value( 'location_id', $params['location'][$locationId] );
        $location->save( );

        $params['location'][$locationId]['id'] = $location->id;

        CRM_Contact_BAO_Address::add( $params, $locationId );
        
        for ( $i = 1; $i <= 3; $i++ ) {
            CRM_Contact_BAO_Phone::add( $params, $locationId, $i );
            CRM_Contact_BAO_Email::add( $params, $locationId, $i );
            CRM_Contact_BAO_IM::add   ( $params, $locationId, $i );
        }
        return $location;
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
        if ( ! array_key_exists( 'location' , $params ) ||
             ! array_key_exists( $locationId, $params['location'] ) ) {
            return false;
        }

        if ( CRM_Contact_BAO_Address::dataExists( $params, $locationId ) ) {
            return true;
        }

        for ( $i = 1; $i <= 3; $i++ ) {
            if ( CRM_Contact_BAO_Phone::dataExists( $params, $locationId, $i ) ||
                 CRM_Contact_BAO_Email::dataExists( $params, $locationId, $i ) ||
                 CRM_Contact_BAO_IM::dataExists   ( $params, $locationId, $i ) ) {
                return true;
            }
        }
        return false;
    }
}

?>