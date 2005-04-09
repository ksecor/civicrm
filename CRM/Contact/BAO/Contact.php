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
require_once 'CRM/DAO/Note.php';

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

    /**
     * create and query the db for a simple contact search
     *
     * @param int      $action   the type of action links
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     *
     * @return CRM_Contact_DAO_Contact 
     * @access public
     */
    function basicSearchQuery($offset, $rowCount, $sort)
    {
        $str_select = $str_from = $str_where = $str_order = $str_limit = '';
        
        $str_select = "SELECT crm_contact.id as contact_id,
                              crm_contact.sort_name as sort_name,
                              crm_address.street_address as street_address,
                              crm_address.city as city,
                              crm_address.postal_code as postal_code,
                              crm_state_province.abbreviation as state,
                              crm_country.name as country,
                              crm_email.email as email,
                              crm_phone.phone as phone,
                              crm_contact.contact_type as contact_type";

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
        return $this;
    }
    



    /**
     * create and query the db for an advanced contact search
     *
     * @param array    $formValues array of reference of the form values submitted
     * @param int      $action   the type of action links
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     *
     * @return CRM_Contact_DAO_Contact 
     * @access public
     */
    function advancedSearchQuery(&$formValues, $offset, $rowCount, $sort)
    {
        $str_select = $str_from = $str_where = $str_order = $str_limit = '';

        // stores all the "AND" clauses
        $andArray = array();

        CRM_Error::debug_var("formValues", $formValues);        

        $str_select = "SELECT crm_contact.id as contact_id,
                              crm_contact.sort_name as sort_name,
                              crm_address.street_address as street_address,
                              crm_address.city as city,
                              crm_address.postal_code as postal_code,
                              crm_state_province.abbreviation as state,
                              crm_country.name as country,
                              crm_email.email as email,
                              crm_phone.phone as phone,
                              crm_contact.contact_type as contact_type";

        $str_from = " FROM crm_contact 
                        LEFT JOIN crm_location ON crm_contact.id = crm_location.contact_id
                        LEFT JOIN crm_address ON crm_location.id = crm_address.location_id
                        LEFT JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT JOIN crm_state_province ON crm_address.state_province_id = crm_state_province.id
                        LEFT JOIN crm_country ON crm_address.country_id = crm_country.id ";



        /*
         * sample formValues for query 
         *
         * Get me all contacts of type individual or organization who are members of group 1 "Newsletter Subscribers"
         * and are categorized as "Non Profit" (catid 1) or "Volunteer" (catid 5) 

        $formValues = Array
            (
             [cb_contact_type] => Array
             (
              [Individual] => 1
              [Organization] => 1
              )
             
             [cb_group] => Array
             (
              [1] => 1
              )
             
             [cb_category] => Array
             (
              [1] => 1
              [5] => 1
              )
             
             [last_name] => 
             [first_name] => 
             [street_name] => 
             [city] => 
             [state_province] => 
             [country] => 
             [postal_code] => 
             [postal_code_low] => 
             [postal_code_high] => 
             )

        */



        // check for contact type restriction
        if ($formValues['cb_contact_type']) {
            $andArray['contact_type'] = "(contact_type IN (";
            foreach ($formValues['cb_contact_type']  as $k => $v) {
                $andArray['contact_type'] .= "'$k',"; 
            }            
            // replace the last comma with the parentheses.
            $andArray['contact_type'] = rtrim($andArray['contact_type'], ",");
            $andArray['contact_type'] .= "))";
        }
        
        // check for group restriction
        if ($formValues['cb_group']) {
            $andArray['group'] = "(group_id IN (";
            foreach ($formValues['cb_group']  as $k => $v) {
                // going with the OR case for this version
                // i.e. it'll select all contacts who are members of group 1 OR group 2
                // if we want all contacts who are members of group 1 AND group 2 then'll
                // we'll have to use self joins
                $andArray['group'] .= "$k,"; 
            }
            $andArray['group'] = rtrim($andArray['group'], ",");
            $andArray['group'] .= "))";
            $str_from .= " LEFT JOIN crm_group_contact ON crm_contact.id = crm_group_contact.contact_id ";
        }

        // check for category restriction
        if ($formValues['cb_category']) {
            $andArray['category'] .= "(category_id IN (";
            foreach ($formValues['cb_category'] as $k => $v) {
                $andArray['category'] .= "$k,"; 
            }
            $andArray['category'] = rtrim($andArray['category'], ",");
            $andArray['category'] .= "))"; 
            $str_from .= " LEFT JOIN crm_entity_category ON crm_contact.id = crm_entity_category.entity_id ";
        }


        // check for last name, as of now only working with sort name
        if ($formValues['last_name']) {
            CRM_Error::debug_var("last_name", $formValues['last_name']);
            $andArray['last_name'] = " crm_contact.sort_name LIKE '%". $formValues['last_name'] ."%'";
        }

        // street_name
        if ($formValues['street_name']) {
            $andArray['street_name'] = " crm_address.street_name LIKE '%". $formValues['street_name'] ."%'";
        }


        foreach ($andArray as $v) {
            $str_where .= " AND $v ";
        }


        // skip the following for now
        // last_name, first_name, street_name, city, state_province, country, postal_code, postal_code_low, postal_code_high

        $str_where = preg_replace("/AND|OR/", "WHERE", $str_where, 1);

        $str_order = " ORDER BY " . $sort->orderBy(); 
        $str_limit = " LIMIT $offset, $rowCount ";

        // building the query string
        $query_string = $str_select . $str_from . $str_where . $str_order . $str_limit;

        CRM_Error::debug_var("query_string", $query_string);

        $this->query($query_string);

        return $this;
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

        CRM_Error::le_method();

        CRM_Error::debug_var("params", $params);

        $contact = new CRM_Contact_BAO_Contact( );

        $contact->copyValues( $params );

        if ( $contact->find(true) ) {

            CRM_Error::debug_log_message("found contact");

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
     * @return object CRM_Contact_BAO_Contact object 
     * @access public
     * @static
     */
    static function create( &$params, &$ids, $maxLocationBlocks ) {
        CRM_DAO::transaction( 'BEGIN' );
        
        $contact = self::add( $params, $ids );
        
        $params['contact_id'] = $contact->id;

        // invoke the add operator on the contact_type class
        eval( '$contact->contact_type_object = CRM_Contact_BAO_' . $params['contact_type'] . '::add( $params, $ids );' );

        $location = array( );
        for ($locationId= 1; $locationId <= $maxLocationBlocks; $locationId++) { // start of for loop for location
            $location[$locationId] = CRM_Contact_BAO_Location::add( $params, $ids, $locationId );
        }
        $contact->location = $location;

        // add notes
        $contact->note = CRM_Contact_BAO_Note::add( $params, $ids );

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

    /**
     * This function is used to convert associative array names to values
     * and vice-versa.
     *
     * This function is used by both the web form layer and the api. Note that
     * the api needs the name => value conversion, also the view layer typically
     * requires value => name conversion
     */
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

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the name / value pairs
     *                        in a hierarchical manner
     * @param array $ids      (reference) the array that holds all the db ids
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults, &$ids ) {
        $contact = CRM_Contact_BAO_Contact::getValues( $params, $defaults, $ids );

        unset($params['id']);
        eval( '$contact->contact_type_object = CRM_Contact_BAO_' . $contact->contact_type . '::getValues( $params, $defaults, $ids );' );

        $contact->location = CRM_Contact_BAO_Location::getValues( $params, $defaults, $ids, 3 );
        $contact->notes    = CRM_Contact_BAO_Note::getValues( $params, $defaults, $ids );
        $contact->relationship = CRM_Contact_BAO_Relationship::getValues( $params, $defaults, $ids );
        $contact->groupContact = CRM_Contact_BAO_GroupContact::getValues( $params, $defaults, $ids );

        return $contact;
    }

}

?>