<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Piotr Szotkowski <shot@caltha.pl>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Call the Drupal's t( ) function on a string enclosed with {t}
 * in Smarty's templates.
 *
 * @param array  $params  attributes passed from the template
 * @param string $text    the contents of the template block
 * @param object &$smarty the Smarty's template object
 *
 * @return string         the contents of the template translated by Drupal
 * @access public
 */

function smarty_block_t( $params, $text, &$smarty ) {
    return t($text);
}

?>
