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

class CRM_Core_PseudoConstant {
    /**
     * All the below elements are dynamic.
     */


    /**
     * location type
     * @var array
     * @static
     */
    private static $locationType;
    
    /**
     * im protocols
     * @var array
     * @static
     */
    private static $imProvider;

    /**
     * states, provinces
     * @var array
     * @static
     */
    private static $stateProvince;

    /**
     * country
     * @var array
     * @static
     */
    private static $country;

    /**
     * category
     * @var array
     * @static
     */
    private static $category;

    /**
     * group
     * @var array
     * @static
     */
    private static $group;
    

    /**
     * relationshipType
     * @var array
     * @static
     */
    private static $relationshipType;


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
    private static function populate( &$var, $name, $all = false, $retrieve = 'name' ) {
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
    public static function &locationType( $all=false )
    {
        if ( ! self::$locationType ) {
            self::populate( self::$locationType, 'CRM_Contact_DAO_LocationType', $all );
        }
        return self::$locationType;
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
    public static function &IMProvider( $all = false ) {
        if (!self::$imProvider) {
            self::populate( self::$imProvider, 'CRM_Core_DAO_IMProvider', $all );
        }
        return self::$imProvider;
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
    public static function &stateProvince()
    {
        if (!self::$stateProvince) {
            self::populate( self::$stateProvince, 'CRM_Core_DAO_StateProvince', true );
        }
        return self::$stateProvince;
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
    public static function &country()
    {
        if (!self::$country) {
            self::populate( self::$country, 'CRM_Core_DAO_Country', true );
        }
        return self::$country;
    }



    /**
     * Get all the categories from database.
     *
     * The static array category is returned, and if it's
     * called the first time, the <b>Category DAO</b> is used 
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
    public static function &category()
    {
        if (!self::$category) {
            self::populate( self::$category, 'CRM_Contact_DAO_Category', true );
        }
        return self::$category;
    }

    /**
     * Get all groups from database.
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
    public static function &group()
    {
        if (!self::$group) {
            self::populate( self::$group, 'CRM_Contact_DAO_Group', true, 'title' );
        }
        return self::$group;
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
    public static function &relationshipType()
    {
        if (!self::$relationshipType) {
            self::$relationshipType = array();
            $relationshipTypeDAO = new CRM_Contact_DAO_RelationshipType();
            $relationshipTypeDAO->selectAdd();
            $relationshipTypeDAO->selectAdd('id, name_a_b, name_b_a, contact_type_a, contact_type_b');
            $relationshipTypeDAO->is_active = 1;
            $relationshipTypeDAO->find();
            while($relationshipTypeDAO->fetch()) {
                self::$relationshipType[$relationshipTypeDAO->id] = array(
                                                                          'name_a_b'       => "$relationshipTypeDAO->name_a_b",
                                                                          'name_b_a'       => "$relationshipTypeDAO->name_b_a",
                                                                          'contact_type_a' => "$relationshipTypeDAO->contact_type_a",
                                                                          'contact_type_b' => "$relationshipTypeDAO->contact_type_b",
                                                                         );
            }
        }
        return self::$relationshipType;
    }
}
?>