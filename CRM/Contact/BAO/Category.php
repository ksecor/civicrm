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

class CRM_Contact_BAO_Category extends CRM_Contact_DAO_Category {

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
     * @return object CRM_BAO_Category object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $category = new CRM_Contact_DAO_Category( );
        $category->copyValues( $params );
        if ( $category->find( true ) ) {
            $category->storeValues( $defaults );
            return $category;
        }
        return null;
    }

    /**
     * Function to delete the category 
     *
     * @param int $id category id
     *
     * @return null
     * @access public
     * @static
     *
     */
    static function del ( $id ) {
        // delete all crm_entity_category records with the selected category id
        $entityCategory = new CRM_Contact_DAO_EntityCategory( );
        $entityCategory->category_id = $id;
        $entityCategory->find();
        while ( $entityCategory->fetch() ) {
            $entityCategory->delete();
        }
        
        // delete from category table
        $category = new CRM_Contact_DAO_Category( );
        $category->id = $id;
        $category->delete();

        
    }
    
}

?>