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
    static function creatCMSUser ( &$params, $mail ) 
    {
        $config  =& CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Drupal' && $config->userFrameworkVersion >= 5.1 ) {
            if ( $params['create_account'] ) {
                $values = array( 
                                'name' => $params['name'],
                                'pass' => array('pass1' => $params['pass'],
                                                'pass2' => $params['confirm_pass']),
                                'mail' => $params[$mail],
                                );

                drupal_execute( 'user_register', $values );

                if ( form_get_errors( ) ) {
                    return false;
                }
                return true;
            }
        }
    }

}

?>
