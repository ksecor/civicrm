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
 * Page for displaying list of IM Providers.
 */
class CRM_Admin_Page_IMProvider extends CRM_Core_Page_Basic 
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
        return 'CRM_Core_BAO_IMProvider';
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
            $disableExtra = ts('Are you sure you want to disable this IM Service Provider?\n\nUsers will no longer be able to select this value when adding or editing contact IM screen names.');

            self::$_links = array( 
                                  CRM_Core_Action::UPDATE  => array( 
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/IMProvider',
                                                                    'qs'    => 'action=update&id=%%id%%',
                                                                    'title' => ts( 'IM Provider' ) 
                                                                   ),
                                  CRM_Core_Action::DISABLE => array( 
                                                                    'name'  => ts('Disable'),
                                                                    'url'   => 'civicrm/admin/IMProvider',
                                                                    'qs'    => 'action=disable&id=%%id%%',
                                                                    'extra' => 'onclick = "return confirm(\''. $disableExtra . '\');"',
                                                                    'title' => ts('Disable IM Service Provider') 
                                                                   ),
                                  CRM_Core_Action::ENABLE  => array( 
                                                                    'name'  => ts('Enable'),
                                                                    'url'   => 'civicrm/admin/IMProvider',
                                                                    'qs'    => 'action=enable&id=%%id%%',
                                                                    'title' => ts( 'Enable IM Service Provider' ) 
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
        return 'CRM_Admin_Form_IMProvider';
    }

    /**
     * Get page name
     *
     * @param none
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Instant Message Provider';
    }

    /**
     * Get user context.
     *
     * @param none
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/IMProvider';
    }
}

?>
