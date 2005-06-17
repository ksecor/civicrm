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

require_once 'CRM/Activity/Page.php';

/**
 * Page for displaying list of Calls
 */
class CRM_Activity_Page_Call extends CRM_Activity_Page 
{

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * Get BAO Name
     *
     * @param none
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Core_BAO_Call';
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

            self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/view/call',
                                                                    'qs'    => 'action=update&id=%%id%%',
                                                                    'title' => ts('Edit Call') 
                                                                    ),
                                  CRM_Core_Action::VIEW  => array(
                                                                  'name'  => ts('View'),
                                                                  'url'   => 'civicrm/contact/view/call',
                                                                    'qs'    => 'action=view&id=%%id%%',
                                                                  'title' => ts('View Call') 
                                                                  ),
                                  CRM_Core_Action::FOLLOWUP  => array(
                                                                  'name'  => ts('Follow-up'),
                                                                  'url'   => 'civicrm/contact/view/call',
                                                                    'qs'    => 'action=followup&id=%%id%%',
                                                                  'title' => ts('Follow-up Call') 
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
        return 'CRM_Activity_Form_Call';
    }

    /**
     * Get edit form name
     *
     * @param none
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Calls';
    }

    /**
     * Get user context.
     *
     * @param none
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/contact/view/call';
    }
      
}
?>
