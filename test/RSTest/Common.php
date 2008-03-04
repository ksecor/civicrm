<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * This class contains common fumctionality required for Test by Recordset size 
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */
require_once '../../civicrm.config.php';
require_once 'CRM/Core/Config.php';

class test_RSTest_Common 
{
    
    /****************************************
     * All constant required during the tests
     ****************************************/
    const DATA_FILENAME                = "sample_data.xml";

    const INITIAL_DATASET_SIZE         = 1000;
    const STEP                         =  500;
    const NUM_DOMAIN                   =   20;

    // percent share of all contacts out of total number of contacts
    const INDIVIDUAL_PERCENT           =   75;
    const HOUSEHOLD_PERCENT            =   15;
    const ORGANIZATION_PERCENT         =   10;

    const NUM_INDIVIDUAL_PER_HOUSEHOLD =    4;
    const NUM_ACTIVITY_HISTORY         =  150;   
    
    //const ADD_TO_DB                    =    0;
    const ADD_TO_DB                    =    1;
    
    const ARRAY_DIRECT_USE             =    1;
    const ARRAY_SHIFT_USE              =    2;
    
    //group contact enums
    public static $groupStatus               = array('1' => 'Pending',
                                                     '2' => 'Added',
                                                     '3' => 'Removed'
                                                     );
    public static $groupMethod               = array('1' => 'Admin',
                                                     '2' => 'Email',
                                                     '3' => 'Web',
                                                     '4' => 'API'
                                                     );
    public static $subscriptionHistoryMethod = array('Admin', 
                                                     'Email');

        // country and state province combo
    public static $CSC                       = array(
                                                     1228 => array( // united states
                                                                   1004 => array ('San Francisco', 'Los Angeles', 'Palo Alto'), // california
                                                                   1031 => array ('New York', 'Albany'), // new york
                                                                   ),
                                                     1101 => array( // india
                                                                   1113 => array ('Mumbai', 'Pune', 'Nasik'), // maharashtra
                                                                   1114 => array ('Bangalore', 'Mangalore', 'Udipi'), // karnataka
                                                                   ),
                                                     1172 => array( // poland
                                                                   1115 => array ('Warszawa', 'Plock'), // Mazowieckie
                                                                   1116 => array ('Gdansk', 'Gdynia'), // Pomorskie 
                                                                   ),
                                                     );
    
    
    // constructor
    function __construct()
    {
    }
    
    /*******************************
     *  Methods required during tests
     ******************************/

    /**
     *  Sizing the Recordset
     * 
     *  This function sizes the recordset in multiple of 1k.
     *  This function can not be called statically. 
     *
     *  @param    $multiple   fixes the dataset size in multiple of 1k
     *
     *  @return   integer
     *  @access   public
     */
    public function recordsetSize($multiple=1)
    {
        return self::INITIAL_DATASET_SIZE * $multiple;
    }
    
    /**
     *  Getter for random country and state province.
     *
     *  This method is used for getting random country and state province 
     *  from the array of country  and state province. 
     *  This method can be called statically.
     *  
     *  @return array
     *  @access public
     *  @static 
     */
    public static function getRandomCSC()
    {
        $array = array();

        //$c = array_rand(self::$CSC);
        $c = 1228;

        // the state array now
        $s = array_rand(self::$CSC[$c]);

        // the city
        $ci = array_rand(self::$CSC[$c][$s]);
        $city = self::$CSC[$c][$s][$ci];
        
        $array[] = $c;
        $array[] = $s;
        $array[] = $city;
        
        return $array;
    }

    /**
     *  Getter for the array values for entities     
     * 
     *  This function is getter for array values for different values. 
     *  This function can not be called statically. 
     *
     *  @param    $multiple   fixes the dataset size in multiple of 1k
     *
     *  @return   integer
     *  @access   public
     */
    public static function getValue($type)
    {
        $typeValue = array();
        switch ($type) {
        case 'locationType'    :
            $typeValue         = CRM_Core_PseudoConstant::locationType();
            break;
        case 'tag'        :
            $typeValue         = CRM_Core_PseudoConstant::tag();
            break;
        case 'phoneType'       :
            $typeValue         = CRM_Core_SelectValues::phoneType();
            break;
        case 'prefixType'      :
            $typeValue         = self::getPrefixArray();
            break;
        case 'suffixType'      :
            $typeValue         = self::getSuffixArray();
            break;
        case 'gender'          :
            $typeValue         = self::getGenderArray();
            break;
        case 'greetingType'    :
            $typeValue         = CRM_Core_SelectValues::greeting();        
            break;
        case 'PCMType'         :
            $typeValue         = CRM_Core_PseudoConstant::pcm();
            break;
        case 'relationshipType':
            $typeValue         = CRM_Core_PseudoConstant::relationshipType();
            break;
        case 'group'           :
            $typeValue         = CRM_Core_PseudoConstant::allGroup();
            break;
        case 'IMProvider'      :
            $typeValue         = CRM_Core_PseudoConstant::IMProvider();
            break;
        case 'contactType'     :
            $typeValue         = CRM_Core_SelectValues::contactType();    
            break;
        } 
        return array_keys($typeValue);
    }

    /**
     *  Getter for random element from the array.
     *
     *  This method is used for getting random element 
     *  from the provided array of. 
     *  This method can be called statically.
     *    
     *
     *  @param  $array  array of elements
     *  
     *  @return $array
     *  @access private
     *  
     */
    public static function getRandomElement($array, $mode=1)
    {
        switch ($mode) {
        case 1:
            return $array[mt_rand(1, count($array))-1];
            break;
        case 2:
            $tmp = array_shift($array);
            return $array[mt_rand(1, count($array))-1]; 
            break;
        } 
        
    }

    /**
     *  Getter for random character.
     *
     *  This method is used for getting random character when get called. 
     *  This method can be called statically.
     *  
     *  @return char
     */
    public static function getRandomChar()
    {
        return chr(mt_rand(65,90));
    }

    public function getRandomString($size=32)
    {
        $string = "";

        // get an ascii code for each character
        for($i=0; $i<$size; $i++) {
            $random_int = mt_rand(65,122);
            if(($random_int<97) && ($random_int>90)) {
                // if ascii code between 90 and 97 substitute with space
                $random_int=32;
            }
            $random_char=chr($random_int);
            $string .= $random_char;
        }
        return $string;
    }

    /**
     *  Generate a random date. 
     *
     *  This method is used for generating random date depending on 
     *  the start date and end date entered.
     *  The conditions or cases while generating random date are : 
     *     1) If both $startDate and $endDate are defined generate
     *        date between them.
     *
     *     2) If only startDate is specified then date generated is
     *        between startDate + 1 year.
     *
     *     3) If only endDate is specified then date generated is
     *        between endDate - 1 year.
     *
     *     4) If none are specified - date is between today - 1year 
     *        and today
     *
     *  This method can be called statically.
     *
     * @param   int $startDate  Start Date in Unix timestamp
     * @param   int $endDate    End Date in Unix timestamp
     *
     * @access  private
     * @return  string          Randomly generated date in the format "Ymd"
     *
     */
    public static function getRandomDate($startDate=0, $endDate=0)
    {
        // number of seconds per year
        $numSecond  = 31536000;
        $dateFormat = "Ymd";
        $today      = time();

        // both are defined
        if ($startDate && $endDate) {
            return date($dateFormat, mt_rand($startDate, $endDate));
        }

        // only startDate is defined
        if ($startDate) {
            return date($dateFormat, mt_rand($startDate, $startDate+$numSecond));
        }

        // only endDate is defined
        if ($endDate) {
            return date($dateFormat, mt_rand($endDate-$numSecond, $endDate));
        }        
        
        // none are defined
        return date($dateFormat, mt_rand($today-$numSecond, $today));
    }
    
    public static function getPrefixArray()
    {
        $prefixArray = CRM_Core_PseudoConstant::individualPrefix();
        return $prefixArray;
    }
    
    public static function getSuffixArray()
    {
        $suffixArray = CRM_Core_PseudoConstant::individualSuffix();
        return $suffixArray;
    }
    
    public static function getGenderArray()
    {
        $genderArray = CRM_Core_PseudoConstant::gender();
        return $genderArray;
    }
    
    public static function getRandomName($firstName, $lastName)
    {
        $first_name    = ucfirst(self::getRandomElement($firstName, self::ARRAY_DIRECT_USE));
        $middle_name   = ucfirst(self::getRandomChar());
        $last_name     = ucfirst(self::getRandomElement($lastName, self::ARRAY_DIRECT_USE));
        $prefixArray   = self::getPrefixArray();
        $suffixArray   = self::getSuffixArray();
        $prefix        = $prefixArray[self::getRandomElement(self::getValue('prefixType'), self::ARRAY_DIRECT_USE)];
        $suffix        = $suffixArray[self::getRandomElement(self::getValue('suffixType'), self::ARRAY_DIRECT_USE)];
        return "$prefix $first_name $middle_name $last_name $suffix"; 
    }
    
    /**
     *  Insert data into the database
     *  
     *  This method is used for inserting data into the database.
     *  This method can be called statically.
     *
     *  @param    $dao      object of DAO
     *
     *  @return   none
     *
     *  @access   public
     *
     */
    public static function _insert($dao)
    {
        if (self::ADD_TO_DB) {
            if (!$dao->insert()) {
                echo mysql_error() . "\n";
                exit(1);
            }
        }
    }

    /**
     *  Update data in the database
     *  
     *  This method is used for upadting the data 
     *  present in the database.
     *  This method can be called statically.
     *
     *  @param    $dao      object of DAO
     *
     *  @return   none
     *
     *  @access   public
     *
     */
    public static function _update($dao)
    {
        if (self::ADD_TO_DB) {
            if (!$dao->update()) {
                echo mysql_error() . "\n";
                exit(1);
            }
        }
    }
}

