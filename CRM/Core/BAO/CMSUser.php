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

/** 
 *  this file contains functions for synchronizing cms users with CiviCRM contacts
 */

require_once 'DB.php';

class CRM_Core_BAO_CMSUser  
{
    /**
     * Function for synchronizing cms users with CiviCRM contacts
     *  
     * @param NULL
     * 
     * @return void
     * 
     * @static
     * @access public
     */
    static function synchronize( ) 
    {
        //start of schronization code
        $config =& CRM_Core_Config::singleton( );
        
        $db_uf = DB::connect($config->userFrameworkDSN);
        if ( DB::isError( $db_uf ) ) { 
            die( "Cannot connect to UF db via $dsn, " . $db_uf->getMessage( ) ); 
        } 
 
        if ( $config->userFramework == 'Drupal' ) { 
            $id   = 'uid'; 
            $mail = 'mail'; 
            $name = 'name';
        } else if ( $config->userFramework == 'Joomla' ) { 
            $id   = 'id'; 
            $mail = 'email'; 
            $name = 'name';
        } else { 
            die( "Unknown user framework" ); 
        } 


        $sql   = "SELECT $id, $mail, $name FROM {$config->userFrameworkUsersTableName} where $mail != ''";
        $query = $db_uf->query( $sql );
        
        $user            = new StdClass( );
        $uf              = $config->userFramework;
        $contactCount    = 0;
        $contactCreated  = 0;
        $contactMatching = 0;
        while ( $row = $query->fetchRow( DB_FETCHMODE_ASSOC ) ) {
            $user->$id   = $row[$id];
            $user->$mail = $row[$mail];
            $user->$name = $row[$name];
            $contactCount++;
            if ( CRM_Core_BAO_UFMatch::synchronizeUFMatch( $user, $row[$id], $row[$mail], $uf, 1 ) ) {
                $contactCreated++;
            } else {
                $contactMatching++;
            } 
        }
        
        $db_uf->disconnect( );
        
        //end of schronization code
        $status = ts('Synchronize Users to Contacts completed.');
        $status .= ' ' . ts('Checked one user record.', array('count' => $contactCount, 'plural' => 'Checked %count user records.'));
        if ($contactMatching) {
            $status .= ' ' . ts('Found one matching contact record.', array('count' => $contactMatching, 'plural' => 'Found %count matching contact records.'));
        }
        $status .= ' ' . ts('Created one new contact record.', array('count' => $contactCreated, 'plural' => 'Created %count new contact records.'));
        CRM_Core_Session::setStatus($status);
        CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/admin', 'reset=1' ) );
    }

    /**
     * Function to create CMS user using Profile
     *
     * @param array  $params associated array 
     * @param string $mail email id for cms user
     *
     * @return int contact id that has been created
     * @access public
     * @static
     */
    static function create ( &$params, $mail ) {
        $config  =& CRM_Core_Config::singleton( );
        
        $isDrupal = ucfirst($config->userFramework) == 'Drupal' ? TRUE : FALSE;
        $isJoomla = ucfirst($config->userFramework) == 'Joomla' ? TRUE : FALSE;
        $version  = $config->userFrameworkVersion;

        if ( $isDrupal && $version >= 5.1 ) {
            $values = array( 
                            'name' => $params['cms_name'],
                            'mail' => $params[$mail],
                            );
            if ( !variable_get('user_email_verification', TRUE )) {
                $values['pass'] = array('pass1' => $params['cms_pass'],
                                        'pass2' => $params['cms_confirm_pass']);
                
            }
            
            // we also need to redirect b
            $config->inCiviCRM = true;
            
            $res = drupal_execute( 'user_register', $values );
            
            $config->inCiviCRM = false;
            
            if ( form_get_errors( ) ) {
                return false;
            }
            return true;
        } elseif ( $isJoomla ) {            
            self::createJoomlaUser( &$params, $mail );
        }
    }

    /**
     * Function to create Form for CMS user using Profile
     *
     * @param object  $form
     * @param integer $gid id of group of profile
     * @param string $emailPresent true, if the profile field has email(primary)
     *
     * @access public
     * @static
     */ 
    static function buildForm ( &$form, $gid, $emailPresent, $action = CRM_Core_Action::NONE) 
    {                                    
        $config =& CRM_Core_Config::singleton( );
        $showCMS = false;
        
        $isDrupal = ucfirst($config->userFramework) == 'Drupal' ? TRUE : FALSE;
        $isJoomla = ucfirst($config->userFramework) == 'Joomla' ? TRUE : FALSE;
        $version  = $config->userFrameworkVersion;
        
        // if cms is drupal having version greater than equal to 5.1
        // we also need email verification enabled, else we dont do it
        // then showCMS will true
        if ( ( $isDrupal  && $version >=5.1 && variable_get('user_email_verification', TRUE ) ) OR ( $isJoomla ) ) {
            if ( $gid ) {                                        
                $isCMSUser = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $gid, 'is_cms_user' );
            } 
            // $cms is true when there is email(primary location) is set in the profile field.
            $session =& CRM_Core_Session::singleton( );                         
            $userID  = $session->get( 'userID' );      
            $showUserRegistration = false;
            if ( $action ) { 
                $showUserRegistration = true;
            }elseif (!$action && !$userID ) { 
                $showUserRegistration = true;
            }
            if ( $isCMSUser && $emailPresent ) {                
                if ( $showUserRegistration ) {  
                    $extra = array('onclick' => "return showHideByValue('cms_create_account', '', 'details','block','radio',false );");
                    $form->addElement('checkbox', 'cms_create_account', ts('Create an account?'), null, $extra);
                    require_once 'CRM/Core/Action.php';
                    if( ! $userID || $action & CRM_Core_Action::PREVIEW || $action & CRM_Core_Action::PROFILE ) {     
                        $form->add('text', 'cms_name', ts('Username') );
                        if ( ( $isDrupal && !variable_get('user_email_verification', TRUE ) ) OR ( $isJoomla ) ) {       
                            $form->add('password', 'cms_pass', ts('Password') );
                            $form->add('password', 'cms_confirm_pass', ts('Confirm Password') );
                        } 
                        
                        $form->addFormRule( array( 'CRM_Core_BAO_CMSUser', 'formRule' ), $form );
                    } 
                    $showCMS = true;
                } 
            }
            
        } 
        $form->assign( 'showCMS', $showCMS ); 
    } 
    
    static function formRule( &$fields, &$files, &$self ) {
        if ( CRM_Utils_Array::value( 'cms_create_account', $fields ) ) {
            $config  =& CRM_Core_Config::singleton( );
            
            $isDrupal = ucfirst($config->userFramework) == 'Drupal' ? TRUE : FALSE;
            $isJoomla = ucfirst($config->userFramework) == 'Joomla' ? TRUE : FALSE;
            $version  = $config->userFrameworkVersion;

            if ( ( $isDrupal && $version >= 5.1 ) OR ( $isJoomla ) ) {
                $errors = array( );
                $emailName = null;
                if ( ! empty( $self->_bltID ) ) {
                    // this is a transaction related page
                    $emailName = 'email-' . $self->_bltID;
                } else {
                    // find the email field in a profile page
                    foreach ( $fields as $name => $dontCare ) {
                        if(substr( $name, 0, 5 ) == 'email' ) {
                            $emailName = $name;
                            break;
                        }
                    }
                }
                
                if ( $emailName == null ) {
                    $errors['_qf_default'] == ts( 'Could not find an email address.' );
                    return $errors;
                }

                if ( empty( $fields['cms_name'] ) ) {
                    $errors['cms_name'] = ts( 'Please specify a username.' );
                }
                
                if ( empty( $fields[ $emailName ] ) ) {
                    $errors[$emailName] = ts( 'Please specify a valid email address.' );
                }
                
                if ( ( $isDrupal && $version >= 5.1 && ! variable_get('user_email_verification', TRUE ) ) OR ( $isJoomla ) ) {
                    if ( empty( $fields['cms_pass'] ) ||
                         empty( $fields['cms_confirm_pass'] ) ) {
                        $errors['cms_pass'] = ts( 'Please enter a password.' );
                    }
                    if ( $fields['cms_pass'] != $fields['cms_confirm_pass'] ) {
                        $errors['cms_pass'] = ts( 'Password and Confirm Password values are not the same.' );
                    }
                }
                
                if ( ! empty( $errors ) ) {
                    return $errors;
                }
                
                // now check that the cms db does not have the user name and/or email
                if ( ( $isDrupal && $version ) OR $isJoomla ) {
                    $params = array( 'name' => $fields['cms_name'],
                                     'mail' => $fields[$emailName] );
                }
                
                self::checkUserNameEmailExists( $params, $errors, $emailName );
                
                if ( ! empty( $errors ) ) {
                    return $errors;
                }

            }
        }
        return true;
    }

    /**
     * Check if username and email exists in the drupal db
     * 
     * @params $params    array   array of name and mail values
     * @params $errors    array   array of errors
     * @params $emailName string  field label for the 'email'
     *
     * @return void
     * @static
     */
    static function checkUserNameEmailExists( &$params, &$errors, $emailName = 'email' )
    {
        $config  =& CRM_Core_Config::singleton( );

        $isDrupal = ucfirst($config->userFramework) == 'Drupal' ? TRUE : FALSE;
        $isJoomla = ucfirst($config->userFramework) == 'Joomla' ? TRUE : FALSE;
        $version  = $config->userFrameworkVersion;
        
        if ( $isDrupal && $version >= 5.1 ) {
            _user_edit_validate(null, $params );
            $errors = form_get_errors( );
        }
        
        if ( $errors ) {
            if ( CRM_Utils_Array::value( 'name', $errors ) ) {
                $errors['cms_name'] = $errors['name'];
            } 
            
            if ( CRM_Utils_Array::value( 'mail', $errors ) ) {
                $errors[$emailName] = $errors['mail'];
            } 
            
            // also unset drupal messages to avoid twice display of errors
            unset( $_SESSION['messages'] );
        }
        
        // drupal api sucks
        // do the name check manually
        //$nameError = user_validate_name( $fields['cms_name'] );
        if ( $isDrupal && $version >= 5.1 ) {
            $nameError = user_validate_name( $params['name'] );
            if ( $nameError ) {
                $errors['cms_name'] = $nameError;
            }
        }
        
        
        $dao =& new CRM_Core_DAO( );
        $name = $dao->escape( $params['name'] );
        if ($isDrupal && $version >= 5.1 ) {
            $sql = "
SELECT count(*)
  FROM {$config->userFrameworkUsersTableName}
 WHERE LOWER(name) = LOWER('$name')
";
        } elseif ( $isJoomla ) {
            $sql = "
SELECT count(*)
  FROM {$config->userFrameworkUsersTableName}
 WHERE LOWER(username) = LOWER('$name')
";
                }
        $db_cms = DB::connect($config->userFrameworkDSN);
        if ( DB::isError( $db_cms ) ) { 
            die( "Cannot connect to UF db via $dsn, " . $db_cms->getMessage( ) ); 
        }
        $query = $db_cms->query( $sql );
        $row = $query->fetchRow( );
        if ( $row[0] >= 1 ) {
            $errors['cms_name'] = ts( 'The username %1 is already taken. Please select another username.', array( 1 => $name) );
        }
    }
    
    /**
     * Function to check if a drupal user already exists.
     *  
     * @param  Array $contact array of contact-details
     *
     * @return uid if user exists, false otherwise
     * 
     * @access public
     * @static
     */
    static function userExists( &$contact ) 
    {
        $config =& CRM_Core_Config::singleton( );
        
        $db_uf = DB::connect($config->userFrameworkDSN);
        
        if ( DB::isError( $db_uf ) ) { 
            die( "Cannot connect to UF db via $dsn, " . $db_uf->getMessage( ) ); 
        } 
        
        if ( $config->userFramework != 'Drupal' ) { 
            die( "Unknown user framework" ); 
        }
        
        $sql   = "SELECT uid FROM {$config->userFrameworkUsersTableName} where mail='" . $contact['email'] . "'";
        
        $query = $db_uf->query( $sql );
        
        if ( $row = $query->fetchRow( DB_FETCHMODE_ASSOC ) ) {
            $contact['user_exists'] = true;
            $result = $row['uid'];
        } else {
            $result = false;
        }
        
        $db_uf->disconnect( );
        return $result;
    }
    
    /**
     * Function to create a user of Joomla.
     *  
     * @param array  $params associated array 
     * @param string $mail email id for cms user
     *
     * @return uid if user exists, false otherwise
     * 
     * @access public
     * @static
     */
    static function createJoomlaUser( &$params, $mail ) 
    {
        $config =& CRM_Core_Config::singleton( );
        $dao =& new CRM_Core_DAO( );
        $name = $dao->escape( $params['cms_name'] );
        
        $fname = trim($params['cms_name']);
        $uname = trim($params['cms_name']);
        $pwd   = md5($params['cms_pass']);
        $email = trim($params[$mail]); 
        $date  = date('y-m-d h:i:s');
        
        //In Joomla, Registerd User is fixed to 18.
        $regiterUser = '18';
       
        $sql = "INSERT INTO {$config->userFrameworkUsersTableName} VALUES 
              ('', '$fname', '$uname', '$email', '$pwd', 'Registered', 1, 0, $regiterUser, '$date', '0000-00-00 00:00:00', '', '')";
       
        $db_cms = DB::connect($config->userFrameworkDSN);
        
        if ( DB::isError( $db_cms ) ) { 
            die( "Cannot connect to UF db via $dsn, " . $db_cms->getMessage( ) ); 
        }
        $query = $db_cms->query( $sql );
        
        return true;
    }
    
}
?>
