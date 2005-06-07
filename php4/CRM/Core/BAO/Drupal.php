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



require_once 'CRM/Core/DAO/Drupal.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Core/Error.php';
require_once 'api/crm.php';
require_once 'CRM/Core/Session.php';

/**
 * The basic class that interfaces with Drupal CMS.
 */
class CRM_Core_BAO_Drupal extends CRM_Core_DAO_Drupal {
    /**
     * Given a Drupal user object, make sure there is a contact
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
     function synchronize( &$user, $update = false ) {
        $session =& CRM_Core_Session::singleton( );
        if ( ! is_object( $session ) ) {
            return;
        }
        
        // have we already processed this user, if so early
        // return
        $userID = $session->get( 'userID' );
        $ufID   = $session->get( 'ufID'   );
        if ( ! $update && $ufID == $user->uid ) {
            return;
        }

        // reset the session if we are a different user
        if ( $ufID && $ufID != $user->uid ) {
            $session->reset( );
        }

        // make sure that a contact id exists for this user id
        $drupal = new CRM_Core_DAO_Drupal( );
        $drupal->uid = $user->uid;
        if ( ! $drupal->find( true ) ) {
            $drupal->uid = $user->uid;

            $query = "
SELECT    crm_contact.id as contact_id, crm_contact.domain_id as domain_id
FROM      crm_contact
LEFT JOIN crm_location ON crm_contact.id  = crm_location.contact_id
LEFT JOIN crm_email    ON crm_location.id = crm_email.location_id
WHERE     crm_email.email = '" . $user->mail . "'";
  
            $dao = new CRM_Core_DAO( );
            $dao->query( $query );
            if ( $dao->fetch( ) ) {
                $drupal->contact_id = $dao->contact_id;
                $drupal->domain_id  = $dao->domain_id ;
            } else {
                $params= array( 'email' => $user->mail, 'location_type' => 'Home' );
                $contact =& crm_create_contact( $params, 'Individual' );
                // does not work for php4 
                //if ( $contact instanceof CRM_Core_Error ) {
                if (is_a($contact, CRM_Core_Error)) {
                    CRM_Core_Error::debug( 'error', $contact );
                    exit(1);
                }
                $drupal->contact_id = $contact->id;
                $drupal->domain_id  = $contact->domain_id ;
            }
            $drupal->save( );
        } 

        $session->set( 'ufID'    , $drupal->uid        );
        $session->set( 'userID'  , $drupal->contact_id );
        $session->set( 'domainID', $drupal->domain_id  ); 
        
        if ( $update ) {
            // some information has changed in the drupal core
            // replicate that information in civicrm
        }
    }

}

?>
