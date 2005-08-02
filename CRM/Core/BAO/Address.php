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

        /* Split the zip and +4, if it's in US format */
        if (preg_match('/^(\d{4,5})[+-](\d{4})$/',
            $params['location'][$locationId]['address']['postal_code'], 
            $match)) 
        {
            $params['location'][$locationId]['address']['postal_code'] =
                $match[1];
            $params['location'][$locationId]['address']['postal_code_suffix'] =
                $match[2];
        }
        // add latitude and longitude and format address if needed
        $config =& CRM_Core_Config::singleton( );
        if ( $config->geocodeMethod == CRM_Core_Config::GEOCODE_ZIP ) {
            CRM_Utils_Geocode_ZipTable::format( $params['location'][$locationId]['address'] );
        } else if ( $config->geocodeMethod == CRM_Core_Config::GEOCODE_RPC ) {
            CRM_Utils_Geocode_RPC::format( $params['location'][$locationId]['address'] );
        }

        if ( $address->copyValues($params['location'][$locationId]['address']) ) {
            // we copied only null stuff, so we delete the object
            $address->delete( );
            return null;
        }
        
        // currently copy values populates empty fields with the string "null"
        // and hence need to check for the string null
        if ( is_numeric( $address->state_province_id ) && !isset($address->country_id)) {
            // since state id present and country id not present, hence lets populate it
            // jira issue http://objectledge.org/jira/browse/CRM-56
            $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
            $stateProvinceDAO->id = $address->state_province_id; 
            $stateProvinceDAO->find(true);
            $address->country_id = $stateProvinceDAO->country_id;
        }

        $address->county_id = $address->geo_coord_id = 1;


        return $address->save();
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
    static function getValues(&$params, &$values, &$ids, $blockCount=0)
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
