<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once '../../../modules/config.inc.php';
require_once 'CRM/Utils/Tree.php';
require_once 'CRM/Core/Error.php';
//require_once 'CRM/Core/I18n.php';

$tree = new CRM_Utils_Tree('domain');

$node1 =& $tree->createNode('im_provider');
$node2 =& $tree->createNode('location_type');
$node3 =& $tree->createNode('im');
$node4 =& $tree->createNode('location');


$tree->addNode('domain', $node1);           // domain -> im_provider
$tree->addNode('domain', $node2);           // domain -> location_type

$tree->addNode('im_provider', $node3);      // domain -> im_provider-> im

$tree->addNode('location_type', $node4);    // domain -> location_type -> location
$tree->addNode('location', $node3);         // domain -> location_type -> location -> imxs


$tree->display();

//echo "hello world!\n";


?>
