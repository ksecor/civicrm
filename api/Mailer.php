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
 * API functions for registering/processing mailer events.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required for this package
 */

require_once 'PEAR.php';
require_once 'CRM/Core/Error.php';
require_once 'api/utils.php';

require_once 'api/Contact.php';
require_once 'api/Group.php';
require_once 'CRM/Contact/BAO/Group.php';
require_once 'CRM/Mailing/BAO/BouncePattern.php';
require_once 'CRM/Mailing/Event/BAO/Bounce.php';
require_once 'CRM/Mailing/Event/BAO/Confirm.php';
require_once 'CRM/Mailing/Event/BAO/Opened.php';
require_once 'CRM/Mailing/Event/BAO/Queue.php';
require_once 'CRM/Mailing/Event/BAO/Reply.php';
require_once 'CRM/Mailing/Event/BAO/Subscribe.php';
require_once 'CRM/Mailing/Event/BAO/Unsubscribe.php';


/**
 * Process a bounce event by passing through to the BAOs.
 *
 * @param int $job          ID of the job that caused this bounce
 * @param int $queue        ID of the queue event that bounced
 * @param string $hash      Security hash
 * @param string $body      Body of the bounce message
 * @return void
 */
function crm_mailer_event_bounce($job, $queue, $hash, $body) {
    
    $params = CRM_Mailing_BAO_BouncePattern::match($body);
    
    $params += array(
                'job_id'            => $job,
                'event_queue_id'    => $queue,
                'hash'              => $hash);

    CRM_Mailing_Event_BAO_Bounce::create($params);
}


/**
 * Handle an unsubscribe event
 *
 * @param int $job          ID of the job that caused this unsub
 * @param int $queue        ID of the queue event
 * @param string $hash      Security hash
 * @return void
 */
function crm_mailer_event_unsubscribe($job, $queue, $hash) {
    $groups =& CRM_Mailing_Event_BAO_Unsubscribe::unsub_from_mailing($job, $queue, $hash);
    
    $email = CRM_Mailing_Event_BAO_Queue::getEmailAddress($queue);
    
    if ($email && count($groups)) {
        CRM_Mailing_Event_BAO_Unsubscribe::send_unsub_response($email, $groups);
    }
}

?>
