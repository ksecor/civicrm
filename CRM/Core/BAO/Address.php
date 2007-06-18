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

require_once 'CRM/Core/DAO/Address.php';

/**
 * BAO object for crm_address table
 */
class CRM_Core_BAO_Address extends CRM_Core_DAO_Address {
    /**
     * Should we overwrite existing address, total hack for now
     * Please do not use this hack in other places, its totally gross
     */
    static $_overwrite = true;

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
    static function add(&$params, &$ids, $locationId, $fixAddress = true)
    {
        if ( ! self::dataExists($params, $locationId, $ids) ) {
            return null;
        }

        if ( empty($params['location'][$locationId]['address']) ) {
            return ;
        }

        $address              =& new CRM_Core_BAO_Address();
        $address->location_id = $params['location'][$locationId]['id'];
        $address->id          = CRM_Utils_Array::value('address', $ids['location'][$locationId]);
        
        if ( $fixAddress ) {
            CRM_Core_BAO_Address::fixAddress( $params['location'][$locationId]['address'] );
        }
      
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
    static function fixAddress( &$params ) 
    {
        /* Split the zip and +4, if it's in US format */
        if (CRM_Utils_Array::value( 'postal_code', $params ) &&
            preg_match('/^(\d{4,5})[+-](\d{4})$/',
                       $params['postal_code'], 
                       $match)) {
            $params['postal_code']        = $match[1];
            $params['postal_code_suffix'] = $match[2];
        }

        // add state_id if state is set
        if ( ( ! isset( $params['state_province_id'] ) || ! is_numeric( $params['state_province_id'] ) )
             && isset( $params['state_province'] ) ) {
            $state_province       = & new CRM_Core_DAO_StateProvince();
            $state_province->name = $params['state_province'];
            if ( ! $state_province->find(true) ) {
                $state_province->name = null;
                $state_province->abbreviation = $params['state_province'];
                $state_province->find(true);
            }
            $params['state_province_id'] = $state_province->id;
        }

        // add country id if not set
        if ( isset( $params['country_id'] ) && ! is_numeric( $params['country_id'] ) &&
             isset( $params['country'] ) ) {
            $country       = & new CRM_Core_DAO_Country( );
            $country->name = $params['country'];
            if ( ! $country->find(true) ) {
                $country->name = null;
                $country->iso_code = $params['country'];
                $country->find(true);
            }
            $params['country_id'] = $country->id;
        }
            
        // currently copy values populates empty fields with the string "null"
        // and hence need to check for the string null
        if (isset( $params['state_province_id'] ) && 
            is_numeric( $params['state_province_id'] ) &&
            ( !isset($params['country_id']) || empty($params['country_id']))) {
            // since state id present and country id not present, hence lets populate it
            // jira issue http://objectledge.org/jira/browse/CRM-56
            $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
            $stateProvinceDAO->id = $params['state_province_id'];
            $stateProvinceDAO->find(true);
            $params['country_id'] = $stateProvinceDAO->country_id;
        }

        // add state and country names from the ids
        if ( isset( $params['state_province_id'] ) && is_numeric( $params['state_province_id'] ) ) {
            $params['state_province'] = CRM_Core_PseudoConstant::stateProvinceAbbreviation( $params['state_province_id'] );
        }
        
        if ( isset( $params['country_id'] ) && is_numeric( $params['country_id'] ) ) {
             $params['country'] = CRM_Core_PseudoConstant::country($params['country_id']);
        }
        
        $config =& CRM_Core_Config::singleton( );

        require_once 'CRM/Core/BAO/Preferences.php';
        $asp = CRM_Core_BAO_Preferences::value( 'address_standardization_provider' );
        // clean up the address via USPS web services if enabled
        if ($asp === 'USPS') {
            require_once 'CRM/Utils/Address/USPS.php';
            CRM_Utils_Address_USPS::checkAddress($params);
        }

        // add latitude and longitude and format address if needed
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
     * 
     * @access public
     * @static
     */
    static function dataExists(&$params, $locationId, &$ids)
    {
        // if we should not overwrite, then the id is not relevant.
        if ( self::$_overwrite && is_array( $ids ) && CRM_Utils_Array::value('address', $ids['location'][$locationId]) ) {
            return true;
        }

        // return if no data present
        if (! array_key_exists('address' , $params['location'][$locationId])) {
            return false;
        }

        foreach ($params['location'][$locationId]['address'] as $name => $value) {
            if (! empty($value) ) {
                if ( $name !='country_id' ) {
                    return true;
                } else {
                    // make sure its different from the default country
                    $config =& CRM_Core_Config::singleton( );
                    if ( $config->defaultContactCountry ) {
                        if ( $value != $config->defaultContactCountry ) {
                            return true;
                        }
                    } else {
                        // return if null default
                        return true;
                    }
                }
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
    static function &getValues(&$params, &$values, &$ids, $blockCount=0, $microformat = false)
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
                $address->state      = CRM_Core_PseudoConstant::stateProvinceAbbreviation( $address->state_province_id );
                $address->state_name = CRM_Core_PseudoConstant::stateProvince( $address->state_province_id );
            }
            if ( ! empty( $address->country_id ) ) {
                $address->country = CRM_Core_PseudoConstant::country( $address->country_id );
            }

            $address->addDisplay( $microformat );

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
        return CRM_Core_DAO::$_nullObject;
    }
    
    /**
     * Add the formatted address to $this-> display
     *
     * @param NULL
     * 
     * @return void
     *
     * @access public
     *
     */
    function addDisplay( $microformat = false )
    {


        require_once 'CRM/Utils/Address.php';
        $fields = array(
            'address_id'             => $this->id, // added this for CRM 1200
            'street_address'         => $this->street_address,
            'supplemental_address_1' => $this->supplemental_address_1,
            'supplemental_address_2' => $this->supplemental_address_2,
            'city'                   => $this->city,
            'state_province_name'    => $this->state_name,
            'state_province'         => $this->state,
            'postal_code'            => $this->postal_code,
            'postal_code_suffix'     => $this->postal_code_suffix,
            'country'                => $this->country
            );

        if( isset( $this->county_id ) && $this->county_id ) {
            $fields['county'] = CRM_Core_Pseudoconstant::county($this->county_id);
        } else {
            $fields['county'] = null;
        }

        $this->display = CRM_Utils_Address::format($fields, null, $microformat);
    }

    /**
     *
     * 
     *
     */
    
    static function setOverwrite( $overwrite ) {
        self::$_overwrite = $overwrite;
    }

}

?>
