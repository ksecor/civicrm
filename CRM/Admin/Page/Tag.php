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

/**
 * Page for displaying list of categories
 */
class CRM_Admin_Page_Tag extends CRM_Core_Page_Basic 
{

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * Get BAO
     *
     * @param none
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Contact_BAO_Tag';
    }


    /**
     * Get action Links
     *
     * @param none
     * @return array (reference) of action links
     */
    function &links()
    {
        if (!(self::$_links)) {
            // helper variable for nicer formatting
            $deleteExtra = ts('Are you sure you want to delete this tag?\n\nThis tag will be removed from any currently tagged contacts, and users will no longer be able to assign contacts to this tag.');

            self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/tag',
                                                                    'qs'    => 'action=update&id=%%id%%',
                                                                    'title' => ts('Edit Tag') 
                                                                   ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/tag',
                                                                    'qs'    => 'action=delete&id=%%id%%',
                                                                    'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\') ) this.href+=\'&confirmed=1\'; else return false;"',
                                                                    'title' => ts('Delete Tag'), 
                                                                   ),
                                 );
        }
        return self::$_links;
    }

    /**
     * Get name of edit form
     *
     * @param none
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_Admin_Form_Tag';
    }

    /**
     * Get form name for edit form
     *
     * @param none
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Tag';
    }

    /**
     * Get form name for delete form
     *
     * @param none
     * @return string name of this page.
     */
    function deleteName() 
    {
        return 'Tag';
    }

    /**
     * Get user context.
     *
     * @param none
     * @return string user context.
     */
    function userContext( $mode = null ) 
    {
        return 'civicrm/admin/tag';
    }

    /**
     * Get name of delete form
     *
     * @param none
     * @return string Classname of delete form.
     */
   function deleteForm() 
   {
        return 'CRM_Admin_Form_Tag';
   }
}

?>
