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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
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
class CRM_Core_BAO_Location extends CRM_Core_DAO
{
    /**
     * Location block element array
     */
    static $blocks = array( 'phone', 'email', 'im', 'openid', 'address' );
    
    /**
     * Function to create various elements of location block
     *
     * @param array    $params       (reference ) an assoc array of name/value pairs
     * @param boolean  $fixAddress   true if you need to fix (format) address values
     *                               before inserting in db
     *
     * @return array   $location 
     * @access public
     * @static
     */
    static function create( &$params, $fixAddress = true, $entity = null ) 
    {
        if ( ! self::dataExists( $params ) ) {
            return null;
        }
        
        //format the params accord to new format. ie. we create all
        //email at one time, then move to another block element.
        
        $formattedBlocks = array( );
        self::formatParams( $params, $formattedBlocks, $entity );
              
        //create location block elements
        foreach ( self::$blocks as $block ) {
            $name = ucfirst( $block );
            if ( $block != 'address' ) {
                eval( '$location[$block] = CRM_Core_BAO_Block::create( $block, $formattedBlocks, $entity );');
            } else {
                $location[$block] = CRM_Core_BAO_Address::create( $formattedBlocks, $fixAddress, $entity );
            }
        }
        
        // this is a special case for adding values in location block table
        if ( $entity ) {
            $entityElements = array( 'entity_table' => $params['entity_table'],
                                     'entity_id'    => $params['entity_id']);
            
            $location['id'] = self::createLocBlock ( $location, $entityElements );
        }
       
        return $location;
    }

    /**
     * Creates the entry in the civicrm_loc_block
     *
     */
    static function createLocBlock ( &$location, &$entityElements ) 
    {
        $locId = self::findExisting( $entityElements );
        $locBlock = array( );

        if ( $locId ) {
            $locBlock['id'] = $locId;
        }

        $locBlock['phone_id']     = $location['phone'  ][0]->id;
        $locBlock['phone_2_id']   = $location['phone'  ][1]->id;
        $locBlock['email_id']     = $location['email'  ][0]->id;
        $locBlock['email_2_id']   = $location['email'  ][1]->id;
        $locBlock['im_id']        = $location['im'     ][0]->id;
        $locBlock['im_2_id ']     = $location['im'     ][1]->id;
        $locBlock['address_id']   = $location['address'][0]->id;
        $locBlock['address_2_id'] = $location['address'][1]->id;
       
        foreach( $locBlock as $key => $block) {
            if ( empty($locBlock[$key] ) ) {
                $locBlock[$key] = 'null';
            }
        }
        
        $locBlockInfo = self::addLocBlock( $locBlock );
        return $locBlockInfo->id;
      
    }

    /**
     * takes an entity array and finds the existing location block 
     * @access public
     * @static
     */
    static function findExisting( $entityElements ) 
    {
        $eid = $entityElements['entity_id'];
        $etable = $entityElements['entity_table'];
        $query = "
SELECT e.loc_block_id as locId
FROM {$etable} e
WHERE e.id = %1";

        $params = array( 1 => array( $eid, 'Integer' ) );
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
         while ( $dao->fetch( ) ) {
             $locBlockId = $dao->locId;
         }
         return $locBlockId;
    }
    
     /**
     * takes an associative array and adds location block 
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_locBlock object on success, null otherwise
     * @access public
     * @static
     */
    static function addLocBlock( &$params ) 
    {
        require_once 'CRM/Core/DAO/LocBlock.php';
        $locBlock =& new CRM_Core_DAO_LocBlock();
        
        $locBlock->copyValues($params);

        return $locBlock->save( );
    }
     /**
     *  This function deletes the Location Block
     *
     * @param  int  $locBlockId    id of the Location Block
     *
     * @return void
     * @access public
     * @static
     */
    
    public static function deleteLocBlock( $locBlockId )
    {
        require_once 'CRM/Core/DAO/LocBlock.php';
        $locBlock     = new CRM_Core_DAO_LocBlock( );
        $locBlock->id = $locBlockId;
        
        $locBlock->find( true );
         
        $store = array( $locBlock->address_id   => 'Address',
                        $locBlock->address_2_id => 'Address', 
                        $locBlock->phone_id     => 'Phone',
                        $locBlock->phone_2_id   => 'Phone',
                        $locBlock->im_id        => 'IM',
                        $locBlock->im_2_id      => 'IM',
                        $locBlock->email_id     => 'Email',
                        $locBlock->email_2_id   => 'Email' );
        
        $locBlock->delete( );
        
        foreach ( $store as $id => $daoName ) {
            if ( $id ) {
                eval( '$dao = new CRM_Core_DAO_' . $daoName . '( );' );
                $dao->id = $id;
                $dao->find( true );
                $dao->delete( );
                $dao->free( );
            }
        }
        
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
    static function formatParams( &$params, &$formattedBlocks, $entity = null ) 
    {
        foreach ( $params['location'] as $key => $value ) {
            // fix location type id if set to Primary
            // this enables us to skip resolving this during block rendering time
            if ( isset( $params['location'][$key]['location_type_id'] ) &&
                 strtolower( $params['location'][$key]['location_type_id'] ) == 'primary' ) {
                $defaultLocation = CRM_Core_BAO_LocationType::getDefault( );
                $params['location'][$key]['location_type_id'] = $defaultLocation->id;
            }
            
            foreach ( self::$blocks as $block ) {
                if ( CRM_Utils_Array::value( $block, $value ) ) {
                    $formattedBlocks[$block][$key]                     = CRM_Utils_Array::value( $block,
                                                                                                 $value );
                    $formattedBlocks[$block][$key]['location_type_id'] = CRM_Utils_Array::value( 'location_type_id',
                                                                                                 $value );
                    $formattedBlocks[$block][$key]['is_primary'      ] = CRM_Utils_Array::value( 'is_primary',
                                                                                                 $value );
                    $formattedBlocks[$block][$key]['is_billing'      ] = CRM_Utils_Array::value( 'is_billing',
                                                                                                 $value );
                    if ( !$entity ) {
                        $formattedBlocks[$block]['contact_id'        ] = $params['contact_id'     ];
                    } else {
                        $formattedBlocks['entity_table']       = $params['entity_table'   ];
                        $formattedBlocks['entity_id']          = $params['entity_id'   ];
                    }
                }
            }
        }
    }
    
    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     *
     * @return array   array of objects(CRM_Core_BAO_Location)
     * @access public
     * @static
     */
    static function &getValues( $entityBlock, &$values, $microformat = false ) 
    {  
        $locations = array( );
        //get all the blocks for this contact
        foreach ( self::$blocks as $block ) {
            $name = ucfirst( $block );
            
            eval( '$location[$block] = CRM_Core_BAO_' . $name . '::getValues( $entityBlock, $values );');
        }
       
        //format locations blocks for setting defaults
        $locationCount = 1;
        $locationTypes = array( );
        foreach ( $location as $key => $value ) {
            
            if ( ! is_array( $value ) || empty( $value) ) {
                continue;
            }
            
            $primary_location_type = null;
            foreach ( $value as $locationTypeId => $val ) { 
                //logic to check when we should increment counter
                if ( !empty( $locationTypes ) ) {
                    if ( in_array ( $locationTypeId, $locationTypes ) ) {
                        $locationNo = array_search( $locationTypeId, $locationTypes );
                    } else {
                        $locationCount++;
                        $locationTypes[ $locationCount ] = $locationTypeId;
                        $locationNo = $locationCount;
                    }
                } else {
                    $locationTypes[ $locationCount ]  = $locationTypeId;
                    $locationNo = $locationCount;
                }
                
                $locations[ $locationNo ]['location_type_id'] = $locationTypeId;
                $locations[ $locationNo ][$key] = $val;
                
                if ( CRM_Utils_Array::value( 'is_primary' , $val ) ) { 
                    $primary_location_type = $locationTypeId;
                }
            }
        }
        
        $values['location'] = $allLocations['location'] = $locations;
        
        foreach($values['location'] as $key => $val) {
            if($val['location_type_id'] == $primary_location_type) {
                $primary_loc_val = $values['location'][$key];
                $values['location'][$key] = $values['location'][1];
                $values['location'][1] = $primary_loc_val;
            }
        }
        if ( empty( $values['location'] ) ) {
            // mark the first location as primary if none exists
            $values['location'][1] = array( );
            $values['location'][1]['is_primary'] = 1;
        }
        
        return $values['location'];
    }

    /**
     * Delete all the block associated with the location
     *
     * @param  int  $contactId      contact id
     * @param  int  $locationTypeId id of the location to delete
     *
     * @return void
     * @access public
     * @static
     */
    static function deleteLocationBlocks( $contactId, $locationTypeId ) 
    {
        // ensure that contactId has a value
        if ( empty( $contactId ) ||
             ! CRM_Utils_Rule::positiveInteger( $contactId ) ) {
            CRM_Core_Error::fatal( );
        }
             
        if ( empty( $locationTypeId ) ||
             ! CRM_Utils_Rule::positiveInteger( $locationTypeId ) ) {
            // so we only delete the blocks which DO NOT have a location type Id
            // CRM-3581
            $locationTypeId = 'null';
        }

        static $blocks = array( 'Address', 'Phone', 'IM', 'OpenID', 'Email' );
        
        require_once "CRM/Core/BAO/Block.php";
        $params = array ( 'contact_id' => $contactId, 'location_type_id' => $locationTypeId );
        foreach ($blocks as $name) {
            CRM_Core_BAO_Block::blockDelete( $name, $params );
        }
    }

    /**
     * Function to cleanup Contact locations
     * Basically we need to delete unwanted location types for a contact in Edit mode
     * create() is also called by createProfileContact(), in that case we should preserve other location type's,
     * This is a special case where we need to delete location types that are not submitted.
     * 
     * @param array $params associated array of formatted params
     * @return void
     * @static
     */
    static function cleanupContactLocations( $params )
    {
        //get the contact id from params
        $contactId = CRM_Utils_Array::value( 'contact_id', $params );

        // build submitted location types
        if ( isset( $params['location'] ) ) {
            $submittedLocationTypes = array( );
            foreach ( $params['location'] as $key => $value ) {
                $submittedLocationTypes[ $value['location_type_id'] ] = $value['location_type_id'];
            }
        }

        // get existing locations
        $entityBlock = array( 'contact_id' => $contactId );
        $locations    = self::getValues( $entityBlock, $defaults );

        if ( !empty( $locations ) ) {
            $existingLocationTypes = array( );
            foreach ( $locations as $key => $value ) {
                if ( CRM_Utils_Array::value( 'location_type_id', $value ) ) {
                    $existingLocationTypes[ $value['location_type_id'] ] = $value['location_type_id'];
                }
            }
        }
        
        // deleted existing locations that are not submitted
        if ( !empty( $existingLocationTypes ) ) {
            foreach ( $existingLocationTypes as $lType ) {
                if ( !in_array( $lType, $submittedLocationTypes ) ) {
                    self::deleteLocationBlocks( $contactId, $lType );
                }
            }
        }
    }
    
    /* Function to copy or update location block. 
     *
     * @param  int  $locBlockId  location block id.
     * @param  int  $updateLocBlockId update location block id
     * @return int  newly created/updated location block id.
     */
    static function copyLocBlock( $locBlockId, $updateLocBlockId = null ) 
    {
        //get the location info.
        $defaults = $updateValues = array( );
        $locBlock = array( 'id' => $locBlockId );
        CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_LocBlock', $locBlock, $defaults );
        
        if ( $updateLocBlockId ) {
            //get the location info for update.
            $copyLocationParams = array( 'id' => $updateLocBlockId );
            CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_LocBlock', $copyLocationParams, $updateValues );
            foreach ( $updateValues as $key => $value) {
                if ( $key != 'id' ) {
                    $copyLocationParams[$key] = 'null';
                }
            }
        }
        
        //copy all location blocks (email, phone, address, etc)
        foreach ( $defaults as $key => $value ) {
            if ( $key != 'id') {
                $tbl  = explode("_", $key);
                $name = ucfirst( $tbl[0] );
                $updateParams = null;
                if ( $updateId = CRM_Utils_Array::value( $key, $updateValues ) ) {
                    $updateParams = array( 'id' => $updateId );
                }
                
                $copy =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_' . $name, array( 'id' => $value ), $updateParams );
                $copyLocationParams[$key] = $copy->id;
            }
        }
        
        $copyLocation =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_LocBlock', 
                                                    array( 'id' => $locBlock['id'] ), 
                                                    $copyLocationParams );
        return $copyLocation->id;
    }
}


