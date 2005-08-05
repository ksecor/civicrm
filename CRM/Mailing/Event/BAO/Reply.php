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
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Mailing_Event_BAO_Reply extends CRM_Mailing_Event_DAO_Reply {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Register a reply event. 
     *
     * @param int $job_id       The job ID of the reply
     * @param int $queue_id     The queue event id
     * @param string $hash      The hash
     * @return string|null      The email address to forward the reply to, or null on failure
     * @access public
     * @static
     */
    public static function reply($job_id, $queue_id, $hash) {
        /* First make sure there's a matching queue event */
        $q =& CRM_Mailing_Event_BAO_Queue::verify($job_id, $queue_id, $hash);

        if (! $q) {
            return null;
        }

        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailings = CRM_Mailing_BAO_Mailing::getTableName();
        $jobs = CRM_Mailing_BAO_Job::getTableName();
        $mailing->query(
            "SELECT * FROM  $mailings 
            INNER JOIN      $jobs 
                ON          $jobs.mailing_id = $mailings.id
            WHERE           $jobs.id = {$q->job_id}");
        $mailing->fetch();

        if (! $mailing->forward_replies || empty($mailing->replyto_email)) {
            return null;
        }

        $re =& new CRM_Mailing_Event_BAO_Reply();
        $re->event_queue_id = $queue_id;
        $re->time_stamp = date('YmdHis');
        $re->save();
        return $mailing->replyto_email;
    }
}

?>
