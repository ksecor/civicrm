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
    }

}


