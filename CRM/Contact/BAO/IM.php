<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Contact/DAO/IM.php';

class CRM_Contact_BAO_IM extends CRM_Contact_DAO_IM {
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $imId
     *
     * @return object CRM_Contact_BAO_IM object
     * @access public
     * @static
     */
    static function add( &$params, $locationId, $imId ) {
        if ( ! self::dataExists( $params, $locationId, $imId ) ) {
            return null;
        }

        $im = new CRM_Contact_DAO_IM();
        $im->location_id  = $params['location'][$locationId]['id'];
        $im->name         = $params['location'][$locationId]['im'][$imId]['name'];
        $im->provider_id  = $params['location'][$locationId]['im'][$imId]['provider_id'];
        $im->is_primary   = CRM_Array::value( 'is_primary', $params['location'][$locationId]['im'][$imId], false );
        return $im->save( );
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $imId
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params, $locationId, $imId ) {
        return CRM_Contact_BAO_Block::dataExists('im', array( 'name', 'provider_id' ), 
                                                 $params, $locationId, $imId );
    }


    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param int   $blockCount    number of blocks to fetch
     *
     * @return void
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, $blockCount = 0 ) {
        $im = new CRM_Contact_BAO_IM( );
        CRM_Contact_BAO_Common::getBlockValues( $im, 'im', $params, $values, $blockCount );
    }

}

?>