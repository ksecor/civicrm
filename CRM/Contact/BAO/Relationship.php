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

require_once 'CRM/Contact/DAO/Relationship.php';
require_once 'CRM/Contact/DAO/RelationshipType.php';
require_once 'CRM/Contact/BAO/Block.php';

class CRM_Contact_BAO_Relationship extends CRM_Contact_DAO_Relationship {
    
    /**
     * const the max number of relationships we display at any given time
     * @var int
     */
    const MAX_RELATIONSHIPS = 3;

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }

    /**
     * takes an associative array and creates a relationship object
     *
     * the function extract all the params it needs to initialize the create a
     * relationship object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contact_BAO_Relationship object
     * @access public
     * @static
     */
    static function add( &$params ) 
    {

        $dataExists = self::dataExists( $params );
        if ( ! $dataExists ) {
            return null;
        }

        /*        
        $relationship = new CRM_Contact_BAO_Relationship( );

        $params['modified_date'] = date("Ymd");
        $params['table_id']      = $params['contact_id'];
        $params['table_name']    = 'crm_contact';

        $relationship->copyValues( $params );

        $relationship->contact_id = 1;
        $relationship->save( );
        */
        return $relationship;
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        return true;
     }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     * @param int   $numRelationships      the maximum number of relationships to return (0 if all)
     *
     * @return void
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids, $numRelationships = self::MAX_RELATIONSHIPS ) {
        $relationship = new CRM_Contact_BAO_Relationship( );
        
        $str_select = $str_from = $str_where = $str_order = $str_limit = '';
        
        $str_select = "SELECT crm_relationship.id as crm_relationship_id,
                              crm_contact.sort_name as sort_name,
                              crm_address.street_address as street_address,
                              crm_address.city as city,
                              crm_address.postal_code as postal_code,
                              crm_state_province.abbreviation as state,
                              crm_country.name as country,
                              crm_email.email as email,
                              crm_phone.phone as phone,
                              crm_contact.contact_type as contact_type,
                              crm_relationship.contact_id_b as contact_id_b,
                              crm_relationship.contact_id_a as contact_id_a,
                              crm_relationship_type.name_a_b as name_a,
                              crm_relationship_type.name_b_a as name_b";

        $str_from = " FROM crm_contact 
                        LEFT OUTER JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                        LEFT OUTER JOIN crm_address ON (crm_location.id = crm_address.location_id )
                        LEFT OUTER JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT OUTER JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT OUTER JOIN crm_state_province ON (crm_address.state_province_id = crm_state_province.id)
                        LEFT OUTER JOIN crm_country ON (crm_address.country_id = crm_country.id),
                      crm_relationship,crm_relationship_type
                       ";

        // add where clause 
        $str_where = " WHERE crm_relationship.relationship_type_id = crm_relationship_type.id 
                         AND (crm_relationship.contact_id_a = ". $params['contact_id']." 
                            OR crm_relationship.contact_id_b = ".$params['contact_id'].") 
                         AND (crm_relationship.contact_id_a = crm_contact.id
                            OR crm_relationship.contact_id_b = crm_contact.id)";

        $str_order = " GROUP BY crm_relationship.id ";
        $str_limit = " LIMIT 0, $numRelationships ";

        // building the query string
        $query_string = $str_select.$str_from.$str_where.$str_order.$str_limit;
        $relationship->query($query_string);
    
   
        $relationships       = array( );
        $ids['relationship'] = array( );
        $count = 0;
        while ( $relationship->fetch() ) {
            
            $values['relationship'][$relationship->crm_relationship_id] = array();
            $ids['relationship'][] = $relationship->crm_relationship_id;
            
            $values['relationship'][$relationship->crm_relationship_id]['id'] = $relationship->crm_relationship_id;
            $values['relationship'][$relationship->crm_relationship_id]['relation'] = $relationship->name_b;
            $values['relationship'][$relationship->crm_relationship_id]['name'] = $relationship->sort_name;
            $values['relationship'][$relationship->crm_relationship_id]['email'] = $relationship->email;
            $values['relationship'][$relationship->crm_relationship_id]['phone'] = $relationship->phone;
            $values['relationship'][$relationship->crm_relationship_id]['city'] = $relationship->city;
            $values['relationship'][$relationship->crm_relationship_id]['state'] = $relationship->state;
            
            $relationship->storeValues( $values['relationship'][$relationship->crm_relationship_id] );
            
            $relationships = $relationship;
            $count++;
        }

        // get the total count of relationships
        $values['relationshipsCount'] = $count;

        //   print_r($relationships);
        return $relationships;
    }
}

?>