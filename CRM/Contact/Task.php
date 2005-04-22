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
        TAG_CONTACTS        =   2,
        ADD_TO_HOUSEHOLD    =   4,
        ADD_TO_ORGANIZATION =   8,
        DELETE_CONTACTS     =  16,
        PRINT_CONTACTS      =  32,
        EXPORT_CONTACTS     =  64;

    static $tasks = array(
                          self::GROUP_CONTACTS      => 'Add Contacts to a Group',
                          self::TAG_CONTACTS        => 'Tag Contacts (assign category)',
                          self::ADD_TO_HOUSEHOLD    => 'Add to Household',
                          self::ADD_TO_ORGANIZATION => 'Add to Organization',
                          self::DELETE_CONTACTS     => 'Delete',
                          self::PRINT_CONTACTS      => 'Print',
                          self::EXPORT_CONTACTS     => 'Export',
                          );
}

?>