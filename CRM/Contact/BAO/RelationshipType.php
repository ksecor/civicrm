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
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Contact_BAO_RelationshipType extends CRM_Contact_DAO_RelationshipType {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Contact_BAO_RelationshipType object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $relationshipType =& new CRM_Contact_DAO_RelationshipType( );
        $relationshipType->copyValues( $params );
        if ( $relationshipType->find( true ) ) {
            CRM_Core_DAO::storeValues( $relationshipType, $defaults );
            return $relationshipType;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) {
        $relationshipType =& new CRM_Contact_DAO_RelationshipType( );
        $relationshipType->id = $id;
        if ( $relationshipType->find( true ) ) {
            $relationshipType->is_active = $is_active;
            return $relationshipType->save( );
        }
        return null;
    }

    /**
     * Function to add the relationship type in the db
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids  
     *
     * @return object CRM_Contact_DAO_RelationshipType
     * @access public
     * @static
     *
     */
    static function add( &$params, &$ids) {
        // action is taken depending upon the mode
        $relationshipType =& new CRM_Contact_DAO_RelationshipType( );
        
        $relationshipType->copyValues( $params );

        // if label B to A is blank, insert the value label A to B for it
        if (!strlen(trim($strName = CRM_Utils_Array::value( 'name_b_a', $params)))) {
            $relationshipType->name_b_a = CRM_Utils_Array::value( 'name_a_b', $params);
        }
        
        $relationshipType->domain_id = CRM_Core_Config::$domainID;

        $relationshipType->id = CRM_Utils_Array::value( 'relationshipType', $ids );

        return $relationshipType->save( );
        
    }
}

?>