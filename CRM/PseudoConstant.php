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

class CRM_PseudoConstant {
    /**
     * various pre defined contact super types
     * @var array
     * @static
     */
    public static $contactType = array(
                                       'Individual'   => 'Individuals',
                                       'Household'    => 'Households',
                                       'Organization' => 'Organizations',
                                       );
    

    /**
     * prefix names
     * @var array
     * @static
     */
    public static $prefixName = array(
                                      'Mrs' => 'Mrs.',
                                      'Ms'  => 'Ms.',
                                      'Mr'  => 'Mr.',
                                      'Dr'   => 'Dr.',
                                      );

    /**
     * suffix names
     * @var array
     * @static
     */
    public static $suffixName = array(
                                      'Jr'  => 'Jr.',
                                      'Sr'  => 'Sr.',
                                      'II'   =>'II',
                                      );

    /**
     * greetings
     * @var array
     * @static
     */
    public static $greeting   = array(
                                      'Formal'    => 'default - Dear [first] [last]',
                                      'Informal'  => 'Dear [first]',
                                      'Honorific' => 'Dear [title] [last]',
                                      'Custom'    => 'Customized',
                                      );
    
    /**
     * different types of phones
     * @var array
     * @static
     */
    public static $phoneType = array(
                                      'Phone'  => 'Phone',
                                      'Mobile' => 'Mobile',
                                      'Fax'    => 'Fax',
                                      'Pager'  => 'Pager'
                                      );



    /**
     * preferred communication method
     * @var array
     * @static
     */
    public static $pcm = array(
                               'Phone' => 'Phone', 
                               'Email' => 'Email', 
                               'Post'  => 'Postal Mail',
                               );  



    /**
     * All the below elements are dynamic.
     */


    /**
     * location type
     * @var array
     * @static
     */
    public static $locationType;
    
    /**
     * im protocols
     * @var array
     * @static
     */
    public static $imProvider;

    /**
     * states, provinces
     * @var array
     * @static
     */
    public static $stateProvince;

    /**
     * country
     * @var array
     * @static
     */
    public static $country;

    /**
     * category
     * @var array
     * @static
     */
    public static $category;

    /**
     * group
     * @var array
     * @static
     */
    public static $group;
    

    /**
     * relationshipType
     * @var array
     * @static
     */
    public static $relationshipType;




    /**
     * Get all the location types from database.
     *
     * The static array locationType is returned, and if it's
     * called the first time, the <b>location DAO</b> is used 
     * to get all the location types.
     *
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param boolean getAll - get All location types - default is to get only active ones.
     *
     * @return array - array reference of all location types.
     *
     */
    public static function &getLocationType($getAll=false)
    {
        if(!self::$locationType) {
            $locationTypeDAO = new CRM_Contact_DAO_LocationType();
            $locationTypeDAO->selectAdd();
            $locationTypeDAO->selectAdd('id, name');
            
            if (!$getAll) {
                $locationTypeDAO->is_active = 1;
            }

            $locationTypeDAO->find();
            while($locationTypeDAO->fetch()) {
                self::$locationType[$locationTypeDAO->id] = "$locationTypeDAO->name";
            }
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
    public static function &getIMProvider()
    {
        if (!self::$imProvider) {
            $imProviderDAO = new CRM_DAO_IMProvider();
            $imProviderDAO->selectAdd();
            $imProviderDAO->selectAdd('id, name');
            $imProviderDAO->find();
            while($imProviderDAO->fetch()) {
                self::$imProvider[$imProviderDAO->id] = "$imProviderDAO->name";
            }
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
    public static function &getStateProvince()
    {
        if (!self::$stateProvince) {
            $stateProvinceDAO = new CRM_DAO_StateProvince();
            $stateProvinceDAO->selectAdd();
            $stateProvinceDAO->selectAdd('id, name');
            $stateProvinceDAO->find();
            while($stateProvinceDAO->fetch()) {
                self::$stateProvince[$stateProvinceDAO->id] = "$stateProvinceDAO->name";
            }
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
    public static function &getCountry()
    {
        if (!self::$country) {
            $countryDAO = new CRM_DAO_Country();
            $countryDAO->selectAdd();
            $countryDAO->selectAdd('id, name');
            $countryDAO->find();
            while($countryDAO->fetch()) {
                self::$country[$countryDAO->id] = "$countryDAO->name";
            }
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
    public static function &getCategory()
    {
        if (!self::$category) {
            self::$category = array();
            $categoryDAO = new CRM_Contact_DAO_Category();
            $categoryDAO->selectAdd();
            $categoryDAO->selectAdd('id, name, parent_id');
            $categoryDAO->find();
            while($categoryDAO->fetch()) {
                self::$category[$categoryDAO->id] = array(
                                                           'name' => "$categoryDAO->name",
                                                           'parent_id' => $categoryDAO->parent_id,
                                                           );
            }
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
    public static function &getGroup()
    {
        if (!self::$group) {
            self::$group = array();
            $groupDAO = new CRM_Contact_DAO_Group();
            $groupDAO->selectAdd();
            $groupDAO->selectAdd('id, name');
            $groupDAO->find();
            while($groupDAO->fetch()) {
                self::$group[$groupDAO->id] = "$groupDAO->name";
            }
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
    public static function &getRelationshipType()
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