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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Mailing_Menu {

    static function permissioned( ) {
        $items = array(
                       array(
                             'path'    => 'civicrm/mailing',
                             'query'   => 'reset=1',
                             'title'   => ts('CiviMail'),
                             'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 600,
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
                                 'path'    => 'civicrm/admin/mail',
                                 'title'   => ts('Mailer Settings '),
                                 'desc'    => ts('Configure spool period, throttling and other mailer settings.'), 
                                 'query'  => 'reset=1',
                                 'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviMail',
                                 'icon'    => 'admin/small/07.png',
                                 'weight'  => 400
                                 ),
                           array(
                                 'path'    => 'civicrm/admin/component',
                                 'title'   => ts('Header/ Footer/ Automated Messages'),
                                 'desc'    => ts('Configure the header and footer used for mailings. Customize the content of automated Subscribe, Unsubscribe, and Opt-out messages.'), 
                                 'query'   => 'reset=1',
                                 'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,  
                                 'adminGroup' => 'CiviMail',
                                 'icon'    => 'admin/small/Profile.png',
                                 'weight'  => 410,
                                 ),
                           
                           );
            break;
                      
        case 'mailing':
            $items = array(
                           
                           array(
                                 'path'    => 'civicrm/mailing/send',
                                 'query'   => 'reset=1',
                                 'title'   => ts('Send Mailing'),
                                 'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 610,
                                 ),
                           
                           array(
                                 'path'    => 'civicrm/mailing/browse',
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Browse Sent Mailings' ),
                                 'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                                 'type'    => CRM_Core_Menu::CALLBACK, 
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 620, 
                                 ),
                           array(
                                 'path'    => 'civicrm/mailing/component',
                                 'query'   => 'reset=1',
                                 'title'   => ts('Mailing Header / Footer'),
                                 'access'  => CRM_Core_Permission::check( 'administer CiviCRM' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 630,
                                 ),
                           );
            
            break;
        }
        return $items;
    }
    
}

?>
