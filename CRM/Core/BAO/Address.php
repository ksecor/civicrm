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

require_once 'CRM/Core/DAO/Address.php';

/**
 * BAO object for crm_address table
 */
class CRM_Core_BAO_Address extends CRM_Core_DAO_Address {
    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $ids            the array that holds all the db ids
     * @param array  $locationId     
     *
     * @return object CRM_Core_BAO_Address object
     * @access public
     * @static
     */
    static function add(&$params, &$ids, $locationId)
    {
        if ( ! self::dataExists($params, $locationId, $ids) ) {
            return null;
        }

        $address              =& new CRM_Core_BAO_Address();
        $address->location_id = $params['location'][$locationId]['id'];
        $address->id          = CRM_Utils_Array::value('address', $ids['location'][$locationId]);

        CRM_Core_BAO_Address::fixAddress( $params['location'][$locationId]['address'] );

        if ( $address->copyValues($params['location'][$locationId]['address']) ) {
            // we copied only null stuff, so we delete the object
            $address->delete( );
            return null;
        }

        return $address->save();
    }

    /**
     * format the address params to have reasonable values
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return void
     * @access public
     * @static
     */
    static function fixAddress( &$params ) {
        /* Split the zip and +4, if it's in US format */
        if (CRM_Utils_Array::value( 'postal_code', $params ) &&
            preg_match('/^(\d{4,5})[+-](\d{4})$/',
                       $params['postal_code'], 
                       $match)) {
            $params['postal_code']        = $match[1];
            $params['postal_code_suffix'] = $match[2];
        }

        // currently copy values populates empty fields with the string "null"
        // and hence need to check for the string null
        if ( is_numeric( $params['state_province_id'] ) && ( !isset($params['country_id']) || empty($params['country_id']))) {
            // since state id present and country id not present, hence lets populate it
            // jira issue http://objectledge.org/jira/browse/CRM-56
            $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
            $stateProvinceDAO->id = $params['state_province_id'];
            $stateProvinceDAO->find(true);
            $params['country_id'] = $stateProvinceDAO->country_id;
        }

        // add state and country names from the ids
        if ( is_numeric( $params['state_province_id'] ) ) {
             $params['state_province'] = CRM_Core_PseudoConstant::stateProvince( $params['state_province_id'] );
        }

        if ( is_numeric( $params['country_id'] ) ) {
             $params['country'] = CRM_Core_PseudoConstant::country($params['country_id']);
        }
        $params['county_id'] = $params['geo_coord_id'] = 1;

        // add latitude and longitude and format address if needed
        $config =& CRM_Core_Config::singleton( );
        if ( ! empty( $config->geocodeMethod ) ) {
            require_once( str_replace('_', DIRECTORY_SEPARATOR, $config->geocodeMethod ) . '.php' );
            eval( $config->geocodeMethod . '::format( $params );' );
        } 
   }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $locationId     
     * @param array  $ids            the array that holds all the db ids
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists(&$params, $locationId, &$ids)
    {
        if ( is_array( $ids ) && CRM_Utils_Array::value('address', $ids['location'][$locationId]) ) {
            return true;
        }

        // return if no data present
        if (! array_key_exists('address' , $params['location'][$locationId])) {
            return false;
        }

        foreach ($params['location'][$locationId]['address'] as $name => $value) {
            // ignore only country id for now
            // since we set a default
            if ( $name == 'country_id' ) {
                continue;
            }

            if (!empty($value)) {
                return true;
            }
        }
        
        return false;
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
     * @return void
     * @access public
     * @static
     */
    static function &getValues(&$params, &$values, &$ids, $blockCount=0)
    {
        $address =& new CRM_Core_BAO_Address();
        $address->copyValues($params);

        $flatten = false;
        if (empty($blockCount)) {
            $flatten = true;
        }
        
        // we first get the primary location due to the order by clause
        if ($address->find(true)) {
            $ids['address'] = $address->id;
            if ($flatten) {
                CRM_Core_DAO::storeValues( $address, $values );
            } else {
                $values['address'] = array();
                CRM_Core_DAO::storeValues( $address, $values['address'] );
            }
            // add state and country information: CRM-369
            if ( ! empty( $address->state_province_id ) ) {
                $address->state = CRM_Core_PseudoConstant::stateProvince( $address->state_province_id );
            }
            if ( ! empty( $address->country_id ) ) {
                $address->country = CRM_Core_PseudoConstant::country( $address->country_id );
            }

            // add address's display form based on the $config->addressFormat setting
            // FIXME: whe should keep $allowedFields' keys and the default $config->addressSequence in one place
            $config =& CRM_Core_Config::singleton();

            $fullPostalCode = $address->postal_code;
            if ($address->postal_code_suffix) $fullPostalCode .= "-$address->postal_code_suffix";

            // this is a gross hack, so please forgive me: lobo:)
            // we need to add a comma between city and state only if both are non empty
            // so we just add it to the city field
            if ( $address->city && $address->state ) {
                $city = $address->city . ',';
            } else {
                $city = $address->city;
            }
            
            $replacements = array(
                'street_address'         => $address->street_address,
                'supplemental_address_1' => $address->supplemental_address_1,
                'supplemental_address_2' => $address->supplemental_address_2,
                'city'                   => $city,
                'state_province'         => $address->state,
                'postal_code'            => $fullPostalCode,
                'country'                => $address->country
            );

            $address->display = str_replace(array_keys($replacements), $replacements, $config->addressFormat);
            while ( substr_count( $address->display, "\n\n" ) ) {
                // note not sure why the below does not replace all double new lines, seems like a php bug
                $address->display = trim( str_replace("\n\n", "\n", $address->display) );
            }
            // FIXME: not sure whether non-DB values are safe to store here
            // if so, we should store state_province and country as well and
            // get rid of the relevant CRM_Contact_BAO_Contact::resolveDefaults()'s code
            if ($flatten) {
                $values['display'] = $address->display;
            } else {
                $values['address']['display'] = $address->display;
            }
            return $address;
        }
        return null;
    }

    /**
     * Given an array of address values (getValues() style), return a formatted
     * string of the address.  TODO: make this i18n-friendly
     *
     * @param array $params     The getValues() array, after resolving
     *                          state/country
     * @param string $separator The string used for separating lines
     * @return string           The formatted address string
     * @access public
     * @static
     */
    static function format(&$params, $separator = "\n") {
        static $elements = array( 'street_address', 'supplemental_address_1',
                                  'supplemental_address_2', 'supplemental_address_3',
                                  'city', 'state_province', 'postal_code' );

        $formatted  = array( );
        foreach ( $elements as $e ) {
            if ( ! empty( $params[$e] ) && ( $params[$e] != 'null' ) ) {
                $formatted[] = $params[$e];
            }
        }
        return implode($separator, $formatted);
    }
}

?>
