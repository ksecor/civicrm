<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
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
        GROUP_CONTACTS        =     1,
        REMOVE_CONTACTS       =     2,
        TAG_CONTACTS          =     4,
        DELETE_CONTACTS       =     8,
        SAVE_SEARCH           =    16,
        SAVE_SEARCH_UPDATE    =    32,
        PRINT_CONTACTS        =    64,
        EMAIL_CONTACTS        =   128,
        HOUSEHOLD_CONTACTS    =   512,
        ORGANIZATION_CONTACTS =  1024,
        MAP_CONTACTS          =  2048,
        EXPORT_CONTACTS       =  4096,
        RECORD_CONTACTS       =  8192;

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
                                  1     => ts( 'Add Contacts to a Group'       ),
                                  2     => ts( 'Remove Contacts from a Group'  ),
                                  4     => ts( 'Tag Contacts (assign tags)'    ),
                                  4096  => ts( 'Export Contacts'               ),
                                  128   => ts( 'Send Email to Contacts'        ), 
                                  8     => ts( 'Delete Contacts'               ),
                                  512   => ts( 'Add Contacts to Household'     ),
                                  1024  => ts( 'Add Contacts to Organization'  ),
                                  8192  => ts( 'Record Activity for Contacts'  ),
                                  2048  => ts( 'Map Contacts'                  ),
                                  16    => ts( 'New Smart Group'               ),
                                  );
            $config =& CRM_Core_Config::singleton( );
            if ( ! isset( $config->smtpServer ) ||
                 $config->smtpServer == 'YOUR SMTP SERVER' ) {
                unset( self::$_tasks[128] );
            }
        }
        return self::$_tasks;
    }

    /**
     * show tasks selectively based on the permission level
     * of the user
     *
     * @param int $permission
     *
     * @return array set of tasks that are valid for the user
     * @access public
     */
    static function &permissionedTasks( $permission ) {
        if ( $permission == CRM_Core_Permission::EDIT ) {
            return self::tasks( );
        } else {
            $tasks = array( 
                           128  => ts( 'Send Email to Contacts'        ), 
                           4096 => ts( 'Export Contacts'               ),
                           2048 => ts( 'Map Contacts using Google Maps'),
                           );
            return $tasks;

        }
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
                32 => ts('Update Smart Group')
            );
        }
        return self::$_optionalTasks;
    }

}

?>
