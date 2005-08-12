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

class CRM_Mailing_Event_BAO_Bounce extends CRM_Mailing_Event_DAO_Bounce {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }


    /**
     * Create a new bounce event, update the email address if necessary
     */
    static function &create(&$params) {
        $q =& CRM_Mailing_Event_BAO_Queue::verify($params['job_id'],
                $params['event_queue_id'], $params['hash']);
        if (! $q) {
            return null;
        }

        CRM_Core_DAO::transaction('BEGIN');
        $bounce =& new CRM_Mailing_Event_BAO_Bounce();
        $bounce->time_stamp = date('YmdHis');
        $bounce->copyValues($params);
        $bounce->save();

        $bounceTable    = CRM_Mailing_Event_BAO_Bounce::getTableName();
        $bounceType     = CRM_Mailing_DAO_BounceType::getTableName();
        $emailTable     = CRM_Core_BAO_Email::getTableName();
        $queueTable     = CRM_Mailing_Event_BAO_Queue::getTableName();
        
        $bounce->reset();
        $query =
                "SELECT     count($bounceTable.id) as bounces,
                            $bounceType.hold_threshold as threshold
                FROM        $bounceTable
                INNER JOIN  $bounceType
                        ON  $bounceTable.bounce_type_id = $bounceType.id
                INNER JOIN  $queueTable
                        ON  $bounceTable.event_queue_id = $queueTable.id
                INNER JOIN  $emailTable
                        ON  $queueTable.email_id = $emailTable.id
                WHERE       $emailTable.id = {$q->email_id}
                    AND     ($emailTable.reset_date IS NULL
                        OR  $bounceTable.time_stamp >= $emailTable.reset_date)
                GROUP BY    $bounceTable.bounce_type_id
                ORDER BY    threshold, bounces desc";
                                
        $bounce->query($query);

        while ($bounce->fetch()) {
            if ($bounce->bounces >= $bounce->threshold) {
                $email =& new CRM_Core_BAO_Email();
                $email->id = $q->email_id;
                $email->on_hold = true;
                $email->hold_date = date('YmdHis');
                $email->save();
                break;
            }
        }
        CRM_Core_DAO::transaction('COMMIT');
    }
}

?>
