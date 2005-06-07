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

$GLOBALS['_CRM_GROUP_PAGE_GROUP']['_links'] =  array(
                           CRM_CORE_ACTION_VIEW    => array(
                                                        'name'  => 'Show Group Members',
                                                        'url'   => 'civicrm/group/search',
                                                        'qs'    => 'reset=1&force=1&context=smog&gid=%%id%%',
                                                        'title' => 'Group Members',
                                                        ),
                           CRM_CORE_ACTION_UPDATE  => array(
                                                        'name'  => 'Edit',
                                                        'url'   => 'civicrm/group',
                                                        'qs'    => 'reset=1&action=update&id=%%id%%',
                                                        'title' => 'Edit Group'),
                           CRM_CORE_ACTION_DELETE  => array(
                                                        'name'  => 'Delete',
                                                        'url'   => 'civicrm/group',
                                                        'qs'    => 'reset=1&action=delete&id=%%id%%',
                                                        'title' => 'Delete Group'),
                           );


require_once 'CRM/Core/Page/Basic.php';
require_once 'CRM/Core/Page/Basic.php';

class CRM_Group_Page_Group extends CRM_Core_Page_Basic {
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    ___

    function getBAOName( ) {
        return 'CRM_Contact_BAO_Group';
    }

    function &links( ) {
        return $GLOBALS['_CRM_GROUP_PAGE_GROUP']['_links'];
    }

    function editForm( ) {
        return 'CRM_Group_Form_Edit';
    }

    function editName( ) {
        return 'Edit Group';
    }

    function deleteForm( ) {
        return 'CRM_Group_Form_Delete';
    }

    function deleteName( ) {
        return 'Delete Group';
    }

    function userContext( $mode = null ) {
        return 'civicrm/group';
    }

    function userContextParams( $mode = null ) {
        return 'reset=1&action=browse';
    }

    /**
     * make sure that the user has permission to access this group
     *
     * @param int $id   the id of the object
     * @param int $name the name or title of the object
     *
     * @return string   the permission that the user has (or null)
     * @access public
     */
    function checkPermission( $id, $title ) {
        if ( user_access( 'edit all contacts' ) || user_access( 'edit ' . $title ) ) {
            return 'edit';
        }

        if ( user_access( 'view all contacts' ) || user_access( 'view ' . $title ) ) {
            return 'view';
        } 
        
        return null;
    }

}

?>
