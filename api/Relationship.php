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
 * Definition of the Group part of the CRM API. 
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
require_once 'PEAR.php';

require_once 'CRM/Core/Error.php';
require_once 'CRM/Utils/Array.php';

/**
 * Function to create new retaionship 
 *
 */
function crm_create_relationship($contact =null, $target_contact= null, $relationship_type_name, $params) {
    
    //$sourceContact          = CRM_Utils_Array::value('id', $contact);
    //$targetContact          = CRM_Utils_Array::value('id', $target_contact);
    $sourceContact          = 101;
    $targetContact          = 102;
    
    $params['relationship_type_id' ] = $relationship_type_name;
    $ids   ['contact'      ] = $sourceContact;
    $params['contact_check'] = array ( $targetContact => $targetContact) ;
    require_once 'CRM/Contact/BAO/Relationship.php';
    CRM_Contact_BAO_Relationship::create($params, $ids);

}

/**
 * Function to get the relationship
 *
 */
function crm_get_relationships(&$contact, $relationship_type_name = null, $returnProperties = null, $sort = null, $offset = 0, $row_count = 25 ) {
}

/**
 * Function to delete relationship    
 *
 */
function crm_delete_relationship(&$contact, &$target_contact, $relationship_type_name) {
}

/**
 * Function to create relationship type
 *
 */
function crm_create_relationship_type($params) {
}







?>
