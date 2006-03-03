<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * This class contains basic functions for Contact Household
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
        return $household->save( );
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
     * @param integer $contactId contact id
     * @param integer $primaryContactId
     *
     * @return Object     DAO object on success
     * @access public
     * @static
     */
    static function updatePrimaryContact($primaryContactId, $contactId) 
    {
        $household =& new CRM_Contact_DAO_Household;

        //$household->primary_contact_id = $primaryContactId;        
        //$household->whereAdd('contact_id ='.$contactId);

        $queryString    = "UPDATE civicrm_household 
                           SET primary_contact_id = "
                        . CRM_Utils_Type::escape($primaryContactId,'Integer')
                        ." WHERE contact_id= "
                        . CRM_Utils_Type::escape($contactId, 'Integer');
        
        return  $household->query($queryString);
    }
    
}

?>
