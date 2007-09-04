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

require_once 'CRM/Core/DAO/IM.php';

/**
 * This class contain function for IM handling
 */
class CRM_Core_BAO_IM extends CRM_Core_DAO_IM 
{

    /**
     * takes an associative array and creates a im
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_IM object on success, null otherwise
     * @access public
     * @static
     */
    static function create( &$params ) 
    {
        if ( ! self::dataExists( $params ) ) {
            return null;
        }
                
        $isPrimary = true;
        foreach ( $params['im'] as $value ) {
            $contactFields = array( );
            $contactFields['contact_id'      ] = $value['contact_id'];
            $contactFields['location_type_id'] = $value['location_type_id'];
            
            foreach ( $value as $val ) {
                if ( !CRM_Core_BAO_Block::dataExists( array( 'name' ), $val ) ) {
                    continue;
                }
                if ( is_array( $val ) ) {
                    if ( $isPrimary && $value['is_primary'] ) {
                        $contactFields['is_primary'] = $value['is_primary'];
                        $isPrimary = false;
                    } else {
                        $contactFields['is_primary'] = false;
                    }

                    $imFields = array_merge( $val, $contactFields);
                    self::add( $imFields );
                }
            }
        }
    }

    /**
     * takes an associative array and adds im
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_IM object on success, null otherwise
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
        $im =& new CRM_Core_DAO_IM();
        
        $im->copyValues($params);

        // need to handle update mode

        // when im field is empty need to delete it


        return $im->save( );
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        // return if no data present
        if ( ! array_key_exists( 'im', $params ) ) {
	        return false;
        }

        return true;
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     * @param int   $blockCount    number of blocks to fetch
     *
     * @return boolean
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids, $blockCount = 0 ) {
        $im =& new CRM_Core_BAO_IM( );
        return CRM_Core_BAO_Block::getValues( $im, 'im', $params, $values, $ids, $blockCount );
    }

}

?>
