<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
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
 * This is class to handle address related functions
 */
class CRM_Core_BAO_Address extends CRM_Core_DAO_Address 
{
    /**
     * Should we overwrite existing address, total hack for now
     * Please do not use this hack in other places, its totally gross
     */
    static $_overwrite = true;

    /**
     * takes an associative array and creates a address
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param boolean  $fixAddress   true if you need to fix (format) address values
     *                               before inserting in db
     *
     * @return array $blocks array of created address 
     * @access public
     * @static
     */
    static function create( &$params, $fixAddress, $entity = null ) 
    {
        if ( ! isset( $params['address'] ) ||
             ! is_array( $params['address'] ) ) {
            return;
        }

        $addresses = array( );
        if ( ! $entity ) {
            $contactId = $params['address']['contact_id'];
            //get all the addresses for this contact
            $addresses = self::allAddress( $contactId );
        } else {
            // get all address from location block
            $entityElements = array( 'entity_table' => $params['entity_table'],
                                     'entity_id'    => $params['entity_id']);
            $addresses = self::allEntityAddress( $entityElements );
        }

        $isPrimary = true;
        $isBilling = true;
        $blocks    = array( );

        require_once "CRM/Core/BAO/Block.php";
        foreach ( $params['address'] as $key => $value ) {
            if ( !is_array( $value ) ) {
                continue;
            }

            if ( ! empty( $addresses ) && array_key_exists( $value['location_type_id'], $addresses ) ) {
                $value['id'] = $addresses[ $value['location_type_id'] ];
            }
            
            $addressExists = self::dataExists( $value );

            if ( isset( $value['id'] ) && !$addressExists ) {
                //delete the existing record
                CRM_Core_BAO_Block::blockDelete( 'Address', array( 'id' => $value['id'] ) );
                continue;
            } else if ( !$addressExists ) {
                continue;
            }

            if ( $isPrimary && $value['is_primary'] ) {
                $isPrimary = false;
            } else {
                $value['is_primary'] = false;
            }
            
            if ( $isBilling && $value['is_billing'] ) {
                $isBilling = false;
            } else {
                $value['is_billing'] = false;
            }
            $value['contact_id'] = $contactId;

            $blocks[] = self::add( $value, $fixAddress );
        }       

        return $blocks;
    }

    /**
     * takes an associative array and adds phone 
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param boolean  $fixAddress   true if you need to fix (format) address values
     *                               before inserting in db
     *
     * @return object       CRM_Core_BAO_Address object on success, null otherwise
     * @access public
     * @static
     */
    static function add( &$params, $fixAddress ) 
    {
        $address =& new CRM_Core_DAO_Address( );

        // fixAddress mode to be done
        if ( $fixAddress ) {
            CRM_Core_BAO_Address::fixAddress( $params );
        }
        
        $address->copyValues($params);

        return $address->save( );
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
        if ( CRM_Utils_Array::value( 'postal_code', $params ) &&
             preg_match('/^(\d{4,5})[+-](\d{4})$/',
                        $params['postal_code'], 
                        $match) ) {
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
        if ( ( ! isset( $params['country_id'] ) || ! is_numeric( $params['country_id'] ) ) &&
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
        if ( isset( $params['state_province_id'] ) && 
             is_numeric( $params['state_province_id'] ) &&
             ( !isset($params['country_id']) || empty($params['country_id'])) ) {
            // since state id present and country id not present, hence lets populate it
            // jira issue http://issues.civicrm.org/jira/browse/CRM-56
            $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
            $stateProvinceDAO->id = $params['state_province_id'];
            $stateProvinceDAO->find(true);
            $params['country_id'] = $stateProvinceDAO->country_id;
        }

        //special check to ignore non numeric values if they are not
        //detected by formRule(sometimes happens due to internet latency), also allow user to unselect state/country
        if ( isset( $params['state_province_id'] ) && ! trim( $params['state_province_id'] ) ) {
            $params['state_province_id'] = 'null'; 
        } else if ( !is_numeric( $params['state_province_id'] ) ) {
            $params['state_province_id'] = null; 
        }

        if ( isset( $params['country_id'] ) && ! trim( $params['country_id'] ) ) {
            $params['country_id'] = 'null'; 
        } else if ( !is_numeric( $params['country_id'] ) ) {
            $params['country_id'] = null; 
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
            CRM_Utils_Address_USPS::checkAddress( $params );
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
     * @param array  $params    (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * 
     * @access public
     * @static
     */
    static function dataExists( &$params )
    {
        // if we should not overwrite, then the id is not relevant.
        if ( self::$_overwrite ) {
            //return true;
        }

        $config =& CRM_Core_Config::singleton( );
        foreach ($params as $name => $value) {
            if ( in_array ($name, array ('is_primary', 'location_type_id', 'id' ) ) ) {
                continue;
            } else if ( !empty($value) ) {
                if ( substr( $name, 0, 14 ) == 'state_province' ) {
                    // hack to skip  - type first
                    // letter(s) - for state_province CRM-2649
                    $selectOption = ts('- type first letter(s) -');
                    if ( $value != $selectOption ) {
                        return true;
                    }
                } else if ( substr( $name, 0, 7 ) == 'country' ) { // name could be country or country id
                    // make sure its different from the default country
                    // iso code
                    $defaultCountry     =& $config->defaultContactCountry( );
                    // full name
                    $defaultCountryName =& $config->defaultContactCountryName( );
                    
                    if ( $defaultCountry ) {
                        if ( $value == $defaultCountry     ||
                             $value == $defaultCountryName ||
                             $value == $config->defaultContactCountry ) {
                            // do nothing
                        } else {
                            return true;
                        }
                    } else {
                        // return if null default
                        return true;
                    }
                } else {
                    return true;
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
    static function &getValues( &$entityBlock, $microformat = false )
    {
        $address =& new CRM_Core_BAO_Address();
       
        if ( ! CRM_Utils_Array::value( 'entity_table' , $entityBlock ) ) {
            $address->contact_id = CRM_Utils_Array::value( 'contact_id' ,$entityBlock );
        } else {
            $addressIds = array();
            $addressIds = self::allEntityAddress($entityBlock );
           
            if( !empty($addressIds[1]) ) {
                $address->id = $addressIds[1];
            } else {
                return $addresses;
            }
        }
        $address->find( );

        while ( $address->fetch( ) ) {
            $stree = $address->street_address;
            $values = array( );
            CRM_Core_DAO::storeValues( $address, $values );
           
            // add state and country information: CRM-369
            if ( ! empty( $address->state_province_id ) ) {
                $address->state      = CRM_Core_PseudoConstant::stateProvinceAbbreviation( $address->state_province_id, false );
                $address->state_name = CRM_Core_PseudoConstant::stateProvince( $address->state_province_id, false );
            }

            if ( ! empty( $address->country_id ) ) {
                $address->country = CRM_Core_PseudoConstant::country( $address->country_id );
                
                //get world region 
                $regionId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Country', $address->country_id, 'region_id' );
                
                $address->world_region = CRM_Core_PseudoConstant::worldregion( $regionId );
            }
            
            $address->addDisplay( $microformat );

            // FIXME: not sure whether non-DB values are safe to store here
            // if so, we should store state_province and country as well and
            // get rid of the relevant CRM_Contact_BAO_Contact::resolveDefaults()'s code

            $values['display'] = $address->display;

            $addresses[$address->location_type_id] = $values;
        }
        return $addresses;
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
                        'state_province_name'    => isset($this->state_name) ? $this->state_name : "",
                        'state_province'         => isset($this->state) ? $this->state : "",
                        'postal_code'            => isset($this->postal_code) ? $this->postal_code : "",
                        'postal_code_suffix'     => isset($this->postal_code_suffix) ? $this->postal_code_suffix : "",
                        'country'                => isset($this->country) ? $this->country : "",
                        'world_region'           => isset($this->world_region) ? $this->world_region : ""
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
    static function setOverwrite( $overwrite ) 
    {
        self::$_overwrite = $overwrite;
    }

    /**
     * Get all the addresses for a specified contact_id, with the primary address being first
     *
     * @param int $id the contact id
     *
     * @return array  the array of adrress data
     * @access public
     * @static
     */
    static function allAddress( $id ) 
    {
        if ( !$id ) {
            return null;
        }

        $query = "
SELECT civicrm_address.id as address_id, civicrm_address.location_type_id as location_type_id
FROM civicrm_contact, civicrm_address 
WHERE civicrm_address.contact_id = civicrm_contact.id AND civicrm_contact.id = %1
ORDER BY civicrm_address.is_primary DESC, civicrm_address.location_type_id DESC, address_id ASC";
        $params = array( 1 => array( $id, 'Integer' ) );

        $addresses = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch( ) ) {
            $addresses[$dao->location_type_id] = $dao->address_id;
        }
        return $addresses;
    }
    
     /**
     * Get all the addresses for a specified location_block id, with the primary address being first
     *
     * @param array $entityElements the array containing entity_id and
     * entity_table name
     *
     * @return array  the array of adrress data
     * @access public
     * @static
     */
    static function allEntityAddress( &$entityElements ) 
    {
        if ( empty($entityElements) ) {
            return $addresses;
        }
        
        $entityId    = $entityElements['entity_id'];
        $entityTable = $entityElements['entity_table'];

        $sql = "
SELECT civicrm_address.id as address_id    
FROM civicrm_loc_block loc, civicrm_location_type ltype, civicrm_address, {$entityTable} ev
WHERE ev.id = %1
  AND loc.id = ev.loc_block_id
  AND civicrm_address.id IN (loc.address_id, loc.address_2_id)
  AND ltype.id = civicrm_address.location_type_id
ORDER BY civicrm_address.is_primary DESC, civicrm_address.location_type_id DESC, address_id ASC ";
               
        $params = array( 1 => array( $entityId, 'Integer' ) );
        $addresses = array( );
        $dao =& CRM_Core_DAO::executeQuery( $sql, $params );
        $locationCount = 1;
        while ( $dao->fetch( ) ) {
            $addresses[$locationCount] = $dao->address_id;
            $locationCount++;
        }
        return $addresses;
    }

}


