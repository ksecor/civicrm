<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'api/crm.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/DAO/UFMatch.php';

/**
 * The basic class that interfaces with the external user framework
 */
class CRM_Core_BAO_UFMatch extends CRM_Core_DAO_UFMatch {
    /**
     * Given a UF user object, make sure there is a contact
     * object for this user. If the user has new values, we need
     * to update the CRM DB with the new values
     *
     * @param Object  $user   the drupal user object
     * @param boolean $update has the user object been edited
     *
     * @return void
     * @access public
     * @static
     */
    static function synchronize( &$user, $update, $uf ) {
        $session =& CRM_Core_Session::singleton( );
        if ( ! is_object( $session ) ) {
            return;
        }

        if ( $uf == 'Drupal' ) {
            $key  = 'uid';
            $mail = 'mail';
        } else {
            $key  = 'id';
            $mail = 'email';
        }

        // have we already processed this user, if so early
        // return
        $userID = $session->get( 'userID' );
        $ufID   = $session->get( 'ufID'   );
        if ( ! $update && $ufID == $user->$key ) {
            return;
        }

        // reset the session if we are a different user
        if ( $ufID && $ufID != $user->$key ) {
            $session->reset( );
        }

        // make sure that a contact id exists for this user id
        $ufmatch =& new CRM_Core_DAO_UFMatch( );
        $ufmatch->ufid = $user->$key;
        if ( ! $ufmatch->find( true ) ) {
            $query = "
SELECT    crm_contact.id as contact_id, crm_contact.domain_id as domain_id
FROM      crm_contact
LEFT JOIN crm_location ON ( crm_contact.id  = crm_location.contact_id AND crm_location.is_primary = 1 )
LEFT JOIN crm_email    ON ( crm_location.id = crm_email.location_id   AND crm_email.is_primary = 1    )
WHERE     crm_email.email = '" . $user->$mail . "'";
  
            $dao =& new CRM_Core_DAO( );
            $dao->query( $query );
            if ( $dao->fetch( ) ) {
                $ufmatch->contact_id = $dao->contact_id;
                $ufmatch->domain_id  = $dao->domain_id ;
            } else {
                if ( $uf == 'Mambo' ) {
                    CRM_Utils_System_Mambo::setEmail( $user );
                }
                $params= array( 'email' => $user->$mail, 'location_type' => 'Home' );
                $contact =& crm_create_contact( $params, 'Individual' );
                if ( is_a( $contact, 'CRM_Core_Error' ) ) {
                    CRM_Core_Error::debug( 'error', $contact );
                    exit(1);
                }
                $ufmatch->contact_id = $contact->id;
                $ufmatch->domain_id  = $contact->domain_id ;
            }
            $ufmatch->save( );
        } 

        $session->set( 'ufID'    , $ufmatch->ufid       );
        $session->set( 'userID'  , $ufmatch->contact_id );
        $session->set( 'domainID', $ufmatch->domain_id  ); 
        
        if ( $update ) {
            // some information has changed in the UF core
            // replicate that information in civicrm
        }
    }

}

?>
