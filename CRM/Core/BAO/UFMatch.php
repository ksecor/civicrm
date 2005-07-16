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
        $ufmatch->uf_id = $user->$key;
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
                $ufmatch->email      = $user->$mail    ;
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
                $ufmatch->email      = $user->$mail    ;
            }
            $ufmatch->save( );
        }

        $session->set( 'ufID'    , $ufmatch->uf_id       );
        $session->set( 'userID'  , $ufmatch->contact_id );
        $session->set( 'domainID', $ufmatch->domain_id  ); 
        $session->set( 'ufEmail' , $ufmatch->email      );

        if ( $update ) {
            // the only information we care about is email, so lets check that
            if ( $user->$mail != $ufmatch->email ) {
                // email has changed, so we need to change all our primary email also
                $ufmatch->email = $user->$mail;
                $ufmatch->save( );

                $query = "
UPDATE  crm_contact
LEFT JOIN crm_location ON ( crm_contact.id  = crm_location.contact_id AND crm_location.is_primary = 1 )
LEFT JOIN crm_email    ON ( crm_location.id = crm_email.location_id   AND crm_email.is_primary = 1    )
SET crm_email.email = '" . $user->$mail . '" WHERE crm_contact.id = ' . $ufmatch->contact_id;
                
                $dao =& new CRM_Core_DAO( );
                $dao->query( $query );
            }
        }
    }

    /**
     * update the email in the user object
     *
     * @param int    $contactId id of the contact to delete
     *
     * @return void
     * @access public
     * @static
     */
    static function updateUFEmail( $contactId ) {
        // fetch the primary email
        $query = "
SELECT    crm_email.email as email
FROM      crm_contact
LEFT JOIN crm_location ON ( crm_contact.id  = crm_location.contact_id AND crm_location.is_primary = 1 )
LEFT JOIN crm_email    ON ( crm_location.id = crm_email.location_id   AND crm_email.is_primary = 1    )
WHERE     crm_contact.id = " . $contactId;

        $dao =& new CRM_Core_DAO( );
        $dao->query( $query );
        if ( ! $dao->fetch( ) || ! $dao->email ) {
            // if we can't find a primary email, return
            return;
        }
        $email = $dao->email;

        $ufmatch =& new CRM_Core_DAO_UFMatch( );
        $ufmatch->contact_id = $contactId;
        if ( ! $ufmatch->find( true ) || $ufmatch->email == $email ) {
            // if object does not exist or the email has not changed
            return;
        }

        // save the updated ufmatch object
        $ufmatch->email = $email;
        $ufmatch->save( );
        $user = user_load( array( 'uid' => $ufmatch->uf_id ) );
        user_save( $user, array( 'mail' => $email ) );
        $user = user_load( array( 'uid' => $ufmatch->uf_id ) );
    }

    /**
     * Delete the object records that are associated with this contact
     *
     * @param  int  $contactId id of the contact to delete
     *
     * @return void
     * @access public
     * @static
     */
    static function deleteContact( $contactId ) {
        $ufmatch =& new CRM_Core_DAO_UFMatch( );

        $ufmatch->contact_id = $contactId;
        $ufmatch->delete( );
    }

    /**
     * get the contact_id given a uf_if
     *
     * @param int $uf_id 
     *
     * @return int contact_id
     * @access public
     * @static
     */
    static function getContactId( $ufID ) {

        //CRM_Core_Error::le_method();
        //CRM_Core_Error::debug_var( 'ufID', $ufID );
        
        if (!isset($ufID)) {
            return null;
        }

        $ufmatch =& new CRM_Core_DAO_UFMatch( );

        $ufmatch->uf_id = $ufID;
        if ( $ufmatch->find( true ) ) {
            return $ufmatch->contact_id;
        }
        return null;
    }

}

?>
