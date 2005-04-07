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
     * @param none
     * @return array - array reference of all location types.
     *
     */
    public static function &getLocationType()
    {
        CRM_Error::le_method();
        if(!isset(self::$locationType)) {
            CRM_Error::debug_log_message("locationType is not set");
            $location_type_dao = new CRM_Contact_DAO_LocationType();
            $location_type_dao->selectAdd('id, name');
            $location_type_dao->find();
            while($location_type_dao->fetch()) {
                self::$locationType[$location_type_dao->id] = "$location_type_dao->name";
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
        CRM_Error::le_method();
        if (!isset(self::$imProvider)) {
            CRM_Error::debug_log_message("imProvider is not set");
            $im_provider_dao = new CRM_DAO_IMProvider();
            $im_provider_dao->selectAdd('id, name');
            $im_provider_dao->find();
            while($im_provider_dao->fetch()) {
                self::$imProvider[$im_provider_dao->id] = "$im_provider_dao->name";
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
        CRM_Error::le_method();
        if (!isset(self::$stateProvince)) {
            CRM_Error::debug_log_message("stateProvince is not set");
            $state_province_dao = new CRM_DAO_StateProvince();
            $state_province_dao->selectAdd('id, name');
            $state_province_dao->find();
            while($state_province_dao->fetch()) {
                self::$stateProvince[$state_province_dao->id] = "$state_province_dao->name";
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
        CRM_Error::le_method();
        if (!isset(self::$country)) {
            CRM_Error::debug_log_message("country is not set");
            $country_dao = new CRM_DAO_Country();
            $country_dao->selectAdd('id, name');
            $country_dao->find();
            while($country_dao->fetch()) {
                self::$country[$country_dao->id] = "$country_dao->name";
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
        CRM_Error::le_method();
        if (!isset(self::$category)) {
            CRM_Error::debug_log_message("category is not set");
            self::$category = array();
            $category_dao = new CRM_Contact_DAO_Category();
            $category_dao->selectAdd('id, name, parent_id');
            $category_dao->find();
            while($category_dao->fetch()) {
                self::$category[$category_dao->id] = array(
                                                           'name' => "$category_dao->name",
                                                           'parent_id' => $category_dao->parent_id,
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
        CRM_Error::le_method();
        if (!isset(self::$group)) {
            CRM_Error::debug_log_message("group is not set");
            self::$group = array();
            $group_dao = new CRM_Contact_DAO_Group();
            $group_dao->selectAdd('id, name');
            $group_dao->find();
            while($group_dao->fetch()) {
                self::$group[$group_dao->id] = "$group_dao->name";
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
        CRM_Error::le_method();
        if (!isset(self::$relationshipType)) {
            CRM_Error::debug_log_message("relationshipType is not set");
            self::$relationshipType = array();
            $relationshipTypeDAO = new CRM_Contact_DAO_RelationshipType();
            $relationshipTypeDAO->selectAdd('id, description');
            $relationshipTypeDAO->find();
            while($relationshipTypeDAO->fetch()) {
                self::$relationshipType[$relationshipTypeDAO->id] = "$relationshipTypeDAO->description";
            }
        }
        return self::$relationshipType;
    }

}

?>