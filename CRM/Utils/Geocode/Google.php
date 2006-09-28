<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */
 
/**
 * Class that uses geocoder.us to retrieve the lat/long of an address
 */
class CRM_Utils_Geocode_Google {
    /**
     * server to retrieve the lat/long
     *
     * @var string
     * @static
     */
    static protected $_server = 'maps.google.com';

    /**
     * uri of service
     *
     * @var string
     * @static
     */
    static protected $_uri = '/maps/geo?q=';
    
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
        require_once 'CRM/Utils/Array.php';
        // we need a valid state and country, else we ignore
        if ( ! CRM_Utils_Array::value( 'state_province' , $values  ) &&
             ! CRM_Utils_Array::value( 'country'        , $values  ) ) {
            return false;
        }
        if ( $values['country'] != 'United States' ) {
            return false;
        }
        
        $config =& CRM_Core_Config::singleton( );
        
        $arg = "&output=xml&key=" . urlencode( $config->mapAPIKey );
        
        $add = '';

        if (  CRM_Utils_Array::value( 'street_address', $values ) ) {
            $add = urlencode( str_replace('', '+', $values['street_address']) );
        }
        
         if (  CRM_Utils_Array::value( 'city', $values ) ) { 
             $add .= '+' . urlencode( str_replace('', '+', $values['city']) );
         }
         
         if (  CRM_Utils_Array::value( 'state_province', $values ) ) { 
             $add .= '+' . urlencode( str_replace('', '+', $values['state_province']) );
         }
         
         if (  CRM_Utils_Array::value( 'postal_code', $values ) ) { 
             $add .= '+' .urlencode( str_replace('', '+', $values['postal_code']) );
         }
        
         $query = 'http://' . self::$_server . self::$_uri . '?' . $add . $arg;

         require_once 'HTTP/Request.php';
         $request =& new HTTP_Request( $query );
         $request->sendRequest( );
         $string = $request->getResponseBody( );
         $xml = simplexml_load_string( $string );
         
         $ret = array( );
         $val = array( );
         
         $ret = $xml->Response->Placemark->Point->children();

         $val = explode(',', (string)$ret[0]);

         if ( $val[0] && $val[1] ) {
             $values['geo_code_1'] = $val[0];
             $values['geo_code_2'] = $val[1];
         }
         return true;
     }
}
?>
