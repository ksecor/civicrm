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

require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/DAO/Household.php';

class CRM_Contact_BAO_Household extends CRM_Contact_DAO_Household
{
    /**
     * This is a contructor of the class.
     */
    function __construct() 
    {
        parent::__construct();
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
     * @return object CRM_Contact_BAO_Household object
     * @access public
     * @static
     */
    static function add( &$params, &$ids ) {
        $household =& new CRM_Contact_BAO_Household( );

        $household->copyValues( $params );

        $household->id = CRM_Utils_Array::value( 'household', $ids );
        $household->save( );
        return $household;
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contact_BAO_Household|null the found object or null
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids ) {
        $household =& new CRM_Contact_BAO_Household( );
        
        $household->copyValues( $params );
        if ( $household->find(true) ) {
            $ids['household'] = $household->id;
            CRM_Core_DAO::storeValues( $household, $values );
            return $household;
        }
        return null;
    }
    
    /**
     * function to update the household with primary contact id
     *
     * @param integer $primaryContactId     null if deleting primary contact
     * @param integer $contactId            contact id
     *
     * @return Object     DAO object on success
     * @access public
     * @static
     */
    static function updatePrimaryContact( $primaryContactId, $contactId ) 
    {
        $queryString    = "UPDATE civicrm_contact
                           SET primary_contact_id = ";
        
        $params = array( );
        if ( $primaryContactId ) {
            $queryString .= '%1';
            $params[1] = array( $primaryContactId, 'Integer' );
        } else {
            $queryString .= "null";
        }
        
        $queryString .=  " WHERE id = %2";
        $params[2] = array( $contactId, 'Integer' );
        
        return  CRM_Core_DAO::executeQuery( $queryString, $params );
    }
}
?>
