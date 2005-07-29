<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Mailing_BAO_MailingEventQueue extends CRM_Mailing_Event_DAO_Queue {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Queue a new recipient
     *
     * @param array     The values of the new EventQueue
     * @return object   The new EventQueue
     * @access public
     * @static
     */
    public static function &create(&$params) {
        $eq =& new CRM_Mailing_BAO_MailingEventQueue();
        $eq->copyValues($params);
        $eq->hash = self::hash($params);
        $eq->save();
    }

    /**
     * Create a security hash from the job, email and contact ids
     *
     * @param array     The ids to be hashed
     * @return int      The hash
     * @access public
     * @static
     */
    public static function hash($params) {
        $jobId      = $params['job_id'];
        $emailId    = $params['email_id'];
        $contactId  = $params['contact_id'];

        return sha1($jobId . $emailId . $contactId);
    }
}

?>
