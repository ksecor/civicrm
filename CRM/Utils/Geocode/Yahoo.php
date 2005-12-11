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

/**
 * Class that uses geocoder.us to retrieve the lat/long of an address
 */
class CRM_Utils_Geocode_Yahoo {
    /**
     * server to retrieve the lat/long
     *
     * @var string
     * @static
     */
    static protected $_server = 'api.local.yahoo.com';

    /**
     * uri of service
     *
     * @var string
     * @static
     */
    static protected $_uri = '/MapsService/V1/geocode';

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
        // we need a valid state and country, else we ignore
        if ( ! CRM_Utils_Array::value( 'state_province' , $values  ) &&
             ! CRM_Utils_Array::value( 'country'        , $values  ) ) {
            return false;
        }

        if ( $values['country'] != 'United States' ) {
            return false;
        }

        $config =& CRM_Core_Config::singleton( );

        $arg = array( );
        $arg[] = "appid=" . urlencode( $config->mapAPIKey );

        if (  CRM_Utils_Array::value( 'street_address', $values ) ) {
            $arg[] = "street=" . urlencode( $values['street_address'] );
        }

        if (  CRM_Utils_Array::value( 'city', $values ) ) { 
            $arg[] = "city=" . urlencode( $values['city'] );
        }

        if (  CRM_Utils_Array::value( 'state_province', $values ) ) { 
            $arg[] = "state=" . urlencode( $values['state_province'] );
        }

        if (  CRM_Utils_Array::value( 'postal_code', $values ) ) { 
            $arg[] = "zip=" . urlencode( $values['postal_code'] );
        }

        $args = implode( '&', $arg );
        $query = 'http://' . self::$_server . self::$_uri . '?' . $args;

        require_once 'HTTP/Request.php';
        $request =& new HTTP_Request( $query );
        $request->sendRequest( );
        $string = $request->getResponseBody( );
        $xml = simplexml_load_string( $string );
        $ret['precision'] = (string)$xml->Result['precision'];
        foreach($xml->Result->children() as $key=>$val) {
            if(strlen($val)) $ret[(string)$key] =  (string)$val;
        } 

        $values['geo_code_1'] = $ret['Latitude' ];
        $values['geo_code_2'] = $ret['Longitude'];

        return true;
    }

}

?>
