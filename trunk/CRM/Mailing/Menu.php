<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * Menu for the mailing module
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

class CRM_Mailing_Menu {

    static function &main( ) {
        $items = array(
                       array(
                             'path'    => 'civicrm/mailing',
                             'title'   => ts('CiviMail'),
                             'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 600,
                             ),

                       array(
                             'path'    => 'civicrm/mailing/component',
                             'title'   => ts('Mailing Header / Footer'),
                             'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 610,
                             ),

                       array(
                             'path'    => 'civicrm/mailing/send',
                             'title'   => ts('Send Mailing'),
                             'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 620,
                             ),
                      
                       array(
                             'path'    => 'civicrm/mailing/browse',
                             'title'   => ts( 'Browse Sent Mailings' ),
                             'access'  => CRM_Core_Permission::check( 'access CiviMail' ),
                             'type'    => CRM_Core_Menu::CALLBACK, 
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 630, 
                             ),

                       );
        return $items;
    }

}

?>
