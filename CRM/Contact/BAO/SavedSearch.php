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

require_once 'CRM/Contact/DAO/SavedSearch.php';

/**
 * Business object for Saved searches
 *
 */
class CRM_Contact_BAO_SavedSearch extends CRM_Contact_DAO_SavedSearch 
{

    /**
     * class constructor
     *
     * @return object CRM_Contact_BAO_SavedSearch
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * query the db for all saved searches.
     *
     * @return array $aSavedSearch - contains the search name as value and and id as key
     *
     * @access public
     */
    function getAll()
    {
        $savedSearch =& new CRM_Contact_DAO_SavedSearch ();
        $savedSearch->selectAdd();
        $savedSearch->selectAdd('id, name');
        $savedSearch->find();
        while($savedSearch->fetch()) {
            $aSavedSearch[$savedSearch->id] = $savedSearch->name;
        }
        return $aSavedSearch;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects.
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Contact_BAO_SavedSearch
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $savedSearch =& new CRM_Contact_DAO_SavedSearch( );
        $savedSearch->copyValues( $params );
        if ( $savedSearch->find( true ) ) {
            CRM_Core_DAO::storeValues( $savedSearch, $defaults );
            return $savedSearch;
        }
        return null;
    }

    /**
     * given an id, extract the formValues of the saved search
     *
     * @param int $id the id of the saved search
     *
     * @return array the values of the posted saved search
     * @access public
     * @static
     */
    static function getFormValues( $id ) {
        $fv = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_SavedSearch', $id, 'form_values' );
        if ( $fv ) {
            // make sure u unserialize - since it's stored in serialized form
            return unserialize( $fv );
        }
        return null;
    }

    /**
     * get the where clause for a saved search
     *
     * @param int $id saved search id
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     *
     * @return string the where clause for this saved search
     * @access public
     * @static
     */
    static function whereClause( $id, &$tables ) {
        $fv = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_SavedSearch', $id, 'form_values' );
        if ( $fv ) {
            $fv = unserialize( $fv );
            return CRM_Contact_BAO_Contact::whereClause( $fv, $false, $tables );
        }
        return null;

    }

    /**
     * given an id, get the name of the saved search
     *
     * @param int $id the id of the saved search
     *
     * @return string the name of the saved search
     * @access public
     * @static
     */
    static function getName( $id ) {
        $group                   =& new CRM_Contact_DAO_Group( );
        $group->saved_search_id = $id;
        if ( $group->find( true ) ) {
            return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group', $group->id, 'name' );
        }
        return null;
    }

}

?>
