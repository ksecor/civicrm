<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * File for the CiviCRM APIv2 location functions
 *
 * @package CiviCRM_APIv2
 * @subpackage API_Location
 *
 * @copyright CiviCRM LLC (c) 2004-2009
 * @version $Id$
 */

/**
 * Include utility functions
 */
require_once 'api/v2/utils.php';

/**
 * Create an additional location for an existing contact
 *
 * @param array $params  input properties
 *  
 * @return array  the created location's params
 *
 * @access public
 */
function civicrm_location_add( &$params ) {
    _civicrm_initialize( );
    $error = _civicrm_location_check_params( $params );
    
    if ( civicrm_error( $error ) ) {
        return $error;
    }  
    
    require_once 'CRM/Core/DAO/LocationType.php';
    $locationTypeDAO = & new CRM_Core_DAO_LocationType();
    $locationTypeDAO->name      = $params['location_type'];
    $locationTypeDAO->find(true);
    $locationTypeId = $locationTypeDAO->id;

    if(! isset($locationTypeId) ) {
        return civicrm_create_error( ts( '$location_type is not valid one' ) );
    }
    $location =& _civicrm_location_add( $params, $locationTypeId );
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
function civicrm_location_update( $params ) {
    _civicrm_initialize( );
    
    if( ! isset( $params['contact_id'] ) ) {
        return civicrm_create_error( ts ('$contact is not valid contact datatype') );
    } 
    
    if ( ! ( $locationTypeId = CRM_Utils_Array::value( 'location_type_id', $params ) ) && 
         ! ( CRM_Utils_Rule::integer( $locationTypeId ) ) ) {
        return civicrm_create_error( ts('missing or invalid location_type_id') );
    }
    
    // $locationObj is the original location object that we are updating
    $locationArray = array();
    $locations     =& civicrm_location_get( $params );
    
    foreach ( $locations as $locNumber => $locValue ) {
        if ( $locValue['location_type_id'] == $locationTypeId) {
            $locationArray = $locValue;
            break;
        }
    }
    
    if ( ! $locationArray ) {
        return civicrm_create_error( ts( 'invalid $location_id') );
    }
    
    $location =& _civicrm_location_update( $params, $locationArray );
    
    return $location ;
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
function civicrm_location_delete( &$contact ) {     
    _civicrm_initialize( );
    
    if( ! isset( $contact['contact_id'] ) ) {
        return civicrm_create_error( ts('$contact is not valid contact datatype') );
    } 
    
    require_once 'CRM/Utils/Rule.php';
    $locationTypeID = CRM_Utils_Array::value( 'location_type', $contact );
    if ( ! $locationTypeID ||
         ! CRM_Utils_Rule::integer( $locationTypeID ) ) {
        return civicrm_create_error( ts('missing or invalid location') );
    }
    
    $result =& _civicrm_location_delete( $contact );

    return $result;
}

/**
 * Returns array of location(s) for a contact
 * 
 * @param array $contact  a valid array of contact parameters
 *
 * @return array  an array of location parameters arrays
 *
 * @access public
 */
function civicrm_location_get( $contact ) {
    _civicrm_initialize( );
    
    if( ! isset( $contact['contact_id'] ) ) {
        return civicrm_create_error('$contact is not valid contact datatype');
    }
    
    $location_types = CRM_Utils_Array::value( 'location_type', $contact );
    if ( is_array($location_types) && ! count($location_types) ) {
        return civicrm_create_error('Location type array can not be empty');
    }
    
    $location=& _civicrm_location_get( $contact, $location_types );
    return $location;
}

/**
 *
 * @param <type> $params
 * @param <type> $locationTypeId
 * @return <type>
 */
function _civicrm_location_add( &$params, $locationTypeId ) {
    
    // 1. if block exists, increament counter and insert new block w/ given values.
    // 2. is_primary, is_billing give preference to params block first.
    
    // Get all existing location blocks.
    $blockParams = array( 'contact_id' => $params['contact_id'],
                          'entity_id'  => $params['contact_id'] );
    
    require_once 'CRM/Core/BAO/Location.php';
    $allBlocks = CRM_Core_BAO_Location::getValues( $blockParams );
    
    //get all ids if not present.
    require_once 'CRM/Contact/BAO/Contact.php';
    CRM_Contact_BAO_Contact::resolveDefaults( $params, true );
    
    // get all blocks in contact array.
    $contact = array_merge( array( 'contact_id' => $params['contact_id'] ), $allBlocks );
    
    $primary = $billing = array( );
    
    $blocks = array( 'Email', 'Phone', 'IM' );
    
    // copy params value in contact array.
    foreach ( $blocks as $block ) {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $block) . ".php");
        eval( '$fields =& CRM_Core_DAO_' . $block . '::fields( );' );
        $name = strtolower($block);
        
        if ( CRM_Utils_Array::value( $name, $params ) ) {
            if ( !isset($contact[$name]) ||
                 !is_array($contact[$name])) {
                $contact[$name] = array( );
            }
            $blockCount = count( $contact[$name] );
            
            $firstBlockCount = null;
            if ( is_array( $params[$name] ) ) {
                foreach ( $params[$name] as $val ) {
                    _civicrm_store_values( $fields, $val, $contact[$name][++$blockCount]);
                    
                    // check for primary and billing.
                    if ( CRM_Utils_Array::value( 'is_primary', $val ) ) {
                        $primary[$name][$blockCount] = true; 
                    }
                    if ( CRM_Utils_Array::value( 'is_billing', $val ) ) {
                        $primary[$name][$blockCount] = true;  
                    }
                    if ( !$firstBlockCount ) {
                        $firstBlockCount = $blockCount;
                    }
                }
            } else {
                $p = array( $name => $params[$name] );
                _civicrm_store_values( $fields, $p, $contact[$name][++$blockCount] );
                
                $firstBlockCount = $blockCount;
            }
            
            // make first block as default primary when is_primary 
            // is not set in sub array and set in main params array.
            if ( !CRM_Utils_Array::value( $name, $primary ) ) {
                $primary[$name][$firstBlockCount] = true;
                $contact[$name][$firstBlockCount]['is_primary'] = true;
            }
            if ( !CRM_Utils_Array::value( $name, $billing ) ) {
                $billing[$name][$firstBlockCount] = true;
                $contact[$name][$firstBlockCount]['is_billing'] = true;
            }
        }
    }
    
    // get address fields in contact array.
    $addressCount = 1;
    if ( array_key_exists( 'address', $contact ) && is_array( $contact['address'] )  ) {
        foreach ( $contact['address'] as $addCount => $values ) {
            if ( in_array( $locationTypeId, $values ) ) {
                $addressCount = $addCount;
                break;
            }
            $addressCount++;
        }
    }
    
    //check for primary address.
    if ( CRM_Utils_Array::value( 'is_primary', $params ) ) {
        $primary['address'][$addressCount] = true;
    }
    if ( CRM_Utils_Array::value( 'is_billing', $params ) ) {
        $billing['address'][$addressCount] = true;   
    }
    
    $ids = array( 'county', 'country_id', 'country', 
                  'state_province_id', 'state_province',
                  'supplemental_address_1', 'supplemental_address_2',
                  'StateProvince.name' );
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            require_once 'CRM/Core/DAO/Address.php';
            $fields =& CRM_Core_DAO_Address::fields( );
            _civicrm_store_values( $fields, $params, $contact['address'][$addressCount] );
            $contact['address'][$addressCount][$id] = $params[$id];
            break;
        }
    }
    
    // format state and country.
    foreach ( array( 'state_province', 'country' ) as $field ) {
        $fName = ( $field == 'state_province' ) ? 'stateProvinceAbbreviation' : 'countryIsoCode';
        if ( CRM_Utils_Array::value( $field, $contact['address'][$addressCount] ) &&
             is_numeric( $contact['address'][$addressCount][$field])) {
            $fValue =& $contact['address'][$addressCount][$field];
            eval( '$fValue = CRM_Core_PseudoConstant::' . $fName . '( $fValue );'  );
            
            //kill the reference.
            unset( $fValue );
        }
    }
    
    //handle primary and billing reset.
    foreach ( array( 'email', 'phone', 'im', 'address' ) as $name ) {
        if ( !array_key_exists($name, $contact) || CRM_Utils_System::isNull($contact[$name]) ) continue; 
        
        $errorMsg = null;
        $primaryBlockIndex = $billingBlockIndex = 0;
        if ( array_key_exists( $name, $primary ) ) {
            if ( count( $primary[$name] ) > 1 ) {
                $errorMsg .= ts ( "<br />Multiple Primary %1.", array( 1 => $block ) );
            } else {
                $primaryBlockIndex = key( $primary[$name] );
            }
        }
        
        if ( array_key_exists( $name, $billing ) ) {
            if ( count( $billing[$name] ) > 1 ) {
                $errorMsg .= ts ( "<br />Multiple Billing %1.", array( 1 => $block ) );
            } else {
                $billingBlockIndex = key( $billing[$name] ); 
            }
        }
        
        if ( $errorMsg ) {
            return civicrm_create_error( $errorMsg  );  
        }
        
        // reset other primary and billing block.
        if ( $primaryBlockIndex || $billingBlockIndex ) {
            foreach ( $contact[$name] as $count => &$values ) {
                if ( $primaryBlockIndex && ($count != $primaryBlockIndex) ) $values['is_primary'] = false;
                if ( $billingBlockIndex && ($count != $billingBlockIndex) ) $values['is_billing'] = false;
                
                // get location type if not present in sub array.
                if (!CRM_Utils_Array::value('location_type_id', $values)) $values['location_type_id'] = $locationTypeId;
                
                //kill the rerefence.
                unset( $values );
            }
        }
    }
    
    // get all ids if not present.
    require_once 'CRM/Contact/BAO/Contact.php';
    CRM_Contact_BAO_Contact::resolveDefaults( $contact, true );
    
    require_once 'CRM/Core/BAO/Location.php';
    $result = CRM_Core_BAO_Location::create( $contact );
    
    if ( empty( $result ) ) {
        return civicrm_create_error( ts ("Location not created" ) );
    }
    
    $blocks = array( 'address', 'phone', 'email', 'im' );
    foreach( $blocks as $block ) {
        for ( $i = 0; $i < count( $result[$block] ); $i++ ) {
            $locArray[$block][$i] = $result[$block][$i]->id;
        }
    }
    
    return civicrm_create_success( $locArray );
}

/**
 *
 * @param <type> $params
 * @param <type> $locationArray
 * @return <type>
 */
function _civicrm_location_update( $params,$locationArray ) {
    $values = array(
                    'contact_id'    => $params['contact_id'],
                    'location'      => array(1 => array()),
                    );
    
    $loc =& $values['location'][1];
    
    // setup required location values using the current ones. they may or may not be overridden by $params later.
    $loc['address']          = CRM_Utils_Array::value( 'address', $locationArray );
    $loc['is_primary']       = CRM_Utils_Array::value( 'is_primary', $locationArray );
    $loc['is_billing']       = CRM_Utils_Array::value( 'is_billing', $locationArray );
    $loc['location_type_id'] = CRM_Utils_Array::value( 'location_type_id', $locationArray );
    $loc['location_type']    = CRM_Utils_Array::value( 'location_type', $locationArray );
    $loc['name']             = CRM_Utils_Array::value( 'name', $locationArray );
    
    require_once 'CRM/Core/DAO/Address.php';
    $fields =& CRM_Core_DAO_Address::fields( );
    
    $names = array( 'county', 'country_id', 'country', 'state_province_id',
                    'state_province', 'supplemental_address_1', 'supplemental_address_2',
                    'StateProvince.name', 'street_address' );
    
    foreach ( $names as $n ) {
        if ( array_key_exists( $n, $params ) ) {
            _civicrm_store_values($fields, $params, $loc['address']);
            $loc['address'][$n] = $params[$n];
        }
    }
    
    if (isset($loc['address']['state_province']) &&
        is_numeric($loc['address']['state_province'])) {
        $loc['address']['state_province'] = CRM_Core_PseudoConstant::stateProvinceAbbreviation($loc['address']['state_province']);
    }
    
    if (isset($loc['address']['country']) &&
        is_numeric($loc['address']['country'])) {
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
    
    if (array_key_exists('is_billing', $params)) {
        $loc['is_billing'] = (int) $params['is_billing'];
    }
    
    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $block) . ".php");
        eval( '$fields =& CRM_Core_DAO_' . $block . '::fields( );' );
        $name = strtolower($block);
        $loc[$name]    = array( );
        if ( isset( $params[$name] ) ){
            $count = 1;
            foreach ( $params[$name] as $val) {
                _civicrm_store_values($fields, $val, $loc[$name][$count++]);
            }
        } else {
            // setup current values so we dont lose them
            if (isset($locationArray[$name]) &&
                is_array($locationArray[$name])){
                foreach($locationArray[$name] as $key => $obj) {
                    $loc[$name][$key] = $obj;
                }
            }
        }
    }
    
    $location = CRM_Core_BAO_Location::create( $values );
    
    if ( empty( $location ) ) {
        return civicrm_create_error( ts ("Location not created" ) );
    }
    
    $locArray                     = array( );
    
    $locArray['location_type_id'] = CRM_Utils_Array::value( 'location_type_id', $loc );
    
    $blocks = array( 'address', 'phone', 'email', 'im' );
    
    foreach( $blocks as $block ) {
        for ( $i = 0; $i < count( $location[$block] ); $i++ ) {
            $locArray[$block][$i] = $location[$block][$i]->id;
        }
    }
    
    return civicrm_create_success( $locArray );
}

/**
 *
 * @param <type> $contact
 * @return <type>
 */
function _civicrm_location_delete( &$contact ) {
    require_once 'CRM/Core/DAO/LocationType.php';
    $locationTypeDAO     =& new CRM_Core_DAO_LocationType( );
    $locationTypeDAO->id = $contact['location_type'];
    
    if ( ! $locationTypeDAO->find( ) ) {
        return civicrm_create_error( ts('invalid location type') );
    }

    require_once 'CRM/Core/BAO/Location.php';
    CRM_Core_BAO_Location::deleteLocationBlocks( $contact['contact_id'], $contact['location_type'] );
    
    return null;
}

/**
 *
 * @param <type> $contact
 * @param <type> $location_types
 * @return <type>
 */
function &_civicrm_location_get( $contact, $location_types ) {
    $params                    = array();
    $params['contact_id']      = $contact['contact_id'];
    $params['entity_id']       = $contact['contact_id'];
    require_once 'CRM/Core/BAO/Location.php';    
    $locationBAO               =& new CRM_Core_BAO_Location();
    
    $values                    = array();
    $locations                 = CRM_Core_BAO_Location::getValues( $params, $values );
    
    if( is_array($location_types) && count($location_types)>0 ) {
        foreach($location_types as $locationName) {
            $newLocations = array();
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
        if($newLocations) {
            foreach($newLocations as $key=> $loc) {
                if ( is_a($loc, 'CRM_Core_BAO_Location') ) {
                    $newLocations[$key] = &_civicrm_location_object_to_array( $loc );
                }
            }
        }
        // its ok to return an empty array
        return $newLocations;
    } else {
        foreach($locations as $key => $loc) {
            if ( is_a($loc, 'CRM_Core_BAO_Location') ) {
                $locations[$key] = &_civicrm_location_object_to_array( $loc );
            }
        }
        return $locations;
    }
}

/**
 *
 * @param <type> $locObject
 * @return <type> 
 */
function &_civicrm_location_object_to_array( $locObject ) {
    
    // building location array
    $locArray = array();
    
    // build address block
    if ( is_a($locObject->address, 'CRM_Core_BAO_Address') ) {
        _civicrm_object_to_array( $locObject->address, $locArray['address']);
        unset($locObject->address);
    }
    
    // build email, phone and im block
    $locElements = array('email', 'phone', 'im');
    foreach ( $locElements as $element ) {
        foreach ( $locObject->{$element} as $key => $eleObject ) {
            if ( is_a($locObject->{$element}[$key], 'CRM_Core_DAO_' . ucfirst($element))) {
                _civicrm_object_to_array( $locObject->{$element}[$key], $locArray[$element][$key]);
            }
        }
        unset($locObject->{$element});
    }
    
    _civicrm_object_to_array( $locObject, $locArray);
    
    return $locArray;
    // building location array ends  
}

/**
 * This function ensures that we have the right input location parameters
 *
 * We also need to make sure we run all the form rules on the params list
 * to ensure that the params are valid
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new location.
 *
 * @return bool|CRM_Utils_Error
 * @access public
 */
function _civicrm_location_check_params( &$params ) {
    static $required = array( 'contact_id', 'location_type' );
    
    // cannot create a location with empty params
    if ( empty( $params ) ) {
        return civicrm_create_error( 'Input Parameters empty' );
    }

    $valid = true;
    $error = '';
    foreach ( $required as $field ) {
        if ( ! CRM_Utils_Array::value( $field, $params ) ) {
            $valid = false;
            $error .= $field;
            break;
        }
    }
    
    if ( ! $valid ) {
        return civicrm_create_error( "Required fields not found for location $error" );
    }
    
    return array();
}
