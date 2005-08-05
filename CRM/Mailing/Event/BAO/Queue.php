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

class CRM_Mailing_Event_BAO_Queue extends CRM_Mailing_Event_DAO_Queue {

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
        $eq =& new CRM_Mailing_Event_BAO_Queue();
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

        return sha1("{$jobId}{$emailId}{$contactId}");
    }
}

?>
