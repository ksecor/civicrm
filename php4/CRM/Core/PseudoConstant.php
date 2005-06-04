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
 * Stores all constants and pseudo constants for CRM application.
 *
 * examples of constants are "Contact Type" which will always be either
 * 'Individual', 'Household', 'Organization'.
 *
 * pseudo constants are entities from the database whose values rarely
 * change. examples are list of countries, states, location types,
 * relationship types.
 *
 * currently we're getting the data from the underlying database. this
 * will be reworked to use caching.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['locationType'] = null;
$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['imProvider'] = null;
$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['stateProvince'] = null;
$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['country'] = null;
$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['tag'] = null;
$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['group'] = null;
$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['savedSearch'] = null;
$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['relationshipType'] = null;


require_once 'CRM/Core/Drupal.php';
require_once 'CRM/Contact/DAO/RelationshipType.php';
class CRM_Core_PseudoConstant {
    /**
     * All the below elements are dynamic.
     */


    /**
     * location type
     * @var array
     * @static
     */
    
    
    /**
     * im protocols
     * @var array
     * @static
     */
    

    /**
     * states, provinces
     * @var array
     * @static
     */
    

    /**
     * country
     * @var array
     * @static
     */
    

    /**
     * tag
     * @var array
     * @static
     */
    

    /**
     * group
     * @var array
     * @static
     */
    

    /**
     * saved search
     * @var array
     * @static
     */
    

    /**
     * relationshipType
     * @var array
     * @static
     */
    


    /**
     * populate the object from the database. generic populate
     * method
     *
     * The static array $var is populated from the db
     * using the <b>$name DAO</b>. 
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @param array   $var      the associative array we will fill
     * @param string  $name     the name of the DAO
     * @param boolean $all      get all objects. default is to get only active ones.
     * @param string  $retrieve the field that we are interested in (normally name, differs in some objects)
     *
     * @return void
     * @access private
     * @static
     */
      function populate( &$var, $name, $all = false, $retrieve = 'name' ) {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $name) . ".php");
        eval( '$object = new ' . $name . '( );' );
        $object->selectAdd( );
        $object->selectAdd( "id, $retrieve" );
        $object->orderBy( $retrieve );
        
        if ( ! $all ) {
            $object->is_active = 1;
        }
        
        $object->find( );
        $var = array( );
        while ( $object->fetch( ) ) {
            $var[$object->id] = $object->$retrieve;
        }

    }

    /**
     * Get all location types.
     *
     * The static array locationType is returned
     *
     * @access public
     * @static
     *
     * @param boolean $all - get All location types - default is to get only active ones.
     *
     * @return array - array reference of all location types.
     *
     */
      function &locationType( $all=false )
    {
        if ( ! $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['locationType'] ) {
            CRM_Core_PseudoConstant::populate( $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['locationType'], 'CRM_Contact_DAO_LocationType', $all );
        }
        return $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['locationType'];
    }


    /**
     * Get all the IM Providers from database.
     *
     * The static array imProvider is returned, and if it's
     * called the first time, the <b>IM DAO</b> is used 
     * to get all the IM Providers.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all IM providers.
     *
     */
      function &IMProvider( $all = false ) {
        if (!$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['imProvider']) {
            CRM_Core_PseudoConstant::populate( $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['imProvider'], 'CRM_Core_DAO_IMProvider', $all );
        }
        return $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['imProvider'];
    }

    /**
     * Get all the State/Province from database.
     *
     * The static array stateProvince is returned, and if it's
     * called the first time, the <b>State Province DAO</b> is used 
     * to get all the States.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all IM providers.
     *
     */
      function &stateProvince()
    {
        if (!$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['stateProvince']) {
            CRM_Core_PseudoConstant::populate( $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['stateProvince'], 'CRM_Core_DAO_StateProvince', true );
        }
        return $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['stateProvince'];
    }


    /**
     * Get all the countries from database.
     *
     * The static array country is returned, and if it's
     * called the first time, the <b>Country DAO</b> is used 
     * to get all the countries.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all countries.
     *
     */
      function &country()
    {
        if (!$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['country']) {
            CRM_Core_PseudoConstant::populate( $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['country'], 'CRM_Core_DAO_Country', true );
        }
        return $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['country'];
    }



    /**
     * Get all the categories from database.
     *
     * The static array tag is returned, and if it's
     * called the first time, the <b>Tag DAO</b> is used 
     * to get all the categories.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all categories.
     *
     */
      function &tag()
    {
        if (!$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['tag']) {
            CRM_Core_PseudoConstant::populate( $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['tag'], 'CRM_Contact_DAO_Tag', true );
        }
        return $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['tag'];
    }

    /**
    * Get all groups from database
    *
    * The static array group is returned, and if it's
    * called the first time, the <b>Group DAO</b> is used
    * to get all the groups.
    *
    * Note: any database errors will be trapped by the DAO.
    *
    * @access public
    * @static
    *
    * @param none
    * @return array - array reference of all groups.
    *
    */
      function &allGroup()
    {
        if (!$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['group']) {
            CRM_Core_PseudoConstant::populate( $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['group'], 'CRM_Contact_DAO_Group', true, 'title' );
        }
        return $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['group'];
    }

    /**
     * Get all permissioned groups from database
     *
     * The static array group is returned, and if it's
     * called the first time, the <b>Group DAO</b> is used 
     * to get all the groups.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all groups.
     *
     */
      function &group()
    {
        return CRM_Core_Drupal::group( );
    }

    /**
     * Get all saved searches from database
     *
     * The static array saved searched is returned, and if it's
     * called the first time, the <b>Saved Search DAO</b> is used
     * to get all the groups.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all saved searches
     *
     */
      function &allSavedSearch()
    {
        if (!$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['savedSearch']) {
            CRM_Core_PseudoConstant::populate( $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['savedSearch'], 'CRM_Contact_DAO_SavedSearch', true, 'name' );
        }
        return $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['savedSearch'];
    }

    /**
     * Get all permissioned saved searched from database
     *
     * @access public
     *
     * @param none
     * @return array - array reference of all groups.
     * @static
     */
      function &savedSearch()
    {
        return CRM_Core_Drupal::savedSearch( );
    }

    /**
     * Get all Relationship Types  from database.
     *
     * The static array group is returned, and if it's
     * called the first time, the <b>RelationshipType DAO</b> is used 
     * to get all the relationship types.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all relationship types.
     *
     */
      function &relationshipType()
    {
        if (!$GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['relationshipType']) {
            $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['relationshipType'] = array();
            $relationshipTypeDAO = new CRM_Contact_DAO_RelationshipType();
            $relationshipTypeDAO->selectAdd();
            $relationshipTypeDAO->selectAdd('id, name_a_b, name_b_a, contact_type_a, contact_type_b');
            $relationshipTypeDAO->is_active = 1;
            $relationshipTypeDAO->find();
            while($relationshipTypeDAO->fetch()) {
                $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['relationshipType'][$relationshipTypeDAO->id] = array(
                                                                          'name_a_b'       => "$relationshipTypeDAO->name_a_b",
                                                                          'name_b_a'       => "$relationshipTypeDAO->name_b_a",
                                                                          'contact_type_a' => "$relationshipTypeDAO->contact_type_a",
                                                                          'contact_type_b' => "$relationshipTypeDAO->contact_type_b",
                                                                         );
            }
        }
        return $GLOBALS['_CRM_CORE_PSEUDOCONSTANT']['relationshipType'];
    }
}
?>