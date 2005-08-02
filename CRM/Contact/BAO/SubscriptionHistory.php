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

require_once 'CRM/Contact/DAO/SubscriptionHistory.php';

/**
 * BAO object for crm_email table
 */
class CRM_Contact_BAO_SubscriptionHistory extends CRM_Contact_DAO_SubscriptionHistory {

    function __construct() {
        parent::__construct();
    }
    
    /**
     * Create a new subscription history record
     *
     * @param array $params     Values for the new history record
     * @return object $history  The new history object
     * @access public
     * @static
     */
    public static function &create(&$params) {
        $history =& new CRM_Contact_BAO_SubscriptionHistory();
        $history->date = date('Ymd');
        $history->copyValues($params);
        $history->save();
        return $history;
    }

    /**
     * Erase a contact's subscription history records
     *
     * @param int $id       The contact id
     * @return none
     * @access public
     * @static
     */
    public static function deleteContact($id) {
        $history =& new CRM_Contact_BAO_SubscriptionHistory();
        $history->contact_id = $id;
        $history->delete();
    }
}

?>
