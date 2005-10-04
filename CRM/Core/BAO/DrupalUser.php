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

/** 
 *  this file contains functions for gender
 */

require_once 'DB.php';

class CRM_Core_BAO_DrupalUser  
{

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
            die( "Cannot connect to drupal db via $dsn, " . $db_drupal->getMessage( ) );
        }
        
        $sql   = "SELECT uid, mail FROM users where mail != ''";
        $query = $db_drupal->query( $sql );
        
        $user           = null;
        $uf             = 'Drupal';
        $contactCount   = 0;
        $contactCreated = 0;
        while ( $row = $query->fetchRow( DB_FETCHMODE_ASSOC ) ) {
            $contactCount++;
            if ( CRM_Core_BAO_UFMatch::synchronizeUFMatch( $user, $row['uid'], $row['mail'], $uf ) ) {
                $contactCreated++;
            }
        }
        
        $db_drupal->disconnect( );
        
        //end of schronization code
        
        CRM_Core_Session::setStatus( ts('Synchronize Users to Contacts completed. Checked "%1" user records. Created "%2" new contact records.', array( 1 => $contactCount, 2 => $contactCreated )) );
        CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/admin', 'reset=1' ) );
   
    }
  
}

?>
