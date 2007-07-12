<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * class to represent the actions that can be performed on a group of
 * contacts (CiviMember)
 * used by the search forms
 *
 */
class CRM_Member_Task {
    const
        DELETE_MEMBERS       =  1,
        PRINT_MEMBERS        =  2,
        EXPORT_MEMBERS       =  3,
        EMAIL_CONTACTS       =  4;

    /**
     * the task array
     *
     * @var array
     * @static
     */
    static $_tasks = null;

    /**
     * the optional task array
     *
     * @var array
     * @static
     */
    static $_optionalTasks = null;

    /**
     * These tasks are the core set of tasks that the user can perform
     * on a contact / group of contacts
     *
     * @return array the set of tasks for a group of contacts
     * @static
     * @access public
     */
    static function &tasks()
    {
        if (!(self::$_tasks)) {
            self::$_tasks = array(
                                  3  => ts( 'Export Members' ),
                                  1  => ts( 'Delete Members' ),
                                  4  => ts( 'Send Email to Contacts' ),
                                  );
        }
        asort(self::$_tasks); 
        return self::$_tasks;
    }

}

?>
