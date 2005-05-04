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
                                      'minYear'   => 1900,
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
     * category array (fetch and cache from generic db)
     * @var array
     * @static
     */
    public static $category;

    /**
     * group array (fetch and cache from generic db)
     * @var array
     * @static
     */
    public static $group;
    

    /**
     * relationshipType array (fetch and cache from generic db)
     * @var array
     * @static
     */
    public static $relationshipType;


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
     * Extended property (custom field) data types
     *
     * @var array
     * @static
     */
    public static $extPropertyDataType = array(
                                               ''           => '-select-',
                                               'String'     => 'Text',
                                               'Int'        => 'Integer',
                                               'Float'      => 'Decimal Number',
                                               'Money'      => 'Money',
                                               'Text'       => 'Memo',
                                               'Date'       => 'Date',
                                               'Boolean'    => 'Yes/No',
                                               );
    
    /**
     * Custom form field types
     * @var array
     * @static
     */
    public static $formFieldType = array(
                                               ''                        => '-select-',
                                               'Text'                    => 'Single-line input field (text or numeric)',
                                               'TextArea'                => 'Multi-line text box (textarea)',
                                               'Select'                  => 'Drop-down (select list)',
                                               'Radio'                   => 'Radio buttons',
                                               'Checkbox'                => 'Checkbox(es)',
                                               'Select Date'             => 'Date selector',
                                               'Select State / Province' => 'State / Province selector',
                                               'Select Country'          => 'Country selector',
                                               );
    
    
    /**
     * various pre defined extensions for dynamic properties and groups
     *
     * @var array
     * @static
     */
    public static $extPropertyGroupExtends = array(
                                                   'Contact'      => 'Contact',
                                                   'Individual'   => 'Individual',
                                                   'Household'    => 'Household',
                                                   'Organization' => 'Organization',
                                                   'Location'     => 'Location',
                                                   'Address'      => 'Address',
                                                   );

    /**
     * the status of a contact within a group
     *
     * @ @var array
     * @static
     */
    public static $groupContactStatus = array(
                                              'In'      => 'In',
                                              'Out'     => 'Out',
                                              'Pending' => 'Pending',
                                              );

    /**
     * list of Group Types
     * @var array
     * @static
     */
    public static $groupType = array(
                                     'query'    => 'Dynamic',
                                     'static'   => 'Static',
                                     );
    
}

/**
 * initialize maxYear to the right value, i.e.
 * the current year
 */
CRM_SelectValues::$date['maxYear'] = date('Y');

?>