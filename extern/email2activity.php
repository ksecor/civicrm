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

// This is a prototype script to convert email messages (raw/EML format)
// to activities. In order to be able to run it, you need to download 
// ezComponents (http://ezcomponents.org/) and store in packages.orig 


// initialise CiviCRM
ini_set( 'include_path', '.' . PATH_SEPARATOR . 
                         '..' . PATH_SEPARATOR . 
                         '..' . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR . 
                         '..' . DIRECTORY_SEPARATOR . 'packages.orig' . PATH_SEPARATOR );

echo ini_get( 'include_path' );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'api/v2/Activity.php';
require_once 'api/v2/Contact.php';

$config =& CRM_Core_Config::singleton();

require_once 'ezcomponents/Mail/docs/tutorial/tutorial_autoload.php';

// get ready for collecting data about activity to be created
$params = array();
$params['activity_type_id'] = 5; // Frontline Action

// explode email to digestable format
$set = new ezcMailFileSet( array( $argv[1] ) );
$parser = new ezcMailParser();
$mail = $parser->parseMail( $set );


// retrieve sender's email address and
// lookup database contact based on email
// we cannot use civicrm_contact_search, since it uses only primary email
// let's do a direct query
$from_email = $mail[0]->from->email;
$dao =& CRM_Core_DAO::executeQuery( "select contact_id from civicrm_email where email like '{$from_email}'",
                                    CRM_Core_DAO::$_nullArray );
while ( $dao->fetch( ) ) {
    $source_contact_id = $dao->contact_id;
}
if( empty( $source_contact_id ) ) {
  die( "\n\n Source contact with address {$from_email} not found!\n\n" );
}
$params['source_contact_id'] = $params['assignee_contact_id'] = $source_contact_id;


// retrieve first recipient from To: field
$to_email = $mail[0]->to[0]->email;
$dao =& CRM_Core_DAO::executeQuery( "select contact_id from civicrm_email where email like '{$to_email}'",
                                    CRM_Core_DAO::$_nullArray );
while ( $dao->fetch( ) ) {
    $target_contact_id = $dao->contact_id;
}
if( empty( $target_contact_id ) ) {
  die( "\n\n Target contact with address {$to_email} not found!\n\n" );
}
$params['target_contact_id'] = $target_contact_id;

// define other parameters

$params['subject'] = $mail[0]->subject;

//CRM_Core_Error::debug( 's', $params );

// create activity
$msg = civicrm_activity_create( &$params );

// debug
// print_r($msg);

?>
