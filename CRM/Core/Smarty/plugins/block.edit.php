<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * CiviCRM's Smarty edit-block plugin
 *
 * Template elements tagged {edit}...{/edit} are hidden unless action is create
 * or update (this facilitates using form templates for read-only display).
 *
 * @package CRM
 * @author Piotr Szotkowski <shot@caltha.pl>
 * @author Michal Mach <mover@artnet.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 */

/** 
 * Smarty block function providing edit-only display support
 *
 * @param array $params   template call's parameters
 * @param string $text    {edit} block contents from the template
 * @param object $smarty  the Smarty object
 *
 * @return string  the string, translated by gettext
 */
function smarty_block_edit($params, $text, &$smarty)
{
    $action = $smarty->_tpl_vars['action'];
    return ( $action & 3 ) ? $text : null;
}

?>
