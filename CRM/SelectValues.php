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
 * One place to store frequently used values in Select Elements. Note that
 * some of the below elements will be dynamic, so we'll probably have a 
 * smart caching scheme on a per domain basis
 * 
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_SelectValues {

    /**
     * prefix names
     * @var array
     * @static
     */
    public static $prefixName = array(
                                      ''    => '-title-',
                                      'Mrs' => 'Mrs.',
                                      'Ms'  => 'Ms.',
                                      'Mr'  => 'Mr.',
                                      'Dr'   => 'Dr.',
                                      'none' => '(none)',
                                      );

    /**
     * suffix names
     * @var array
     * @static
     */
    public static $suffixName = array(
                                      ''    => '-suffix-',
                                      'Jr'  => 'Jr.',
                                      'Sr'  => 'Sr.',
                                      'II'   =>'II',
                                      'none' => '(none)',
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
     * date combinations. We need to fix maxYear (and we do so at the
     * end of this file)
     * static values cannot invoke a function in php
     * @var array
     * @static
     */
    public static $date       = array(
                                      'language'  => 'en',
                                      'format'    => 'dMY',
                                      'minYear'   => 1950,
                                      'maxYear'   => 2005,
                                      'addEmptyOption'   => true,
                                      'emptyOptionText'  => '-select-',
                                      'emptyOptionValue' => ''
                                      );

    /**
     * different types of phones
     * @var array
     * @static
     */
    public static $phoneType = array(
                                      ''       => '-select-',
                                      'Phone'  => 'Phone',
                                      'Mobile' => 'Mobile',
                                      'Fax'    => 'Fax',
                                      'Pager'  => 'Pager'
                                      );

    /**
     * All the below elements are dynamic. Constants
     */

    /**
     * Location Type (fetch and cache from db based on domain)
     * @var array
     * @static
     */
    public static $locationType;
    
    /**
     * im protocols (fetch and cache from db based on locale)
     * @var array
     * @static
     */
    public static $imProvider;

    /**
     * states array (fetch and cache from generic db, based on locale)
     * @var array
     * @static
     */
    public static $stateProvince;

    /**
     * country array (fetch and cache from generic db, based on locale)
     * @var array
     * @static
     */
    public static $country;

    /**
     * list of counties
     * @var array
     * @static
     */
    public static $county = array(
                                  ''   => '-select-',
                                  1001 => 'San Francisco',
                                  );

    /**
     * preferred communication method
     * @var array
     * @static
     */
    public static $pcm = array(
                               ''     => '-no preference-',
                               'Phone' => 'Phone', 
                               'Email' => 'Email', 
                               'Post'  => 'Postal Mail',
                               );  

    /**
     * various pre defined contact super types
     * @var array
     * @static
     */
    public static $contactType = array(
                                       ''            => '-all contacts-',
                                       'Individual'   => 'Individuals',
                                       'Household'    => 'Households',
                                       'Organization' => 'Organizations',
                                       );
    



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
            self::$locationType = array('' => '-select-');
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
            self::$imProvider = array('' => '-select-');
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
            self::$stateProvince = array('' => '-select-');
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
            self::$country = array('' => '-select-');
            $country_dao = new CRM_DAO_Country();
            $country_dao->selectAdd('id, name');
            $country_dao->find();
            while($country_dao->fetch()) {
                self::$country[$country_dao->id] = "$country_dao->name";
            }
        }
        return self::$country;
    }

}

/**
 * initialize maxYear to the right value, i.e.
 * the current year
 */
CRM_SelectValues::$date['maxYear'] = date('Y');

?>