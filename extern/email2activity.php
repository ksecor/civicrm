<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
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

function run( ) {
    session_start( );

    require_once '../civicrm.config.php';
    require_once 'CRM/Core/Config.php';
    require_once 'CRM/Core/Error.php';

    $config =& CRM_Core_Config::singleton();

    // this does not return on failure
    CRM_Utils_System::authenticateScript( true );

    $file = "/Users/lobo/public_html/drupal6/files/civicrm/upload/lobo3.txt";
    process( $file );
    return;

    // check if there is an email
    if ( isset( $_REQUEST['email'] ) ) {
        process( $_REQUEST['email'] );
    } else {
        CRM_Core_Error::fatal( ts('Could not find email in input request') );
    }

}

function process( &$file ) {
    // might want to check that email is ok here

    require_once 'CRM/Utils/Mail/Incoming.php';
    $result = CRM_Utils_Mail_Incoming::parse( $file );
    if ( $result['is_error'] ) {
        CRM_Core_Error::fatal( $result['error_message'] );
    }

    // get ready for collecting data about activity to be created
    $params = array();
    $params['activity_type_id']   = 1; // Frontline Action
    $params['status_id']          = 1;
    $params['source_contact_id']  = $params['assignee_contact_id'] = $result['from']['id'];
    $params['target_contact_id']  = array( );
    $keys = array( 'to', 'cc', 'bcc' );
    foreach ( $keys as $key ) {
        if ( is_array( $result[$key] ) ) {
            foreach ( $result[$key] as $key => $keyValue ) {
                $params['target_contact_id'][]  = $keyValue['id'];
            }
        }
    }
    $params['subject']            = $result['subject'];
    $params['activity_date_time'] = $result['date'];
    $params['details']            = $result['body'];

    for ( $i = 1; $i <= 5; $i++ ) {
        if ( isset( $result["attachFile_$i"] ) ) {
            $params["attachFile_$i"] = $result["attachFile_$i"];
        }
    }

    // create activity
    require_once 'api/v2/Activity.php';
    $result = civicrm_activity_create( $params );
    if ( $result['is_error'] ) {
        CRM_Core_Error::fatal( $result['error_message'] );
    }
    
}

run( );
