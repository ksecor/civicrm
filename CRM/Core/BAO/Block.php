<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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
 * add static functions to include some common functionality
 * used across location sub object BAO classes
 *
 */

class CRM_Core_BAO_Block 
{
    /**
     * Fields that are required for a valid block
     */
    static $requiredBlockFields = array ( 'email' => array( 'email' ),
                                          'phone' => array( 'phone' ),
                                          'im'    => array( 'name' )
                                          );

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param Object $block         typically a Phone|Email|IM|OpenID object
     * @param string $blockName     name of the above object
     * @param array  $params        input parameters to find object
     * @param array  $values        output values of the object
     * @param array  $ids           the array that holds all the db ids
     * @param int    $blockCount    number of blocks to fetch
     *
     * @return array of $block objects.
     * @access public
     * @static
     */
    static function &getValues( &$block, $blockName, $contactId )  
    {
        $block->contact_id = $contactId;

        $blocks = array( );

        // we first get the primary location due to the order by clause
        $block->orderBy( 'is_primary desc, location_type_id desc, id asc' );
        $block->find( );

        $locationTypes = array( );
        $count = 1;
        while ( $block->fetch( ) ) {
            $values = array( );
            CRM_Core_DAO::storeValues( $block, $values );

            //logic to check when we should increment counter
            if ( !empty( $locationTypes ) ) {
                if ( array_key_exists ( $block->location_type_id, $locationTypes ) ) {
                    $count = $locationTypes[$block->location_type_id];
                    $count++;
                    $locationTypes[$block->location_type_id] = $count;
                } else {
                    $locationTypes[$block->location_type_id]  = 1;
                    $count = 1;
                }
            } else {
                $locationTypes[$block->location_type_id]  = 1;
                $count = 1;
            }

            $blocks[$block->location_type_id][$count] = $values;
        }

//         for ($i = 0; $i < $blockCount; $i++) {
//             if ($block->fetch()) {
//                 if ( $flatten ) {
//                     CRM_Core_DAO::storeValues( $block, $values );
//                     $ids[$blockName] = $block->id;
//                 } else {
//                     $values[$blockName][$i+1] = array();
//                     CRM_Core_DAO::storeValues( $block, $values[$blockName][$i+1] );
//                     $ids[$blockName][$i+1] = $block->id;
//                 }
//                 $blocks[$i + 1] = clone($block);
//             }
//         }
        return $blocks;
    }

    /**
     * check if the current block object has any valid data
     *
     * @param array  $blockFields   array of fields that are of interest for this object
     * @param array  $params        associated array of submitted fields
     *
     * @return boolean              true if the block has data, otherwise false
     * @access public
     * @static
     */
    static function dataExists( $blockFields, &$params ) 
    {
        foreach ( $blockFields as $field ) {
            if ( empty( $params[$field] ) ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * check if the current block exits
     *
     * @param string  $blockName   bloack name
     * @param array   $params      associated array of submitted fields
     *
     * @return boolean             true if the block exits, otherwise false
     * @access public
     * @static
     */
    static function blockExists( $blockName, &$params ) 
    {
        // return if no data present
        if ( ! array_key_exists( $blockName, $params ) ) {
	        return false;
        }

        return true;
    }

    /**
     * Function to get all block ids for a contact
     *
     * @param string $blockName block name
     * @param int    $contactId contact id
     *
     * @return array $contactBlockIds formatted array of block ids
     *
     * @access public
     * @static
     */
    static function getBlockIds ( $blockName, $contactId )
    {
        $contactBlockIds = $allBlocks = array( );
        
        $name = ucfirst( $blockName );
        eval ('$allBlocks = CRM_Core_BAO_' . $name . '::all' . $name . 's( $contactId );');
        
        $locationCount = 1;
        $blockCount    = 1;
        $locationTypes = array( );

        foreach ( $allBlocks as $blocks ) {
            //logic to check when we should increment counter
            $locationTypeId = $blocks['locationTypeId'];
            if ( !empty( $locationTypes ) ) {
                if ( in_array ( $locationTypeId, $locationTypes ) ) {
                    $locationCount = array_search( $locationTypeId, $locationTypes );
                } else {
                    $locationCount++;
                    $locationTypes[ $locationCount ] = $locationTypeId;
                }
                } else {
                    $locationTypes[ $locationCount ]  = $locationTypeId;
                }

            $contactBlockIds[ $locationCount ][ $blockCount ] = $blocks['id'];
            $blockCount++;
        }

        return $contactBlockIds;
    }

    /**
     * takes an associative array and creates a block
     *
     * @param string $blockName      block name
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $requiredFields fields that's are required in a block
     *
     * @return object       CRM_Core_BAO_Block object on success, null otherwise
     * @access public
     * @static
     */
    static function create( $blockName, &$params ) 
    {
        if ( !self::blockExists( $blockName, $params ) ) {
            return null;
        }
        
        $contactId = $params[$blockName]['contact_id'];
        $name = ucfirst( $blockName );
        
        //get exixting block ids if exist for this contact
        $contactBlockIds = array( );
        $contactBlockIds = self::getBlockIds( $blockName, $contactId );
        
        $isPrimary     = true;
        $locationCount = 1;
        foreach ( $params[$blockName] as $value ) {
            if ( !is_array( $value ) ) {
                continue;
            }

            $contactFields = array( );
            $contactFields['contact_id'      ] = $contactId;
            $contactFields['location_type_id'] = $value['location_type_id'];
            
            foreach ( $value as $val ) {
                
                if ( !is_array( $val ) || !self::dataExists( self::$requiredBlockFields[$blockName], $val ) ) {
                    continue;
                }
                
                if ( $isPrimary && $value['is_primary'] ) {
                    $contactFields['is_primary'] = $value['is_primary'];
                    $isPrimary = false;
                } else {
                    $contactFields['is_primary'] = false;
                }

                if ( !empty( $contactBlockIds[ $locationCount ] ) ) {
                    $val['id'] = array_shift( $contactBlockIds[ $locationCount ] );
                }
                
                $blockFields = array_merge( $val, $contactFields );
                //crm_core_error::debug('$blockFields', $blockFields);
                eval ( '$blocks[] = CRM_Core_BAO_' . $name . '::add( $blockFields );' );
            }
            
            $locationCount++;
        }

        return $blocks;
    }
    
}

?>
