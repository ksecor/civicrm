<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * Menu for the civievent module
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

class CRM_Grant_Menu {

    static function permissioned( ) {
        $items = array(
                       array( 
                             'path'    => 'civicrm/grant',
                             'query'   => 'reset=1',
                             'title'   => ts('CiviGrant'), 
                             'access'  => CRM_Core_Permission::check( 'access CiviGrant'), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,
                             'weight'  => 1000,
                             ),
                       array( 
                              'path'    => 'civicrm/grant/info', 
                              'query'   => 'reset=1',
                              'access'  => CRM_Core_Permission::check( 'access CiviGrant'), 
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       );
        return $items;
    }

    static function &main( $task ) {
        $items = array( );
        switch ( $task ) {
        case 'grant':
            $items = array(
                           array( 
                                 'path'    => 'civicrm/grant/search',
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Find Grants' ),
                                 'access'  => CRM_Core_Permission::check( 'access CiviGrant' ), 
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 1010,  
                                 ),
                       
                           /*           array(
                                 'path'    => 'civicrm/grant/add',
                                 'query'   => 'action=add&reset=1',
                                 'title'   => ts( 'New Grant' ),
                                 'access'  => CRM_Core_Permission::check( 'access CiviGrant' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 1030,  
                                 ),

                           array(
                                 'path'    => 'civicrm/grant/import', 
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Import Grants' ),
                                 'access'  => CRM_Core_Permission::check('access CiviGrant'),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 1040,  
                                 ),*/
                           
                           );
            break;
        }
        return $items;
    }

}


