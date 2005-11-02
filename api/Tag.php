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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of the Tag of the CRM API. 
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

#require_once 'CRM/Core/BAO/UFGroup.php';

/**
 * Most API functions take in associative arrays ( name => value pairs
 * as parameters. Some of the most commonly used parameters are
 * described below
 *
 * @param array $params           an associative array used in construction
                                  / retrieval of the object
 * @param array $returnProperties the limited set of object properties that
 *                                need to be returned to the caller
 *
 */


/**
 *  Add a Tag. Tags are used to classify CRM entities (including Contacts, Groups and Actions).
 *
 * @param array $params an associative array used in construction / retrieval of the object
 * @return $tag object a new tag object
 * @access public
 */
function crm_create_tag($params) 
{
    $ids = array();
    require_once 'CRM/Core/BAO/Tag.php';
    return CRM_Core_BAO_Tag::add($params, $ids);
}

/**
 * Deletes an existing Tag
 *
 * @param object $tag valid tag object
 * @access public
 */
function crm_delete_tag(&$tag) 
{
    require_once 'CRM/Core/BAO/Tag.php';
    //$id = CRM_Utils_Array::value('id', $tag);
    $id = $tag->id;
    CRM_Core_BAO_Tag::del($id);
}

/**
 * Assigns an entity (e.g. Individual, Organization, Group, Contact_action) to a Tag (i.e. 'tags' that entity).
 *
 * @param object $tag valid tag object
 * @param object $entity valid entity object
 *
 * @return $entityTag object A new Entity tag object
 * @access public
 */
function crm_create_entity_tag(&$tag, &$entity)
{

}

/**
 * Returns all entities assigned to a specific Tag. Optionally filtered by entity_type.
 *
 * @param $tag object Valid Tag object.
 * @param $entity_type enum Optional filter for type of entity being queried. Valid values: 'Individual', 'Organization', 'Household', 'Group', 'Contact_action'.
 * @param $sort array Associative array of one or more "property_name"=>"sort direction" pairs which will control order of entities objects returned.
 * @param $offset int Starting row index.
 * @param $row_count int Maximum number of rows to returns.
 *
 * @return array An array of entity objects (Individuals and/or Organizations and/or etc.).
 * @access public
 */
function crm_get_entities_by_tag(&$tag, $entity_type = NULL, $sort = null, $offset = 0, $row_count =25)
{

}

/**
 * Returns all Tags assigned to a single entity instance. For example, you can use this API to find out what tag(s) have been assigned to a particular organization.
 *
 * @param $entity object Valid object of one of the supported entity types.
 *
 * @return array An array of Tag objects.
 * @access public
 */
function crm_tags_by_entity(&$entity)
{

}

/**
 * Deletes an existing entity tag assignment.
 *
 * @param $entity_tag object Valid entity_tag object.
 * @access public
 */
function crm_delete_entity_tag(&$entity_tag)
{
    
}

?>
