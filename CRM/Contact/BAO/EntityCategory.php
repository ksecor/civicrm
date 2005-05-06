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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */




class CRM_Contact_BAO_EntityCategory extends CRM_Contact_DAO_EntityCategory 
{


    /**
     *
     * Given a contact id, it returns an array of category id's the 
     * contact belongs to.
     *
     * @param string $entityTable name of the entity table usually 'crm_contact'
     * @param int $entityID id of the entity usually the contactID.
     * @returns array() reference $category array of catagory id's the contact belongs to.
     *
     * @access public
     * @static
     */

    static function &getCategory($entityTable = 'crm_contact', $entityID) 
    {
        $category = array();

        $entityCategory = new CRM_Contact_BAO_EntityCategory();
        $entityCategory->entity_table = $entityTable;
        $entityCategory->entity_id = $entityID;
        $entityCategory->find();

        while ($entityCategory->fetch()) {
            $category[$entityCategory->category_id] = $entityCategory->category_id;
        } 
        return $category;        
    }

    /**
     * takes an associative array and creates a entityCategory object
     *
     * the function extract all the params it needs to initialize the create a
     * group object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contact_BAO_EntityCategory object
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
       
        $dataExists = self::dataExists( $params );
        if ( ! $dataExists ) {
            return null;
        }

        $entityCategory = new CRM_Contact_BAO_EntityCategory( );
        $entityCategory->copyValues( $params );
        $entityCategory->save( );
        return $entityCategory;
    }

    /**
     * Check if there is data to create the object
     *
     * @params array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        return ($params['category_id'] == 0) ? false : true;
     }

    /**
     * Function to delete the category for a contact
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contact_BAO_EntityCategory object
     * @access public
     * @static
     *
     */
    static function del( &$params ) 
    {
        $entityCategory = new CRM_Contact_BAO_EntityCategory( );
        $entityCategory->copyValues( $params );
        $entityCategory->delete( );
        return $entityCategory;
    }


    /**
     * Given an array of contact ids, add all the contacts to the tags 
     *
     * @param array  $contactIds (reference ) the array of contact ids to be added
     * @param int    $categoryId the id of the category
     *
     * @return array             (total, added, notAdded) count of contacts added to group
     * @access public
     * @static
     */
    static function addContactsToTag( &$contactIds, $categoryId ) {
        $numContactsAdded    = 0;
        $numContactsNotAdded = 0;

        foreach ( $contactIds as $contactId ) {
            $category = new CRM_Contact_DAO_EntityCategory( );
            
            $category->entity_id    = $contactId;
            $category->entity_table = 'crm_contact';
            $category->category_id  = $categoryId;
            if ( ! $category->find( ) ) {
                $category->save( );
                $numContactsAdded++;
            } else {
                $numContactsNotAdded++;
            }
        }

        return array( count($contactIds), $numContactsAdded, $numContactsNotAdded );
    }


}

?>