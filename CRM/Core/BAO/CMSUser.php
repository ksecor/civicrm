<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 *  this file contains functions for synchronizing drupal users with CiviCRM contacts
 */

require_once 'DB.php';

class CRM_Core_BAO_CMSUser  
{
    /**
     * Function for synchronizing drupal users with CiviCRM contacts
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
        
        /**
         * Update the next line with the correct Drupal database user, password, db_server and db name
         * for your Drupal installation.
         */

        $db_drupal = DB::connect($config->userFrameworkDSN);
        if ( DB::isError( $db_drupal ) ) { 
            die( "Cannot connect to UF db via $dsn, " . $db_drupal->getMessage( ) ); 
        } 
 
        if ( $config->userFramework == 'Drupal' ) { 
            $id   = 'uid'; 
            $mail = 'mail'; 
        } else if ( $config->userFramework == 'Joomla' ) { 
            $id   = 'id'; 
            $mail = 'email'; 
        } else { 
            die( "Unknown user framework" ); 
        } 


        $sql   = "SELECT $id, $mail FROM {$config->userFrameworkUsersTableName} where $mail != ''";
        $query = $db_drupal->query( $sql );
        
        $user            = null;
        $uf              = 'Drupal';
        $contactCount    = 0;
        $contactCreated  = 0;
        $contactMatching = 0;
        while ( $row = $query->fetchRow( DB_FETCHMODE_ASSOC ) ) {
            $contactCount++;
            if ( CRM_Core_BAO_UFMatch::synchronizeUFMatch( $user, $row[$id], $row[$mail], $uf, 1 ) ) {
                $contactCreated++;
            } else {
                $contactMatching++;
            } 
        }
        
        $db_drupal->disconnect( );
        
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
    static function create ( &$params, $mail ) 
        {
        $config  =& CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Drupal' && $config->userFrameworkVersion >= 5.1 ) {
            $values = array( 
                            'name' => $params['cms_name'],
                            'mail' => $params[$mail],
                            );
            if ( !variable_get('user_email_verification', TRUE )) {
                $values['pass'] = array('pass1' => $params['cms_pass'],
                                        'pass2' => $params['cms_confirm_pass']);

            }

            $config->cmsCall = true;
            
            $res = drupal_execute( 'user_register', $values );
            
            $config->cmsCall = false;

            if ( form_get_errors( ) ) {
                return false;
            }
            return true;
        }
    }

    /**
     * Function to create Form for CMS user using Profile
     *
     * @param object  $form
     * @param integer $gid id of group of profile
     *
     * @param string $cms true, if the profile field has email(primary)
     * @access public
     * @static
     */
    static function buildForm ( &$form, $gid, $cms ) 
    {
        $config =& CRM_Core_Config::singleton( );
        $showCMS = false;
        // if cms is drupal having version greater than equal to 5.1
        // then showCMS will true
        if ( $config->userFramework == 'Drupal' && $config->userFrameworkVersion >=5.1 ) {
            if ( $gid ) {
                $cmsUser = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $gid, 'is_cms_user' );
            }
            // $cms is true when there is email(primary location) is set in the profile field.
            if ( $cmsUser && $cms) {
                $extra = array('onclick' => "if (this.checked) showMessage(this); return showHideByValue('create_account', '', 'details','block','radio',false );");
                $form->addElement('checkbox', 'create_account', ts('Create an account for CMS?'), null, $extra);
                $session =& CRM_Core_Session::singleton( );
                $cmsCid = $session->get( 'userID' );
                if( ! $cmsCid ) {
                    $form->add('text', 'cms_name', ts('User Name') );
                    if ( !variable_get('user_email_verification', TRUE )) {
                        $form->add('password', 'cms_pass', ts('Password') );
                        $form->add('password', 'cms_confirm_pass', ts('Confirm Password') );
                    }
                    $form->addFormRule( array( 'CRM_Core_BAO_CMSUser', 'formRule' ), $form );
                }
                $showCMS = true;
            } 
        }
        $form->assign( 'showCMS', $showCMS ); 
    }

    static function formRule( &$fields, &$files, &$self ) {
        if ( CRM_Utils_Array::value( 'create_account', $fields ) ) {
            $config  =& CRM_Core_Config::singleton( );
            if ( $config->userFramework == 'Drupal' && $config->userFrameworkVersion >= 5.1 ) {
                $errors = array( );
                $emailName = 'email-' . $self->_bltID;
                if ( empty( $fields['cms_name'] ) ) {
                    $errors['cms_name'] = ts( 'Please specify a CMS user name' );
                }
                if ( empty( $fields[ $emailName ] ) ) {
                    $errors[$emailName] = ts( 'Please specify a valid email address' );
                }
                if ( ! variable_get('user_email_verification', TRUE ) ) {
                    if ( empty( $fields['cms_pass'] ) ||
                         empty( $fields['cms_confirm_pass'] ) ) {
                        $errors['cms_pass'] = ts( 'Please enter a password' );
                    }
                    if ( $fields['cms_pass'] != $fields['cms_confirm_pass'] ) {
                        $errors['cms_pass'] = ts( 'The password fields do not match' );
                    }
                }

                if ( ! empty( $errors ) ) {
                    return $errors;
                }

                // now check that the drupal db does not have the user name and/or email
                $params = array( 'name' => $fields['name'],
                                 'mail' => $fields[$emailName] );
                _user_edit_validate(null, $params );
                $errors = form_get_errors( );
                if ( $errors ) {
                    if ( CRM_Utils_Array::value( 'name', $errors ) ) {
                        $errors['cms_name'] = $errors['name'];
                    } else if ( CRM_Utils_Array::value( 'mail', $errors ) ) {
                        $errors[$emailName] = $errors['mail'];
                    } else {
                        $errors['cms_name'] = $errors[$emailName] = implode( '<br/>', array_values( $errors ) );
                    }
                    // also unset drupal messages to avoid twice display of errors
                    unset( $_SESSION['messages'] );
                }
                if ( ! empty( $errors ) ) {

                    return $errors;
                }


            }
        }
        return true;
    }

}

?>
