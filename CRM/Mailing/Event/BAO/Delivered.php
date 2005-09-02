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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

class CRM_Mailing_Event_BAO_Delivered extends CRM_Mailing_Event_DAO_Delivered {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }


    /**
     * Create a new delivery event
     * @param array $params     Associative array of delivery event values
     * @return void
     * @access public
     * @static
     */
    public static function &create(&$params) {
        $q =& CRM_Mailing_Event_BAO_Queue::verify($params['job_id'],
            $params['event_queue_id'], $params['hash']);
        if (! $q) {
            return null;
        }
        $delivered =& new CRM_Mailing_Event_BAO_Delivered();
        $delivered->time_stamp = date('YmdHis');
        $delivered->copyValues($params);
        $delivered->save();

        return $delivered;
    }
}

?>
