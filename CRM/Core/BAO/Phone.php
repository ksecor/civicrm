<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/Phone.php';

/**
 * BAO object for crm_phone table
 */
class CRM_Core_BAO_Phone extends CRM_Core_DAO_Phone 
{
    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $ids            the array that holds all the db ids
     * @param int    $locationId
     * @param int    $phoneId
     * @param bool   $isPrimary      Has any previous entry been marked as isPrimary?
     *
     * @return object       CRM_Core_BAO_Phone object on success, null otherwise
     * @access public
     * @static
     */
    static function add( &$params, &$ids, $locationId, $phoneId, &$isPrimary ) 
    {
        if ( ! self::dataExists( $params, $locationId, $phoneId, $ids ) ) {
            return null;
        }

        $phone =& new CRM_Core_DAO_Phone();
        $phone->id                 = CRM_Utils_Array::value( $phoneId, $ids['location'][$locationId]['phone'] );
        $phone->phone              = $params['location'][$locationId]['phone'][$phoneId]['phone'];
        if ( empty( $phone->phone ) ) {
            $phone->delete( );
            return null;
        }

        $phone->location_id        = $params['location'][$locationId]['id'];
        $phone->phone_type         = $params['location'][$locationId]['phone'][$phoneId]['phone_type'];
        $phone->mobile_provider_id = CRM_Utils_Array::value( 'mobile_provider_id', $params['location'][$locationId]['phone'][$phoneId] );

        // set this object to be the value of isPrimary and make sure no one else can be isPrimary
        $phone->is_primary         = $isPrimary;
        $isPrimary                 = false;

        return $phone->save( );
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $phoneId
     * @param array  $ids            (reference) the array that holds all the db ids
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params, $locationId, $phoneId, &$ids ) 
    {
        if ( CRM_Utils_Array::value( $phoneId, $ids['location'][$locationId]['phone'] )) {
            return true;
        }

        return CRM_Core_BAO_Block::dataExists('phone', array( 'phone' ), $params, $locationId, $phoneId );
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
     * @return array    array of phone objects
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids, $blockCount = 0 ) 
    {
        $phone =& new CRM_Core_BAO_Phone( );
        $getValues =& CRM_Core_BAO_Block::getValues( $phone, 'phone', $params, $values, $ids, $blockCount );
        foreach ($values['phone'] as $key => $array) {
            CRM_Core_DAO_Phone::addDisplayEnums($values['phone'][$key]);
        }
        return $getValues;
    }

    /**
     * Function to return the phone numbers of a contact
     * 
     * @param int   $contactId    ID of the contact for which phone no is required
     * 
     * @return array $contactPhones    array of phone no values for the given contact ID
     * 
     * @access public 
     * @static
     */
    static function getphoneNumber ( $contactId ) 
    {
        $phone =& new CRM_Core_DAO( );
        
        $strQuery = "SELECT civicrm_phone.id as phone_id, civicrm_phone.phone as phone 
                     FROM civicrm_phone, civicrm_location 
                     WHERE civicrm_phone.location_id = civicrm_location.id
                       AND civicrm_location.entity_table = 'civicrm_contact'
                       AND civicrm_location.entity_id = " .
                       CRM_Utils_Type::escape($contactId, 'Integer');
        
        $phone->query($strQuery);
        
        $contactPhones = array( );
        while($phone->fetch()) {
            $contactPhones[$phone->phone_id] = $phone->phone;
        }

        return $contactPhones;
    }
}
?>