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
     * @param Object  $user    the drupal user object
     * @param boolean $update  has the user object been edited
     * @param         $uf
     * 
     * @return void
     * @access public
     * @static
     */
  static function synchronize( &$user, $update, $uf, $ctype ) {
        $session =& CRM_Core_Session::singleton( );
        if ( ! is_object( $session ) ) {
            CRM_Core_Error::fatal( 'wow, session is not an object?' );
            return;
        }
        
        //print "synchronize called with uniq_id " . $user->identity_url . "<br/>";

        if ( $uf == 'Drupal' ) {
            $key   = 'uid';
            $login = 'name';
            $mail  = 'mail';
        } else if ( $uf == 'Joomla' ) {
            $key   = 'id';
            $login = 'username';
            $mail  = 'email';
        } else if ( $uf == 'Standalone' ) {
            $key = 'id';
            $mail = 'email';
            $uniqId = $user->identity_url;
            $query = "SELECT uf_id FROM civicrm_uf_match WHERE user_unique_id = %1";
            $p = array( 1 => array( $uniqId, 'String' ) );
            $dao = CRM_Core_DAO::executeQuery( $query, $p );
            $result = $dao->getDatabaseResult( );
            if ( $result ) {
                $row = $result->fetchRow( );
                if ( $row ) {
                    $user->$key = $row['uf_id'];
                }
            }
            if ( ! $user->$key ) {
                // Let's get the next uf_id since we don't actually have one
                $user->$key = self::getNextUfIdValue( );
            }
        } else {
            CRM_Core_Error::statusBounce(ts('Please set the user framework variable'));
        }
        
        // have we already processed this user, if so early
        // return.
        $userID = $session->get( 'userID' );
        $ufID   = $session->get( 'ufID'   );
        if ( ! $update && $ufID == $user->$key ) {
            //print "Already processed this user<br/>";
            return;
        }

        // reset the session if we are a different user
        if ( $ufID && $ufID != $user->$key ) {
            $session->reset( );
        }

        // make sure we load the joomla object to get valid information
        if ( $uf == 'Joomla' ) {
            if ( class_exists( 'JFactory' ) ) {
                $user =& JFactory::getUser( );
            } else {
                $user->load( );
            }
        }

        // if the id of the object is zero (true for anon users in drupal)
        // return early
        if ( $user->$key == 0 ) {
            return;
        }

        if ( ! isset( $uniqId ) ||
             ! $uniqId ) {
            $uniqId = $user->$mail;
        }

        //print "Calling synchronizeUFMatch...<br/>";
        $ufmatch =& self::synchronizeUFMatch( $user, $user->$key, $uniqId, $uf, null, $ctype );
        if ( ! $ufmatch ) {
            return;
        }

        $session->set( 'ufID'    , $ufmatch->uf_id          );
        $session->set( 'userID'  , $ufmatch->contact_id     );
        $session->set( 'domainID', $ufmatch->domain_id      ); 
        $session->set( 'ufUniqID', $ufmatch->user_unique_id );

        if ( $update ) {
            // the only information we care about is uniqId, so lets check that
            if ( $uniqId != $ufmatch->user_unique_id ) {
                // uniqId has changed, so we need to update that everywhere
                $ufmatch->user_unique_id = $uniqId;
                $ufmatch->save( );
                
                /* I don't think we should do this here anymore, since
                   we don't use email address as the user identifier.
                   ----
                   However, we should revisit this (and probably 
                   make it start updating the OpenID) once we move
                   to using the OpenID associated w/ the contact
                   (rather than storing it as a field in civicrm_contact).
                   That's awaiting the schema re-design for 2.0.
                */
                //CRM_Contact_BAO_Contact::updatePrimaryEmail( $ufmatch->contact_id, $user->$mail );
            }
        }
    }

    /**
     * Synchronize the object with the UF Match entry. Can be called stand-alone from
     * the drupalUsers script
     *
     * @param Object  $user    the drupal user object
     * @param string  $userKey the id of the user from the uf object
     * @param string  $uniqId    the OpenID of the user
     * @param string  $uf      the name of the user framework
     * @param integer $status  returns the status if user created or already exits (used for CMS sync)
     *
     * @return the ufmatch object that was found or created
     * @access public
     * @static
     */
    static function &synchronizeUFMatch( &$user, $userKey, $uniqId, $uf, $status = null, $ctype = null ) 
    {
        // validate that uniqId is a valid url. it will either be
        // an OpenID (which should always be a valid url) or a
        // http://uf_username.domain/ construction (so that it can
        // be used as an OpenID in the future)
        require_once 'CRM/Utils/Rule.php';
        if ( $uf == 'Standalone' &&
             ! CRM_Utils_Rule::url( $uniqId ) ) {
            return $status ? null : false;
        }
        
        $newContact   = false;

        // make sure that a contact id exists for this user id
        $ufmatch =& new CRM_Core_DAO_UFMatch( );
        $ufmatch->uf_id = $userKey;
        $ufmatch->domain_id = CRM_Core_Config::domainID( );
        if ( ! $ufmatch->find( true ) ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $dao =& CRM_Contact_BAO_Contact::matchContactOnUniqId( $uniqId, $ctype );
            if ( $dao ) {
                //print "Found contact with uniqId $uniqId<br/>";
                $ufmatch->contact_id     = $dao->contact_id;
                $ufmatch->domain_id      = $dao->domain_id ;
                $ufmatch->user_unique_id = $uniqId;
            } else {
                if ( $uf == 'Drupal' ) {
                    $mail = 'mail';
                } else {
                    $mail = 'email';
                }
                
                require_once 'CRM/Core/BAO/LocationType.php';
                $locationType   =& CRM_Core_BAO_LocationType::getDefault( );  
                $params = array( 'user_unique_id' => $uniqId, 'location_type' => $locationType->name, 
                                 'email' => $user->$mail, 'openid' => $uniqId );
                if ( $ctype == 'Organization' ) {
                    $params['organization_name'] = $uniqId;
                } else if ( $ctype == 'Household' ) {
                    $params['household_name'] = $uniqId;
                }
                if ( ! $ctype ) {
                    $ctype = "Individual";
                }
                $params['contact_type'] = $ctype;

                // extract first / middle / last name
                // for joomla
                if ( $uf == 'Joomla' && $user->name ) {
                    $name = trim( $user->name );
                    $names = explode( ' ', $user->name );
                    if ( count( $names ) == 1 ) {
                        $params['first_name'] = $names[0];
                    } else if ( count( $names ) == 2 ) {
                        $params['first_name'] = $names[0];
                        $params['last_name' ] = $names[1];
                    } else {
                        $params['first_name' ] = $names[0];
                        $params['middle_name'] = $names[1];
                        $params['last_name'  ] = $names[2];
                    }
                }
                
                if ( $uf == 'Standalone' ) {
		            if ( ( ! empty( $user->first_name ) ) || ( ! empty( $user->last_name ) ) ) {
		                $params['first_name'] = $user->first_name;
		                $params['last_name'] = $user->last_name;
	                } elseif ( ! empty( $user->name ) ) {
	                    $name = trim( $user->name );
	                    $names = explode( ' ', $user->name );
	                    if ( count( $names ) == 1 ) {
	                        $params['first_name'] = $names[0];
	                    } else if ( count ( $names ) == 2 ) {
	                        $params['first_name'] = $names[0];
	                        $params['last_name' ] = $names[1];
	                    } else {
	                        $params['first_name' ] = $names[0];
	                        $params['middle_name'] = $names[1];
	                        $params['last_name'  ] = $names[2];
	                    }
	                }
		        }
		    
                require_once 'api/Contact.php';

                $contact =& crm_create_contact( $params, $ctype, false );
                
                if ( is_a( $contact, 'CRM_Core_Error' ) ) {
                    CRM_Core_Error::debug( 'error', $contact );
                    exit(1);
                }
                $ufmatch->contact_id     = $contact->id;
                $ufmatch->domain_id      = $contact->domain_id;
                $ufmatch->user_unique_id = $uniqId;
            }
            $ufmatch->save( );
            $newContact   = true;
        }

        if ( $status ) {
            return $newContact;
        } else {
            return $ufmatch;
        }
    }

    /**
     * update the user_unique_id in the user object
     *
     * @param int    $contactId id of the contact to update
     *
     * @return void
     * @access public
     * @static
     */
    static function updateUFUserUniqueId( $contactId ) {
        $openId = CRM_Contact_BAO_Contact::getPrimaryOpenId( $contactId );
        if ( ! $openId ) {
            return;
        }

        $ufmatch =& new CRM_Core_DAO_UFMatch( );
        $ufmatch->contact_id = $contactId;
        if ( ! $ufmatch->find( true ) ||
             $ufmatch->user_unique_id == $openId ) {
            // if object does not exist or the OpenID has not changed
            return;
        }

        // save the updated ufmatch object
        $ufmatch->user_unique_id = $openId;
        $ufmatch->save( );
        $config =& CRM_Core_Config::singleton( );
    }
    
    /**
     * set whether this user is allowed to login or not
     *
     * @param int    $contactId id of the contact to update
     * @param bool   $allowedToLogin whether or not this user should be 
     *                  allowed to login
     *
     * @return void
     * @access public
     * @static
     */
    static function setAllowedToLogin( $contactId, $allowedToLogin ) {
        $ufmatch =& new CRM_Core_DAO_UFMatch( );
        $ufmatch->contact_id = $contactId;
        
        $allowedToLoginValue = $allowedToLogin ? 1 : 0;
        
        if ( ! $ufmatch->find( true ) ||
             $ufmatch->allowed_to_login == $allowedToLoginValue ) {
            // if object does not exist or the login permission
            // has not changed
            return;
        }
        
        // save the updated ufmatch object
        $ufmatch->allowed_to_login = $allowedToLoginValue;
        $ufmatch->save( );
        $config =& CRM_Core_Config::singleton( );
    }
    
    /**
     * Update the email value for the contact and user profile
     *  
     * @param  $contactId  Int     Contact ID of the user
     * @param  $email      String  email to be modified for the user
     *
     * @return void
     * @access public
     * @static
     */
    static function updateContactEmail($contactId, $emailAddress) 
    {
        $ufmatch =& new CRM_Core_DAO_UFMatch( );
        $ufmatch->contact_id = $contactId;
        if ( $ufmatch->find( true ) ) {
            // Save the email in UF Match table
            $ufmatch->email = $emailAddress;
            $ufmatch->save( );
            
            //check if the primary email for the contact exists 
            //$contactDetails[1] - email 
            //$contactDetails[3] - location id
            $contactDetails = CRM_Contact_BAO_Contact::getEmailDetails($contactId);

            if (trim($contactDetails[1])) {
                //update if record is found
                $query ="UPDATE  civicrm_contact, civicrm_location,civicrm_email
                     SET email = %1
                     WHERE civicrm_location.entity_table = 'civicrm_contact' 
                       AND civicrm_contact.id  = civicrm_location.entity_id 
                       AND civicrm_location.is_primary = 1 
                       AND civicrm_location.id = civicrm_email.location_id 
                       AND civicrm_email.is_primary = 1   
                       AND civicrm_contact.id =  %2";
                $p = array( 1 => array( $emailAddress, 'String'  ),
                            2 => array( $contactId   , 'Integer' ) );
                $dao =& CRM_Core_DAO::executeQuery( $query, $p );
            } else {
                //else insert a new email record
                $email =& new CRM_Core_DAO_Email();
                $email->location_id = $contactDetails[3];
                $email->is_primary  = 1;
                $email->email       = $emailAddress; 
                $email->save( );
                $emailID = $email->id;
            }
            require_once 'CRM/Core/BAO/Log.php';
            // we dont know the email id, so we use the location id
            CRM_Core_BAO_Log::register( $contactId,
                                        'civicrm_location',
                                        $contactDetails[3] );
        }
    }
    
    /**
     * Delete the object records that are associated with this contact
     *
     * @param  int  $contactID id of the contact to delete
     *
     * @return void
     * @access public
     * @static
     */
    static function deleteContact( $contactID ) {
        $ufmatch =& new CRM_Core_DAO_UFMatch( );

        $ufmatch->contact_id = $contactID;
        $ufmatch->delete( );
    }

    /**
     * Delete the object records that are associated with this cms user
     *
     * @param  int  $ufID id of the user to delete
     *
     * @return void
     * @access public
     * @static
     */
    static function deleteUser( $ufID ) {
        $ufmatch =& new CRM_Core_DAO_UFMatch( );

        $ufmatch->uf_id = $ufID;
        $ufmatch->delete( );
    }

    /**
     * get the contact_id given a uf_id
     *
     * @param int  $ufID  Id of UF for which related contact_id is required
     *
     * @return int    contact_id on success, null otherwise
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

    /** 
     * get the uf_id given a contact_id 
     * 
     * @param int  $contactID   ID of the contact for which related uf_id is required
     * 
     * @return int    uf_id of the given contact_id on success, null otherwise
     * @access public 
     * @static 
     */ 
    static function getUFId( $contactID ) { 
        if (!isset($contactID)) { 
            return null; 
        } 
        
        $ufmatch =& new CRM_Core_DAO_UFMatch( ); 
        
        $ufmatch->contact_id = $contactID;
        if ( $ufmatch->find( true ) ) {
            return $ufmatch->uf_id;
        }
        return null;
    }
    
    /**
     * get the list of contact_id
     *
     *
     * @return int    contact_id on success, null otherwise
     * @access public
     * @static
     */
    static function getContactIDs() {
        $id = array();
        $dao =& new CRM_Core_DAO_UFMatch();
        $dao->find();
        while ($dao->fetch()) {
            $id[] = $dao->contact_id;
        }
        return $id;
    }
    
    /**
     * see if this user exists, and if so, if they're allowed to login
     *
     *
     * @return bool     true if allowed to login, false otherwise
     * @access public
     * @static
     */
    static function getAllowedToLogin( $openId ) {
        $ufmatch =& new CRM_Core_DAO_UFMatch( );
        $ufmatch->user_unique_id = $openId;
        $ufmatch->allowed_to_login = 1;
        if ( $ufmatch->find( true ) ) {
            return true;
        }
        return false;
    }
    
    /**
     * get the next unused uf_id value, since the standalone UF doesn't
     * have id's (it uses OpenIDs, which go in a different field)
     *
     *
     * @return int     next highest unused value for uf_id
     * @access public
     * @static
     */
    static function getNextUfIdValue( ) {
        $query = "SELECT MAX(uf_id)+1 AS next_uf_id FROM civicrm_uf_match";
        $dao =& new CRM_Core_DAO( );
        $dao->query( $query );
        $result = $dao->getDatabaseResult( );
        if ( $result ) {
            $row = $result->fetchRow( );
            if ( $row ) {
                $ufId = $row[0];
            }
        }
        if ( ! $ufId ) {
            $ufId = 1;
        }
        return $ufId;
    }
}
?>
