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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/DAO/Location.php';
require_once 'CRM/Contact/DAO/Address.php';
require_once 'CRM/Contact/DAO/Phone.php';
require_once 'CRM/Contact/DAO/Email.php';

/**
 * rare case where because of inheritance etc, we actually store a reference
 * to the dao object rather than inherit from it
 */

class CRM_Contact_BAO_Contact extends CRM_Contact_DAO_Contact 
{

    /**
     * the types of communication preferences
     *
     * @var array
     */
    static $_commPrefs = array( 'do_not_phone', 'do_not_email', 'do_not_mail' );

    function __construct()
    {
        parent::__construct();
    }
    
    function getSearchRows($offset, $rowCount, $sort)
    {
        // we need to run the loop thru the num rows with offset in mind.
        $rows = array();
        $str_select = $str_from = $str_where = $str_order = $str_limit = '';
        
        $str_select = "SELECT crm_contact.id as crm_contact_id,
                              crm_contact.sort_name as crm_contact_sort_name,
                              crm_address.street_address as crm_address_street_address,
                              crm_address.city as crm_address_city,
                              crm_address.postal_code as crm_address_postal_code,
                              crm_state_province.abbreviation as crm_state_province_name,
                              crm_country.name as crm_country_name,
                              crm_email.email as crm_email_email,
                              crm_phone.phone as crm_phone_phone,
                              crm_contact.contact_type as crm_contact_contact_type";

        $str_from = " FROM crm_contact 
                        LEFT OUTER JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                        LEFT OUTER JOIN crm_address ON (crm_location.id = crm_address.location_id )
                        LEFT OUTER JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT OUTER JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT OUTER JOIN crm_state_province ON (crm_address.state_province_id = crm_state_province.id)
                        LEFT OUTER JOIN crm_country ON (crm_address.country_id = crm_country.id)";

        // add where clause if any condition exists..
        if (strlen($this->contact_type) || strlen(trim($this->sort_name))){
            $str_where = " WHERE ";
        }

        // adding contact_type in where
        if (strlen($this->contact_type)) {
            $str_where .= " crm_contact.contact_type ='".$this->contact_type."'";
        }

        // adding sort_name
        if (strlen(trim($this->sort_name))) {
            if (strlen($this->contact_type)) { // check if contact_type is present..
                $str_where .= " AND LOWER(crm_contact.sort_name) like '%".strtolower($this->sort_name)."%'";
            } else {
                $str_where .= " LOWER(crm_contact.sort_name) like '%".strtolower($this->sort_name)."%'";
            }   
        }

        $str_order = " ORDER BY " . $sort->orderBy(); 
        $str_limit = " LIMIT $offset, $rowCount ";

        // building the query string
        $query_string = $str_select.$str_from.$str_where.$str_order.$str_limit;
        $this->query($query_string);

        while($this->fetch()) {
            $row = array();
            // $row['contact_id'] = $this->crm_contact_id;
            $row['sort_name'] = $this->crm_contact_sort_name;
            $row['street_address'] = $this->crm_address_street_address;
            $row['city'] = $this->crm_address_city;
            $row['state'] = $this->crm_state_province_name;
            $row['postal_code'] = $this->crm_address_postal_code;
            $row['country'] = $this->crm_country_name;
            $row['email'] = $this->crm_email_email;
            $row['phone'] = $this->crm_phone_phone;
            
            $row['edit']  = 'index.php?q=/crm/contact/edit&cid='.$this->crm_contact_id;
            $row['view']  = 'index.php?q=/crm/contact/view&cid='.$this->crm_contact_id;
            $str_type = "";
            switch ($this->crm_contact_contact_type) {
            case 'Individual' :
                $str_type = '(I)';
                break;
            case 'Household' :
                $str_type = '(H)';
                break;
            case 'Organization' :
                $str_type = '(O)';
                break;
                
            }

            $row['c_type'] = $str_type;
            
            $rows[] = $row;
        }
        return $rows;
    }
    


    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function add( &$params, &$ids ) {
        $contact = new CRM_Contact_BAO_Contact( );
        
        $contact->copyValues( $params );

        if ($contact->contact_type == 'Individual') {
            $contact->sort_name = CRM_Array::value( 'last_name', $params, '' ) . ', ' . CRM_Array::value( 'first_name', $params, '' );
        } else if ($contact->contact_type == 'Household') {
            $contact->sort_name = CRM_Array::value( 'household_name', $params, '' ) ;
        } else {
            $contact->sort_name = CRM_Array::value( 'organization_name', $params, '' ) ;
        } 

        //$contact->sort_name = CRM_Array::value( 'last_name', $params, '' ) . ', ' . CRM_Array::value( 'first_name', $params, '' );

        $privacy = CRM_Array::value( 'privacy', $params );
        if ( $privacy && is_array( $privacy ) ) {
            foreach ( self::$_commPrefs as $name ) {
                if ( array_key_exists( $name, $privacy ) ) {
                    $contact->$name = $privacy[$name];
                }
            }
        }

        $contact->domain_id = CRM_Array::value( 'domain' , $ids, 1 );
        $contact->id        = CRM_Array::value( 'contact', $ids );
        return $contact->save( );
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contact_BAO_Contact|null the found object or null
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids ) {
        $contact = new CRM_Contact_BAO_Contact( );

        $contact->copyValues( $params );
        if ( $contact->find(true) ) {
            $ids['contact'] = $contact->id;
            $ids['domain' ] = $contact->domain_id;

            $contact->storeValues( $values );

            $privacy = array( );
            foreach ( self::$_commPrefs as $name ) {
                if ( isset( $contact->$name ) ) {
                    $privacy[$name] = $contact->$name;
                }
            }
            if ( !empty($privacy) ) {
                $values['privacy'] = $privacy;
            }
            return $contact;
        }
        return null;
    }

    /**
     * takes an associative array and creates a contact object and all the associated
     * derived objects (i.e. individual, location, email, phone etc)
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     * @param int   $maxLocationBlocks the maximum number of location blocks to process
     *
     * @return object CRM_Contact_BAO_Individual object 
     * @access public
     * @static
     */
    static function create( &$params, &$ids, $maxLocationBlocks ) {
        CRM_DAO::transaction( 'BEGIN' );
        
        $contact = self::add( $params, $ids );
        
        $params['contact_id'] = $contact->id;

        // invoke the add operator on the contact_type class
        eval( '$contact->contact_type_object = CRM_Contact_BAO_' . $params['contact_type'] . '::add( $params, $ids );' );

        $locations = array( );
        for ($locationId= 1; $locationId <= $maxLocationBlocks; $locationId++) { // start of for loop for location
            $locations[] = CRM_Contact_BAO_Location::add( $params, $ids, $locationId );
        }
        $contact->locations = $locations;

        CRM_DAO::transaction( 'COMMIT' );

        return $contact;
    }

    static function resolveDefaults( &$defaults, $reverse = false ) {
        if ( array_key_exists( 'location', $defaults ) ) {
            $locations =& $defaults['location'];
            foreach ( $locations as $index => &$location ) {
                // self::lookupValue( $location, 'location_type', CRM_SelectValues::$locationType, $reverse );
                self::lookupValue( $location, 'location_type', CRM_SelectValues::getLocationType(), $reverse );
                if ( array_key_exists( 'address', $location ) ) {
                    // self::lookupValue( $location['address'], 'state_province', CRM_SelectValues::$stateProvince, $reverse );
                    self::lookupValue( $location['address'], 'state_province', CRM_SelectValues::getStateProvince(), $reverse );
                    // self::lookupValue( $location['address'], 'country'       , CRM_SelectValues::$country      , $reverse );
                    self::lookupValue( $location['address'], 'country'       , CRM_SelectValues::getCountry()      , $reverse );
                    self::lookupValue( $location['address'], 'county'        , CRM_SelectValues::$county       , $reverse );
                }
                if ( array_key_exists( 'im', $location ) ) {
                    $ims =& $location['im'];
                    foreach ( $ims as $innerIndex => &$im ) {
                        // self::lookupValue( $im, 'provider', CRM_SelectValues::$imProvider , $reverse );
                        self::lookupValue( $im, 'provider', CRM_SelectValues::getIMProvider(), $reverse );
                    }
                }
            }
        }
    }

    static function lookupValue( &$defaults, $property, &$lookup, $reverse ) {
        $id = $property . '_id';

        $src = $reverse ? $property : $id;
        $dst = $reverse ? $id       : $property;

        if ( ! array_key_exists( $src, $defaults ) ) {
            return;
        }

        $look = $reverse ? array_flip( $lookup ) : $lookup;
        if ( ! array_key_exists( $defaults[$src], $look ) ) {
            return;
        }
        $defaults[$dst] = $look[$defaults[$src]];
    }


}

?>