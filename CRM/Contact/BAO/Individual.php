<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Class contains functions for individual contact type
 */
class CRM_Contact_BAO_Individual
{
    /**
     * This is a contructor of the class.
     */
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * Function is used to format the individual contact values
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array  $contact  contact object
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function format( &$params, &$contact )
    {
        if ( ! self::dataExists($params ) ) {
            return;
        }

        $sortName = "";
        $firstName  = CRM_Utils_Array::value('first_name'   , $params, '');
        $middleName = CRM_Utils_Array::value('middle_name'  , $params, '');
        $lastName   = CRM_Utils_Array::value('last_name'    , $params, '');
        $prefix_id  = CRM_Utils_Array::value('prefix_id'    , $params, '');
        $suffix_id  = CRM_Utils_Array::value('suffix_id'    , $params, '');
        
        // get prefix and suffix names
        $prefixes = CRM_Core_PseudoConstant::individualPrefix();
        $suffixes = CRM_Core_PseudoConstant::individualSuffix();
        
        $prefix = $suffix = null;
        if ( $prefix_id ) {
            $prefix = $prefixes[$prefix_id];
        }
        if ( $suffix_id ) {
            $suffix = $suffixes[$suffix_id];
        }
        
        // a comma should only be present if both first_name and last name are present.
        if ($firstName && $lastName) {
            $sortName = "$lastName, $firstName";
        } else {
            if ( empty($firstName) && $lastName ) {
                $sortName = $lastName;
            } else if ( $firstName && empty($lastName) ) {
                $sortName = $firstName;
            } else if ( $contact->id ){
                $individual =& new CRM_Contact_BAO_Contact();
                $individual->contact_id = $contact->id;
                $individual->find();
                while ( $individual->fetch( ) ) {
                    $individualLastName   = $individual->last_name;
                    $individualFirstName  = $individual->first_name;
                    $individualPrefix     = $individual->prefix_id;
                    $individualSuffix     = $individual->suffix_id;
                    $individualMiddleName = $individual->middle_name;
                }
                
                if (empty($lastName) && CRM_Utils_Array::value('last_name', $params) && !empty($individualLastName)) {
                    $lastName = $individualLastName;
                } 
                
                if (empty($firstName) && CRM_Utils_Array::value('first_name', $params) && !empty($individualFirstName)) {
                    $firstName = $individualFirstName;
                }
                
                if (empty($prefix) && CRM_Utils_Array::value('prefix_id', $params) && !empty($individualPrefix)) {
                    $prefix = $individualPrefix;
                }
                
                if (empty($middleName) && CRM_Utils_Array::value('middle_name', $params) && !empty($individualMiddleName)) {
                    $middleName = $individualMiddleName;
                }
                
                if (empty($suffix) && CRM_Utils_Array::value('suffix_id', $params) && !empty($individualSuffix)) {
                    $suffix = $individualSuffix;
                }
                if ( $lastName || $firstName ) {
                    $sortName = "$lastName, $firstName";
                }
            }
        }
        
        if (trim($sortName)) {
            $contact->sort_name    = trim($sortName);
        }
        
        if ( $lastName || $firstName || $middleName ) {
            $display_name =
                trim( "$prefix $firstName $middleName $lastName $suffix" );
            $display_name = str_replace( '  ', ' ', $display_name );
        }
        
        if (trim($display_name)) {
            $contact->display_name = $display_name;
        }
        
        if ( CRM_Utils_Array::value( 'location', $params ) ) {
            foreach ($params['location'] as $locBlock) {
                if (! isset($locBlock['is_primary']) || ! ($locBlock['is_primary']) ) {
                    continue;
                }
                $email = $locBlock['email'][1]['email'];
                break;
            }
        }
        
        $uniqId = $params['user_unique_id'];
        if (empty($contact->display_name)) {
            if (isset($email)) {
                $contact->display_name = $email;
            } else if (isset($uniqId)) {
                $contact->display_name = $uniqId;
            }
        }
        
        if (empty($contact->sort_name)) {
            if (isset($email)) {
                $contact->sort_name = $email;
            } else if (isset($uniqId)) {
                $contact->sort_name = $uniqId;
            }
        }
        
        if ( $date = CRM_Utils_Array::value('birth_date', $params) ) {
            if (is_array($date)) {
                $contact->birth_date = CRM_Utils_Date::format( $date );
            } else {
                $contact->birth_date = preg_replace('/[^0-9]/', '', $date);
            }
        } else if ( $contact->birth_date ) {
            $contact->birth_date = CRM_Utils_Date::isoToMysql( $contact->birth_date );
        }
        
        if ( $date = CRM_Utils_Array::value('deceased_date', $params) ) {
            if (is_array($date)) {
                $contact->deceased_date = CRM_Utils_Date::format( $date );
            } else {
                $contact->deceased_date = preg_replace('/[^0-9]/', '', $date);
            }
        } else if ( $contact->deceased_date ) {
            $contact->deceased_date = CRM_Utils_Date::isoToMysql( $contact->deceased_date );
        }
        if ( $middle_name = CRM_Utils_Array::value('middle_name', $params)) {
            $contact->middle_name = $middle_name;
        }
        // hack to make db_do save a null value to a field
        if ( ! $contact->birth_date ) {
            $contact->birth_date = 'NULL';
        }

        if (!array_key_exists('is_deceased', $params) && !$contact->is_deceased) {
            $contact->is_deceased = 0;
        }

        return $contact;
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contact_BAO_Contact|null the found object or null
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids ) {
        $individual =& new CRM_Contact_BAO_Individual( );
        
        $individual->copyValues( $params );
        if ( $individual->find(true) ) {
            $ids['individual'] = $individual->id;
            CRM_Core_DAO::storeValues( $individual, $values );

            if ( isset( $individual->birth_date ) ) {
                $values['birth_date'] = CRM_Utils_Date::unformat( $individual->birth_date );
            }

            CRM_Contact_DAO_Individual::addDisplayEnums($values);
            return $individual;

        }
        return null;
    }

    /**
     * regenerates display_name for contacts with given prefixes/suffixes
     *
     * @param array $ids     the array with the prefix/suffix id governing which contacts to regenerate
     * @param int   $action  the action describing whether prefix/suffix was UPDATED or DELETED
     *
     * @return void
     */
    static function updateDisplayNames(&$ids, $action) {
        
        // get the proper field name (prefix_id or suffix_id) and its value
        $fieldName = '';
        foreach ($ids as $key => $value) {
            switch ($key) {
            case 'individualPrefix':
                $fieldName = 'prefix_id';
                $fieldValue = $value;
                break 2;
            case 'individualSuffix':
                $fieldName = 'suffix_id';
                $fieldValue = $value;
                break 2;
            }
        }
        if ($fieldName == '') return;

        // query for the affected individuals
        $fieldValue = CRM_Utils_Type::escape($fieldValue, 'Integer');
        $contact =& new CRM_Contact_BAO_Contact( );
        $contact->$fieldName = $fieldValue;
        $contact->find();

        // iterate through the affected individuals and rebuild their display_names
        require_once 'CRM/Contact/BAO/Contact.php';
        while ($contact->fetch()) {
            $contact =& new CRM_Contact_BAO_Contact();
            $contact->id = $contact->contact_id;
            if ($action == CRM_Core_Action::DELETE) {
                $contact->$fieldName = 'NULL';
                $contact->save();
            }
            $contact->display_name = $contact->displayName();
            $contact->save();
        }
    }

    /**
     * creates display name
     *
     * @return string  the constructed display name
     */
    function displayName()
    {
        $prefix =& CRM_Core_PseudoConstant::individualPrefix();
        $suffix =& CRM_Core_PseudoConstant::individualSuffix();
        return str_replace('  ', ' ', trim($prefix[$this->prefix_id] . ' ' . $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name . ' ' . $suffix[$this->suffix_id]));
    }

    /** 
     * Check if there is data to create the object 
     * 
     * @param array  $params         (reference ) an assoc array of name/value pairs 
     * 
     * @return boolean 
     * @access public 
     * @static 
     */ 
    static function dataExists( &$params ) 
    {
        if ( $params['contact_type'] == 'Individual' ) {
            return true; 
        } 

        return false;
    }

}

?>
