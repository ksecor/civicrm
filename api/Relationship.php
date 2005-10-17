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
    $relationTypeID = null;
    if( ! isset( $contact->id ) and ! isset( $target_contact->id )) {
        return _crm_error('source or  target contact object does not have contact ID');
    }

    $sourceContact          = $contact->id;
    $targetContact          = $target_contact->id;
    require_once 'CRM/Contact/DAO/RelationshipType.php';
    $reletionType = & new CRM_Contact_DAO_RelationshipType();
    $reletionType->name_a_b = $relationship_type_name;
    $reletionType->find();
    if($reletionType->fetch()) {
        
        $relationTypeID = $reletionType->id;
        $relationTypeID .='_a_b';
    }
   
    if (!$relationTypeID) {
        $reletionType = & new CRM_Contact_DAO_RelationshipType();
        $reletionType->name_b_a = $relationship_type_name;
        $reletionType->find();
        if($reletionType->fetch()) {
            
            $relationTypeID = $reletionType->id;
            $relationTypeID .='_b_a';
        }
    }
    
    if (!$relationTypeID) {
        return _crm_error('$relationship_type_name is not valid relationship type ');
    }
    $params['relationship_type_id' ] = $relationTypeID;
    $ids   ['contact'      ] = $sourceContact;
    $params['contact_check'] = array ( $targetContact => $targetContact) ;
    require_once 'CRM/Contact/BAO/Relationship.php';
    return CRM_Contact_BAO_Relationship::create($params, $ids);
    
}

/**
 * Function to get the relationship
 *
 */
function crm_get_relationships($contact_a, $contact_b=null, $relationship_type_name = null, $returnProperties = null, $sort = null, $offset = 0, $row_count = 25 ) {
   
    if( ! isset( $contact_a->id ) ) {
        return _crm_error('$contact_a is not valid contact datatype');
    }
   
    require_once 'CRM/Contact/BAO/Relationship.php';
    $contactID = $contact_a->id;
    $relationships = CRM_Contact_BAO_Relationship::getRelationship($contactID);
    $relationships_b =array();
    
    $cid = $contact_b->id;
    if(isset( $contact_b->id )) {
        foreach($relationships as $key => $relationship) {
            if ($relationship['cid'] == $cid ) {
                $relationships_b[$key] = $relationship;
            }
        }
         return $relationships_b;
    }
    
    return $relationships;
    
}

/**
 * Function to delete relationship    
 *
 */
function crm_delete_relationship(&$contact, &$target_contact, $relationship_type_name) {
   
    $relationTypeID = null;
    if( ! isset( $contact->id ) and ! isset( $target_contact->id )) {
        return _crm_error('source or  target contact object does not have contact ID');
    }
    
    $sourceContact          = $contact->id;
    $targetContact          = $target_contact->id;
    require_once 'CRM/Contact/DAO/RelationshipType.php';
    $reletionType = & new CRM_Contact_DAO_RelationshipType();
    $reletionType->name_a_b = $relationship_type_name;
    $reletionType->find();
    if($reletionType->fetch()) {
        $relationTypeID = $reletionType->id;
    }
    
    if (!$relationTypeID) {
        $reletionType = & new CRM_Contact_DAO_RelationshipType();
        $reletionType->name_b_a = $relationship_type_name;
        $reletionType->find();
        if($reletionType->fetch()) {
            $relationTypeID = $reletionType->id;
            
        }
    }
   
    if (!$relationTypeID) {
        return _crm_error('$relationship_type_name is not valid relationship type ');
    }
    require_once 'CRM/Contact/DAO/Relationship.php';
    $relationShip =  & new CRM_Contact_DAO_Relationship();
    $relationShip->contact_id_a = $sourceContact;
    $relationShip->contact_id_b = $targetContact;
    $relationShip->relationship_type_id = $relationTypeID ;
    $relationShip->find();
    if($relationShip->fetch()) {
        require_once 'CRM/Contact/BAO/Relationship.php';
        CRM_Contact_BAO_Relationship::del($relationShip->id);   
        return null;
    }

}

/**
 * Function to create relationship type
 *
 */
function crm_create_relationship_type($params) {
   
    if(! isset($params['name_a_b']) and ! isset($params['name_b_a']) and ! isset($params['contact_type_a']) and ! isset($params['contact_type_b'] )) {
        return _crm_error('Return array is not properly set');
    }
    require_once 'CRM/Contact/BAO/RelationshipType.php';
    $relationType = CRM_Contact_BAO_RelationshipType::add( $params, $ids);
   
    return $relationType;
    
}

/**
 * Function to get all relationship type
 *
 */

function crm_get_relationship_types() {
    require_once 'CRM/Contact/DAO/RelationshipType.php';
    $relationshipTypes = array();
    $relationType = & new CRM_Contact_DAO_RelationshipType();
    $relationType->find();
    while($relationType->fetch())
        {
            $relationshipTypes[] = $relationType;
        }
    return $relationshipTypes;
    
}





?>
