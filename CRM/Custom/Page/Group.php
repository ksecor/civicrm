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

class CRM_Custom_Page_Group extends CRM_Core_Page_Basic {
    
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    static $_links;
    
    function getBAOName()
    {
        return 'CRM_Core_BAO_CustomGroup';
    }

    static function &links()
    {

        if ( ! isset( self::$_links ) ) 
        {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this custom data group?');

	    self::$_links = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('View'),
                                                                    'url'   => 'civicrm/admin/custom/group',
                                                                    'qs'    => 'action=view&id=%%id%%',
                                                                    'title' => ts('View Custom Group'),
                                                                   ),
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/custom/group',
                                                                    'qs'    => 'action=update&id=%%id%%',
                                                                    'title' => ts('Edit Custom Group') 
                                                                   ),
                                  CRM_Core_Action::DISABLE => array(
                                                                    'name'  => ts('Disable'),
                                                                    'url'   => 'civicrm/admin/custom/group',
                                                                    'qs'    => 'action=disable&id=%%id%%',
                                                                    'title' => ts('Disable Custom Group'),
                                                                    'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                   ),
                                  CRM_Core_Action::ENABLE  => array(
                                                                    'name'  => ts('Enable'),
                                                                    'url'   => 'civicrm/admin/custom/group',
                                                                    'qs'    => 'action=enable&id=%%id%%',
                                                                    'title' => ts('Enable Custom Group'),
                                                                   ),
                                  CRM_Core_Action::BROWSE  => array(
                                                                    'name'  => ts('List'),
                                                                    'url'   => 'civicrm/admin/custom/group/field',
                                                                    'qs'    => 'reset=1&action=browse&gid=%%id%%',
                                                                    'title' => ts('List Custom Group Fields'),
                                                                   ),
                                 );
        }
        return self::$_links;
    }

    function editForm()
    {
        return 'CRM_Custom_Form_Group';
    }

    function editName()
    {
        return 'Custom Groups';
    }


    function userContext($mode=null)
    {
        return 'civicrm/admin/custom/group';
    }

}

?>