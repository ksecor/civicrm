<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
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
        } else if ( $uf == 'Mambo' ) {
            $key  = 'id';
            $mail = 'email';
        } else {
            CRM_Utils_System::statusBounce(ts('Please set the user framework variable'));
        }

        // have we already processed this user, if so early
        // return.
        $userID = $session->get( 'userID' );
        $ufID   = $session->get( 'ufID'   );
        if ( ! $update && $ufID == $user->$key ) {
            return;
        }

        // reset the session if we are a different user
        if ( $ufID && $ufID != $user->$key ) {
            $session->reset( );
        }

        // make sure we load the mambo object to get valid information
        if ( $uf == 'Mambo' ) {
            $user->load( );
        }

        // if the id of the object is zero (true for drupal), return early
        if ( $user->$key == 0 ) {
            return;
        }

        $ufmatch =& self::synchronizeUFMatch( $user, $user->$key, $user->$mail, $uf );

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
UPDATE  civicrm_contact
LEFT JOIN civicrm_location ON ( civicrm_location.entity_table = 'civicrm_contact' AND
                                civicrm_contact.id  = civicrm_location.entity_id  AND
                                civicrm_location.is_primary = 1 )
LEFT JOIN civicrm_email    ON ( civicrm_location.id = civicrm_email.location_id   AND
                                civicrm_email.is_primary = 1    )
SET civicrm_email.email = '" . $user->$mail . "' WHERE civicrm_contact.id = " . $ufmatch->contact_id;
                
                CRM_Core_DAO::executeQuery( $query );
            }
        }
    }

    /**
     * Synchronize the object with the UF Match entry. Can be called stand-alone from
     * the drupalUsers script
     *
     * @param Object  $user    the drupal user object
     * @param string  $userKey the id of the user from the uf object
     * @param string  $mail    the email address of the user
     * @param string  $uf      the name of the user framework
     *
     * @return the ufmatch object that was found or created
     * @access public
     * @static
     */
    static function &synchronizeUFMatch( &$user, $userKey, $mail, $uf ) {
        // make sure that a contact id exists for this user id
        $ufmatch =& new CRM_Core_DAO_UFMatch( );
        $ufmatch->uf_id = $userKey;
        if ( ! $ufmatch->find( true ) ) {
            $query = "
SELECT    civicrm_contact.id as contact_id, civicrm_contact.domain_id as domain_id
FROM      civicrm_contact
LEFT JOIN civicrm_location ON ( civicrm_location.entity_table = 'civicrm_contact' AND
                                civicrm_contact.id  = civicrm_location.entity_id AND 
                                civicrm_location.is_primary = 1 )
LEFT JOIN civicrm_email    ON ( civicrm_location.id = civicrm_email.location_id   AND civicrm_email.is_primary = 1    )
WHERE     civicrm_email.email = '"  . CRM_Utils_Type::escape($mail, 'String') 
                                    . "'";
  
            $dao =& new CRM_Core_DAO( );
            $dao->query( $query );
            if ( $dao->fetch( ) ) {
                $ufmatch->contact_id = $dao->contact_id;
                $ufmatch->domain_id  = $dao->domain_id ;
                $ufmatch->email      = $mail           ;
            } else {
                if ( $uf == 'Mambo' ) {
                    CRM_Utils_System_Mambo::setEmail( $user );
                }
                
                $locationType   =& CRM_Core_BAO_LocationType::getDefault( );  
                $params= array( 'email' => $mail, 'location_type' => $locationType->name );
                $contact =& crm_create_contact( $params, 'Individual' );
                if ( is_a( $contact, 'CRM_Core_Error' ) ) {
                    CRM_Core_Error::debug( 'error', $contact );
                    exit(1);
                }
                $ufmatch->contact_id = $contact->id;
                $ufmatch->domain_id  = $contact->domain_id ;
                $ufmatch->email      = $mail    ;
            }
            $ufmatch->save( );
            return $ufmatch;
        }
        return 0;
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
SELECT    civicrm_email.email as email
FROM      civicrm_contact
LEFT JOIN civicrm_location ON ( civicrm_location.entity_table = 'civicrm_contact' AND
                                civicrm_contact.id  = civicrm_location.entity_id  AND
                                civicrm_location.is_primary = 1 )
LEFT JOIN civicrm_email    ON ( civicrm_location.id = civicrm_email.location_id   AND
                                civicrm_email.is_primary = 1    )
WHERE     civicrm_contact.id = " 
                            . CRM_Utils_Type::escape($contactId, 'Integer');

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
     * get the contact_id given a uf_id
     *
     * @param int $ufID
     *
     * @return int contact_id
     * @access public
     * @static
     */
    static function getContactId( $ufID ) {
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
