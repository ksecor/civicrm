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

class CRM_Mailing_Event_BAO_Confirm extends CRM_Mailing_Event_DAO_Confirm {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Confirm a pending subscription
     *
     * @param int $contact_id       The id of the contact
     * @param int $subscribe_id     The id of the subscription event
     * @param string $hash          The hash
     * @return void
     * @access public
     * @static
     */
    public static function confirm($contact_id, $subscribe_id, $hash) {
        $se =& CRM_Mailing_Event_BAO_Subscribe::verify($contact_id,
                                            $subscribe_id, $hash);
        
        if (! $se) {
            return;
        }

        CRM_Core_DAO::transaction('BEGIN');
        
        $ce =& new CRM_Mailing_Event_BAO_Confirm();
        $ce->event_subscribe_id = $se->id;
        $ce->time_stamp = date('YmdHis');
        $ce->save();
        
        CRM_Contact_BAO_GroupContact::updateGroupMembershipStatus(
                $contact_id, $se->group_id,'Email',$ce->id);
        
        CRM_Core_DAO::transaction('COMMIT');
    }
}

?>
