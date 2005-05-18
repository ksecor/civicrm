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

class CRM_Admin_Page_IMProvider extends CRM_Core_Page_Basic {

    function getBAOName( ) {
        return 'CRM_Core_BAO_IMProvider';
    }

    function &links( ) {
        return self::_links();
    }

    function editForm( ) {
        return 'CRM_Admin_Form_IMProvider';
    }

    function editName( ) {
        return 'Instant Message Provider';
    }

    function userContext( $mode = null ) {
        return 'civicrm/admin/IMProvider';
    }

    /**
     * Static function returning action links 
     * that we need to display for the browse screen.
     *
     * @return array
     * @access private
     * @static
     */
    static private function _links() {

	$disableExtra = ts('Are you sure you want to disable this IM Service Provider?\n\nUsers will no longer be able to select this value when adding or editing contact IM screen names.');

	return array( CRM_Core_Action::UPDATE  => array( 'name'  => ts('Edit'),
                                                         'url'   => 'civicrm/admin/IMProvider',
                                                         'qs'    => 'action=update&id=%%id%%',
                                                         'title' => ts( 'IM Provider' ) ),
                      CRM_Core_Action::DISABLE => array( 'name'  => ts('Disable'),
                                                         'url'   => 'civicrm/admin/IMProvider',
                                                         'qs'    => 'action=disable&id=%%id%%',
                                                         'extra' => 'onclick = "return confirm(\''. $disableExtra . '\');"',
                                                         'title' => ts('Disable IM Service Provider'),
                                                        ),
                      CRM_Core_Action::ENABLE  => array( 'name'  => ts('Enable'),
                                                         'url'   => 'civicrm/admin/IMProvider',
                                                         'qs'    => 'action=enable&id=%%id%%',
                                                         'title' => ts( 'Enable IM Service Provider' ),
                                                         ),
                      );
    }

}

?>
