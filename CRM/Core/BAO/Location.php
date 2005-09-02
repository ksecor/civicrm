<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/Location.php';

require_once 'CRM/Core/BAO/Block.php';

require_once 'CRM/Contact/Form/Location.php';

/**
 * BAO object for crm_location table
 */
class CRM_Core_BAO_Location extends CRM_Core_DAO_Location {
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
     * @return object CRM_Core_BAO_Location object
     * @access public
     * @static
     */
    static function add( &$params, &$ids, $locationId ) {
        if ( ! self::dataExists( $params, $locationId, $ids ) ) {
            return null;
        }
        
        $location =& new CRM_Core_BAO_Location( );
        
        if (! isset($params['contact_id'])) {
            $location->entity_table = CRM_Core_BAO_Domain::getTableName();
            $location->entity_id    = $params['domain_id'];
        } else {
            $location->entity_table = CRM_Contact_BAO_Contact::getTableName();
            $location->entity_id    = $params['contact_id'];
        }
        $location->is_primary       = CRM_Utils_Array::value( 'is_primary', $params['location'][$locationId], false );
        $location->location_type_id = CRM_Utils_Array::value( 'location_type_id', $params['location'][$locationId] );

        $location->id = CRM_Utils_Array::value( 'id', $ids['location'][$locationId] );
        $location->save( );

        $params['location'][$locationId]['id'] = $location->id;

        CRM_Core_BAO_Address::add( $params, $ids, $locationId );

        // set this to true if this has been made the primary IM.
        // the rule is the first entered value is the primary object
        $isPrimaryPhone = $isPrimaryEmail = $isPrimaryIM = true;

        $location->phone = array( );
        $location->email = array( );
        $location->im    = array( );

        for ( $i = 1; $i <= CRM_Contact_Form_Location::BLOCKS; $i++ ) {
            $location->phone[$i] = CRM_Core_BAO_Phone::add( $params, $ids, $locationId, $i, $isPrimaryPhone );
            $location->email[$i] = CRM_Core_BAO_Email::add( $params, $ids, $locationId, $i, $isPrimaryEmail );
            $location->im   [$i] = CRM_Core_BAO_IM::add   ( $params, $ids, $locationId, $i, $isPrimaryIM    );
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
    static function dataExists( &$params, $locationId, &$ids ) {
        if ( CRM_Utils_Array::value( 'id', $ids['location'][$locationId] ) ) {
            return true;
        }

        // return if no data present
        if ( ! array_key_exists( 'location' , $params ) ||
             ! array_key_exists( $locationId, $params['location'] ) ) {
            return false;
        }

        if ( CRM_Core_BAO_Address::dataExists( $params, $locationId, $ids ) ) {
            return true;
        }

        for ( $i = 1; $i <= CRM_Contact_Form_Location::BLOCKS; $i++ ) {
            if ( CRM_Core_BAO_Phone::dataExists( $params, $locationId, $i, $ids ) ||
                 CRM_Core_BAO_Email::dataExists( $params, $locationId, $i, $ids ) ||
                 CRM_Core_BAO_IM::dataExists   ( $params, $locationId, $i, $ids ) ) {
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
    static function &getValues( &$params, &$values, &$ids, $locationCount = 0 ) {
        $location =& new CRM_Core_BAO_Location( );
        $location->copyValues( $params );
        if ( $params['contact_id'] ) {
            $location->entity_table = 'civicrm_contact';
            $location->entity_id    = $params['contact_id'];
        } else if ( $params['domain_id'] ) {
            $location->entity_table = 'civicrm_domain';
            $location->entity_id    = $params['domain_id'];
        }

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
                    CRM_Core_DAO::storeValues( $location, $values );
                    self::getBlocks( $params, $values, $ids, 0, $location );
                } else {
                    $values['location'][$i+1] = array();
                    $ids['location'][$i+1]    = array();
                    $ids['location'][$i+1]['id'] = $location->id;
                    CRM_Core_DAO::storeValues( $location, $values['location'][$i+1] );
                    self::getBlocks( $params, $values['location'][$i+1], $ids['location'][$i+1],
                                     CRM_Contact_Form_Location::BLOCKS, $location );
                }
                $locations[$i + 1] = $location;
            }
        }
        return $locations;
    }

    /**
     * simple helper function to dispatch getCall to lower comm blocks
     */
    static function getBlocks( &$params, &$values, &$ids, $blockCount = 0, &$parent ) {
        $parent->address =& CRM_Core_BAO_Address::getValues( $params, $values, $ids, $blockCount );

        $parent->phone   =& CRM_Core_BAO_Phone::getValues( $params, $values, $ids, $blockCount );
        $parent->email   =& CRM_Core_BAO_Email::getValues( $params, $values, $ids, $blockCount );
        $parent->im      =& CRM_Core_BAO_IM::getValues   ( $params, $values, $ids, $blockCount );
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
    static function deleteContact( $contactId ) {
        $location =& new CRM_Core_DAO_Location( );
        $location->entity_id = $contactId;
        $location->entity_table = CRM_Contact_DAO_Contact::getTableName();
        $location->find( );
        while ( $location->fetch( ) ) {
            self::deleteLocationBlocks( $location->id );
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
    static function deleteLocationBlocks( $locationId ) {
        static $blocks = array( 'Address', 'Phone', 'IM' );
        foreach ($blocks as $name) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $name) . ".php");
            eval( '$object =& new CRM_Core_DAO_' . $name . '( );' );
            $object->location_id = $locationId;
            $object->delete( );
        }
        
        CRM_Core_BAO_Email::deleteLocation($locationId);
    }
}

?>
