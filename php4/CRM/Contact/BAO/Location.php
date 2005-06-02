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

$GLOBALS['_CRM_CONTACT_BAO_LOCATION']['blocks'] =  array( 'Address', 'Phone', 'Email', 'IM' );

require_once 'CRM/Contact/DAO/Location.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Contact/BAO/Address.php';
require_once 'CRM/Contact/BAO/Phone.php';
require_once 'CRM/Contact/BAO/Email.php';
require_once 'CRM/Contact/BAO/IM.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Contact/DAO/Location.php';

require_once 'CRM/Contact/BAO/Block.php';

/**
 * BAO object for crm_location table
 */
class CRM_Contact_BAO_Location extends CRM_Contact_DAO_Location {
    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $ids            the array that holds all the db ids
     * @param array  $locationId     
     *
     * @return object CRM_Contact_BAO_Location object
     * @access public
     * @static
     */
     function add( &$params, &$ids, $locationId ) {
        if ( ! CRM_Contact_BAO_Location::dataExists( $params, $locationId, $ids ) ) {
            return null;
        }
        
        $location = new CRM_Contact_BAO_Location( );
        
        $location->contact_id       = $params['contact_id'];
        $location->is_primary       = CRM_Utils_Array::value( 'is_primary', $params['location'][$locationId] );
        $location->location_type_id = CRM_Utils_Array::value( 'location_type_id', $params['location'][$locationId] );

        $location->id = CRM_Utils_Array::value( 'id', $ids['location'][$locationId] );
        $location->save( );

        $params['location'][$locationId]['id'] = $location->id;

        CRM_Contact_BAO_Address::add( $params, $ids, $locationId );

        // set this to true if this has been made the primary IM.
        // the rule is the first entered value is the primary object
        $isPrimaryPhone = $isPrimaryEmail = $isPrimaryIM = true;

        $location->phone = array( );
        $location->email = array( );
        $location->im    = array( );

        for ( $i = 1; $i <= CRM_CONTACT_FORM_LOCATION_BLOCKS; $i++ ) {
            $location->phone[$i] = CRM_Contact_BAO_Phone::add( $params, $ids, $locationId, $i, $isPrimaryPhone );
            $location->email[$i] = CRM_Contact_BAO_Email::add( $params, $ids, $locationId, $i, $isPrimaryEmail );
            $location->im   [$i] = CRM_Contact_BAO_IM::add   ( $params, $ids, $locationId, $i, $isPrimaryIM    );
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
     function dataExists( &$params, $locationId, &$ids ) {
        if ( CRM_Utils_Array::value( 'id', $ids['location'][$locationId] ) ) {
            return true;
        }

        // return if no data present
        if ( ! array_key_exists( 'location' , $params ) ||
             ! array_key_exists( $locationId, $params['location'] ) ) {
            return false;
        }

        if ( CRM_Contact_BAO_Address::dataExists( $params, $locationId, $ids ) ) {
            return true;
        }

        for ( $i = 1; $i <= CRM_CONTACT_FORM_LOCATION_BLOCKS; $i++ ) {
            if ( CRM_Contact_BAO_Phone::dataExists( $params, $locationId, $i, $ids ) ||
                 CRM_Contact_BAO_Email::dataExists( $params, $locationId, $i, $ids ) ||
                 CRM_Contact_BAO_IM::dataExists   ( $params, $locationId, $i, $ids ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     * @param int   $locationCount number of locations to fetch
     *
     * @return void
     * @access public
     * @static
     */
     function &getValues( &$params, &$values, &$ids, $locationCount = 0 ) {
        $location = new CRM_Contact_BAO_Location( );
        $location->copyValues( $params );

        $flatten = false;
        if ( empty($locationCount) ) {
            $locationCount = 1;
            $flatten       = true;
        } else {
            $values['location'] = array();
            $ids['location']    = array();
        }

        // we first get the primary location due to the order by clause
        $location->orderBy( 'is_primary desc' );
        $location->find( );
        $locations = array( );
        for ($i = 0; $i < $locationCount; $i++) {
            if ($location->fetch()) {
                $params['location_id'] = $location->id;
                if ($flatten) {
                    $ids['location'] = $location->id;
                    $location->storeValues( $values );
                    CRM_Contact_BAO_Location::getBlocks( $params, $values, $ids, 0, $location );
                } else {
                    $values['location'][$i+1] = array();
                    $ids['location'][$i+1]    = array();
                    $ids['location'][$i+1]['id'] = $location->id;
                    $location->storeValues( $values['location'][$i+1] );
                    CRM_Contact_BAO_Location::getBlocks( $params, $values['location'][$i+1], $ids['location'][$i+1],
                                     CRM_CONTACT_FORM_LOCATION_BLOCKS, $location );
                }
                $locations[$i + 1] = $location;
            }
        }
        return $locations;
    }

    /**
     * simple helper function to dispatch getCall to lower comm blocks
     */
     function getBlocks( &$params, &$values, &$ids, $blockCount = 0, $parent ) {
        $parent->address = CRM_Contact_BAO_Address::getValues( $params, $values, $ids, $blockCount );

        $parent->phone   = CRM_Contact_BAO_Phone::getValues( $params, $values, $ids, $blockCount );
        $parent->email   = CRM_Contact_BAO_Email::getValues( $params, $values, $ids, $blockCount );
        $parent->im      = CRM_Contact_BAO_IM::getValues   ( $params, $values, $ids, $blockCount );
    }

    /**
     * Delete the object records that are associated with this contact
     *
     * @param  int  $contactId id of the contact to delete
     *
     * @return void
     * @access public
     * @static
     */
     function deleteContact( $contactId ) {
        $location = new CRM_Contact_DAO_Location( );
        $location->contact_id = $contactId;
        $location->find( );
        while ( $location->fetch( ) ) {
            CRM_Contact_BAO_Location::deleteLocationBlocks( $location->id );
            $location->delete( );
        }

    }

    /**
     * Delete the object records that are associated with this location
     *
     * @param  int  $locationId id of the location to delete
     *
     * @return void
     * @access public
     * @static
     */
     function deleteLocationBlocks( $locationId ) {
        
        foreach ($GLOBALS['_CRM_CONTACT_BAO_LOCATION']['blocks'] as $name) {
            if (CRM_Utils_System::isPHP4()) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $name) . ".php");
            }
            eval( '$object = new CRM_Contact_DAO_' . $name . '( );' );
            $object->location_id = $locationId;
            $object->delete( );
        }
    }
}

?>