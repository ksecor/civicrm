<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
class CRM_Utils_Geocode_RPC {
    /**
     * server to retrieve the lat/long
     *
     * @var string
     * @static
     */
    static protected $_server = 'rpc.geocoder.us';

    /**
     * uri of service
     *
     * @var string
     * @static
     */
    static protected $_uri = '/service/xmlrpc';

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
        // we need a valid zipcode, state and country, else we ignore
        if ( ! CRM_Utils_Array::value( 'postal_code'    , $values  ) &&
             ! CRM_Utils_Array::value( 'state_province' , $values  ) &&
             ! CRM_Utils_Array::value( 'country'        , $values  ) ) {
            return false;
        }

        if ( $values['country'] != 'United States' ) {
            return false;
        }

        $string = CRM_Core_BAO_Address::format( $values, ', ' );
        if ( ! $string ) {
            return false;
        }

        $params   = array( new XML_RPC_Value( $string, 'string' ) );
        $message  = new XML_RPC_Message( 'geocode', $params );
        $client   = new XML_RPC_Client ( self::$_uri, self::$_server );
        $response = $client->send( $message );
        if ( ! $response && ! $response->faultCode( ) ) {
            return false;
        }

        $data = XML_RPC_decode( $response->value( ) );
        if ( ! CRM_Utils_Array::value( 0, $data ) ) {
            return false;
        }
        $data = $data[0];
        $values['geo_code_1'] = $data['lat' ];
        $values['geo_code_2'] = $data['long'];

        return true;
    }

}

?>
