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
    static public $prefixName = array(
                                      ' '    => '-title-',
                                      'Mrs.' => 'Mrs.',
                                      'Ms.'  => 'Ms.',
                                      'Mr.'  => 'Mr.',
                                      'Dr'   => 'Dr.',
                                      'none' => '(none)',
                                      );

    /**
     * suffix names
     * @var array
     * @static
     */
    static public $suffixName = array(
                                      ' '    => '-suffix-',
                                      'Jr.'  => 'Jr.',
                                      'Sr.'  => 'Sr.',
                                      '||'   =>'||',
                                      'none' => '(none)',
                                      );

    /**
     * greetings
     * @var array
     * @static
     */
    static public $greeting   = array(
                                      'Formal'    => 'default - Dear [first] [last]',
                                      'Informal'  => 'Dear [first]',
                                      'Honorific' => 'Dear [title] [last]',
                                      'Custom'    => 'Customized',
                                      );
    
    /**
     * date combinations. We need to fix maxYear (maybe in the constructor)
     * static values cannot invoke a function in php
     * @var array
     * @static
     */
    static public $date       = array(
                                      'language'  => 'en',
                                      'format'    => 'dMY',
                                      'minYear'   => 1900,
                                      'maxYear'   => 2005,
                                      );

    /**
     * different types of phones
     * @var array
     * @static
     */
    static public $phone      = array(
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
    static public $locationType    = array(
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
    static public $im         = array(
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
    static public $state      = array(
                                      1004 => 'California',
                                      1036 => 'Oregon',
                                      1046 => 'Washington'
                                      );

    /**
     * country array (fetch and cache from generic db, based on locale)
     * @var array
     * @static
     */
    static public $country    = array(
                                      1039 => 'Canada',
                                      1101 => 'India',
                                      1172 => 'Poland',
                                      1128 => 'United States'
                                      );

    /**
     * preferred communication method
     * @var array
     * @static
     */
    static public $pcm = array(
                               ' '     => '-no preference-',
                               'Phone' => 'by phone', 
                               'Email' => 'by email', 
                               'Post'  => 'by postal email',
                               );  

    /**
     * various pre defined contact super types
     * @var array
     * @static
     */
    static public $contactType = array(
                                       ' '            => '-no preference-',
                                       'Individual'   => 'Individual',
                                       'Household'    => 'Household'
                                       'Organization' => 'Organization',
                                       );
    
}

?>