<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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
 * Menu for the contribute module
 * 
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

class CRM_Contribute_Menu {

    static function &permissioned( ) {
        $items = array( 
                       array( 
                             'path'    => 'civicrm/contribute', 
                             'query'   => 'reset=1',
                             'title'   => ts( 'CiviContribute' ), 
                             'access'  => CRM_Core_Permission::check( 'access CiviContribute'), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,
                             'weight'  => 500,  
                             ),

                       array( 
                             'path'    => 'civicrm/contribute/transact', 
                             'query'   => 'reset=1',
                             'title'   => ts( 'CiviContribute' ), 
                             'access'  => CRM_Core_Permission::check( 'make online contributions'), 
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
        case 'admin':
            $items = array(
                           array(
                                 'path'    => 'civicrm/admin/contribute',
                                 'title'   => ts('Manage Contribution Pages'),
                                 'desc'    => ts('CiviContribute allows you to create and maintain any number of Online Contribution Pages. You can create different pages for different programs or campaigns - and customize text, amounts, types of information collected from contributors, etc.'), 
                                 'query'   => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/small/online_contribution_pages.png',
                                 'weight'  => 360
                                 ),
                      
                           array(
                                 'path'    => 'civicrm/admin/contribute/managePremiums',
                                 'title'   => ts('Manage Premiums'),
                                 'desc'    => ts('CiviContribute allows you to configure any number of Premiums which can be offered to contributors as incentives / thank-you gifts. Define the premiums you want to offer here.'), 
                                 'query'   => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/small/Premiums.png',
                                 'weight'  => 365
                                 ),
                       
                           array(
                                 'path'    => 'civicrm/admin/contribute/contributionType',
                                 'title'   => ts('Contribution Types'),
                                 'desc'    => ts('Contribution types are used to categorize contributions for reporting and accounting purposes. These are also referred to as Funds.'), 
                                 'query'   => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/small/contribution_types.png',
                                 'weight'  => 370
                                 ),
                      
                           array(
                                 'path'    => 'civicrm/admin/options',
                                 'title'   => ts('Payment Instruments'),
                                 'desc'    => ts('You may choose to record the payment instrument used for each contribution. Common payment methods are installed by default (e.g. Check, Cash, Credit Card...). If your site requires additional payment methods, add them here.'), 
                                 'query'   => 'group=payment_instrument&reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/small/payment_instruments.png',
                                 'weight'  => 380
                                 ),

                           array(
                                 'path'    => 'civicrm/admin/options',
                                 'title'   => ts('Accepted Credit Cards'),
                                 'desc'    => ts('Credit card options that will be offered to contributors using your Online Contribution pages.'), 
                                 'query'   => 'group=accept_creditcard&reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/small/accepted_creditcards.png',
                                 'weight'  => 395
                                 ),
                           );
            break;

        case 'contact':
            $items = array(
                           array( 
                                 'path'    => 'civicrm/contact/view/contribution', 
                                 'query'   => 'reset=1&force=1&cid=%%cid%%', 
                                 'title'   => ts('Contributions'), 
                                 'type'    => CRM_Core_Menu::CALLBACK, 
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK, 
                                 'weight'  => 1
                                 ),
                           );
            break;

        case 'contribute':
            $items = array(
                           array( 
                                 'path'    => 'civicrm/contribute/search',
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Find Contributions' ), 
                                 'access'  => CRM_Core_Permission::check( 'access CiviContribute' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 510,  
                                 ),
                           array( 
                                 'path'    => 'civicrm/contribute/import', 
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Import Contributions' ), 
                                 'access'  => CRM_Core_Permission::check( 'administer CiviCRM' ) &&
                                              CRM_Core_Permission::check( 'access CiviContribute' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 520,  
                                 ),
                            array( 
                                 'path'    => 'civicrm/contribute/manage', 
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Manage Contribution Pages' ), 
                                 'access'  => CRM_Core_Permission::check( 'administer CiviCRM' ) &&
                                              CRM_Core_Permission::check( 'access CiviContribute' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 530,  
                                 ),
                           );
            break;

        }

        return $items;
    }

}

?>
