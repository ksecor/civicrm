<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
