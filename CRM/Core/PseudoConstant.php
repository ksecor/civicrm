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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

class CRM_Core_PseudoConstant {
    /**
     * location type
     * @var array
     * @static
     */
    private static $locationType;
    
    /**
     * activity type
     * @var array
     * @static
     */
    private static $activityType;
    

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
     * states/provinces abbreviations
     * @var array
     * @static
     */
    private static $stateProvinceAbbreviation;

    /**
     * country
     * @var array
     * @static
     */
    private static $country;


    /**
     * countryIsoCode
     * @var array
     * @static
     */
    private static $countryIsoCode;

    /**
     * tag
     * @var array
     * @static
     */
    private static $tag;

    /**
     * group
     * @var array
     * @static
     */
    private static $group;

    /**
     * saved search
     * @var array
     * @static
     */
    private static $savedSearch;

    /**
     * relationshipType
     * @var array
     * @static
     */
    private static $relationshipType;

    /**
     * user framework groups
     * @var array
     * @static
     */
    private static $ufGroup;

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
     * @param string  $filter   the field that we want to filter the result set with
     *
     * @return void
     * @access protected
     * @static
     */
    protected static function populate( &$var, $name, $all = false, $retrieve = 'name', $filter = 'is_active', $condition = null ) {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $name) . ".php");
        eval( '$object =& new ' . $name . '( );' );
       
        $object->domain_id = CRM_Core_Config::domainID( );
        $object->selectAdd( );
        $object->selectAdd( "id, $retrieve" );
        if ($condition) {
            $object->whereAdd($condition);
        }
        
        $object->orderBy( $retrieve );

        if ( ! $all ) {
            $object->$filter = 1;
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
            self::populate( self::$locationType, 'CRM_Core_DAO_LocationType', $all );
        }
        return self::$locationType;
    }


    /**
     * Get all Activty types.
     *
     * The static array activityType is returned
     *
     * @access public
     * @static
     *
     * @param boolean $all - get All Activity  types - default is to get only active ones.
     *
     * @return array - array reference of all activty types.
     *
     */
    public static function &activityType( $all=false )
    {
        if ( ! self::$activityType ) {
            self::populate( self::$activityType, 'CRM_Core_DAO_ActivityType', $all, 'name', 'is_active', 'id > 3' );
        }
        return self::$activityType;
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
     * @param int $id -  Optional id to return
     * @return array - array reference of all State/Provinces.
     *
     */
    public static function &stateProvince($id = false)
    {
        if (!self::$stateProvince) {
            self::populate( self::$stateProvince, 'CRM_Core_DAO_StateProvince', true );
        }
        if ($id) {
            if (array_key_exists($id, self::$stateProvince)) {
                return self::$stateProvince[$id];
            } else {
                return null;
            }
        }
        return self::$stateProvince;
    }

    /**
     * Get all the State/Province abbreviations from the database.
     * 
     * Same as above, except gets the abbreviations instead of the names.
     *
     * @access public
     * @static
     * @param int $id  -     Optional id to return
     * @return array - array reference of all State/Province abbreviations.
     */
    public static function &stateProvinceAbbreviation($id = false)
    {
        if (!self::$stateProvinceAbbreviation) {
            self::populate( self::$stateProvinceAbbreviation,
            'CRM_Core_DAO_StateProvince', true, 'abbreviation');
        }
        if ($id) {
            if (array_key_exists($id, self::$stateProvince)) {
                return self::$stateProvinceAbbreviation[$id];
            } else {
                return null;
            }
        }
        return self::$stateProvinceAbbreviation;
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
     * @param int $id - Optional id to return
     * @return array - array reference of all countries.
     *
     */
    public static function &country($id = false)
    {
        if (!self::$country) {
            self::populate( self::$country, 'CRM_Core_DAO_Country', true );
        }
        if ($id) {
            if (array_key_exists($id, self::$country)) {
                return self::$country[$id];
            } else {
                return null;
            }
        }
        $config =& CRM_Core_Config::singleton();
        if ($config->lcMessages != '' and $config->lcMessages != 'en_US') {
            $i18n =& CRM_Core_I18n::singleton();
            $i18n->localizeArray(self::$country);
            asort(self::$country);
        }
        return self::$country;
    }

    /**
     * Get all the country ISO Code abbreviations from the database.
     *
     * The static array countryIsoCode is returned, and if it's
     * called the first time, the <b>Country DAO</b> is used
     * to get all the countries' ISO codes.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @return array - array reference of all country ISO codes.
     *
     */
    public static function &countryIsoCode()
    {
        if (!self::$countryIsoCode) {
            self::populate( self::$countryIsoCode, 'CRM_Core_DAO_Country',
            'true', 'iso_code');
        }
        return self::$countryIsoCode;
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
     * @return array - array reference of all categories.
     *
     */
    public static function &tag()
    {
        if (!self::$tag) {
            self::populate( self::$tag, 'CRM_Core_DAO_Tag', true );
        }
        return self::$tag;
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
    * @return array - array reference of all groups.
    *
    */
    public static function &allGroup()
    {
        if (!self::$group) {
            self::populate( self::$group, 'CRM_Contact_DAO_Group', false, 'title' );
        }
        return self::$group;
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
     * @return array - array reference of all groups.
     *
     */
    public static function &group()
    {
        return CRM_Core_Permission::group( );
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
     * @return array - array reference of all saved searches
     *
     */
    public static function &allSavedSearch()
    {
        if (!self::$savedSearch) {
            self::populate( self::$savedSearch, 'CRM_Contact_DAO_SavedSearch', true, 'title' );
        }
        return self::$savedSearch;
    }

    /**
     * Get all permissioned saved searched from database
     *
     * @access public
     *
     * @return array - array reference of all groups.
     * @static
     */
    public static function &savedSearch()
    {
        return CRM_Core_Permission::savedSearch( );
    }

    /**
     * Get all the user framework groups
     *
     * @access public
     * @return array - array reference of all groups.
     * @static
     */
    public static function &ufGroup( )
    {
        if ( ! self::$ufGroup ) {
            self::populate( self::$ufGroup, 'CRM_Core_DAO_UFGroup', false, 'title' );
        }
        return self::$ufGroup;
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
     * @return array - array reference of all relationship types.
     *
     */
    public static function &relationshipType()
    {
        if (!self::$relationshipType) {
            self::$relationshipType = array();
            $relationshipTypeDAO =& new CRM_Contact_DAO_RelationshipType();
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
