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

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying Administer CiviCRM Control Panel
 */
class CRM_Admin_Page_Admin extends CRM_Core_Page
{
    function run ( ) {
        require_once 'CRM/Core/Menu.php';
        $items =& CRM_Core_Menu::items( );
        $groups = array( ts('Manage'), ts('Configure'), ts('Setup') );
        // Create array to hold descriptive text for collapsed view of control panel
        $groupDesc = array ( 'Manage'    => ts('Access Control, Backup Data, and Synchronize Users to Contacts'),
                        'Configure' => ts('Activity Types, Profiles, Custom Data, Duplicate Matching, Relationship Types, Tags and other site-configurable option lists.'),
                        'Setup'     => ts('Global Settings, Gender Options, Instant Messenger Services, Mobile Phone Providers, Individual Name Prefixes and Suffixes, Preferred Communication Options'),
                        );

        $config =& CRM_Core_Config::singleton( );
        if ( in_array("CiviContribute", $config->enableComponents) ) {
            $groups[] = 'CiviContribute';
            $groupDesc['CiviContribute']  = ts('Manage Online Contribution Pages, Premiums, Contribution Types, Payment Instruments, and Accepted Credit Cards');
        }
        
        if ( in_array("CiviMember", $config->enableComponents) ) {
            $groups[] = 'CiviMember';
            $groupDesc['CiviMember'] = ts('Manage Membership Types and Membership Status Rules');
        }

        if ( in_array("CiviEvent", $config->enableComponents) ) {
            $groups[] = 'CiviEvent';
            $groupDesc['CiviEvent'] = ts('Manage Events, Event Types, Participant Statuses, Participant Roles and Price Sets');
        }

         if ( in_array("CiviMail", $config->enableComponents) ) {
            $groups[] = 'CiviMail';
            $groupDesc['CiviMail'] = ts('Manage Mailer Settings and Mailing Headers, Footers and Automated Replies');
        }

       $adminPanel = array( );
       require_once 'CRM/Core/ShowHideBlocks.php';
       $this->_showHide =& new CRM_Core_ShowHideBlocks( );
       foreach ( $groups as $group ) {
           // Hide (compress) all panel groups by default. We'll remember last state of each when we save user prefs later.
           $this->_showHide->addShow( "id_{$group}_show" );
           $this->_showHide->addHide( "id_{$group}" );
           $adminPanel[$group] = array( );
           $v = CRM_Core_ShowHideBlocks::links($this, $group, '' , '', false);
           $adminPanel[$group]['show'] = $v['show'];
           $adminPanel[$group]['hide'] = $v['hide'];
            foreach ( $items as $item ) {
                if ( CRM_Utils_Array::value( 'adminGroup', $item ) == $group ) {
                    $value = array( 'title' => $item['title'],
                                    'id'    => strtr($item['title'], array('('=>'_', ')'=>'', ' '=>'',
                                                                           ','=>'_', '/'=>'_' 
                                                                           )
                                                     ),
                                    'url'   => CRM_Utils_System::url( $item['path'],
                                                                      CRM_Utils_Array::value( 'query', $item ) ),
                                    'icon'  => $item['icon'],
                                    'extra' => CRM_Utils_Array::value( 'extra', $item ) );
                    $adminPanel[$group][$item['weight'] . '.' . $item['title']] = $value;
                }
            }
            ksort( $adminPanel[$group] );
        }

        if ( $this->_contactType == 'Individual' ) {
        }
        
        require_once 'CRM/Utils/VersionCheck.php';
        $versionCheck =& CRM_Utils_VersionCheck::singleton();
        $this->assign('newVersion',   $versionCheck->newerVersion());
        $this->assign('localVersion', $versionCheck->localVersion);

        $this->assign('adminPanel', $adminPanel);
        $this->assign('groupDesc', $groupDesc);
        $this->_showHide->addToTemplate( );
        return parent::run( );
    }
}
?>
