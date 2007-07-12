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

require_once '../../../civicrm.settings.php';
require_once 'CRM/Utils/Tree.php';
require_once 'CRM/Core/Error.php';
//require_once 'CRM/Core/I18n.php';

$tree = new CRM_Utils_Tree('domain');

$node1 =& $tree->createNode('custom_group');
$node2 =& $tree->createNode('custom_field');
$node3 =& $tree->createNode('validation');
$node4 =& $tree->createNode('custom_value');
$node5 =& $tree->createNode('custom_option');


$tree->addNode('domain', $node1);           // domain -> custom_group
$tree->addNode('domain', $node3);           // domain -> validation


$tree->addNode('custom_group', $node2);      // domain -> custom_group -> custom_field

$tree->addNode('validation', $node2);    // domain -> validation -> custom_field

$tree->addNode('custom_field', $node4);
$tree->addNode('custom_field', $node5);

$tree->display();

//echo "hello world!\n";

?>
