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
 
    function __construct()
    {
        parent::__construct();
    }
    
    function getSearchRows($offset, $rowCount, $sort)
    {
        //
        // create the DAO's
        // all trash code... will clean it up in next commit... --- yvb
        //

        $location_DAO = new CRM_Contact_DAO_Location();
        $address_DAO = new CRM_Contact_DAO_Address();
        $email_DAO = new CRM_Contact_DAO_Email();
        $phone_DAO = new CRM_Contact_DAO_Phone();

        
        // we need to run the loop thru the num rows with offset in mind.
        $rows = array();
        /*
        $query_string = <<<QS
            SELECT crm_contact.id as crm_contact_id, crm_contact.sort_name as crm_contact_sort_name,
            crm_address.street_address as crm_address_street_address, crm_address.city as crm_address_city,
            crm_state_province.name as crm_state_province_name,
            crm_email.email as crm_email_email,
            crm_phone.phone as crm_phone_phone
            FROM crm_contact, crm_location, crm_address, crm_phone, crm_email, crm_state_province
            WHERE crm_contact.id = crm_location.contact_id AND
            crm_location.id = crm_address.location_id AND
            crm_location.id = crm_phone.location_id AND
            crm_location.id = crm_email.location_id AND
            crm_address.state_province_id = crm_state_province.id AND
            crm.location.is_primary = TRUE AND
            crm.email.pri
            QS;
        */
        /*   
        $query_string = "
            SELECT distinct(crm_contact.id) as crm_contact_id, crm_contact.sort_name as crm_contact_sort_name,
            crm_address.street_address as crm_address_street_address, crm_address.city as crm_address_city,
            crm_state_province.name as crm_state_province_name,
            crm_email.email as crm_email_email,
            crm_phone.phone as crm_phone_phone
            FROM crm_contact 
                 left outer join crm_location on (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1)
                 left outer join crm_address on (crm_location.id = crm_address.location_id )
                 left outer join crm_phone on (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1)
                 left outer join crm_email on (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)
                 left outer join  crm_state_province on (crm_address.state_province_id = crm_state_province.id)
            ";


        $query_string .= " ORDER BY " . $sort->orderBy(); 
        $query_string .= " LIMIT $offset, $rowCount ";

        //CRM_Error::debug_var("query_string", $query_string); 

        $this->query($query_string);
        */

        $this->selectAdd( );
        
        $location_DAO->joinAdd($email_DAO, "LEFT");
        
        //$location_DAO->whereAdd($email_DAO->location_id = $location_DAO->id );
        //$location_DAO->whereAdd($email_DAO->getTableName().'.id = '.$location_DAO->getTableName().'.id' );

        $location_DAO->joinAdd($phone_DAO, "LEFT");
        // $location_DAO->whereAdd($phone_DAO->location_id = $location_DAO->id );
        //$location_DAO->whereAdd($phone_DAO->getTableName().'.id = '.$location_DAO->getTableName().'.id' );

        $location_DAO->joinAdd($address_DAO, "LEFT");
        //$location_DAO->whereAdd($address_DAO->location_id = $location_DAO->id );
        //$location_DAO->whereAdd($address_DAO->getTableName().'.id = '.$location_DAO->getTableName().'.id' );

        $this->_join( 'AND '.$location_DAO->getTableName().'.is_primary = 1' );
        $this->joinAdd($location_DAO, "LEFT");         
        //$location_DAO->whereAdd($location_DAO->getTableName().'.is_primary = 1' );
        // $this->_join( 'AND '.$location_DAO->getTableName().'.is_primary = 1' );

        //$location_DAO->whereAdd($location_DAO->getTableName().'.contact_id = '.$location_DAO->getTableName().'.id AND '.$location_DAO->getTableName().'.is_primary = 1' );
        //$location_DAO->whereAdd($location_DAO->getTableName().'.is_primary = 1' );

        $this->selectAs($this,'crm_contact_%s');
        $this->selectAs($email_DAO, 'crm_email_%s' );
        $this->selectAs($phone_DAO, 'crm_phone_%s' );
        $this->selectAs($address_DAO, 'crm_address_%s' );
        $this->selectAs($location_DAO, 'crm_location_%s' );
        $this->selectAdd('distinct ' . $this->selectAdd());

        $this->orderBy($sort->orderBy());
        $this->limit($offset, $rowCount);
        

        $this->find();

        while($this->fetch())
            {
                $row = array();
                $row['contact_id'] = $this->crm_contact_id;
                $row['sort_name'] = $this->crm_contact_sort_name;
                $row['email'] = $this->crm_email_email;
                $row['phone'] = $this->crm_phone_phone;
                $row['street_address'] = $this->crm_address_street_address;
                $row['city'] = $this->crm_address_city;
                $row['state'] = $this->state_province_name;
                $row['edit']  = 'index.php?q=/crm/contact/edit/'.$this->crm_contact_id;
                $row['view']  = 'index.php?q=/crm/contact/view/'.$this->crm_contact_id;
                $rows[] = $row;

                /*                $row = array();
                $row['contact_id'] = $this->crm_contact_id;
                $row['sort_name'] = $this->crm_contact_sort_name;
                $row['email'] = $this->crm_email_email;
                $row['phone'] = $this->crm_phone_phone;
                $row['street_address'] = $this->crm_address_street_address;
                $row['city'] = $this->crm_address_city;
                $row['state'] = $this->crm_state_province_name;
                $row['edit']  = 'index.php?q=/crm/contact/edit/'.$this->crm_contact_id;
                $row['view']  = 'index.php?q=/crm/contact/view/'.$this->crm_contact_id;
                $rows[] = $row;*/
            }
        return $rows;
    }



    function fetch() 
    {
        return parent::fetch();
    }

    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function add( &$params ) {
        $contact = new CRM_Contact_BAO_Contact( );
        
        $contact->domain_id = 1;
        
        $contact->copyValues( $params );

        $contact->sort_name = CRM_Array::value( 'first_name', $params, '' ) . ' ' . CRM_Array::value( 'last_name', $params, '' );

        $privacy = CRM_Array::value( 'privacy', $params );
        static $commPrefs = array( 'do_not_phone', 'do_not_email', 'do_not_mail' );
        foreach ( $commPrefs as $name ) {
            if ( array_key_exists( $name, $privacy ) ) {
                $contact->$name = $privacy[$name];
            }
        }

        $id = CRM_Array::value( 'contact_id', $params );
        if ( $id ) {
            $contact->id = $id;
        }
        return $contact->save( );
    }

}

?>