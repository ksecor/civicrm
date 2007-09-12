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
 * add static functions to include some common functionality
 * used across location sub object BAO classes
 *
 */

class CRM_Core_BAO_Block 
{
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
     * @param array  $params        input parameters to find object
     *
     * @return boolean              true if the block has data, otherwise false
     * @access public
     * @static
     */
    static function dataExists( $blockFields, $params ) 
    {
        foreach ( $blockFields as $field ) {
            if ( empty( $params[$field] ) ) {
                return false;
            }
        }
        return true;
    }

}

?>
