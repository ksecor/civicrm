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

class CRM_Mailing_BAO_MailingEventBounce extends CRM_Mailing_DAO_MailingEventBounce {

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
        $bounce =& new CRM_Mailing_BAO_MailingEventBounce();
        $bounce->copyValues($params);
        $bounce->save();

        $bounceTable    = $bounce->tableName();
        $bounceType     = CRM_Mailing_DAO_BounceType::tableName();
        
        $email  =& new CRM_Core_BAO_Email();
        $email->id = $bounce->email_id;
        $email->find(true);
        
        $bounce->reset();
        $query =
                "SELECT         count(id) as bounces,
                                $bounceType.hold_threshold as threshold
                FROM            $bounceTable
                INNER JOIN      $bounceType
                        ON      $bounceTable.bounce_type_id = $bounceType.id
                WHERE
                                $bounceTable.email_id = " . $email->id . "
                GROUP BY        $bounceTable.bounce_type_id";
                                
        if (isset($email->reset_date)) {
            $query .= " AND $bounceTable.time_stamp > " . $email->reset_date;
        }
        $bounce->query($query);
        $bounce->find();

        while ($bounce->fetch()) {
            if ($bounce->bounces >= $bounce->threshold) {
                $email->bounce_hold = 1;
                $email->hold_date = date('Ymd');
                $email->save();
                break;
            }
        }
    }
}

?>
