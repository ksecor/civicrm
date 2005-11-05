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
 * Create an additional location for an existing contact
 *
 * @param CRM_Contact $contact     Contact object to be deleted
 * @param array       $params      input properties
 * @param enum        context_name Name of a valid Context
 *  
 * @return CRM_Location or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */
function crm_create_location(&$contact, $params) {
    _crm_initialize( );
      
    $locationTypeDAO = & new CRM_Core_DAO_LocationType();
    $locationTypeDAO->name = $params['location_type'];
    $locationTypeDAO->find(true);
    $locationTypeId = $locationTypeDAO->id;
    if(! isset($locationTypeId) ) {
        return _crm_error('$location_type is not valid one');
    }
    
    $values = array(
                    'contact_id'    => $contact->id,
                    'location'      => array(1 => array()),
                    );
    
    $loc =& $values['location'][1];
    
    $loc['address'] = array( );
    
    require_once 'CRM/Core/DAO/Address.php';
    $fields =& CRM_Core_DAO_Address::fields( );
    _crm_store_values($fields, $params, $loc['address']);
    $ids = array( 'county', 'country_id', 'state_province_id', 'supplemental_address_1', 'supplemental_address_2', 'StateProvince.name' );
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $loc['address'][$id] = $params[$id];
        }
    }
    
    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        $name = strtolower($block);
        $loc[$name]    = array( );
        if ( $params[$name] ){
            $count = 1;
            foreach ( $params[$name] as $val) {
                CRM_Core_DAO::storeValues($val,$loc[$name][$count++]);
            }   
        }
    }
    
    $loc['location_type_id'] = $locationTypeId;
    
    $ids = array();
    require_once 'CRM/Core/BAO/Location.php';
    $location = CRM_Core_BAO_Location::add($values, $ids,1);
    return $location;
}



/**
 *  Update a specified location with the provided property values.
 * 
 *  @param  object  $contact        A valid Contact object (passed by reference).
 *  @param  string  $location_type  Valid label for location to be updated. 
 *  @param  Array   $params         Associative array of property name/value pairs to be updated
 *  @param  Array   $return_properties Which properties should be included in the returned updated location object
 *
 *  @return Location object with updated property values
 * 
 *  @access public
 *
 */
function crm_update_location(&$contact, $location_type, $params) {
    _crm_initialize( );
    
    $locationTypeDAO = & new CRM_Core_DAO_LocationType();
    $locationTypeDAO->name = $location_type;
    $locationTypeDAO->find(true);
    $locationTypeId = $locationTypeDAO->id;
    if(! isset($locationTypeId) ) {
        return _crm_error('$location_type is not valid one');
    }
    
    if( ! isset( $contact->id ) ) {
        return _crm_error('$contact is not valid contact datatype');
    } 
    
    if( ! isset($location_type) ) {
        return _crm_error('$location_type is not set');
    }
    $values = array(
                    'contact_id'    => $contact->id,
                    'location'      => array(1 => array()),
                    );
    
    $loc =& $values['location'][1];
    
    $loc['address'] = array( );
    
    require_once 'CRM/Core/DAO/Address.php';
    $fields =& CRM_Core_DAO_Address::fields( );
    _crm_store_values($fields, $params, $loc['address']);
    $ids = array( 'county', 'country_id', 'state_province_id', 'supplemental_address_1', 'supplemental_address_2', 'StateProvince.name' );
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $loc['address'][$id] = $params[$id];
        }
    }
    
    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        $name = strtolower($block);
        $loc[$name]    = array( );
        if ( $params[$name] ){
            $count = 1;
            foreach ( $params[$name] as $val) {
                CRM_Core_DAO::storeValues($val,$loc[$name][$count++]);
            }   
        }
    }
    
    $loc['location_type_id'] = $locationTypeId;
    $par = array('id' => $contact->id,'contact_id' => $contact->id);
    $contact = CRM_Contact_BAO_Contact::retrieve( $par , $defaults , $ids );
    $location = CRM_Core_BAO_Location::add($values, $ids, 1);
    return $location;

}


/**
 * Deletes a contact location.
 * 
 * @param object $contact        A valid Contact object (passed by reference).
 * @param string $location_type   Valid context name for location to be deleted.
 *
 * @return  null, if successful. CRM error object, if 'contact' or 'location_type' is invalid, permissions are insufficient, etc.
 *
 * @access public
 *
 */
function crm_delete_location(&$contact,$location_type) {
    _crm_initialize( );
    
    if( ! isset( $contact->id ) ) {
        return _crm_error('$contact is not valid contact datatype');
    } 
    
    if( ! isset($location_type) ) {
        return _crm_error('$location_type is not set');
    }
    
    $locationTypeDAO = & new CRM_Core_DAO_LocationType();
    $locationTypeDAO->name = $location_type;
    $locationTypeDAO->find();
    $locationTypeDAO->fetch();
    $locationTypeId = $locationTypeDAO->id;
    if(! isset($locationTypeId) ) {
        return _crm_error('$location_type is not valid one');
    }
    
    $locationDAO = & new CRM_Core_DAO_Location();
    $locationDAO->entity_id        = $contact->id;
    $locationDAO->location_type_id = $locationTypeId;
    $locationDAO->find();
    $locationDAO->fetch();
    
    $locationId = $locationDAO->id;
    
    CRM_Core_BAO_Location::deleteLocationBlocks($locationId);
    return null;
}
/**
 * Returns array of location(s) for a contact
 * 
 * @param  object  $contact               A valid Contact object (passed by reference).
 * @param  Array   $location_type         Valid location_type label Array. If NULL, all locations are returned.
 *
 *
 * @return  An array of Location objects. 'location_id' and 'location_type' are always returned.
 *
 * @acces public
 *
 */

function crm_get_locations(&$contact, $location_types = null) {
    _crm_initialize( );
    
    if( ! isset( $contact->id ) ) {
        return _crm_error('$contact is not valid contact datatype');
    } 
    
    $params = array();
    $params['contact_id']   = $contact->id;
    $params['entity_id']    = $contact->id;
    $LocationTypeDAO = & new CRM_Core_DAO_LocationType();
    $locationCount = $LocationTypeDAO->count();
    $values = array();
    $locations = CRM_Core_BAO_Location::getValues($params,$values,$ids,$locationCount);
    
    if( is_array($location_types) && count($location_types)>0 ) {
        $newLocations = array();
        foreach($location_types as $locationName) {
            $LocationTypeDAO = & new CRM_Core_DAO_LocationType();
            $LocationTypeDAO->name = $locationName;
            $LocationTypeDAO->find();
            $LocationTypeDAO->fetch();
            foreach($locations as $location) {
                if($location->location_type_id == $LocationTypeDAO->id) {
                    $newLocations[] = $location;
                }
            }
        }
        return $newLocations;
    }

    return $locations;
}


?>