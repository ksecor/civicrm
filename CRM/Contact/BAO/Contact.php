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
        
        /*   
        $str_select = "SELECT crm_contact.id as crm_contact_id, crm_contact.sort_name as crm_contact_sort_name,
                              crm_address.street_address as crm_address_street_address, crm_address.city as crm_address_city,
                              crm_state_province.name as crm_state_province_name, crm_email.email as crm_email_email,
                              crm_phone.phone as crm_phone_phone, crm_contact.contact_type as crm_contact_contact_type";
        */

        $str_select = "SELECT crm_contact.id AS crm_contact_id, crm_contact.sort_name AS crm_contact_sort_name,

                          IFNULL( crm_address.street_address, (SELECT crm_address.street_address 
                          FROM crm_contact
                          LEFT OUTER JOIN crm_location ON ( crm_contact.id = crm_location.contact_id
                                                            AND crm_location.is_primary =1 )
                          LEFT OUTER JOIN crm_address ON ( crm_location.id = crm_address.location_id )
 
                          WHERE  crm_contact.id = (SELECT IFNULL(contact_id_b,crm_contact_id) AS crm_contact_id
                                                                    FROM crm_relationship
                                                                    WHERE crm_relationship.contact_id_a =crm_contact_id
                                                                    AND ( crm_relationship.relationship_type_id =6 OR crm_relationship.relationship_type_id =7)
                                                                     )
                                                            )                                                                
                           ) AS crm_address_street_address,

                          IFNULL( crm_address.city, (SELECT crm_address.city
                          FROM crm_contact
                          LEFT OUTER JOIN crm_location ON ( crm_contact.id = crm_location.contact_id
                                                            AND crm_location.is_primary =1 )
                          LEFT OUTER JOIN crm_address ON ( crm_location.id = crm_address.location_id )

                          WHERE  crm_contact.id = (SELECT IFNULL(contact_id_b,crm_contact_id) AS crm_contact_id
                                                                    FROM crm_relationship
                                                                    WHERE crm_relationship.contact_id_a =crm_contact_id
                                                                    AND ( crm_relationship.relationship_type_id =6 OR crm_relationship.relationship_type_id =7)
                                                                     )
                                                            )             
                          ) AS crm_address_city, 

                          IFNULL( crm_state_province.name, (SELECT crm_state_province.name
                          FROM crm_contact
                          LEFT OUTER JOIN crm_location ON ( crm_contact.id = crm_location.contact_id
                                                            AND crm_location.is_primary =1 )
                          LEFT OUTER JOIN crm_address ON ( crm_location.id = crm_address.location_id )
                          LEFT OUTER JOIN crm_state_province ON ( crm_address.state_province_id = crm_state_province.id )
                          WHERE  crm_contact.id = (SELECT IFNULL(contact_id_b,crm_contact_id) AS crm_contact_id
                                                                    FROM crm_relationship
                                                                    WHERE crm_relationship.contact_id_a =crm_contact_id
                                                                    AND ( crm_relationship.relationship_type_id =6 OR crm_relationship.relationship_type_id =7)
                                                                     )
                                                            )
                          ) AS crm_state_province_name, 


                          IFNULL( crm_email.email, (SELECT crm_email.email
                          FROM crm_contact
                          LEFT OUTER JOIN crm_location ON ( crm_contact.id = crm_location.contact_id
                                                            AND crm_location.is_primary =1 )
                          LEFT OUTER JOIN crm_email ON ( crm_location.id = crm_email.location_id
                                                         AND crm_email.is_primary =1 )
                          WHERE  crm_contact.id = (SELECT IFNULL(contact_id_b,crm_contact_id) AS crm_contact_id
                                                                    FROM crm_relationship
                                                                    WHERE crm_relationship.contact_id_a =crm_contact_id
                                                                    AND ( crm_relationship.relationship_type_id =6 OR crm_relationship.relationship_type_id =7)
                                                                     )
                                                                    )
                          ) AS crm_email_email,

                          IFNULL( crm_phone.phone, (SELECT crm_phone.phone
                          FROM crm_contact
                          LEFT OUTER JOIN crm_location ON ( crm_contact.id = crm_location.contact_id
                                                            AND crm_location.is_primary =1 )
                          LEFT OUTER JOIN crm_phone ON ( crm_location.id = crm_phone.location_id
                                                         AND crm_phone.is_primary =1 )
                          WHERE  crm_contact.id = (SELECT IFNULL(contact_id_b,crm_contact_id) AS crm_contact_id
                                                                    FROM crm_relationship
                                                                    WHERE crm_relationship.contact_id_a =crm_contact_id
                                                                    AND ( crm_relationship.relationship_type_id =6 OR crm_relationship.relationship_type_id =7)
                                                                     )
                                                                    )
                          ) AS crm_phone_phone,

                          crm_contact.contact_type AS crm_contact_contact_type";

        $str_from = " FROM crm_contact 
                        LEFT OUTER JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                        LEFT OUTER JOIN crm_address ON (crm_location.id = crm_address.location_id )
                        LEFT OUTER JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                        LEFT OUTER JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                        LEFT OUTER JOIN crm_state_province ON (crm_address.state_province_id = crm_state_province.id)";

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
            $row['contact_id'] = $this->crm_contact_id;
            $row['sort_name'] = $this->crm_contact_sort_name;
            $row['email'] = $this->crm_email_email;
            $row['phone'] = $this->crm_phone_phone;
            $row['street_address'] = $this->crm_address_street_address;
            $row['city'] = $this->crm_address_city;
            $row['state'] = $this->crm_state_province_name;
            
            switch ($this->crm_contact_contact_type) {
            case 'Individual' :
                $row['edit']  = 'index.php?q=/crm/contact/edit/'.$this->crm_contact_id;
                $row['view']  = 'index.php?q=/crm/contact/view/'.$this->crm_contact_id;
                break;
            case 'Household' :
                $row['edit']  = 'index.php?q=/crm/contact/edit_house/'.$this->crm_contact_id;
                $row['view']  = 'index.php?q=/crm/contact/view_house/'.$this->crm_contact_id;
                break;
            case 'Organization' :
                $row['edit']  = 'index.php?q=/crm/contact/edit_org/'.$this->crm_contact_id;
                $row['view']  = 'index.php?q=/crm/contact/view_org/'.$this->crm_contact_id;
                break;
                
            }
            
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
            $contact->sort_name = CRM_Array::value( 'first_name', $params, '' ) . ' ' . CRM_Array::value( 'last_name', $params, '' );
        } else if ($contact->contact_type == 'Household') {
            $contact->sort_name = CRM_Array::value( 'household_name', $params, '' ) ;
        } else {
            $contact->sort_name = CRM_Array::value( 'organization_name', $params, '' ) ;
        } 

        //$contact->sort_name = CRM_Array::value( 'last_name', $params, '' ) . ', ' . CRM_Array::value( 'first_name', $params, '' );

        $privacy = CRM_Array::value( 'privacy', $params );
        foreach ( self::$_commPrefs as $name ) {
            if ( array_key_exists( $name, $privacy ) ) {
                $contact->$name = $privacy[$name];
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

}

?>