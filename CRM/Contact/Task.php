<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
        TAG_CONTACTS          =     3,
        REMOVE_TAGS           =     4,
        EXPORT_CONTACTS       =     5,
        EMAIL_CONTACTS        =     6,
        SMS_CONTACTS          =     7,
        DELETE_CONTACTS       =     8,
        HOUSEHOLD_CONTACTS    =     9,
        ORGANIZATION_CONTACTS =    10,
        RECORD_CONTACTS       =    11,
        MAP_CONTACTS          =    12,
        SAVE_SEARCH           =    13,
        SAVE_SEARCH_UPDATE    =    14,
        PRINT_CONTACTS        =    15,
        LABEL_CONTACTS        =    16;

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

    static function initTasks( ) {
        if ( ! self::$_tasks ) {
            self::$_tasks = array(
                                  1     => array( 'title'  => ts( 'Add Contacts to a Group'       ),
                                                  'class'  => 'CRM_Contact_Form_Task_AddToGroup',
                                                  'result' => true ),
                                  2     => array( 'title'  => ts( 'Remove Contacts from a Group'  ),
                                                  'class'  => 'CRM_Contact_Form_Task_RemoveFromGroup',
                                                  'result' => true ),
                                  3     => array( 'title'  => ts( 'Tag Contacts (assign tags)'    ),
                                                  'class'  => 'CRM_Contact_Form_Task_AddToTag',
                                                  'result' => true ),
                                  4     => array( 'title'  => ts( 'Untag Contacts (remove tags)'  ),  
                                                  'class'  => 'CRM_Contact_Form_Task_RemoveFromTag',
                                                  'result' => true ),
                                  5     => array( 'title'  => ts( 'Export Contacts'               ),
                                                  'class'  => array( 'CRM_Contact_Form_Task_Export_Select',
                                                                     'CRM_Contact_Form_Task_Export_Map' ),
                                                  'result' => false ),
                                  6     => array( 'title'  => ts( 'Send Email to Contacts'        ),
                                                  'class'  => 'CRM_Contact_Form_Task_Email',
                                                  'result' => true ),
                                  7     => array( 'title'  => ts( 'Send SMS to Contacts'          ),
                                                  'class'  => 'CRM_Contact_Form_Task_SMS',
                                                  'result' => true ),
                                  8     => array( 'title'  => ts( 'Delete Contacts'               ),
                                                  'class'  => 'CRM_Contact_Form_Task_Delete',
                                                  'result' => false ),
                                  9     => array( 'title'  => ts( 'Add Contacts to Household'     ),
                                                  'class'  => 'CRM_Contact_Form_Task_AddToHousehold',
                                                  'result' => true ),
                                  10    => array( 'title'  => ts( 'Add Contacts to Organization'  ),
                                                  'class'  => 'CRM_Contact_Form_Task_AddToOrganization',
                                                  'result' => true ),
                                  11    => array( 'title'  => ts( 'Record Activity for Contacts'  ),
                                                  'class'  => 'CRM_Contact_Form_Task_Record',
                                                  'result' => true ),
                                  12    => array( 'title'  => ts( 'Map Contacts'                  ),
                                                  'class'  => 'CRM_Contact_Form_Task_Map',
                                                  'result' => false ),
                                  13    => array( 'title'  => ts( 'New Smart Group'               ),
                                                  'class'  => 'CRM_Contact_Form_Task_SaveSearch',
                                                  'result' => true ),
                                  14    => array( 'title'  => ts( 'Update Smart Group'            ),
                                                  'class'  => 'CRM_Contact_Form_Task_SaveSearch_Update',
                                                  'result' => true ),
                                  15    => array( 'title'  => ts( 'Print Contacts'                ),
                                                  'class'  => 'CRM_Contact_Form_Task_Print',
                                                  'result' => false ),
                                  16    => array( 'title'  => ts( 'Make Mailing Lables'       ),
                                                  'class'  => 'CRM_Contact_Form_Task_Label',
                                                  'result' => true ),
                                  );

            self::$_tasks += CRM_Core_Component::taskList( );
        }
    }

    /**
     * These tasks are the core set of tasks that the user can perform
     * on a contact / group of contacts
     *
     * @return array the set of tasks for a group of contacts
     * @static
     * @access public
     */
    static function &taskTitles()
    {
        self::initTasks( );

        $titles = array( );
        foreach ( self::$_tasks as $id => $value ) {
            $titles[$id] = $value['title'];
        }

        // hack unset update saved search and print contacts
        unset( $titles[14] );
        unset( $titles[15] );

        $config =& CRM_Core_Config::singleton( );

        if ( ! isset( $config->smtpServer ) ||
             $config->smtpServer == '' ||
             $config->smtpServer == 'YOUR SMTP SERVER' ) {
            unset( $titles[6] );
        }
        
        if ( ! in_array( 'CiviSMS', $config->enableComponents ) ) {
            unset( $titles[7] );
        }

        return $titles;
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
    static function &permissionedTaskTitles( $permission ) {
        if ( $permission == CRM_Core_Permission::EDIT ) {
            return self::taskTitles( );
        } else {
            $tasks = array( 
                           5  => self::$_tasks[5] ['title'],
                           12 => self::$_tasks[12]['title'],
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
    static function &optionalTaskTitle()
    {
        $tasks = array(
                       14 => self::$_tasks[14]['title'],
                       );
        return $tasks;
    }

    static function getTask( $value ) {
        self::initTasks( );
        
        if ( ! CRM_Utils_Array::value( $value, self::$_tasks ) ) {
            $value = 15; // make it the print task by default
        }
        return array( self::$_tasks[$value]['class' ],
                      self::$_tasks[$value]['result'] );
    }

}

?>
