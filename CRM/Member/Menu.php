<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
                       'civicrm/member' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts('CiviMember'), 
                             'access_arguments'  => array( array( 'access CiviMember' ) ), 
                             'page_type'  => CRM_Core_Menu::MENU_ITEM,
                             'weight'     => 700,  
                             'component'  => 'CiviMember',  
                             ),
                       );
        return $items;
    }

    static function &main( ) {
        $items = array(
                       'civicrm/admin/member/membershipType' => 
                       array(
                             'title'   => ts('Membership Types'),
                             'desc'    => ts('Define the types of memberships you want to offer. For each type, you can specify a \'name\' (Gold Member, Honor Society Member...), a description, duration, and a minimum fee.'), 
                             'query'  => array('reset' => 1),
                             'adminGroup' => 'CiviMember',
                             'icon'    => 'admin/small/membership_type.png',
                             'weight'  => 370
                             ),
                       
                       'civicrm/admin/member/membershipStatus' => 
                       array(
                             'title'   => ts('Membership Status Rules'),
                             'desc'    => ts('Status \'rules\' define the current status for a membership based on that membership\'s start and end dates. You can adjust the default status options and rules as needed to meet your needs.'), 
                             'query'  => array('reset' => 1),
                             'adminGroup' => 'CiviMember',
                             'icon'    => 'admin/small/membership_status.png',
                             'weight'  => 380
                             ),
                       
                       'civicrm/contact/view/membership' => 
                       array( 
                             'query'   => array('reset' => 1, 'force' => 1, 'cid' =>'%%cid%%'),
                             'title'   => ts('Memberships'), 
                             'weight'  => 2
                             ),
                       
                       'civicrm/member/search' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Find Members' ),
                             'access_arguments'  => array( array( 'access CiviMember' ) ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 710,  
                             ),
                       
                       'civicrm/member/import' => 
                       array(
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Import Members' ), 
                             'access_arguments'  => array( array( 'access CiviMember' ) ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 720,  
                             ),
                       );
        
        return $items;
    }
}


