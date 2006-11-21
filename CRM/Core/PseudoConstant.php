<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */
class CRM_Core_PseudoConstant 
{
    /**
     * location type
     * @var array
     * @static
     */
    private static $locationType;
    
    /**
     * location vCard name
     * @var array
     * @static
     */
    private static $locationVcardName;
    
    /**
     * activity type
     * @var array
     * @static
     */
    private static $activityType;
  
    /**
     * individual prefix
     * @var array
     * @static
     */
    private static $individualPrefix;

    /**
     * individual suffix
     * @var array
     * @static
     */
    private static $individualSuffix;
    
    /**
     * gender
     * @var array
     * @static
     */
    private static $gender;

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
     * counties
     * @var array
     * @static
     */
    private static $county;

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
     * relationshipType
     * @var array
     * @static
     */
    private static $relationshipType;

    /**
     * civicrm groups that are not smart groups
     * @var array
     * @static
     */
    private static $staticGroup;

    /**
     * user framework groups
     * @var array
     * @static
     */
    private static $ufGroup;

    /**
     * currency codes
     * @var array
     * @static
     */
    private static $currencyCode;

    /**
     * project tasks
     * @var array
     * @static
     */
    private static $tasks;

    /**
     * preferred communication methods
     * @var array
     * @static
     */
    private static $pcm;
    
    /**
     * populate the object from the database. generic populate
     * method
     *
     * The static array $var is populated from the db
     * using the <b>$name DAO</b>. 
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @param array   $var        the associative array we will fill
     * @param string  $name       the name of the DAO
     * @param boolean $all        get all objects. default is to get only active ones.
     * @param string  $retrieve   the field that we are interested in (normally name, differs in some objects)
     * @param string  $filter     the field that we want to filter the result set with
     * @param string  $condition  the condition that gets passed to the final query as the WHERE clause
     *
     * @return void
     * @access public
     * @static
     */
    public static function populate( &$var, $name, $all = false, $retrieve = 'name',
                                     $filter = 'is_active', $condition = null, $orderby = null ) 
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $name) . ".php");
        eval( '$object =& new ' . $name . '( );' );
        
        $object->domain_id = CRM_Core_Config::domainID( );
        $object->selectAdd( );
        $object->selectAdd( "id, $retrieve" );
        if ($condition) {
            $object->whereAdd($condition);
        }
        
        if (!$orderby) {
            $object->orderBy( $retrieve );
        } else {
            $object->orderBy( $orderby );
        }
        
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
     * Get all location vCard names.
     *
     * The static array locationVcardName is returned
     *
     * @access public
     * @static
     *
     * @param boolean $all - get All location vCard names - default is to get only active ones.
     *
     * @return array - array reference of all location vCard names.
     *
     */
    public static function &locationVcardName( $all=false )
    {
        if ( ! self::$locationVcardName ) {
            self::populate( self::$locationVcardName, 'CRM_Core_DAO_LocationType', $all, 'vcard_name' );
        }
        return self::$locationVcardName;
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
    public static function &activityType( $all = true )
    {
        require_once 'CRM/Core/OptionGroup.php';
        $activityType = CRM_Core_OptionGroup::values('activity_type');
        
        if ( $all ) {
            return $activityType;
        } else {
            $otherActivities = array();
            foreach( $activityType as $key => $value ) {
                if ( $key > 4 ) {
                    $otherActivities [$key] = $value;
                }
            }
            return $otherActivities;
        }
    }

    /**
     * Get all Individual Prefix.
     *
     * The static array individualPrefix is returned
     *
     * @access public
     * @static
     *
     * @param boolean $all - get All Individual Prefix - default is to get only active ones.
     *
     * @return array - array reference of all individual prefix.
     *
     */
    public static function &individualPrefix( )
    {
        if ( ! self::$individualPrefix ) {
            require_once 'CRM/Core/OptionGroup.php';
            self::$individualPrefix = CRM_Core_OptionGroup::values('individual_prefix');
        }
        return self::$individualPrefix;
    }

    /**
     * Get all Individual Suffix.
     *
     * The static array individualSuffix is returned
     *
     * @access public
     * @static
     *
     * @param boolean $all - get All Individual Suffix - default is to get only active ones.
     *
     * @return array - array reference of all individual suffix.
     *
     */
    public static function &individualSuffix( )
    {
        if ( ! self::$individualSuffix ) {
            require_once 'CRM/Core/OptionGroup.php';
            self::$individualSuffix = CRM_Core_OptionGroup::values('individual_suffix');
        }
        return self::$individualSuffix;
    }

    /**
     * Get all Gender.
     *
     * The static array gender is returned
     *
     * @access public
     * @static
     *
     * @param boolean $all - get All Gender - default is to get only active ones.
     *
     * @return array - array reference of all gender.
     *
     */
    public static function &gender( )
    {
        if ( ! self::$gender ) {
            require_once 'CRM/Core/OptionGroup.php';
            self::$gender = CRM_Core_OptionGroup::values('gender');
        }
        return self::$gender;
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
    public static function &IMProvider( ) 
    {
        if ( ! self::$imProvider ) {
            require_once 'CRM/Core/OptionGroup.php';
            self::$imProvider = CRM_Core_OptionGroup::values('instant_messenger_service');
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

            // limit the state/province list to the countries specified in CIVICRM_PROVINCE_LIMIT
            $config =& CRM_Core_Config::singleton();
            $countryIsoCodes =& self::countryIsoCode();
            $limitCodes = $config->provinceLimit;
            $limitIds = array();
            foreach ($limitCodes as $code) {
                $limitIds = array_merge($limitIds, array_keys($countryIsoCodes, $code));
            }
            $whereClause = 'country_id IN (' . implode(', ', $limitIds) . ')';

            self::populate( self::$stateProvince, 'CRM_Core_DAO_StateProvince', true, 'name', 'is_active', $whereClause );

            // localise the province names if in an non-en_US locale
            if ($config->lcMessages != '' and $config->lcMessages != 'en_US') {
                $i18n =& CRM_Core_I18n::singleton();
                $i18n->localizeArray(self::$stateProvince);
                asort(self::$stateProvince);
            }
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

            // limit the state/province list to the countries specified in CIVICRM_PROVINCE_LIMIT
            $config =& CRM_Core_Config::singleton();
            $countryIsoCodes =& self::countryIsoCode();
            $limitCodes = $config->provinceLimit;
            $limitIds = array();
            foreach ($limitCodes as $code) {
                $tmpArray   = array_keys($countryIsoCodes, $code);

                if (!empty($tmpArray)) {
                    $limitIds[] = array_shift($tmpArray);
                }
            }
            $whereClause = 'country_id IN (' . implode(', ', $limitIds) . ')';

            self::populate( self::$stateProvinceAbbreviation, 'CRM_Core_DAO_StateProvince', true, 'abbreviation', 'is_active', $whereClause );
        }

        if ($id) {
            if (array_key_exists($id, self::$stateProvinceAbbreviation)) {
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

            $config =& CRM_Core_Config::singleton();

            // limit the country list to the countries specified in CIVICRM_COUNTRY_LIMIT
            // (ensuring it's a subset of the legal values)
            // K/P: We need to fix this, i dont think it works with new setting files
            $limitCodes = $config->countryLimit;
            if ( ! is_array( $limitCodes ) ) {
                $limitCodes = array( $config->countryLimit => 1);
            }

            $limitCodes = array_intersect(self::countryIsoCode(), $limitCodes);
            if (count($limitCodes)) {
                $whereClause = "iso_code IN ('" . implode("', '", $limitCodes) . "')";
            } else {
                $whereClause = null;
            }

            self::populate( self::$country, 'CRM_Core_DAO_Country', true, 'name', 'is_active', $whereClause );

            // if default country is set, percolate it to the top
            if ( $config->defaultContactCountry ) {
                $countryIsoCodes =& self::countryIsoCode();
                $defaultID = array_search($config->defaultContactCountry, $countryIsoCodes); 
                if ( $defaultID !== false ) {
                    $default[$defaultID] = self::$country[$defaultID];
                    self::$country = $default + self::$country;
                }
            }

            // localise the country names if in an non-en_US locale
            if ($config->lcMessages != '' and $config->lcMessages != 'en_US') {
                $i18n =& CRM_Core_I18n::singleton();
                $i18n->localizeArray(self::$country);
                asort(self::$country);
            }
        }

        if ($id) {
            if (array_key_exists($id, self::$country)) {
                return self::$country[$id];
            } else {
                return null;
            }
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
    public static function &countryIsoCode( $id = false )
    {
        if (!self::$countryIsoCode) {
            self::populate( self::$countryIsoCode, 'CRM_Core_DAO_Country',
                            true, 'iso_code');
        }
        if ($id) { 
            if (array_key_exists($id, self::$countryIsoCode)) { 
                return self::$countryIsoCode[$id]; 
            } else { 
                return null; 
            } 
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
        require_once 'CRM/Core/Permission.php';
        return CRM_Core_Permission::group( );
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
    public static function &staticGroup()
    {
        if ( ! self::$staticGroup ) {
            $condition = 'saved_search_id = 0 OR saved_search_id IS NULL';
            self::populate( self::$staticGroup, 'CRM_Contact_DAO_Group', false, 'title', 'is_active', $condition, 'title' );
        }
        return self::$staticGroup;        
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
            self::populate( self::$ufGroup, 'CRM_Core_DAO_UFGroup', false, 'title', 'is_active', null, 'title' );
        }
        return self::$ufGroup;
    }

    /**
     * Get all the project tasks
     *
     * @access public
     * @return array - array reference of all tasks
     * @static
     */
    public static function &tasks( )
    {
        if ( ! self::$tasks ) {
            self::populate( self::$tasks, 'CRM_Project_DAO_Task', false, 'title', 'is_active', null, 'title' );
        }
        return self::$tasks;
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

    /**
     * get all the ISO 4217 currency codes
     *
     * so far, we use this for validation only, so there's no point of putting this into the database
     *
     * @access public
     * @return array - array reference of all currency codes
     * @static
     */
    public static function &currencyCode()
    {
        if (!self::$currencyCode) {
            self::$currencyCode = array('AFN','ALL','DZD','USD','EUR','AOA','XCD','XCD','ARS','AMD',
                'AWG','AUD','EUR','AZM','BSD','BHD','BDT','BBD','BYR','EUR','BZD','XOF','BMD','INR',
                'BTN','BOB','BOV','BAM','BWP','NOK','BRL','USD','BND','BGN','XOF','BIF','KHR','XAF',
                'CAD','CVE','KYD','XAF','XAF','CLP','CLF','CNY','AUD','AUD','COP','COU','KMF','XAF',
                'CDF','NZD','CRC','XOF','HRK','CUP','CYP','CZK','DKK','DJF','XCD','DOP','USD','EGP',
                'SVC','USD','XAF','ERN','EEK','ETB','FKP','DKK','FJD','EUR','EUR','EUR','XPF','EUR',
                'XAF','GMD','GEL','EUR','GHC','GIP','EUR','DKK','XCD','EUR','USD','GTQ','GNF','GWP',
                'XOF','GYD','HTG','USD','AUD','EUR','HNL','HKD','HUF','ISK','INR','IDR','XDR','IRR',
                'IQD','EUR','ILS','EUR','JMD','JPY','JOD','KZT','KES','AUD','KPW','KRW','KWD','KGS',
                'LAK','LVL','LBP','ZAR','LSL','LRD','LYD','CHF','LTL','EUR','MOP','MKD','MGA','MWK',
                'MYR','MVR','XOF','MTL','USD','EUR','MRO','MUR','EUR','MXN','MXV','USD','MDL','EUR',
                'MNT','XCD','MAD','MZM','MMK','ZAR','NAD','AUD','NPR','EUR','ANG','XPF','NZD','NIO',
                'XOF','NGN','NZD','AUD','USD','NOK','OMR','PKR','USD','PAB','USD','PGK','PYG','PEN',
                'PHP','NZD','PLN','EUR','USD','QAR','EUR','ROL','RON','RUB','RWF','SHP','XCD','XCD',
                'EUR','XCD','WST','EUR','STD','SAR','XOF','CSD','EUR','SCR','SLL','SGD','SKK','SIT',
                'SBD','SOS','ZAR','EUR','LKR','SDD','SRD','NOK','SZL','SEK','CHF','CHW','CHE','SYP',
                'TWD','TJS','TZS','THB','USD','XOF','NZD','TOP','TTD','TND','TRY','TRL','TMM','USD',
                'AUD','UGX','UAH','AED','GBP','USD','USS','USN','USD','UYU','UZS','VUV','VEB','VND',
                'USD','USD','XPF','MAD','YER','ZMK','ZWD','XAU','XBA','XBB','XBC','XBD','XPD','XPT',
                'XAG','XFU','XFO','XTS','XXX');
        }
        return self::$currencyCode;
    }

    /**
     * Get all the County from database.
     *
     * The static array county is returned, and if it's
     * called the first time, the <b>County DAO</b> is used 
     * to get all the Counties.
     *
     * Note: any database errors will be trapped by the DAO.
     *
     * @access public
     * @static
     *
     * @param int $id -  Optional id to return
     * @return array - array reference of all Counties
     *
     */
    public static function &county($id = false)
    {
        if (!self::$county) {

            $config =& CRM_Core_Config::singleton();
            // order by id so users who populate civicrm_county can have more control over sort by the order they load the counties
            self::populate( self::$county, 'CRM_Core_DAO_County', true, 'name', null, null, 'id');
        }
        if ($id) {
            if (array_key_exists($id, self::$county)) {
                return self::$county[$id];
            } else {
                return null;
            }
        }
        return self::$county;
    }

    /**
     * Get all the Preferred Communication Methods from database.
     *
     * @access public
     * @static
     *
     * @return array self::pcm - array reference of all preferred communication methods.
     *
     */
    public static function &pcm( ) 
    {
        if ( ! self::$pcm ) {
            require_once 'CRM/Core/OptionGroup.php';
            self::$pcm = CRM_Core_OptionGroup::values('preferred_communication_method');
        }        
        return self::$pcm;
    }
}

?>
