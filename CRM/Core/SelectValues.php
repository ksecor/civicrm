<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * One place to store frequently used values in Select Elements. Note that
 * some of the below elements will be dynamic, so we'll probably have a 
 * smart caching scheme on a per domain basis
 * 
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */
class CRM_Core_SelectValues 
{

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
                'Honorific' => ts('Dear [prefix] [last]'),
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
                ''       => ts('- select -'),
                'Phone'  => ts('Phone'),
                'Mobile' => ts('Mobile'),
                'Fax'    => ts('Fax'),
                'Pager'  => ts('Pager')
            );
        }
        return $phoneType;
    }

    /**
     * preferred mail format
     * @static
     */
    static function &pmf()
    {
        static $pmf = null;
        if (!$pmf) {
            $pmf = array(
                         'Both' => ts('Both'),
                         'HTML' => ts('HTML'),
                         'Text' => ts('Text')
            );
            
        }
        return $pmf;
    }
    
    /**
     * privacy options
     * @static
     */
    static function &privacy()
    {
        static $privacy = null;
        if (!$privacy) {
            $privacy = array(
                'do_not_phone' => ts('Do not phone'),
                'do_not_email' => ts('Do not email'),
                'do_not_mail'  => ts('Do not mail'),
                'do_not_trade' => ts('Do not trade')
            );
        }
        return $privacy;
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
     * various pre defined unit list
     * @static
     */
    static function &unitList($unitType = null)
    {
        static $unitList = null;
        if (!$unitList) {
            $unitList = array(
                              ''             => ts('- select -'),
                              'day'          => ts('day'),
                              'month'        => ts('month'),
                              'year'         => ts('year')
                              );
            if ( $unitType == 'duration' ) {
                $unitAdd = array(
                                 'lifetime'      => ts('life time')
                                 );
                $unitList = array_merge( $unitList, $unitAdd);
            }
        }
        return $unitList;
    }

    /**
     * various pre defined period types
     * @static
     */
    static function &periodType()
    {
        static $periodType = null;
        if (!$periodType) {
            $periodType = array(
                 ''             => ts('- select -'),
                 'rolling'      => ts('rolling'),
                 'fixed'        => ts('fixed')
             );
        }
        return $periodType;
    }

    /**
     * various pre defined member visibility options
     * @static
     */
    static function &memberVisibility()
    {
        static $visible = null;
        if (!$visible) {
            $visible = array(
                 'Public'       => ts('Public'),
                 'Admin'        => ts('Admin')
             );
        }
        return $visible;
    }

    /**
     * various pre defined event dates
     * @static
     */
    static function &eventDate()
    {
        static $eventDate = null;
        if (!$eventDate) {
            $eventDate = array(
                 ''             => ts('- select -'),
                 'start_date'   => ts('start date'),
                 'end_date'     => ts('end date'),
                 'join_date'    => ts('join date')
             );
        }
        return $eventDate;
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
                'File'    => ts('File'),
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
                'File'                    => ts('File'),
                'Select State / Province' => ts('State / Province selector'),
                'Select Country'          => ts('Country selector')
            );
        }
        return $customHtmlType;
    }
    
    // /**
//      * various pre defined extensions for dynamic properties and groups
//      *
//      * @static
//      */
//     static function &customGroupExtends()
//     {
//         static $customGroupExtends = null;
//         if (!$customGroupExtends) {
//             $customGroupExtends = array(
//                 'Contact'      => ts('- All Contacts -'),
//                 'Individual'   => ts('Individuals'),
//                 'Household'    => ts('Households'),
//                 'Organization' => ts('Organizations'),
//                 'Activity'     => ts('Activities'),
//                 'Relationship' => ts('Relationships'),
//                 'Phonecall'    => ts('Phonecalls'),
//                 'Meeting'      => ts('Meetings'),
//                 'Group'        => ts('Groups'),
//                 'Contribution' => ts('Contributions'),
//             );
//         }
//         return $customGroupExtends;
//     }

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
                'Contact'      => ts('Contacts'),
                'Individual'   => ts('Individuals'),
                'Household'    => ts('Households'),
                'Organization' => ts('Organizations'),
                'Activity'     => ts('Activities'),
                'Relationship' => ts('Relationships'),
                'Contribution' => ts('Contributions'),
                'Group'        => ts('Groups'),
                'Membership'   => ts('Memberships'),
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
     * for displaying the uf group types
     *
     * @static
     */
    static function &ufGroupTypes()
    {
        static $ufGroupType = null;
        if (!$ufGroupType) {
            $ufGroupType = array(
                'User Registration' => ts('User Registration'),
                'User Account'      => ts('View/Edit User Account'),
                'Profile'           => ts('Profile'),
                'Search Profile'    => ts('Search Results'),
            );
        }
        return $ufGroupType;
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
                'Added'     => ts('Added'),
                'Removed'   => ts('Removed'),
                'Pending'   => ts('Pending')
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
    static function &date($type = 'birth', $min = null, $max = null,$dateParts = null)
    {
        static $_date = null;
        static $config = null;

        if (!$config) {
            $config =& CRM_Core_Config::singleton();
        }

        if (!$_date) {
            require_once 'CRM/Utils/Date.php';
            $_date = array(
                'format'           => CRM_Utils_Date::posixToPhp($config->dateformatQfDate),
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
            $minOffset = $min; 
            $maxOffset = $max; 
            if( $dateParts ) {
                require_once 'CRM/Core/BAO/CustomOption.php';
                $format = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$dateParts);
                foreach( $format as $v ) {
                    $stringFormat = $stringFormat ." ".$v;  
                }
                $newDate['format'] = $stringFormat;
            }
        } elseif ($type == 'fixed') {
            $minOffset = 0;
            $maxOffset = 5;
        } elseif ( $type == 'manual' ) {
            $minOffset = $min;
            $maxOffset = $max;
        } elseif ($type == 'creditCard') {
            $newDate['format'] = 'M Y';
            $minOffset = 0;
            $maxOffset = 5;
        } elseif ($type == 'mailing') {
            $minOffset = 0;
            $maxOffset = 1;
            $newDate['format'] = 'Y M d H i';
            $newDate['optionIncrement']['i'] = 15;
        } elseif ($type == 'datetime') {
            require_once 'CRM/Utils/Date.php';
            $newDate['format'] = CRM_Utils_Date::posixToPhp($config->dateformatQfDatetime);
            $newDate['optionIncrement']['i'] = 15;
            // change this to minus 1 so folks can at least go back 1 year
            $minOffset = 1;
            $maxOffset = 3;
        } elseif ($type =='duration') {
            $newDate['format'] = 'H i';
            $newDate['optionIncrement']['i'] = 15;
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
                                 'User and User Admin Only'       => ts('User and User Admin Only'),
                                 'Public User Pages'              => ts('Public User Pages'),
                                 'Public User Pages and Listings' => ts('Public User Pages and Listings'),
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
     * different type of Mailing Components
     *
     * @static
     * return array
     */
    static function &mailingComponents( ) {
        static $components = null;

        if (! $components ) {
            $components = array( 'Header'      => ts('Header'),
                                 'Footer'      => ts('Footer'),
                                 'Reply'       => ts('Reply Auto-responder'),
                                 'OptOut'      => ts('Opt-out Message'),
                                 'Subscribe'   => ts('Subscription Message to organization'),
                                 'Welcome'     => ts('Welcome Message'),
                                 'Unsubscribe' => ts('Farewell Message'),
                                 );
        }
        return $components;
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
        for ($i = 0; $i < 60; $i = $i+15 ) {
            $minutes[$i] = $i;
        }
        return $minutes;
    }


    /**
     * Function to get CiviCRM components 
     * 
     * @return array $components array of components
     * @static
     */
    static function &component()
    {
        static $components = null;
        if (!$components) {
            $components = array(
                                'CiviContribute'  => ts('CiviContribute'),
                                'CiviMail'        => ts('CiviMail'),
                                'CiviMember'      => ts('CiviMember'),
                                'CiviEvent'       => ts('CiviEvent'),
                                'Quest'           => ts('Quest')
                                );
        }
        return $components;
    }

    /**
     * Function to get the Payment Processor 
     * 
     * @return array $processor array of payment processor
     * @static
     */
    static function &paymentProcessor()
    {
        static $processor = null;
        if (!$processor) {
            $processor = array(
                               'PayPal'           => ts('PayPal - Website Payments Pro'),
                               'PayPal_Express'   => ts('PayPal - Express'),
                               'PayPal_Standard'  => ts('PayPal - Website Payments Standard'),
                               'Moneris'          => ts('Moneris')
                               );
        }
        return $processor;
    }

    /**
     * Function to get the Map Provider 
     * 
     * @return array $map array of map providers
     * @static
     */
    static function &mapProvider()
    {
        static $map = null;
        if (!$map) {
            $map = array(
                         'Yahoo'  => ts('Yahoo'),
                         'Google' => ts('Google')
                         );
        }
        return $map;
    }
}

?>
