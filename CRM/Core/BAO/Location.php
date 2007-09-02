<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/BAO/Phone.php';
require_once 'CRM/Core/BAO/Email.php';
require_once 'CRM/Core/BAO/IM.php';
require_once 'CRM/Core/BAO/OpenID.php';
require_once 'CRM/Core/BAO/Address.php';
require_once 'CRM/Core/BAO/Block.php';

/**
 * This class handle creation of location block elements
 */
class CRM_Core_BAO_Location
{
    /**
     * Location block element array
     */
    //static $blocks = array( 'phone', 'email', 'im', 'openid', 'address' );
    static $blocks = array( 'phone' );

    /**
     * Function to create various elements of location block
     *
     * @param array    $params        (reference ) an assoc array of name/value pairs
     * @param boolean  $fixAddress   
     *
     * @return array   $location 
     * @access public
     * @static
     */
    static function create( &$params, $fixAddress = true ) 
    {
        if ( ! self::dataExists( $params ) ) {
            return null;
        }

        //format the params accord to new format. ie. we create all
        //email at one time, then move to another block element.

        $formattedBlocks = array( );
        self::formatParams( $params, $formattedBlocks );

        //create location block elements
        foreach ( self::$blocks as $block ) {
            $name = ucfirst( $block );
            eval( '$location[$block] = CRM_Core_BAO_' . $name . '::create( $formattedBlocks );');
        }

//         crm_core_error::debug('$formattedBlocks', $formattedBlocks);
//         exit();

//         $params['location'][$locationId]['id'] = $location->id;
//         $address_object = CRM_Core_BAO_Address::add( $params, $ids, $locationId, $fixAddress );
//         $location->address = $address_object;
//         // set this to true if this has been made the primary IM.
//         // the rule is the first entered value is the primary object
//         $isPrimaryPhone = $isPrimaryEmail = $isPrimaryIm = $isPrimaryOpenid = true;

//         // Check if no two values of a field has is_primary=1. 
//         // If yes then make sure only one has is_primary=1 and set rest to false.
//         $locElements = array('email', 'phone', 'im', 'openid');
//         foreach ($locElements as $element) {
//             $varName = 'isPrimary' . ucfirst($element);
//             $primarySet = false;
//             if ( isset(  $params['location'][$locationId][$element] ) &&
//                  is_array( $params['location'][$locationId][$element] ) ) {
//                 foreach ( $params['location'][$locationId][$element] as $eleKey => $eleVal ) {
//                     if ( CRM_Utils_Array::value( 'is_primary', $eleVal ) && !$primarySet) {
//                         $$varName   = false;
//                         $primarySet = true;
//                     } elseif ( CRM_Utils_Array::value( 'is_primary', $eleVal ) && $primarySet ) {
//                         //set is_primary to zero if already set.
//                         $params['location'][$locationId][$element][$eleKey]['is_primary'] = 0;
//                     }
//                 }
//             }
//         }

//         $location->phone  = array( );
//         $location->email  = array( );
//         $location->im     = array( );
//         $location->openid = array( );
        
//         for ( $i = 1; $i <= CRM_Contact_Form_Location::BLOCKS; $i++ ) {
//             $location->phone [$i] = CRM_Core_BAO_Phone::add ( $params, $ids, $locationId, $i, $isPrimaryPhone  );
//             $location->email [$i] = CRM_Core_BAO_Email::add ( $params, $ids, $locationId, $i, $isPrimaryEmail  );
//             $location->im    [$i] = CRM_Core_BAO_IM::add    ( $params, $ids, $locationId, $i, $isPrimaryIm     );
//             $location->openid[$i] = CRM_Core_BAO_OpenID::add( $params, $ids, $locationId, $i, $isPrimaryOpenid );
//         }

//         if ( isset( $ids['location'] ) ) {
//             foreach ( $ids['location'] as $lValues ) {
//                 // check if location is empty
//                 if ( isset( $lValues['id'] ) &&
//                      $lValues['id']          &&
//                      self::isLocationEmpty( $lValues['id'] ) ) {
//                     $locationDAO =& new CRM_Core_DAO_Location( );
//                     $locationDAO->id = $lValues['id'];
//                     $locationDAO->find( );
//                     // delete the empty location
//                     while ( $locationDAO->fetch( ) ) {
//                         self::deleteLocationBlocks( $locationDAO->id );
//                         $locationDAO->delete( );
//                     }
//                 }
//             }
//         }
        return $location;
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        // return if no data present
        if ( ! array_key_exists( 'location' , $params ) ) {
	        return false;
        }
        return true;
    }

    /**
     * Function formats the submitted array in new format so that we
     * can create all same block element in one go
     *
     * @param array  $params           (reference ) an assoc array of name/value pairs
     * @param array  $formattedBlocks  (reference ) formatted array of blocks 
     *
     * @access public
     * @static
     */
    static function formatParams( &$params, &$formattedBlocks ) 
    {
        foreach ( $params['location'] as $key => $value ) {
            foreach ( self::$blocks as $block ) { 
                $formattedBlocks[$block][$key]                     = $value[$block            ];
                $formattedBlocks[$block][$key]['contact_id'      ] = $params['contact_id'     ];
                $formattedBlocks[$block][$key]['location_type_id'] = $value['location_type_id'];
                $formattedBlocks[$block][$key]['is_primary'      ] = $value['is_primary'      ];
            }
        }
    }
    
    /**
     * Check if location Empty
     *
     * Check if the location is empty Location
     *
     * @params int     $lid       location id
     *
     * @return boolean  true if location is empty, false otherwise
     * 
     * @static
     * @access public
     */
    static function isLocationEmpty( $lid )
    {
        // if no valid lid, abort
        if ( ! $lid ) {
            CRM_Core_Error::fatal( ts( 'Please contact CiviCRM support for help' ) );
        }

        // get the location values
        $location =& new CRM_Core_BAO_Location( );
        $location->id = $lid;
        
        $location->find( true );
        $values = array( );
        if ( !empty($location->name) || !empty($location->location_name)  ) {
            $values[$lid]['location'] = array( $location->name, (isset($location->location_name)) ? $location->location_name : "" );
        }
        
        //get the phone values
        $phone =& new CRM_Core_BAO_Phone( );
        $phone->location_id = $lid;
        
        $phone->find( );
        while( $phone->fetch( ) ) {
            $values[$lid]['phone'][] = $phone->phone;
        }
        
        // get the email values
        $email =& new CRM_Core_BAO_Email( );
        $email->location_id = $lid;
        
        $email->find( );
        while( $email->fetch( ) ) {
            $values[$lid]['email'][] = $email->email;
        }
        
        // get the IM values
        $im =& new CRM_Core_BAO_IM( );
        $im->location_id = $lid;
        
        $im->find( );
        while( $im->fetch( ) ) {
            $values[$lid]['im'][] = $im->name;
        }
        
        // get the OpenID values
        $openId =& new CRM_Core_BAO_OpenID( );
        $openId->location_id = $lid;
        
        $openId->find( );
        while( $openId->fetch( ) ) {
            $values[$lid]['openid'][] = $openId->openid;
        }
        
        // get the address values
        $address =& new CRM_Core_BAO_Address( );
        $address->location_id = $lid;
        
        if ( $address->find( true ) ) {
            $address->storeValues( $address, $values[$lid]['address'] );
            unset( $values[$lid]['address'][0]['id'] );
            unset( $values[$lid]['address'][0]['location_id'] );
        }
        
        return empty($values) ? true : false;
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
     * @return array   array of objects(CRM_Core_BAO_Location)
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids, $locationCount = 0, $microformat = false ) {
        $location =& new CRM_Core_BAO_Location( );
        $location->copyValues( $params );
        if ( CRM_Utils_Array::value( 'contact_id', $params ) ) {
            $location->entity_table = 'civicrm_contact';
            $location->entity_id    = $params['contact_id'];
        } else if ( CRM_Utils_Array::value( 'domain_id', $params ) ) {
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
        $location->orderBy( 'is_primary desc, id' );
        $location->find( );
        $locations = array( );
        for ($i = 0; $i < $locationCount; $i++) {
            if ($location->fetch()) {
                $params['location_id'] = $location->id;
                if ($flatten) {
                    $ids['location'] = $location->id;
                    CRM_Core_DAO::storeValues( $location, $values );
                    self::getBlocks( $params, $values, $ids, 0, $location, $microformat );
                } else {
                    $values['location'][$i+1] = array();
                    $ids['location'][$i+1]    = array();
                    $ids['location'][$i+1]['id'] = $location->id;
                    CRM_Core_DAO::storeValues( $location, $values['location'][$i+1] );
                    self::getBlocks( $params, $values['location'][$i+1], $ids['location'][$i+1],
                                     CRM_Contact_Form_Location::BLOCKS, $location, $microformat );
                }
                $locations[$i + 1] = clone($location);
            }
        }

        if ( empty( $values['location'] ) ) {
            // mark the first location as primary if none exists
            $values['location'][1] = array( );
            $values['location'][1]['is_primary'] = 1;
        }
        return $locations;
    }

    /**
     * simple helper function to dispatch getCall to lower comm blocks
     */
    static function getBlocks( &$params, &$values, &$ids, $blockCount = 0, &$parent, $microformat = false ) {
        $parent->address =& CRM_Core_BAO_Address::getValues( $params, $values, $ids, $blockCount, $microformat );

        $parent->phone   =& CRM_Core_BAO_Phone::getValues ( $params, $values, $ids, $blockCount );
        $parent->email   =& CRM_Core_BAO_Email::getValues ( $params, $values, $ids, $blockCount );
        $parent->im      =& CRM_Core_BAO_IM::getValues    ( $params, $values, $ids, $blockCount );
        $parent->openid  =& CRM_Core_BAO_OpenID::getValues( $params, $values, $ids, $blockCount );
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
    static function deleteContact( $contactId, $entityTable = 'civicrm_contact', $locationTypeId = null ) {
        $location =& new CRM_Core_DAO_Location( );
        $location->entity_id = $contactId;
        require_once 'CRM/Contact/DAO/Contact.php';
        $location->entity_table = $entityTable;
        if($locationTypeId) {
            $location->location_type_id = $locationTypeId;
        }
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
        static $blocks = array( 'Address', 'Phone', 'IM', 'OpenID' );
        foreach ($blocks as $name) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $name) . ".php");
            eval( '$object =& new CRM_Core_DAO_' . $name . '( );' );
            $object->location_id = $locationId;
            $object->delete( );
        }
        
        CRM_Core_BAO_Email::deleteLocation($locationId);
    }

    static function primaryLocationValue( $entityID, $entityTable = 'civicrm_contact', $locationID = null ) {
        $sql = "
SELECT count( civicrm_location.id )
  FROM civicrm_location
 WHERE entity_table = %1
   AND entity_id    = %2
   AND is_primary   = 1";
        
        if ( $locationID ) {
            $sql .= " AND id != $locationID";
        }

        $sqlParams = array( 1 => array( $entityTable, 'String'  ),
                            2 => array( $entityID   , 'Integer' ) );
        $count = CRM_Core_DAO::singleValueQuery( $sql, $sqlParams );
        return ( $count == 0 ) ? true : false;
    }
    /**
     * get default values for the location block
     *
     */
    static function getLocationDefaultValues( $action, $maxLocationBlock, &$ids, &$defaults, $contactID = null )
    {
        if ( $action & CRM_Core_Action::ADD ) {
            if ( $maxLocationBlocks >= 1 ) {
                // set the is_primary location for the first location
                $defaults['location']    = array( );
                
                $locationTypeKeys = array_filter(array_keys( CRM_Core_PseudoConstant::locationType() ), 'is_int' );
                sort( $locationTypeKeys );
                
                // also set the location types for each location block
                for ( $i = 0; $i < $maxLocationBlocks; $i++ ) {
                    $defaults['location'][$i+1] = array( );
                    if ( $i == 0 ) {
                        require_once 'CRM/Core/BAO/LocationType.php';
                        $defaultLocation =& new CRM_Core_BAO_LocationType();
                        $locationType = $defaultLocation->getDefault();
                        $defaults['location'][$i+1]['location_type_id'] = $locationType->id;
                        
                    } else {
                        $defaults['location'][$i+1]['location_type_id'] = $locationTypeKeys[$i];
                    }
                    $defaults['location'][$i+1]['address'] = array( );
                    $config =& CRM_Core_Config::singleton( );
                    if( $config->defaultContactCountry ) {
                        $defaults['location'][$i+1]['address']['country_id'] = $config->defaultContactCountry;
                    }
                }
                $defaults['location'][1]['is_primary'] = true;
                return true;
            }
        } else {
            $params   = array( );
            $params['id'] = $params['contact_id'] = $contactID;
            require_once "CRM/Contact/BAO/Contact.php";
            $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );
            return $contact;
        }
    }
}

?>
