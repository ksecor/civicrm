<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
                                 'title'   => ts('Configure Online Contribution Pages'),
                                 'query'   => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/online_contribution_pages.png',
                                 'weight'  => 360
                                 ),
                      
                           array(
                                 'path'    => 'civicrm/admin/contribute/managePremiums',
                                 'title'   => ts('Manage Premiums'),
                                 'query'   => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/Premiums.png',
                                 'weight'  => 365
                                 ),
                       
                           array(
                                 'path'    => 'civicrm/admin/contribute/contributionType',
                                 'title'   => ts('Contribution Types'),
                                 'query'   => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/contribution_types.png',
                                 'weight'  => 370
                                 ),
                      
                           array(
                                 'path'    => 'civicrm/admin/options',
                                 'title'   => ts('Payment Instruments'),
                                 'query'   => 'group=payment_instrument&reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/payment_instruments.png',
                                 'weight'  => 380
                                 ),

                           array(
                                 'path'    => 'civicrm/admin/options',
                                 'title'   => ts('Accepted Credit Cards'),
                                 'query'   => 'group=accept_creditcard&reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/accepted_creditcards.png',
                                 'weight'  => 395
                                 ),
                           );
            $config =& CRM_Core_Config::singleton( );
            if ( $config->paymentProcessor == 'PayPal' || $config->paymentProcessor == 'PayPal_Express' ) {
                $items[] = array(
                                 'path'    => 'civicrm/admin/contribute/createPPD',
                                 'title'   => ts('Create PayPal API Profile'),
                                 'query'   => 'reset=1',
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviContribute',
                                 'icon'    => 'admin/PayPal_mark_37x23.gif',
                                 'weight'  => 400
                                 );
            }
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
                                 'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                              CRM_Core_Permission::check( 'access CiviContribute' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 520,  
                                 ),
                           );
            break;

        }

        return $items;
    }

}

?>
