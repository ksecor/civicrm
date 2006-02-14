<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'XML/RPC.php';

/**
 * Class that uses geocoder.us to retrieve the lat/long of an address
 */
class CRM_Utils_Geocode_ZipTable {
    /**
     * zipcode table name
     *
     * @var string
     * @static
     */
    static protected $_tableName = 'zipcodes';

    /**
     * function that takes an address object and gets the latitude / longitude for this
     * address. Note that at a later stage, we could make this function also clean up
     * the address into a more valid format
     *
     * @param object $address
     *
     * @return boolean true if we modified the address, false otherwise
     * @static
     */
    static function format( &$values ) {
        // we need a valid zipcode and country, else we ignore
        if ( ! CRM_Utils_Array::value( 'postal_code', $values  ) &&
             ! CRM_Utils_Array::value( 'country'    , $values  ) &&
             $values['country'] != 'United States' ) {
            return false;
        }

        $postalCode = trim( $values['postal_code'] );
        if ( empty( $postalCode ) ) {
            return false;
        }
        
        $query = 'SELECT latitude, longitude FROM zipcodes WHERE zip = ' .
            CRM_Utils_Type::escape( $postalCode, 'String' );
        $dao =& CRM_Core_DAO::executeQuery( $query );

        if ( $dao->fetch( ) ) {
            $values['geo_code_1'] = $dao->latitude ;
            $values['geo_code_2'] = $dao->longitude;
            return true;
        }
        return false;
    }
}


