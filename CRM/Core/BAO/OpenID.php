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
 * BAO object for crm_openid table
 */
class CRM_Core_BAO_OpenID extends CRM_Core_DAO_OpenID {
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
     * @param int    $openIdId
     * @param bool   $isPrimary      Has any previous entry been marked as isPrimary?
     *
     * @return object    CRM_Core_BAO_OpenID object if successful 
     *                   else null will be returned
     * @access public
     * @static
     */
    static function add( &$params, &$ids, $locationId, $openIdId, &$isPrimary ) {
        print "adding an OpenID to the database<br/>";
        print "\$params:";
        print_r($params);
        print "<br/>";
        print "\$ids:";
        print_r($ids);
        print "<br/>";
        // if no data and we are not updating an exisiting record
        if ( ! self::dataExists( $params, $locationId, $openIdId, $ids ) ) {
            return null;
        }
        
        $openId =& new CRM_Core_DAO_OpenID();
        $openId->id     = CRM_Utils_Array::value( $openIdId, $ids['location'][$locationId]['openid'] );
        $openId->openid = $params['location'][$locationId]['openid'][$openIdId]['openid'];
        if ( empty( $openId->openid ) ) {
            $openId->delete( );
            print "D'OH!<br/>";
            return null;
        }
        
        $openId->location_id = $params['location'][$locationId]['id'];
        
        // set this object to be the value of isPrimary and make sure no one else can be isPrimary
        if ( $isPrimary ) {
            $openId->is_primary     = $isPrimary;
            $isPrimary              = false;
        } else {
            $openId->is_primary     = $params['location'][$locationId]['openid'][$openIdId]['is_primary'];
        }
        
        if ( array_key_exists( 'allowed_to_login', $params['location'][$locationId]['openid'][$openIdId]) ) {
            
            self::setAllowedToLogin( $openId,
                CRM_Utils_Array::value( 'allowed_to_login', $params['location'][$locationId]['openid'][$openIdId], false));
            
            return $openId;
        }
        print "saving OpenID<br/>";
        return $openId->save();
    }
    
    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $emailId
     * @param array  $ids            the array that holds all the db ids
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params, $openIdId, &$ids) {
        if ( CRM_Utils_Array::value( $openIdId, $ids['location'][$locationId]['openid'] ) ) {
            return true;
        }

        return CRM_Core_BAO_Block::dataExists( 'openid', array( 'openid' ), $params, $locationId, $openIdId );
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
    static function &getValues( &$params, &$values, &$ids, $blockCount = 0 ) {
        $openId =& new CRM_Core_BAO_OpenID( );
        return CRM_Core_BAO_Block::getValues( $openId, 'openid', $params, $values, $ids, $blockCount );
    }

    /**
     * Delete OpenID address records from a location
     *
     * @param int $locationId       Location ID to delete for
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public static function deleteLocation( $locationId ) {
        $dao =& new CRM_Core_DAO_OpenID();
        $dao->location_id = $locationId;
        $dao->find();

        /* Copied this from CRM_Core_BAO_Email, but is it necessary here?
           Or anything like it?
        require_once 'CRM/Mailing/Event/BAO/Queue.php';
        while ($dao->fetch()) {
            CRM_Mailing_Event_BAO_Queue::deleteEventQueue( $dao->id );
        }
        
        $dao->reset();
        $dao->location_id = $locationId;
        */
        $dao->delete();
    }
    
    /**
     * Method to set whether or not an OpenID is allowed to login or not
     * 
     * This method is used to set the allowed_to_login bit on the OpenID(s) 
     * according to the 'allowedToLogin' value provided.
     * 'Values' array contains values required to search for required
     * OpenID record in update mode.
     * An example Values array looks like : 
     * 
     * Values
     *
     * Array
     * (
     * [location] => Array
     *      (
     *       [2] => 92
     *      )
     *
     * [openid] => Array
     *      (
     *       [1] => 170
     *       [2] => 171
     *       [3] => 172
     *      )
     *
     * )
     * 
     * @param object  $openIdDAO         (reference) OpenID dao object
     * @param array   $values
     * @param int     $locationBlockId   Location Block Number
     * @param int     $openIDBlockId     OpenID Block Number
     * @param boolean $allowedToLogin    flag to indicate whether this
     *                                   OpenID can be used for login
     *
     */
    public static function setAllowedToLogin( &$openIdDAO, $allowedToLogin = false) {
        require_once 'CRM/Core/DAO/UFMatch.php';
        $ufmatch =& new CRM_Core_DAO_UFMatch();
        $ufmatch->user_unique_id = $openIdDAO->openid;
        if ($allowedToLogin) {
            $ufmatch->allowed_to_login = 1;
        } else {
            $ufmatch->allowed_to_login = 0;
        }
        
        $ufmatch->save();
        return true;
    }
}
?>
