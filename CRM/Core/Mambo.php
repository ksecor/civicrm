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

/**
 *
 */
class CRM_Core_Mambo {

    /**
     * Reuse drupal blocks into a left sidebar. Assign the generated template
     * to the smarty instance
     *
     * @return void
     * @access public
     * @static
     */
    static function sidebarLeft( ) {
        $blockIds = array( 1, 2, 4, 8 );

        $blocks = array( );
        foreach ( $blockIds as $id ) {
            $blocks[] = CRM_Core_Block::getContent( $id );
        }

        $template =& CRM_Core_Smarty::singleton( );
        $template->assign_by_ref( 'blocks', $blocks );
        $sidebarLeft = $template->fetch( 'CRM/Block/blocks.tpl' );
        $template->assign_by_ref( 'sidebarLeft', $sidebarLeft );
    }

}

?>
