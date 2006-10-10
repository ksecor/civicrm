<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Member/DAO/MembershipLog.php';

class CRM_Member_BAO_MembershipLog extends CRM_Member_DAO_MembershipLog 
{

    /**
     * function to add the membership types
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) 
    {
        $membershipLog              =& new CRM_Member_DAO_MembershipLog( );
        $membershipLog->copyValues( $params );
        
        $membershipLog->save( );
        
        return $membershipLog;
    }

    /**
     * Function to delete membership Types 
     * 
     * @param int $membershipTypeId
     * @static
     */
    
    static function del( $membershiID ) 
    {
        $membershipLog  =& new CRM_Member_DAO_MembershipLog( );
        $membershipLog->membership_id = $membershiID ;
        return $membershipLog->delete();
    }


}    