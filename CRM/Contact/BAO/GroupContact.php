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

require_once 'CRM/Contact/DAO/GroupContact.php';

require_once 'CRM/Contact/BAO/Block.php';

class CRM_Contact_BAO_GroupContact extends CRM_Contact_DAO_GroupContact {
    
    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }

    /**
     * takes an associative array and creates a group object
     *
     * the function extract all the params it needs to initialize the create a
     * group object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contact_BAO_Group object
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
        /*
        $dataExists = self::dataExists( $params );
        if ( ! $dataExists ) {
            return null;
        }

        $group = new CRM_Contact_BAO_Group( );
        
        $params['modified_date'] = date("Ymd");
        $params['table_id']      = $params['contact_id'];
        $params['table_name']    = 'crm_contact';

        $group->copyValues( $params );

        $group->contact_id = 1;
        $group->save( );

        return $group;
        */
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
        // return if no data present
        if ( ! strlen( $params['group']) ) {
            return false;
        } 
        return true;
     }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     *
     * @return void
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids ) {
        $groupContact = new CRM_Contact_BAO_GroupContact( );

        $groupContact->contact_id   = $params['contact_id'] ;        

        // get the total count of groups for the contact 
        $values['groupCount'] = $groupContact->count( );

        return $groupContact;
    }
}

?>