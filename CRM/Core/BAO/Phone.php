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

require_once 'CRM/Core/DAO/Phone.php';

/**
 * Class contains functions for phone
 */
class CRM_Core_BAO_Phone extends CRM_Core_DAO_Phone 
{
    /**
     * takes an associative array and creates a phone 
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_Phone object on success, null otherwise
     * @access public
     * @static
     */
    static function create( &$params ) 
    {
        if ( ! self::dataExists( $params ) ) {
            return null;
        }
                
        $isPrimary = true;
        foreach ( $params['phone'] as $value ) {
            $contactFields = array( );
            $contactFields['contact_id'      ] = $value['contact_id'];
            $contactFields['location_type_id'] = $value['location_type_id'];
            
            foreach ( $value as $val ) {
                if ( !CRM_Core_BAO_Block::dataExists( array( 'phone' ), $val ) ) {
                    continue;
                }
                if ( is_array( $val ) ) {
                    if ( $isPrimary && $value['is_primary'] ) {
                        $contactFields['is_primary'] = $value['is_primary'];
                        $isPrimary = false;
                    } else {
                        $contactFields['is_primary'] = false;
                    }

                    $phoneFields = array_merge( $val, $contactFields);
                    self::add( $phoneFields );
                }
            }
        }
    }

    /**
     * takes an associative array and adds phone 
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_Phone object on success, null otherwise
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
        $phone =& new CRM_Core_DAO_Phone();
        
        $phone->copyValues($params);
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
    static function dataExists( &$params ) 
    {
        // return if no data present
        if ( ! array_key_exists( 'phone', $params ) ) {
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
        $strQuery = "
SELECT civicrm_phone.id as phone_id, civicrm_phone.phone as phone 
FROM   civicrm_phone
WHERE  civicrm_phone.contact_id = %1";
        $params = array( 1 => array( $contactId, 'Integer' ) );
                         
        $phone =& CRM_Core_DAO::executeQuery($strQuery, $params);
        
        $contactPhones = array( );
        while ( $phone->fetch( ) ) {
            $contactPhones[$phone->phone_id] = $phone->phone;
        }

        return $contactPhones;
    }
}
?>
