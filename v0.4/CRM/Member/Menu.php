<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * Menu for the civimember module
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

class CRM_Member_Menu {

    static function permissioned( ) {
        $items = array(
                         array( 
                             'path'    => 'civicrm/member', 
                             'query'   => 'reset=1',
                             'title'   => ts('CiviMember'), 
                             'access'  => CRM_Core_Permission::check( 'access CiviMember'), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,
                             'weight'  => 700,  
                             ),
                         );
        return $items;
    }

    static function &main( $task ) {
        $items = array( );
        switch ( $task ) {
        case 'admin':
            $items = array(
                           array(
                                 'path'    => 'civicrm/admin/member/membershipType',
                                 'title'   => ts('Membership Types'),
                                 'desc'    => ts('Define the types of memberships you want to offer. For each type, you can specify a "name" (Gold Member, Honor Society Member...), a description, duration, and a minimum fee.'), 
                                 'query'  => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviMember',
                                 'icon'    => 'admin/small/membership_type.png',
                                 'weight'  => 370
                                 ),
                      
                           array(
                                 'path'    => 'civicrm/admin/member/membershipStatus',
                                 'title'   => ts('Membership Status Rules'),
                                 'desc'    => ts('Status "rules" define the current status for a membership based on that membership\'s start and end dates. You can adjust the default status options and rules as needed to meet your needs.'), 
                                 'query'  => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviMember',
                                 'icon'    => 'admin/small/membership_status.png',
                                 'weight'  => 380
                                 ),
                       
                           );
            break;

        case 'contact':
            $items = array(
                           array( 
                                 'path'    => 'civicrm/contact/view/membership', 
                                 'query'   => 'reset=1&force=1&cid=%%cid%%', 
                                 'title'   => ts('Memberships'), 
                                 'type'    => CRM_Core_Menu::CALLBACK, 
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK, 
                                 'weight'  => 2
                                 ),
                           );
            break;

        case 'member':
            $items = array(
                           array( 
                                 'path'    => 'civicrm/member/search',
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Find Members' ),
                                 'access'  => CRM_Core_Permission::check( 'access CiviMember' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 710,  
                                 ),
                       
                           array(
                                 'path'    => 'civicrm/member/import', 
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Import Members' ), 
                                 'access'  => CRM_Core_Permission::check( 'access CiviMember' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 720,  
                                 )
                           );
            break;
        }
        return $items;
    }

}

?>
