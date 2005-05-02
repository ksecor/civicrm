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

class CRM_Admin_Page_IMProvider extends CRM_Page_Basic {
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    static $_links = array(
                           CRM_Action::UPDATE  => array(
                                                        'name'  => 'Edit',
                                                        'url'   => 'civicrm/admin/IMProvider',
                                                        'qs'    => 'action=update&id=%%id%%',
                                                        'title' => 'IM Provider'),
                           );

    function getBAOName( ) {
        return 'CRM_BAO_IMProvider';
    }

    function &links( ) {
        return self::$_links;
    }

    function formClass( ) {
        return 'CRM_Admin_Form_IMProvider';
    }

    function formName( ) {
        return 'Instant Message Provider';
    }

    function UserContext( ) {
        return 'civicrm/admin/IMProvider';
    }

}

?>