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
                                      '||'   =>'||',
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
    public static $locationType = array(
                                        '' => '-select-',
                                        1 => 'Home',
                                        2 => 'Work',
                                        3 => 'Main',
                                        4 => 'Other'
                                        );
    
    /**
     * im protocols (fetch and cache from db based on locale)
     * @var array
     * @static
     */
    public static $imProvider = array(
                                      '' => '-select-',
                                      1 => 'Yahoo',
                                      2 => 'MSN',
                                      3 => 'AIM',
                                      4 => 'Jabber',
                                      5 => 'Indiatimes'
                                      );

    /**
     * states array (fetch and cache from generic db, based on locale)
     * @var array
     * @static
     */
    public static $stateProvince = array(
                                         ''   => '-select-',
                                         1004 => 'California',
                                         1036 => 'Oregon',
                                         1046 => 'Washington'
                                         );

    /**
     * country array (fetch and cache from generic db, based on locale)
     * @var array
     * @static
     */
    public static $country    = array(
                                      ''   => '-select-',
                                      1039 => 'Canada',
                                      1101 => 'India',
                                      1172 => 'Poland',
                                      1228 => 'United States'
                                      );

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
     * This method uses the <b>location DAO</b> to get all the
     * location types. It pushes them into an array and
     * and returns the array reference.
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
        $location_type_array = array('' => '-select-');
        $location_type_dao = new CRM_Contact_DAO_LocationType();
        $location_type_dao->selectAdd('id, name');
        $location_type_dao->find();
        while($location_type_dao->fetch()) {
            $location_type_array[$location_type_dao->id] = "$location_type_dao->name";
        }
        return $location_type_array;
    }


    /**
     * Get all the IM Providers from database.
     *
     * This method uses the <b>IM Provider DAO</b> to get all the
     * IM Providers. It pushes them into an array and
     * and returns the array reference.
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
        $im_provider_array = array('' => '-select-');
        $im_provider_dao = new CRM_DAO_IMProvider();
        $im_provider_dao->selectAdd('id, name');
        $im_provider_dao->find();
        while($im_provider_dao->fetch()) {
            $im_provider_array[$im_provider_dao->id] = "$im_provider_dao->name";
        }
        return $im_provider_array;
    }
}

/**
 * initialize maxYear to the right value, i.e.
 * the current year
 */
CRM_SelectValues::$date['maxYear'] = date('Y');

?>