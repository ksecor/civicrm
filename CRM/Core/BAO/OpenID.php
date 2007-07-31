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
        /*
        print "adding an OpenID to the database<br/>";
        print "\$params:";
        print_r($params);
        print "<br/>";
        print "\$ids:";
        print_r($ids);
        print "<br/>";
        */
        // if no data and we are not updating an exisiting record
        if ( ! self::dataExists( $params, $locationId, $openIdId, $ids ) ) {
            return null;
        }
        
        $openId =& new CRM_Core_DAO_OpenID();
        $openId->id     = CRM_Utils_Array::value( $openIdId, $ids['location'][$locationId]['openid'] );
        $openId->openid = $params['location'][$locationId]['openid'][$openIdId]['openid'];
        if ( empty( $openId->openid ) ) {
            $openId->delete( );
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
            
            if ( !empty( $params['contact_id'] ) ) {
                $contactId = $params['contact_id'];

                self::setAllowedToLogin( $openId, $contactId,
                CRM_Utils_Array::value( 'allowed_to_login', $params['location'][$locationId]['openid'][$openIdId], false));
                
                // Copied from Email.php, but I don't think we want to do
                // this here (i.e. return)
                //return $openId;
            }
        }
        //print "saving OpenID<br/>";
        return $openId->save();
    }
    
    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $openIdId
     * @param array  $ids            the array that holds all the db ids
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params, $locationId, $openIdId, &$ids) {
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
        $blocks = CRM_Core_BAO_Block::getValues( $openId, 'openid', $params, $values, $ids, $blockCount );
        require_once 'CRM/Core/BAO/UFMatch.php';
        foreach ( $values['openid'] as $idx => $oid ) {
            $values['openid'][$idx]['allowed_to_login'] = CRM_Core_BAO_UFMatch::getAllowedToLogin( $oid['openid'] ) ? 1 : 0;
        }
        return $blocks;
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
     * This method is used to set the allowed_to_login bit on the UFMatch
     * record according to the 'allowedToLogin' value provided.
     * 
     * @param object  $openIdDAO         (reference) OpenID dao object
     * @param int     $contactId         id of the contact to update
     * @param boolean $allowedToLogin    flag to indicate whether this
     *                                   OpenID can be used for login
     *
     */
    public static function setAllowedToLogin( &$openIdDAO, $contactId, $allowedToLogin = false ) {
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
