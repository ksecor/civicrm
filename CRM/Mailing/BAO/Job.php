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

class CRM_Mailing_BAO_Job extends CRM_Mailing_DAO_Job {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Initiate all pending/ready jobs
     *
     * @param void
     * @return void
     * @access public
     * @static
     */
    public static function runJobs() {
        $job =& new CRM_Mailing_BAO_Job();
        $jobTable = CRM_Mailing_DAO_Job::getTableName();
        
        $query = "  SELECT  *
                    FROM    $jobTable
                    WHERE   $jobTable.start_date IS null
                    AND     $jobTable.scheduled_date <= NOW()
                    ORDER BY $jobTable.scheduled_date ASC"

        $job->query($query);
        $job->find();

        while ($job->fetch()) {
            $job->execute();
        }
    }

    /**
     * Execute a job.  Queue recipients and generate content.
     *
     * @param void
     * @return void
     * @access public
     */
    public function execute() {
        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->id = $this->mailing_id;
        
        $recipients =& $mailing->getRecipients();

        foreach ($recipients as $recipient) {
            $params = array(
                'job_id'        => $this->id,
                'email_id'      => $recipient['email_id'],
                'contact_id'    => $recipient['contact_id']
            );
            CRM_Mailing_BAO_MailingEventQueue::create($params);
        }
    }
}

?>
