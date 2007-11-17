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

require_once 'facebook/facebook.php';
require_once 'facebook/facebookapi_php5_restlib.php';

class CRM_Core_BAO_Facebook extends CRM_Core_DAO
{
    static $_key    = '73e4552c69857ce0b5436f397335f301';
    static $_secret = '2f20f697438a812543b2982395f241df';
            
    /**
     * Function to add entry to facebook table
     */
    static function create( &$params ) 
    {
        $facebook =& new CRM_Core_DAO_Facebook( );
        $facebook->copyValues($params);
        
        if ( !$facebook->find() ) {
            self::deleteEntry( $params['contact_id'] );
            // create permanant session key
            self::generateSession( $facebook );
        }
    }

    /**
     * Function to generate permanant session if its not created
     *
     */
    static function generateSession( &$facebook )
    {
        $api = new FacebookRestClient( self::$_key, self::$_secret );
        
        $sessionDetails = $api->auth_getSession( $facebook->gen_key );   
        
        if ( is_array( $sessionDetails ) ) {
            $facebook->session_key = $sessionDetails['session_key'];
            $facebook->user_id     = $sessionDetails['uid'];
            $facebook->save( );
        } else {
            CRM_Core_Error::fatal("Error occurred while processing your Facebook Generated Key.");
        }
    } 

    /**
     * Delete existing facebook entry
     */
    static function deleteEntry( $contactId )
    {
        $sql = "DELETE FROM civicrm_facebook WHERE contact_id = {$contactId}";
        
        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
 
    }

}

?>
