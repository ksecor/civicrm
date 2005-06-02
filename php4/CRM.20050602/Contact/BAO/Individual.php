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
 * This class contains basic functions for Contact Individual
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */


require_once 'CRM/Contact/DAO/Individual.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Utils/Date.php';
require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/DAO/Individual.php';
require_once 'CRM/Contact/DAO/Location.php';
require_once 'CRM/Contact/DAO/Address.php';
require_once 'CRM/Contact/DAO/Phone.php';
require_once 'CRM/Contact/DAO/IM.php';
require_once 'CRM/Contact/DAO/Email.php';

/**
 * BAO object for crm_individual table
 */
class CRM_Contact_BAO_Individual extends CRM_Contact_DAO_Individual 
{
    /**
     * This is a contructor of the class.
     */
    function CRM_Contact_BAO_Individual() 
    {
        parent::CRM_Contact_DAO_Individual();
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
     * @return object CRM_Contact_BAO_Individual object
     * @access public
     * @static
     */
     function add(&$params, &$ids)
    {
        $individual = new CRM_Contact_BAO_Individual();

        $individual->copyValues($params);

        $individual->display_name =
            CRM_Utils_Array::value( 'first_name' , $params, '' ) . ' ' .
            CRM_Utils_Array::value( 'middle_name', $params, '' ) . ' ' .
            CRM_Utils_Array::value( 'last_name'  , $params, '' );
            
        // fix gender and date
        $individual->gender = $params['gender']['gender'];
        $date = CRM_Utils_Array::value('birth_date', $params);
        $individual->birth_date = CRM_Utils_Date::format( CRM_Utils_Array::value('birth_date', $params) );

        if (!array_key_exists('is_deceased', $params)) {
            $individual->is_deceased = 0;
        }

        $individual->id = CRM_Utils_Array::value( 'individual', $ids );

        return $individual->save();
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contact_BAO_Individual|null the found object or null
     * @access public
     * @static
     */
     function getValues( &$params, &$values, &$ids ) {
        $individual = new CRM_Contact_BAO_Individual( );
        
        $individual->copyValues( $params );
        if ( $individual->find(true) ) {
            $ids['individual'] = $individual->id;
            $individual->storeValues( $values );
            if ( isset( $individual->gender ) ) {
                $values['gender'] = array( 'gender' => $individual->gender );
            }
            if ( isset( $individual->birth_date ) ) {
                $values['birth_date'] = CRM_Utils_Date::unformat( $individual->birth_date );
            }
     
            return $individual;
        }
        return null;
    }
        
}

?>