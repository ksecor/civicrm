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

require_once 'CRM/Core/DAO/OpenID.php';

/**
 * This class contains function for Open Id
 */
class CRM_Core_BAO_OpenID extends CRM_Core_DAO_OpenID 
{
    /**
     * takes an associative array and creates a OpenId
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_OpenID object on success, null otherwise
     * @access public
     * @static
     */
    static function create( &$params ) 
    {
        if ( ! self::dataExists( $params ) ) {
            return null;
        }
                
        $isPrimary = true;
        foreach ( $params['openid'] as $value ) {
            $contactFields = array( );
            $contactFields['contact_id'      ] = $value['contact_id'];
            $contactFields['location_type_id'] = $value['location_type_id'];
            
            foreach ( $value as $val ) {
                if ( !CRM_Core_BAO_Block::dataExists( array( 'openid' ), $val ) ) {
                    continue;
                }
                if ( is_array( $val ) ) {
                    if ( $isPrimary && $value['is_primary'] ) {
                        $contactFields['is_primary'] = $value['is_primary'];
                        $isPrimary = false;
                    } else {
                        $contactFields['is_primary'] = false;
                    }

                    $openIdFields = array_merge( $val, $contactFields);
                    self::add( $openIdFields );
                }
            }
        }
    }

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
     * Check if there is data to create the object
     *
     * @param array  $params         (reference) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        // return if no data present
        if ( ! array_key_exists( 'openid', $params ) ) {
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
     * @return boolean
     * @access public
     * @static
     */
    static function &getValues( $contactId ) 
    {
        $openId =& new CRM_Core_BAO_OpenID( );
        $blocks = CRM_Core_BAO_Block::getValues( $openId, 'openid', $contactId );
//         require_once 'CRM/Core/BAO/UFMatch.php';
//         foreach ( $values['openid'] as $idx => $oid ) {
//             $values['openid'][$idx]['allowed_to_login'] = CRM_Core_BAO_UFMatch::getAllowedToLogin( $oid['openid'] ) ? 1 : 0;
//         }
        return $blocks;
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
}
?>
