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

require_once 'CRM/Core/DAO/IM.php';

/**
 * This class contain function for IM handling
 */
class CRM_Core_BAO_IM extends CRM_Core_DAO_IM 
{

    /**
     * takes an associative array and adds im
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_IM object on success, null otherwise
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
        $im =& new CRM_Core_DAO_IM();
        
        $im->copyValues($params);

        return $im->save( );
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
     * @return boolean
     * @access public
     * @static
     */
    static function &getValues( $contactId ) 
    {
        $im =& new CRM_Core_BAO_IM( );
        return CRM_Core_BAO_Block::getValues( $im, 'im', $contactId );
    }

    /**
     * Get all the ims for a specified contact_id, with the primary im being first
     *
     * @param int $id the contact id
     *
     * @return array  the array of im details
     * @access public
     * @static
     */
    static function allIMs( $id ) 
    {
        if ( !$id ) {
            return null;
        }

        $query = "
SELECT civicrm_im.name as im, civicrm_location_type.name as locationType, civicrm_im.is_primary as is_primary,
civicrm_im.id as im_id, civicrm_im.location_type_id as locationTypeId
FROM      civicrm_contact
LEFT JOIN civicrm_im ON ( civicrm_im.contact_id = civicrm_contact.id )
LEFT JOIN civicrm_location_type ON ( civicrm_im.location_type_id = civicrm_location_type.id )
WHERE
  civicrm_contact.id = %1
ORDER BY
  civicrm_im.is_primary DESC, civicrm_im.location_type_id DESC, im_id ASC ";
        $params = array( 1 => array( $id, 'Integer' ) );

        $ims = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch( ) ) {
            $ims[$dao->im_id] = array( 'locationType'   => $dao->locationType,
                                       'is_primary'     => $dao->is_primary,
                                       'id'             => $dao->im_id,
                                       'name'           => $dao->im,
                                       'locationTypeId' => $dao->locationTypeId );
        }
        return $ims;
    }
}

?>
