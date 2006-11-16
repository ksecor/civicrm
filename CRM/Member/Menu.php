<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * Menu for the civimember module
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

class CRM_Member_Menu {

    static function &main( ) {
        $items = array(
                       array(
                             'path'    => 'civicrm/admin/member/membershipType',
                             'title'   => ts('Membership Types'),
                             'query'  => 'reset=1',
                             'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check( 'access CiviMember' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,
                             'adminGroup' => 'CiviMember',
                             'icon'    => 'admin/membership_type.png',
                             'weight'  => 370
                             ),
                      
                       array(
                             'path'    => 'civicrm/admin/member/membershipStatus',
                             'title'   => ts('Membership Status Rules'),
                             'query'  => 'reset=1',
                             'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check( 'access CiviMember' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,
                             'adminGroup' => 'CiviMember',
                             'icon'    => 'admin/membership_status.png',
                             'weight'  => 380
                             ),
                       
                       array(
                             'path'    => 'civicrm/admin/member/messageTemplates',
                             'title'   => ts('Message Templates'),
                             'query'  => 'reset=1',
                             'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check( 'access CiviMember' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,
                             'adminGroup' => 'CiviMember',
                             'icon'    => 'admin/template.png',
                             'weight'  => 390
                             ),

                       array( 
                             'path'    => 'civicrm/contact/view/membership', 
                             'query'   => 'reset=1&force=1&cid=%%cid%%', 
                             'access'  => CRM_Core_Permission::check('access CiviMember'),
                             'title'   => ts('Memberships'), 
                             'type'    => CRM_Core_Menu::CALLBACK, 
                             'crmType' => CRM_Core_Menu::LOCAL_TASK, 
                             'weight'  => 2
                             ),


                        array( 
                             'path'    => 'civicrm/member', 
                             'query'   => 'reset=1',
                             'title'   => ts('CiviMember'), 
                             'access'  => CRM_Core_Permission::check( 'access CiviMember'), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,
                             'weight'  => 700,  
                             ),

                       array( 
                             'path'    => 'civicrm/member/search',
                             'query'   => 'reset=1',
                             'title'   => ts( 'Find Members' ),
                             'access'  => CRM_Core_Permission::check( 'access CiviMember'), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 710,  
                             ),
                       
                       array(
                             'path'    => 'civicrm/member/import', 
                             'query'   => 'reset=1',
                             'title'   => ts( 'Import Members' ), 
                             'access' => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check('access CiviMember'),
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 720,  
                             )
                       );

        return $items;
    }

}

?>
