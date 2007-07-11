<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

/**
 *
 */
class CRM_Core_Standalone {
	
		/* Copied from CRM/Core/Joomla.php */

    /**
     * Reuse drupal blocks into a left sidebar. Assign the generated template
     * to the smarty instance
     *
     * @return void
     * @access public
     * @static
     */
    static function sidebarLeft( ) {
        $config =& CRM_Core_Config::singleton( );

        // intialize the menu and set the default title
        CRM_Core_Menu::createLocalTasks( $_GET[$config->userFrameworkURLVar] );
	// Not sure we want this for the standalone version
	/*
        if ( $config->userFrameworkFrontend ) {
            return;
        }
	*/

        $blockIds = array( 1, 2, 4, 8 );

        $blocks = array( );
        foreach ( $blockIds as $id ) {
            require_once 'CRM/Core/Block.php';
            $blocks[] = CRM_Core_Block::getContent( $id );
        }

        require_once 'CRM/Core/Smarty.php';
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign_by_ref( 'blocks', $blocks );
        $sidebarLeft = $template->fetch( 'CRM/Block/blocks.tpl' );
        $template->assign_by_ref( 'sidebarLeft', $sidebarLeft );

        $args = explode( '/', trim( $_GET[CIVICRM_UF_URLVAR] ) );
        require_once 'CRM/Core/Menu.php';
        $breadcrumb =& CRM_Core_Menu::breadcrumb( $args );

        $template->assign_by_ref( 'breadcrumb', $breadcrumb );
    }

}

?>
