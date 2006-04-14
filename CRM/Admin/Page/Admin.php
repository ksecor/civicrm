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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying Administer CiviCRM Control Panel
 */
class CRM_Admin_Page_Admin extends CRM_Core_Page
{
    function run ( ) {
        require_once 'CRM/Core/Menu.php';
        $items =& CRM_Core_Menu::items( );

        $config =& CRM_Core_Config::singleton( );

        $groups     = array( ts('Manage'), ts('Configure'), ts('Setup') );       
        if ( in_array("CiviContribute", $config->enableComponents) ) {
            $groups[] = 'CiviContribute';
        }
        
        $adminPanel = array( );
        foreach ( $groups as $group ) {
            $adminPanel[$group] = array( );
            foreach ( $items as $item ) {
                if ( $config->userFramework == 'Mambo' &&
                     $item['path'] == 'civicrm/admin/access' ) {
                    // access control not yet implemented for mambo
                    continue;
                }

                if ( CRM_Utils_Array::value( 'adminGroup', $item ) == $group ) {
                    $value = array( 'title' => $item['title'],
                                    'url'   => CRM_Utils_System::url( $item['path'],
                                                                      CRM_Utils_Array::value( 'qs', $item ) ),
                                    'icon'  => $item['icon'],
                                    'extra' => CRM_Utils_Array::value( 'extra', $item ) );
                    $adminPanel[$group][$item['weight'] . '.' . $item['title']] = $value;
                }
            }
            ksort( $adminPanel[$group] );
        }

        require_once 'CRM/Utils/VersionCheck.php';
        $versionCheck =& CRM_Utils_VersionCheck::singleton();
        $this->assign('newVersion',   $versionCheck->newerVersion());
        $this->assign('localVersion', $versionCheck->localVersion);

        $this->assign('adminPanel', $adminPanel);
        return parent::run( );
    }
}
?>
