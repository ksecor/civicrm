<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
    static function &getValues( $contactId ) 
    {
        $phone =& new CRM_Core_BAO_Phone( );
        $getValues =& CRM_Core_BAO_Block::getValues( $phone, 'phone', $contactId );

        foreach ($getValues as $key => $values ) {
            foreach ( $values as $k => $v ) {
                CRM_Core_DAO_Phone::addDisplayEnums( $getValues[$key][$k] );
            }
        }
        return $getValues;
    }

    /**
     * Get all the mobile numbers for a specified contact_id, with the primary mobile being first
     *
     * @param int $id the contact id
     *
     * @return array  the array of phone ids which are potential numbers
     * @access public
     * @static
     */
    static function allPhones( $id, $type = null ) 
    {
        if ( ! $id ) {
            return null;
        }

        $cond = null;
        if ( $type ) {
            $cond = " AND civicrm_phone.phone_type = '$type'";
        }

        $query = "
   SELECT phone, civicrm_location_type.name as locationType, civicrm_phone.is_primary as is_primary,
     civicrm_phone.id as phone_id, civicrm_phone.location_type_id as locationTypeId
     FROM civicrm_contact
LEFT JOIN civicrm_phone ON ( civicrm_contact.id = civicrm_phone.contact_id )
LEFT JOIN civicrm_location_type ON ( civicrm_phone.location_type_id = civicrm_location_type.id )
WHERE     civicrm_contact.id = %1 $cond
ORDER BY civicrm_phone.is_primary DESC, civicrm_phone.location_type_id DESC, phone_id ASC ";

        $params = array( 1 => array( $id, 'Integer' ) );
        
        $numbers = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch( ) ) {
            $numbers[$dao->phone_id] = array( 'locationType'   => $dao->locationType,
                                              'is_primary'     => $dao->is_primary,
                                              'id'             => $dao->phone_id,
                                              'phone'          => $dao->phone,
                                              'locationTypeId' => $dao->locationTypeId);
        }
        return $numbers;
    }
}
?>
