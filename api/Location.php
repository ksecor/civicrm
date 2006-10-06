<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
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
    $locationTypeDAO->name      = $params['location_type'];
    $locationTypeDAO->domain_id = CRM_Core_Config::domainID( );
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
    
    $loc['location_type_id'] = $locationTypeId;
    $loc['is_primary'] = CRM_Utils_Array::value( 'is_primary', $params);
    $loc['name'] = CRM_Utils_Array::value( 'name', $params);

    require_once 'CRM/Core/DAO/Address.php';
    $fields =& CRM_Core_DAO_Address::fields( );
    $loc['address'] = array( );    
    _crm_store_values($fields, $params, $loc['address']);
    
    //$ids = array( 'county', 'country_id', 'state_province_id', 'supplemental_address_1', 'supplemental_address_2', 'StateProvince.name' );
    $ids = array( 'county', 'country_id', 'country', 'state_province_id', 'state_province', 'supplemental_address_1', 'supplemental_address_2', 'StateProvince.name' );
    
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $loc['address'][$id] = $params[$id];
        }
    }
     
    if (is_numeric($loc['address']['state_province'])) {
        $loc['address']['state_province'] = CRM_Core_PseudoConstant::stateProvinceAbbreviation($loc['address']['state_province']);
    }
    
    if (is_numeric($loc['address']['country'])) {
        $loc['address']['country']        = CRM_Core_PseudoConstant::countryIsoCode($loc['address']['country']);
    }
    
    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $block) . ".php");
        eval( '$fields =& CRM_Core_DAO_' . $block . '::fields( );' );
        $name = strtolower($block);
        $loc[$name]    = array( );
        if ( $params[$name] ){
            $count = 1;
            foreach ( $params[$name] as $val) {
                _crm_store_values($fields, $val, $loc[$name][$count++]);
            }
        }
    }
    /*
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
    */
    $ids = array();
    require_once 'CRM/Core/BAO/Location.php';
    
    CRM_Contact_BAO_Contact::resolveDefaults($values, true);
    
    $location = CRM_Core_BAO_Location::add($values, $ids,1);
    return $location;
}



/**
 *  Update a specified location with the provided property values.
 * 
 *  @param  object  $contact        A valid Contact object (passed by reference).
 *  @param  string  $location_id    Valid (db-level) id for location to be updated. 
 *  @param  Array   $params         Associative array of property name/value pairs to be updated
 *
 *  @return Location object with updated property values
 * 
 *  @access public
 *
 */
function crm_update_location(&$contact, $location_id, $params) {
    _crm_initialize( );
    
    if( ! isset( $contact->id ) ) {
        return _crm_error('$contact is not valid contact datatype');
    } 

    $locationId = (int) $location_id;
    if ($locationId == 0) {
        return _crm_error('missing or invalid $location_id');
    }
    
    // $locationObj is the original location object that we are updating
    $locationObj = null;

    $locations =& crm_get_locations($contact);
    foreach ($locations as $locNumber => $locValue) {
        if ($locValue->id == $locationId) {
            $locationObj = $locValue;
            break;
        }
    }
    if ( ! $locationObj ) {
        return _crm_error('invalid $location_id');
    }
    
    $values = array(
                    'contact_id'    => $contact->id,
                    'location'      => array(1 => array()),
                    );
    
    
    $loc =& $values['location'][1];

    // setup required location values using the current ones. they may or may not be overridden by $params later.
    $loc['address']          = get_object_vars($locationObj->address);
    $loc['is_primary']       = $locationObj->is_primary;
    $loc['location_type_id'] = $locationObj->location_type_id;
    $loc['location_type']    = $locationObj->location_type;
    $loc['name']             = $locationObj->name;
    
    require_once 'CRM/Core/DAO/Address.php';
    $fields =& CRM_Core_DAO_Address::fields( );
    _crm_store_values($fields, $params, $loc['address']);

    $ids = array( 'county', 'country_id', 'country', 'state_province_id', 'state_province', 'supplemental_address_1', 'supplemental_address_2', 'StateProvince.name', 'street_address' );
    
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $loc['address'][$id] = $params[$id];
        }
    }
        
    if (is_numeric($loc['address']['state_province'])) {
        $loc['address']['state_province'] = CRM_Core_PseudoConstant::stateProvinceAbbreviation($loc['address']['state_province']);
    }
    
    if (is_numeric($loc['address']['country'])) {
        $loc['address']['country']        = CRM_Core_PseudoConstant::countryIsoCode($loc['address']['country']);
    }

    if (array_key_exists('location_type_id', $params)) {
        $loc['location_type_id'] = $params['location_type_id'];
    }

    if (array_key_exists('location_type', $params)) {
        $locTypes =& CRM_Core_PseudoConstant::locationType();
        $loc['location_type_id'] = CRM_Utils_Array::key($params['location_type'], $locTypes);
    }

    if (array_key_exists('name', $params)) {
        $loc['name'] = $params['name'];
    }

    if (array_key_exists('is_primary', $params)) {
        $loc['is_primary'] = (int) $params['is_primary'];
    }

    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $block) . ".php");
        eval( '$fields =& CRM_Core_DAO_' . $block . '::fields( );' );
        $name = strtolower($block);
        $loc[$name]    = array( );
        if ( $params[$name] ){
            $count = 1;
            foreach ( $params[$name] as $val) {
                _crm_store_values($fields, $val, $loc[$name][$count++]);
            }
        } else {
            // setup current values so we dont lose them
            foreach($locationObj->$name as $key => $obj) {
                $loc[$name][$key] = get_object_vars($obj);
            }
        }
    }

    $par = array('id' => $contact->id,'contact_id' => $contact->id);
    $contact = CRM_Contact_BAO_Contact::retrieve( $par , $defaults , $ids );
    
    CRM_Contact_BAO_Contact::resolveDefaults($values, true);
   
    $location = CRM_Core_BAO_Location::add($values, $ids, 1);
    return $location;
}


/**
 * Deletes a contact location.
 * 
 * @param object $contact        A valid Contact object (passed by reference).
 * @param string $location_id    A valid location ID.
 *
 * @return  null, if successful. CRM error object, if 'contact' or 'location_id' is invalid, permissions are insufficient, etc.
 *
 * @access public
 *
 */
function crm_delete_location(&$contact, $location_id) {
    _crm_initialize( );
    
    if( ! isset( $contact->id ) ) {
        return _crm_error('$contact is not valid contact datatype');
    } 
    
    $locationId = (int) $location_id;
    if ($locationId == 0) {
        return _crm_error('missing or invalid $location_id');
    }
    
    $locationDAO =& new CRM_Core_DAO_Location();
    $locationDAO->entity_table = 'civicrm_contact';
    $locationDAO->entity_id    = $contact->id;
    $locationDAO->id           = $locationId;
    if (!$locationDAO->find()) {
        return _crm_error('invalid $location_id');
    }
    $locationDAO->fetch();

    CRM_Core_BAO_Location::deleteLocationBlocks($locationId);
    // if we're deleting primary, lets change another one to primary
    if ($locationDAO->is_primary) {
        $otherLocationDAO =& new CRM_Core_DAO_Location();
        $otherLocationDAO->entity_table = 'civicrm_contact';
        $otherLocationDAO->entity_id    = $contact->id;
        $otherLocationDAO->whereAdd("id != $locationId");
        $otherLocationDAO->orderBy('id');
        if ($otherLocationDAO->find()) {
            $otherLocationDAO->fetch();
            $otherLocationDAO->is_primary = 1;
            $otherLocationDAO->save();
        }
    }
    $locationDAO->delete();

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
    $locationDAO =& new CRM_Core_DAO_Location();
    $locationDAO->entity_table = 'civicrm_contact';
    $locationDAO->entity_id = $contact->id;
    $locationCount = $locationDAO->count();
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
        // its ok to return an empty array of locations
        return $newLocations;
    }

    return $locations;
}


?>
