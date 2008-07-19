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

    // check if there is an email
    if ( isset( $_REQUEST['email'] ) ) {
        $file = $_REQUEST['email'];
    } else {
        $file = "/Users/lobo/public_html/drupal6/files/civicrm/upload/lobo3.txt";
    }

    require_once 'api/v2/Activity.php';
    $result = civicrm_activity_process_email( $file, 1 );
    if ( $result['is_error'] ) {
        CRM_Core_Error::fatal( $result['error_message'] );
    }
}

run( );
