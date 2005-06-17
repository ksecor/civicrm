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

/**
 * class to represent the actions that can be performed on a group of contacts
 * used by the search forms
 *
 */
class CRM_Contact_Task {
    const
        GROUP_CONTACTS      =   1,
        REMOVE_CONTACTS     =   2,
        TAG_CONTACTS        =   4,
        DELETE_CONTACTS     =   8,
        SAVE_SEARCH         =  16,
        SAVE_SEARCH_UPDATE  =  32,
        PRINT_CONTACTS      =  64,
        EMAIL_CONTACTS      = 128,
        HOUSEHOLD_CONTACTS  = 512,
        ORGANIZATION_CONTACTS = 1024;
    

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
                  1 => ts('Add Contacts to a Group'),
                  2 => ts('Remove Contacts from a Group'),
                  4 => ts('Tag Contacts (assign tags)'),
                128 => ts('Send Email to Contacts'),
                  8 => ts('Delete Contacts'),
                 16 => ts('New Saved Search'),
                512 => ts('Add Contacts to Household'),
               1024 =>ts('Add Contacts to Organization'),
            );
        }
        return self::$_tasks;
    }

    /**
     * These tasks get added based on the context the user is in
     *
     * @return array the set of optional tasks for a group of contacts
     * @static
     * @access public
     */
    static function &optionalTasks()
    {
        if (!(self::$_optionalTasks)) {
            self::$_optionalTasks = array(
                32 => ts('Update Saved Search')
            );
        }
        return self::$_optionalTasks;
    }

}

?>
