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
$GLOBALS['_CRM_CONTACT_TASK']['tasks'] =  array(
                          1   => 'Add Contacts to a Group',
                          2   => 'Remove Contacts from a Group',
                          4   => 'Tag Contacts (assign tags)',
                          8   => 'Delete Contacts',
                          16  => 'New Saved Search',
                          );


define( 'CRM_CONTACT_TASK_GROUP_CONTACTS',1);
define( 'CRM_CONTACT_TASK_REMOVE_CONTACTS',2);
define( 'CRM_CONTACT_TASK_TAG_CONTACTS',4);
define( 'CRM_CONTACT_TASK_DELETE_CONTACTS',8);
define( 'CRM_CONTACT_TASK_SAVE_SEARCH',16);
define( 'CRM_CONTACT_TASK_SAVE_SEARCH_UPDATE',32);
define( 'CRM_CONTACT_TASK_PRINT_CONTACTS',64);
$GLOBALS['_CRM_CONTACT_TASK']['optionalTasks'] =  array(
                                  32 => 'Update Saved Search',
                                  );


class CRM_Contact_Task {
    
                 
                
                   
                
                   
            
                

    

}

?>