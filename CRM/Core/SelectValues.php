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

class CRM_Core_SelectValues {

    /**
     * prefix names
     * @static
     */
    static function &prefixName()
    {
        static $prefixName = null;
        if (!$prefixName) {
            $prefixName = array(
                ''    => ts('-title-'),
                'Mrs' => ts('Mrs.'),
                'Ms'  => ts('Ms.'),
                'Mr'  => ts('Mr.'),
                'Dr'  => ts('Dr.'),
                ' '   => ts('(none)')
            );
        }
        return $prefixName;
    }

    /**
     * suffix names
     * @static
     */
    static function &suffixName()
    {
        static $suffixName = null;
        if (!$suffixName) {
            $suffixName = array(
                ''   => ts('-suffix-'),
                'Jr' => ts('Jr.'),
                'Sr' => ts('Sr.'),
                'II' =>ts('II'),
                ' '  => ts('(none)')
            );
        }
        return $suffixName;
    }

    /**
     * greetings
     * @static
     */
    static function &greeting()
    {
        static $greeting = null;
        if (!$greeting) {
            $greeting = array(
                'Formal'    => ts('default - Dear [first] [last]'),
                'Informal'  => ts('Dear [first]'),
                'Honorific' => ts('Dear [title] [last]'),
                'Custom'    => ts('Customized')
            );
        }
        return $greeting;
    }
    
    /**
     * different types of phones
     * @static
     */
    static function &phoneType()
    {
        static $phoneType = null;
        if (!$phoneType) {
            $phoneType = array(
                ''       => ts('-select-'),
                'Phone'  => ts('Phone'),
                'Mobile' => ts('Mobile'),
                'Fax'    => ts('Fax'),
                'Pager'  => ts('Pager')
            );
        }
        return $phoneType;
    }

    /**
     * list of counties
     * FIXME a bit short at the moment
     * @static
     */
    static function &county()
    {
        static $county = null;
        if (!$country) {
            $country = array(
                ''   => ts('-select-'),
                1001 => ts('San Francisco')
            );
        }
        return $county;
    }
    
    /**
     * preferred communication method
     * @static
     */
    static function &pcm()
    {
        static $pcm = null;
        if (!$pcm) {
            $pcm = array(
                ''      => ts('-no preference-'),
                'Phone' => ts('Phone'),
                'Email' => ts('Email'), 
                'Post'  => ts('Postal Mail')
            );
        }
        return $pcm;
    }

    /**
     * various pre defined contact super types
     * @static
     */
    static function &contactType()
    {
        static $contactType = null;
        if (!$contactType) {
            $contactType = array(
                ''             => ts('- all contacts -'),
                'Individual'   => ts('Individuals'),
                'Household'    => ts('Households'),
                'Organization' => ts('Organizations')
            );
        }
        return $contactType;
    }

    /**
     * Extended property (custom field) data types
     * @static
     */
    static function &customDataType()
    {
        static $customDataType = null;
        if (!$customDataType) {
            $customDataType = array(
                ''        => ts('-select-'),
                'String'  => ts('Text'),
                'Int'     => ts('Integer'),
                'Float'   => ts('Decimal Number'),
                'Money'   => ts('Money'),
                'Text'    => ts('Memo'),
                'Date'    => ts('Date'),
                'Boolean' => ts('Yes/No')
            );
        }
        return $customDataType;
    }
    
    /**
     * Custom form field types
     * @static
     */
    static function &customHtmlType()
    {
        static $customHtmlType = null;
        if (!$customHtmlType) {
            $customHtmlType = array(
                ''                        => ts('-select-'),
                'Text'                    => ts('Single-line input field (text or numeric)'),
                'TextArea'                => ts('Multi-line text box (textarea)'),
                'Select'                  => ts('Drop-down (select list)'),
                'Radio'                   => ts('Radio buttons'),
                'Checkbox'                => ts('Checkbox(es)'),
                'Select Date'             => ts('Date selector'),
                'Select State / Province' => ts('State / Province selector'),
                'Select Country'          => ts('Country selector')
            );
        }
        return $customHtmlType;
    }
    
    /**
     * various pre defined extensions for dynamic properties and groups
     *
     * @static
     */
    static function &customGroupExtends()
    {
        static $customGroupExtends = null;
        if (!$customGroupExtends) {
            $customGroupExtends = array(
                'Contact'      => ts('-all contact types-'),
                'Individual'   => ts('Individuals'),
                'Household'    => ts('Households'),
                'Organization' => ts('Organizations')
            );
        }
        return $customGroupExtends;
    }

    /**
     * styles for displaying the custom data group
     *
     * @static
     */
    static function &customGroupStyle()
    {
        static $customGroupStyle = null;
        if (!$customGroupStyle) {
            $customGroupStyle = array(
                'Tab'    => ts('Tab'),
                'Inline' => ts('Inline')
            );
        }
        return $customGroupStyle;
    }

    /**
     * the status of a contact within a group
     *
     * @static
     */
    static function &groupContactStatus()
    {
        static $groupContactStatus = null;
        if (!$groupContactStatus) {
            $groupContactStatus = array(
                'In'      => ts('In'),
                'Out'     => ts('Out'),
                'Pending' => ts('Pending')
            );
        }
        return $groupContactStatus;
    }

    /**
     * list of Group Types
     * @static
     */
    static function &groupType()
    {
        static $groupType = null;
        if (!$groupType) {
            $groupType = array(
                'query'  => ts('Dynamic'),
                'static' => ts('Static')
            );
        }
        return $groupType;
    }
  
    
    /**
     * compose the parameters for a date select object
     *
     * @param  $type the type of date
     *
     * @return array         the date array
     * @static
     */
    static function &date($type = 'birth')
    {
        static $_date = null;
        if (!$_date) {
            $_date = array(
                'format'           => 'd M Y',
                'addEmptyOption'   => true,
                'emptyOptionText'  => ts('-select-'),
                'emptyOptionValue' => ''
            );
        }
        
        $newDate = $_date;

        if ($type == 'birth') {
            $minOffset = 100;
            $maxOffset = 0;
        } elseif ($type == 'relative') {
            $minOffset = 20;
            $maxOffset = 20;
        } elseif ($type == 'custom') {
            $minOffset = 100;
            $maxOffset = 20;
        }
        
        $year = date('Y');
        $newDate['minYear'] = $year - $minOffset;
        $newDate['maxYear'] = $year + $maxOffset;

        return $newDate;
    }

    /**
     * values for UF form visibility options
     *
     * @static
     */
    static function ufVisibility( ) {
        static $_visibility = null;
        if ( ! $_visibility ) {
            $_visibility = array(
                                 'User and User Admin Only'       => 'User and User Admin Only',
                                 'Public User Pages'              => 'Public User Pages',
                                 'Public User Pages and Listings' => 'Public User Pages and Listings',
                                 );
        }
        return $_visibility;
    }


    /**
     * different types of status for activities
     * @param $type if true Call status array else Meeting status array
     *
     * @static
     *
     */
    static function &activityStatus($type = false)
    {
        static $activityStatus = null;
        if (!$activityStatus) {
            if ($type) {
                $activityStatus = array(
                                        'Scheduled'         => ts('Scheduled'),
                                        'Completed'         => ts('Completed'),
                                        'Unreachable'       => ts('Unreachable'),
                                        'Left Message'      => ts('Left Message')
                                        );
            } else {
                $activityStatus = array(
                                        'Scheduled'         => ts('Scheduled'),
                                        'Completed'         => ts('Completed'),
                                        );
            }
        }
        return $activityStatus;
    }

    /**
     * Function to get hours
     *
     * 
     * @static
     */
    function getHours () 
    {
        for ($i = 0; $i <= 6; $i++ ) {
            $hours[$i] = $i;
        }
        return $hours;
    }

    /**
     * Function to get minutes
     *
     * 
     * @static
     */
    function getMinutes () 
    {
        for ($i = 0; $i <= 60; $i++ ) {
            $minutes[$i] = $i;
        }
        return $minutes;
    }

}

?>
