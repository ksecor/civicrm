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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/**
 * Replace the value of an attribute in the input string. Assume
 * the the attribute is well formed, of the type name="value". If
 * no replacement is mentioned the value is inserted at the end of
 * the form element
 *
 * @param array  $params the function params
 * @param object $smarty reference to the smarty object 
 *
 * @return string the help html to be inserted
 * @access public
 */
function smarty_function_help( $params, &$smarty ) {
    if ( ! isset( $params['id'] ) || ! isset( $smarty->_tpl_vars[ 'config'] ) ) {
        return;
    }

    if ( isset( $params['file'] ) ) {
        $file = $params['file'];
    } else if ( isset( $smarty->_tpl_vars[ 'tplFile' ] ) ) {
        $file = $smarty->_tpl_vars[ 'tplFile' ];
    } else {
        return;
    }

    $id   = urlencode( $params['id'] );
    $file = urlencode( $file );
    return "
<img id=\"{$id}_help\" src=\"{$smarty->_tpl_vars[ 'config']->resourceBase}/i/Inform.gif\">
<span dojoType=\"tooltip\" connectId=\"{$id}_help\" href=\"{$smarty->_tpl_vars[ 'config']->resourceBase}/extern/ajax.php?q=civicrm/help&id=$id&file=$file\" style=\"width: 300px;\">
</span>
";

}

?>
