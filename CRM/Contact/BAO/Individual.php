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
 * This class contains basic functions for Contact Individual
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/DAO/Individual.php';

/**
 * BAO object for crm_individual table
 */
class CRM_Contact_BAO_Individual extends CRM_Contact_DAO_Individual 
{
    /**
     * This is a contructor of the class.
     */
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contact_BAO_Individual object
     * @access public
     * @static
     */
    static function add(&$params, &$ids)
    {
        $individual =& new CRM_Contact_BAO_Individual();
       
        $individual->copyValues($params);

        $genders = array(
                'Male' => ts('Male'),
                'Female' => ts('Female'),
                'Transgender' => ts('Transgender')
            );
        $gender = CRM_Utils_Array::value( 'gender', $params );
        
        if (! CRM_Utils_Array::value($gender, $genders)) {
            $gender = CRM_Utils_Array::key($gender, $genders);
        }
        
        $individual->gender = $gender;
        
        $date = CRM_Utils_Array::value('birth_date', $params);
        if (is_array($date)) {
            $individual->birth_date = CRM_Utils_Date::format( $date );
        } else {
            $individual->birth_date = preg_replace('/[^0-9]/', '', $date);
        }
        $individual->middle_name = CRM_Utils_Array::value('middle_name', $params);
        // hack to make db_do save a null value to a field
        if ( ! $individual->birth_date ) {
            $individual->birth_date = 'NULL';
        }

        if (!array_key_exists('is_deceased', $params)) {
            $individual->is_deceased = 0;
        }

        $individual->id = CRM_Utils_Array::value( 'individual', $ids );

        return $individual->save();
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contact_BAO_Individual|null the found object or null
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids ) {
        $individual =& new CRM_Contact_BAO_Individual( );
        
        $individual->copyValues( $params );
        if ( $individual->find(true) ) {
            $ids['individual'] = $individual->id;
            CRM_Core_DAO::storeValues( $individual, $values );
            if ( isset( $individual->gender ) ) {
                $values['gender'] = $individual->gender;
            }
            if ( isset( $individual->birth_date ) ) {
                $values['birth_date'] = CRM_Utils_Date::unformat( $individual->birth_date );
            }

            CRM_Contact_DAO_Individual::addDisplayEnums($values);

            return $individual;

        }
        return null;
    }
        
}

?>
