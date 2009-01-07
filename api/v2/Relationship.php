<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id: Contribute.php 10015 2007-06-17 22:00:12Z lobo $
 *
 */

require_once 'api/v2/utils.php';
require_once 'CRM/Contact/BAO/Relationship.php';
require_once 'CRM/Contact/BAO/RelationshipType.php';


/**
 * Add or update a relationship
 *
 * @param  array   $params   (reference ) input parameters
 *
 * @return array (reference) id of created or updated record
 * @static void
 * @access public
 */
function &civicrm_relationship_create( &$params ) {
    _civicrm_initialize( );

    if ( empty( $params ) ) { 
        return civicrm_create_error( 'No input parameter present' );
    }
    
    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Input parameter is not an array' ) );
    }
    
    if( ! isset( $params['contact_id_a'] ) &&
        ! isset( $params['contact_id_b'] ) &&
        ! isset( $params['relationship_type_id'] )) { 
        
        return civicrm_create_error( ts('Missing required parameters'));
    }
    
    require_once 'CRM/Utils/Rule.php';
    if( !CRM_Utils_Rule::integer( $params['relationship_type_id'] ) ) {
       
        return civicrm_create_error( 'Invalid value for relationship type ID' );
    }

    $ids = array( );
    require_once 'CRM/Utils/Array.php';
    
    if( CRM_Utils_Array::value( 'id', $params ) ) {
        
        $ids['relationship']  = $params['id'];
        $ids['contactTarget'] = $params['contact_id_b'];
    }
       
    $params['relationship_type_id'] = $params['relationship_type_id'].'_a_b';
    $params['contact_check']        = array ( $params['contact_id_b'] => $params['contact_id_b'] );
    $ids   ['contact'      ]        = $params['contact_id_a'];
    
    $relationshipBAO = CRM_Contact_BAO_Relationship::create( $params, $ids );
    if ( is_a( $relationshipBAO, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Relationship can not be created" );
    } 
    return civicrm_create_success( array( 'id' => implode( ",", $relationshipBAO[4] ) ) );
}


/**
 * Delete a relationship 
 *
 * @param  id of relationship  $id
 *
 * @return boolean  true if success, else false
 * @static void
 * @access public
 */

function civicrm_relationship_delete( &$params ) {
     
    if ( empty( $params ) ) { 
        return civicrm_create_error( 'No input parameter present' );
    }

    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Input parameter is not an array' ) );
    }
        
    if( ! CRM_Utils_Array::value( 'id',$params )  ) {
        return civicrm_create_error( 'Missing required parameter' );
    }
    require_once 'CRM/Utils/Rule.php';
    if( $params['id'] != null && ! CRM_Utils_Rule::integer( $params['id'] ) ) {
        return civicrm_create_error( 'Invalid value for relationship ID' );
    }
    
    $relationBAO = new CRM_Contact_BAO_Relationship( );
    return $relationBAO->del( $params['id'] ) ? civicrm_create_success( ts( 'Deleted relationship successfully' ) ):civicrm_create_error( ts( 'Could not delete relationship' ) );

}

/**
 * Function to create relationship type
 *
 * @param  array $params   Associative array of property name/value pairs to insert in new relationship type.
 *
 * @return Newly created Relationship_type object
 *
 * @access public
 *
 */
function civicrm_relationship_type_add( $params ) {
    
    if ( empty( $params ) ) {
        return civicrm_create_error( ts( 'No input parameters present' ) );
    }

    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Parameter is not an array' ) );
    }

    if(! isset($params['name_a_b']) &&
       ! isset($params['name_b_a']) || CRM_Utils_Array::value( 'name_a_b', $params ) == null) {
        return civicrm_create_error('Missing required parameters');
    }

    require_once 'CRM/Utils/Rule.php';

    $ids = array( );
    if( isset( $params['id'] ) && ! CRM_Utils_Rule::integer(  $params['id'] ) ) {
        return civicrm_create_error( 'Invalid value for relationship type ID' );
    } else {
        $ids['relationshipType'] = CRM_Utils_Array::value( 'id', $params );
    }
    
    require_once 'CRM/Contact/BAO/RelationshipType.php';
    $relationType = CRM_Contact_BAO_RelationshipType::add( $params, $ids );
    
    $relType = array( );
    _civicrm_object_to_array( $relationType, $relType );
       
    return $relType;
    
}

/**
 * Delete a relationship type delete
 *
 * @param  id of relationship type  $id
 *
 * @return boolean  true if success, else false
 * @static void
 * @access public
 */
function civicrm_relationship_type_delete( &$params ) {

    if( ! CRM_Utils_Array::value( 'id',$params )  ) {
        return civicrm_create_error( 'Missing required parameter' );
    }
    require_once 'CRM/Utils/Rule.php';
    if( $params['id'] != null && ! CRM_Utils_Rule::integer( $params['id'] ) ) {
        return civicrm_create_error( 'Invalid value for relationship type ID' );
    }
    
    $relationTypeBAO = new CRM_Contact_BAO_RelationshipType( );
    return $relationTypeBAO->del( $params['id'] ) ? civicrm_create_success( ts( 'Deleted relationship type successfully' )  ):civicrm_create_error( ts( 'Could not delete relationship type' ) );
}

/**
 * Function to get the relationship
 *
 * @param array   $params          (reference ) input parameters 
         param['contact_id'] is mandatory
 * @return        Array of all relationship.
 *
 * @access  public
 */
function civicrm_relationship_get( $params ) {
    if ( !isset( $params['contact_id'] ) ) {
        return civicrm_create_error( ts( 'Could not find contact_id in input parameters.' ) );
    }
    require_once 'CRM/Contact/BAO/Relationship.php';
    $contactID     = $params['contact_id'];
    $relationships = CRM_Contact_BAO_Relationship::getRelationship($contactID);
    
    if ( !empty( $relationshipTypes ) ) {
        $result = array();
        foreach ( $relationshipTypes as $relationshipName ) {
            foreach( $relationships as $key => $relationship ) {
                if ( $relationship['relation'] ==  $relationshipName ) {
                    $result[$key] = $relationship;
                }
            }
        }
        $relationships = $result;
    }
    if ( $relationships ) {
        return civicrm_create_success( $relationships );
    } else {
        return civicrm_create_error( ts( 'Invalid Data' ) );
    }
  
}

/**
 * Function to get the relationship
 *
 * @param array   $contact_a          (reference ) input parameters.
 * @param array   $contact_b          (reference ) input parameters.
 * @param array   $relationshipTypes  an array of Relationship Type Name.
 * @param string  $sort               sort all relationship by relationshipId (eg asc/desc)
 *
 * @return        Array of all relationship.
 *
 * @access  public
 */
function civicrm_get_relationships( $contact_a, $contact_b = null, $relationshipTypes = null, $sort = null ) 
{
    if ( !isset( $contact_a['contact_id'] ) ) {
        return civicrm_create_error( ts( 'Could not find contact_id in input parameters.' ) );
    }
    require_once 'CRM/Contact/BAO/Relationship.php';
    $contactID     = $contact_a['contact_id'];
    $relationships = CRM_Contact_BAO_Relationship::getRelationship($contactID);
    
    if ( !empty( $relationshipTypes ) ) {
        $result = array();
        foreach ( $relationshipTypes as $relationshipName ) {
            foreach( $relationships as $key => $relationship ) {
                if ( $relationship['relation'] ==  $relationshipName ) {
                    $result[$key] = $relationship;
                }
            }
        }
        $relationships = $result;
    }
    
    if( isset( $contact_b['contact_id']) ) {
        $cid = $contact_b['contact_id'];
        $result =array( );
        
        foreach($relationships as $key => $relationship) {
            if ($relationship['cid'] == $cid ) {
                $result[$key] = $relationship;
            }
        }
        $relationships = $result;
    }
    
    //sort by relationship id
    if ( $sort ) {
        if ( strtolower( $sort ) == 'asc' ) {
            ksort( $relationships );
        } 
        else if ( strtolower( $sort ) == 'desc' ) {
            krsort( $relationships );
        }
    }
    
    //handle custom data.
    require_once 'CRM/Core/BAO/CustomGroup.php';

    foreach ( $relationships as $relationshipId => $values ) {
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Relationship', $relationshipId, false,
                                                         $values['civicrm_relationship_type_id'] );
        $defaults = array( );
        CRM_Core_BAO_CustomGroup::setDefaults( $groupTree, $defaults );
        
        if ( !empty( $defaults ) ) {
            foreach ( $defaults as $key => $val ) {
                $relationships[$relationshipId][$key] = $val;
            }
        }
    }
    
    if ( $relationships ) {
        return civicrm_create_success( $relationships );
    } else {
        return civicrm_create_error( ts( 'Invalid Data' ) );
    }
}
