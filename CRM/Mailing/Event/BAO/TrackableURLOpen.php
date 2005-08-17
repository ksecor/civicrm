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

class CRM_Mailing_Event_BAO_TrackableURLOpen extends CRM_Mailing_Event_DAO_TrackableURLOpen{

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Track a click-through and return the URL to redirect.  If the numbers
     * don't match up, return the base url.
     *
     * @param int $queue_id     The Queue Event ID of the clicker
     * @param int $url_id       The ID of the trackable URL
     * @return string $url      The redirection url, or base url on failure.
     * @access public
     * @static
     */
    public static function track($queue_id, $url_id) {
        $search =& new CRM_Mailing_BAO_TrackableURL();
        
        /* To find the url, we also join on the queue and job tables.  This
         * prevents foreign key violations. */
       
        $job = CRM_Mailing_BAO_Job::getTableName();
        $eq = CRM_Mailing_Event_BAO_Queue::getTableName();
        $turl = CRM_Mailing_BAO_TrackableURL::getTableName();
        
        $search->query("SELECT $turl.url as url from $turl
                    INNER JOIN $job ON $turl.mailing_id = $job.mailing_id
                    INNER JOIN $eq ON $job.id = $eq.job_id
                    WHERE $eq.id = " 
                        . CRM_Utils_Type::escape($queue_id, 'Integer') 
                . " AND $turl.id = " 
                        . CRM_Utils_Type::escape($url_id, 'Integer')
        );
        
        if (! $search->fetch()) {
            /* Whoops, error, don't track it.  Return the base url. */
            return CRM_Utils_System::baseURL();
        }

        $open =& new CRM_Mailing_Event_BAO_TrackableURLOpen();
        $open->event_queue_id = $queue_id;
        $open->trackable_url_id = $url_id;
        $open->time_stamp = date('YmdHis');
        $open->save();

        return $search->url;
    }
}

?>
