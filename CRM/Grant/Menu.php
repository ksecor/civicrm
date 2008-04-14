<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                  |
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
                       'civicrm/grant' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts('CiviGrant'), 
                             'access_arguments'  => array( array( 'access CiviGrant') ), 
                             'page_type' => CRM_Core_Menu::MENU_ITEM,
                             'weight'    => 1000,
                             'component' => 'CiviGrant',
                             ),
                       
                       'civicrm/grant/info' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'access_arguments'  => array( array( 'access CiviGrant' ) ), 
                             'weight'  => 0, 
                             ),
                       );
        return $items;
    }

    static function &main( ) {
        $items = array(
                       
                       'civicrm/grant/search' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Find Grants' ),
                             'access_arguments'  => array( array( 'access CiviGrant' ) ), 
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 1010,  
                             ),
                       
                       'civicrm/grant/add' => 
                       array(
                             'query'   => array('reset' => 1, 'action' => 'add'),
                             'title'   => ts( 'New Grant' ),
                             'access_arguments'  => array( array( 'access CiviGrant' ) ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 1030,  
                             ),
                       
                       'civicrm/grant/import' => 
                       array(
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Import Grants' ),
                             'access_arguments'  => array( array('access CiviGrant') ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 1040,  
                             ),
                       );

        return $items;
    }
}


