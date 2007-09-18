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
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'api/v2/utils.php';

/**
 * Create a log for an email
 *
 */
function civicrm_email_history_add( &$params ) {
    // make sure we have all the parameters
    $required = array( 'subject', 'message', 'contact_id' );
    foreach ( $required as $r ) {
        if ( ! array_key_exists( $r, $params ) ) {
            return civicrm_create_error( ts( '%1 is a required field',
                                             array( 1 => $r ) ) );
        }
    }

    $recipientIDs = $recipientNames = array( );
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 12 ) == 'recipient_id' ) {
            $recipientIDs[]   = $v;
            $name = str_replace( '_id', '_name', $n );
            $recipientNames[] = CRM_Utils_Array::value( $name, $params, ts( 'To name not set' ) );
        }
    }
    if ( empty( $recipientIDs ) ) {
        return civicrm_create_error( ts( 'recipient_id is a required field' ) );
    }        

    // first create the email history meta record
    require_once 'CRM/Core/BAO/EmailHistory.php';
    $emailHistory =& CRM_Core_BAO_EmailHistory::add( $params );

    // now create a activity history record for all the folks
    // that this message was sent to
    $history = array('entity_table'     => 'civicrm_contact',
                     'activity_type'    => ts('Email Sent'),
                     'module'           => 'CiviCRM',
                     'callback'         => 'CRM_Core_BAO_EmailHistory::showEmailDetails',
                     'activity_id'      => $emailHistory->id,
                     'activity_date'    => date('YmdHis')
                     );

    $contactID = $params['contact_id'];
    $subject   = $params['subject'];
    $fromName  = CRM_Utils_Array::value( 'from_name', $params, ts( 'From name not set' ) );
    for ( $i = 0; $i < count( $recipientIDs ); $i++ ) {
        $history['entity_id']        = $recipientIDs[$i];
        $history['activity_summary'] = ts( 'To: %1; Subject: %2',
                                           array( 1 => $recipientNames[$i],
                                                  2 => $subject ) );

        $ids = array( );
        CRM_Core_BAO_History::create( $history, $ids, 'Activity' );

        $history['entity_id'] = $contactID;
        $history['from_name'] = $fromName;
        CRM_Core_BAO_History::create( $history, $ids, 'Activity' );
    }
}

?>
