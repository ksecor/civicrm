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
                       'civicrm/contribute' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'CiviContribute' ), 
                             'access_arguments'  => array( array( 'access CiviContribute') ), 
                             'page_type' => CRM_Core_Menu::MENU_ITEM,
                             'weight'  => 500,  
                             'component'  => 'CiviContribute',  
                             ),

                       'civicrm/contribute/transact' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'CiviContribute' ), 
                             'access_arguments'  => array( array( 'make online contributions') ), 
                             'weight'  => 0, 
                             ),
                       );
        return $items;
    }

    static function &main( ) {
        $items = array(
                       'civicrm/admin/contribute' => 
                       array(
                             'title'   => ts('Manage Contribution Pages'),
                             'desc'    => ts('CiviContribute allows you to create and maintain any number of Online Contribution Pages. You can create different pages for different programs or campaigns - and customize text, amounts, types of information collected from contributors, etc.'), 
                             'query'   => array('reset' => 1),
                             'adminGroup' => 'CiviContribute',
                             'icon'    => 'admin/small/online_contribution_pages.png',
                             'weight'  => 360
                             ),
                       
                       'civicrm/admin/contribute/managePremiums' => 
                       array(
                             'title'   => ts('Manage Premiums'),
                             'desc'    => ts('CiviContribute allows you to configure any number of Premiums which can be offered to contributors as incentives / thank-you gifts. Define the premiums you want to offer here.'), 
                             'query'   => array('reset' => 1),
                             'adminGroup' => 'CiviContribute',
                             'icon'    => 'admin/small/Premiums.png',
                             'weight'  => 365
                             ),
                       
                       'civicrm/admin/contribute/contributionType' => 
                       array(
                             'title'   => ts('Contribution Types'),
                             'desc'    => ts('Contribution types are used to categorize contributions for reporting and accounting purposes. These are also referred to as Funds.'), 
                             'query'   => array('reset' => 1),
                             'adminGroup' => 'CiviContribute',
                             'icon'    => 'admin/small/contribution_types.png',
                             'weight'  => 370
                             ),
                       
                       'civicrm/admin/options/payment_instrument' => 
                       array(
                             'title'   => ts('Payment Instruments'),
                             'desc'    => ts('You may choose to record the payment instrument used for each contribution. Common payment methods are installed by default (e.g. Check, Cash, Credit Card...). If your site requires additional payment methods, add them here.'), 
                             'query'   => array('reset' => 1, 'group' => 'payment_instrument'),
                             'adminGroup' => 'CiviContribute',
                             'icon'    => 'admin/small/payment_instruments.png',
                             'weight'  => 380
                             ),

                       'civicrm/admin/options/accept_creditcard' => 
                       array(
                             'title'   => ts('Accepted Credit Cards'),
                             'desc'    => ts('Credit card options that will be offered to contributors using your Online Contribution pages.'), 
                             'query'   => array('reset' => 1, 'group' => 'accept_creditcard'),
                             'adminGroup' => 'CiviContribute',
                             'icon'    => 'admin/small/accepted_creditcards.png',
                             'weight'  => 395
                             ),

                       'civicrm/contact/view/contribution' => 
                       array( 
                             'query'   => array('reset' => 1, 'force' => 1, 'cid' => '%%cid%%'), 
                             'title'   => ts('Contributions'), 
                             'weight'  => 1
                             ),

                       'civicrm/contribute/search' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Find Contributions' ), 
                             'access_arguments'  => array( array( 'access CiviContribute' ) ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 510,  
                             ),

                       'civicrm/contribute/import' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Import Contributions' ), 
                             'access_arguments'  => array( array( 'administer CiviCRM', 'access CiviContribute' ) ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                                 'weight'  => 520,  
                             ),

                       'civicrm/contribute/manage' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Manage Contribution Pages' ), 
                             'access_arguments'  => array( array( 'administer CiviCRM', 'access CiviContribute' ) ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 530,  
                             ),
                       );
        
        return $items;
    }
}


