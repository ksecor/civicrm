<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying list of location types
 */
class CRM_Admin_Page_Admin extends CRM_Core_Page
{
    function run ( ) {
        $items =& CRM_Utils_Menu::items( );
        
        $groups     = array( 'Manage', 'Configure', 'Setup' );
        $adminPanel = array( );
        foreach ( $groups as $group ) {
            $adminPanel[$group] = array( );
            foreach ( $items as $item ) {
                if ( CRM_Utils_Array::value( 'adminGroup', $item ) == $group ) {
                    $value = array( 'title' => $item['title'],
                                    'path'  => $item['path'],
                                    'icon'  => $item['icon']);
                    $adminPanel[$group][] = $value;
                }
            }
        }
        $this->assign('adminPanel', $adminPanel);
        return parent::run( );
    }
}
/*
        $items = array(
            'Manage'    => array(
                                 array( 'title' => 'Access Control',
                                        'url'   => '',
                                        'icon'  => 'DataStore.gif',
                                      )
                                 ),
            'Configure' => array(
                                 array( 'title' => 'Activity Types',
                                        'url'   => 'civicrm/admin/activityType',
                                        'icon'  => 'DataStore.gif',
                                      ),
                                 array( 'title' => 'CiviCRM Profile',
                                        'url'   => 'civicrm/admin/uf/group',
                                        'icon'  => 'DataStore.gif',
                                        ),
                                 array( 'title' => 'Custom Data',
                                        'url'   => 'admin/custom/group',
                                        'icon'  => 'DataStore.gif',
                                        ),
                                 array( 'title' => 'Relationship Types',
                                        'url'   => 'admin/reltype',
                                        'icon'  => 'DataStore.gif',
                                        ),
                                 array( 'title' => 'Tags (Categories)',
                                        'url'   => 'admin/tag',
                                        'icon'  => 'DataStore.gif',
                                        )
                                 ),
            'Setup'    => array(
                                 array( 'title' => 'Gender Options',
                                        'url'   => 'admin/IMProvider',
                                        'icon'  => 'DataStore.gif',
                                      ),
                                array( 'title' => 'Instant Message Services',
                                       'url'   => 'admin/IMProvider',
                                       'icon'  => 'DataStore.gif',
                                       ),
                                array( 'title' => 'Location Types',
                                       'url'   => 'admin/locationType',
                                       'icon'  => 'DataStore.gif',
                                       )
                                )
                       );
*/
?>
