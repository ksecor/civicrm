<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
    require_once 'CRM/Core/BAO/Tag.php';
    
    $error = _crm_check_required_fields($params, 'CRM_Core_DAO_Tag');
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }
    
    $ids = array();
    return CRM_Core_BAO_Tag::add($params, $ids);
}

/**
 * Deletes an existing Tag
 *
 * @param  object        $tag valid tag object
 * @return NULL | error  if delete successfull then NULL otherwise object of CRM_Core_Error
 * @access public
 */
function crm_delete_tag(&$tag) 
{
    require_once 'CRM/Core/BAO/Tag.php';
    
    if ( ! isset($tag->id) ) {
        return _crm_error('Invalid Tag object passed in');
    }
    
    if (CRM_Core_BAO_Tag::del($tag->id)) {
        return null;
    } else {
        return _crm_error('Error while deleting Tag object');
    }
}

/**
 * Assigns an entity (e.g. Individual, Organization, Group, Contact_action) to a Tag (i.e. 'tags' that entity).
 *
 * @param $tag        object  valid tag object
 * @param $entity     object  valid entity object
 *
 * @return $entityTag object  A new Entity tag object
 * @access public
 */
function crm_create_entity_tag(&$tag, &$entity)
{
    require_once 'CRM/Core/BAO/EntityTag.php';

    if ( ! isset($tag->id) || ! isset($entity->id)) {
        return _crm_error('Required parameters missing');
    }

    $params = array ('tag_id' => $tag->id,
                     'entity_id' => $entity->id,
                     'entity_table' => 'civicrm_contact'
                     );
    return CRM_Core_BAO_EntityTag::add($params);
}

/**
 * Returns all entities assigned to a specific Tag. Optionally filtered by entity_type.
 *
 * @param  $tag         object  Valid Tag object.
 * @param  $entity_type enum    Optional filter for type of entity being queried. Valid values: 'Individual', 'Organization', 'Household', 'Group', 'Contact_action'.
 *
 * @return $entities    Array   An array of entity objects (Individuals and/or Organizations and/or etc.).
 * @access public
 */
function crm_get_entities_by_tag(&$tag, $entity_type = null)
{
    require_once 'CRM/Core/BAO/EntityTag.php';

    if ( ! isset($tag->id) ) {
        return _crm_error('Invalid tag object passed in');
    }

    $contactIDs=& CRM_Core_BAO_EntityTag::getEntitiesByTag($tag);
    $entities = array();
    foreach($contactIDs as $Id) { 
        $params  = array('contact_id' => $Id );
        if($entity_type != null) {
            $temp = clone(crm_get_contact($params));
            if($entity_type == $temp->contact_type)
                $entities[] = $temp;
        } else {
            $entities[] =clone(crm_get_contact($params));
        }
    }
    
    return $entities;
   
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
    require_once 'CRM/Core/BAO/EntityTag.php';

    if (! isset($entity->id)) {
        return _crm_error('Required parameters missing');
    }

    $entityID=$entity->id;
    return CRM_Core_BAO_EntityTag::getTag($entityTable = 'civicrm_contact', $entityID);

}

/**
 * Deletes an existing entity tag assignment.
 *
 * @param $entity_tag object Valid entity_tag object.
 * @access public
 */
function crm_delete_entity_tag(&$entity_tag)
{
    require_once 'CRM/Core/BAO/EntityTag.php';

    if ( ! isset($entity_tag->id)) {
        return _crm_error('Required parameters missing');
    }

    $params=array('id' => $entity_tag->id );
    
    
    return CRM_Core_BAO_EntityTag::del($params);
}

?>
