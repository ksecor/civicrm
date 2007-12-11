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
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
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

require_once 'CRM/Core/DAO/OpenID.php';

/**
 * This class contains function for Open Id
 */
class CRM_Core_BAO_OpenID extends CRM_Core_DAO_OpenID 
{

    /**
     * takes an associative array and adds phone 
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_OpenID object on success, null otherwise
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
        $openId =& new CRM_Core_DAO_OpenID();
        
        $openId->copyValues($params);

        // need to handle update mode

        // when open id field is empty need to delete it

        // fix allowed login to do
//         if ( array_key_exists( 'allowed_to_login', $params['location'][$locationId]['openid'][$openIdId]) ) {
            
//             if ( !empty( $params['contact_id'] ) ) {
//                 $contactId = $params['contact_id'];

//                 self::setAllowedToLogin( $openId, $contactId,
//                 CRM_Utils_Array::value( 'allowed_to_login', $params['location'][$locationId]['openid'][$openIdId], false));
                
//                 // Copied from Email.php, but I don't think we want to do
//                 // this here (i.e. return)
//                 //return $openId;
//             }
//         }
        

        return $openId->save( );
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $entityBlock   input parameters to find object
     *
     * @return boolean
     * @access public
     * @static
     */
    static function &getValues( $entityBlock ) 
    {
        return CRM_Core_BAO_Block::getValues( 'openid', $entityBlock );
    }
    
    /**
     * Method to set whether or not an OpenID is allowed to login or not
     * 
     * This method is used to set the allowed_to_login bit on the UFMatch
     * record according to the 'allowedToLogin' value provided.
     * 
     * @param object  $openIdDAO         (reference) OpenID dao object
     * @param int     $contactId         id of the contact to update
     * @param boolean $allowedToLogin    flag to indicate whether this
     *                                   OpenID can be used for login
     *
     */
    public static function setAllowedToLogin( &$openIdDAO, $contactId, $allowedToLogin = false ) 
    {
        // first make sure a ufmatch record exists, if not,
        // this will create one
        require_once 'CRM/Core/BAO/UFMatch.php';
        require_once 'standalone/user.php';
        $user =& new Standalone_User( $openIdDAO->openid );
        CRM_Core_BAO_UFMatch::synchronize( $user, true, 'Standalone', 'Individual' );
        if ( $allowedToLogin ) {
            CRM_Core_BAO_UFMatch::setAllowedToLogin( $contactId, 1 );
        } else {
            CRM_Core_BAO_UFMatch::setAllowedToLogin( $contactId, 0 );
        }
        return true;
    }

    /**
     * Get all the emails for a specified contact_id, with the primary openid being first
     *
     * @param int $id the contact id
     *
     * @return array  the array of openid id's
     * @access public
     * @static
     */
    static function allOpenids( $id ) 
    {
        if ( ! $id ) {
            return null;
        }

        $query = "
SELECT openid, civicrm_location_type.name as locationType, civicrm_openid.is_primary as is_primary, 
civicrm_openid.id as openid_id, civicrm_openid.location_type_id as locationTypeId
FROM      civicrm_contact
LEFT JOIN civicrm_openid ON ( civicrm_openid.contact_id = civicrm_contact.id )
LEFT JOIN civicrm_location_type ON ( civicrm_openid.location_type_id = civicrm_location_type.id )
WHERE
  civicrm_contact.id = %1
ORDER BY
  civicrm_openid.is_primary DESC, civicrm_openid.location_type_id DESC, openid_id ASC ";
        $params = array( 1 => array( $id, 'Integer' ) );

        $emails = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch( ) ) {
            $emails[$dao->email_id] = array( 'locationType'   => $dao->locationType,
                                             'is_primary'     => $dao->is_primary,
                                             'id'             => $dao->openid_id,
                                             'openid'          => $dao->openid,
                                             'locationTypeId' => $dao->locationTypeId );
        }
        return $emails;
    }
    
}
?>
