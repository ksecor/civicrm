<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying list of site configuration tasks with links to each setting form
 */
class CRM_Admin_Page_ConfigTaskList extends CRM_Core_Page {
    
    function run() {
        
        CRM_Utils_System::setTitle(ts("Configuration Checklist"));
        $this->assign('recentlyViewed', false);
        require_once 'CRM/Core/ShowHideBlocks.php';
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );
        /*
        $rows = array( 'sc1','sc2','vc1','vc2','vc3','vc4','em1','em2',
                       'cn1','ct1','ct2','cu1','cu2','co1','co2','co3','co4','co5','co6');
         */
        $rows = array( 'scCollapsed','vcCollapsed', 'emCollapsed', 'cnCollapsed',
                      'ctCollapsed', 'cuCollapsed', 'coCollapsed' );
        foreach( $rows as $id ) {
            $this->_showHide->addHide( $id );
        }
        require_once 'CRM/Core/Config.php';
        
        /*
        $config =& CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Drupal' ) {
            $this->_showHide->addHide( 'cn2' );
        }
        */
        
        $this->_showHide->addToTemplate( );

        parent::run();
    }   
}
    
    
    
    
    
