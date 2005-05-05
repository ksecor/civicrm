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

require_once 'CRM/Core/Page/Basic.php';

class CRM_Admin_Page_RelationshipType extends CRM_Core_Page_Basic {
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    static $_links = array(
                           CRM_Core_Action::VIEW  => array(
                                                        'name'  => 'View',
                                                        'url'   => 'civicrm/admin/reltype',
                                                        'qs'    => 'action=view&id=%%id%%',
                                                        'title' => 'View Relationship Type'),
                           CRM_Core_Action::UPDATE  => array(
                                                        'name'  => 'Edit',
                                                        'url'   => 'civicrm/admin/reltype',
                                                        'qs'    => 'action=update&id=%%id%%',
                                                        'title' => 'Edit Relationship Type'),
                           CRM_Core_Action::DISABLE => array(
                                                        'name'  => 'Disable',
                                                        'url'   => 'civicrm/admin/reltype',
                                                        'qs'    => 'action=disable&id=%%id%%',
                                                        'extra' => 'onclick = "return confirm(\'Are you sure you want to disable this relationship type.\n\nUsers will no longer be able to select this value when adding or editing relationships between contacts.\');"',
                                                        'title' => 'Disable Relationship Type',
                                                        ),
                           CRM_Core_Action::ENABLE  => array(
                                                        'name'  => 'Enable',
                                                        'url'   => 'civicrm/admin/reltype',
                                                        'qs'    => 'action=enable&id=%%id%%',
                                                        'title' => 'Enable Relationship Type',
                                                        ),
                           );

    function getBAOName( ) {
        return 'CRM_Contact_BAO_RelationshipType';
    }

    function &links( ) {
        return self::$_links;
    }

    function editForm( ) {
        return 'CRM_Admin_Form_RelationshipType';
    }

    function editName( ) {
        return 'Relationship Types';
    }

    function UserContext( ) {
        return 'civicrm/admin/reltype';
    }

}

?>