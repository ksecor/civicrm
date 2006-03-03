<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of the User Profile Group of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php'; 

require_once 'CRM/Core/BAO/UFJoin.php';

/**
 * takes an associative array and creates a uf join object
 *
 * @param array $params assoc array of name/value pairs
 *
 * @return object CRM_Core_DAO_UFJoin object 
 * @access public
 * 
 */

function crm_edit_uf_join($params) {
    return CRM_Core_BAO_UFJoin::create($params);
}

/**
 * Given an assoc list of params, finds if there is a record
 * for this set of params
 *
 * @param array $params (reference) an assoc array of name/value pairs 
 * 
 * @return int or null
 * @access public
 * 
 */

function crm_find_uf_join_id(&$params) {
    return CRM_Core_BAO_UFJoin::findJoinEntryId($params);
}

/**
 * Given an assoc list of params, find if there is a record
 * for this set of params and return the group id
 *
 * @param array $params (reference) an assoc array of name/value pairs 
 * 
 * @return int or null
 * @access public
 * 
 */

function crm_find_uf_join_UFGroupId(&$params) {
    return CRM_Core_BAO_UFJoin::findUFGroupId($params);
}

?>
