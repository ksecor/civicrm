<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/IM.php';

/**
 * BAO object for crm_im table
 */
class CRM_Core_BAO_IM extends CRM_Core_DAO_IM {
    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $ids            the array that holds all the db ids
     * @param int    $locationId
     * @param int    $imId
     * @param bool   $isPrimary      Has any previous entry been marked as isPrimary?
     *
     * @return object  CRM_Core_BAO_IM object on success, null otherwise
     * @access public
     * @static
     */
    static function add( &$params, &$ids, $locationId, $imId, &$isPrimary ) {
        if ( ! self::dataExists( $params, $locationId, $imId, $ids ) ) {
            return null;
        }

        $im =& new CRM_Core_DAO_IM();
        $im->name         = $params['location'][$locationId]['im'][$imId]['name'];
        $im->id = CRM_Utils_Array::value( $imId, $ids['location'][$locationId]['im'] );
        if ( empty( $im->name ) ) {
            $im->delete( );
            return null;
        }

        $im->location_id  = $params['location'][$locationId]['id'];
        $im->provider_id  = $params['location'][$locationId]['im'][$imId]['provider_id'];
        if (! $im->provider_id ) {
            $im->provider_id  = 'null';
        }

        // set this object to be the value of isPrimary and make sure no one else can be isPrimary
        $im->is_primary   = $isPrimary;
        $isPrimary        = false;

        return $im->save( );
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $imId
     * @param array  $ids            the array that holds all the db ids
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params, $locationId, $imId, &$ids ) {
        if (CRM_Utils_Array::value( $imId, $ids['location'][$locationId]['im'] )) {
            return true;
        }
        
        return CRM_Core_BAO_Block::dataExists('im', array( 'name' ), $params, $locationId, $imId );
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