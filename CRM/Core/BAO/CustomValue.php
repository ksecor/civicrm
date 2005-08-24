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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/CustomValue.php';


/**
 * Business objects for managing custom data values.
 *
 */
class CRM_Core_BAO_CustomValue extends CRM_Core_DAO_CustomValue {

    /**
     * Validate a value against a CustomField type
     *
     * @param string $type  The type of the data
     * @param string $value The data to be validated
     * 
     * @return boolean True if the value is of the specified type
     * @access public
     * @static
     */
    public static function typecheck($type, $value) {
        switch($type) {
            case 'Memo':
                return true;

            case 'String':
                return CRM_Utils_Rule::string($value);

            case 'Int':
                return CRM_Utils_Rule::integer($value);

            case 'Float':
            case 'Money':
                return CRM_Utils_Rule::numeric($value);

            case 'Date':
                return CRM_Utils_Rule::date($value);

            case 'Boolean':
                return CRM_Utils_Rule::boolean($value);

            case 'StateProvince':
                return
                    in_array($value,
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation())
                    || in_array($value, CRM_Core_PseudoConstant::stateProvince());

            case 'Country':
                return
                    in_array($value, CRM_Core_PseudoConstant::countryIsoCode())
                    || in_array($value, CRM_Core_PseudoConstant::country());
        }
        return false;
    }



    /**
     * Create a new CustomValue record
     *
     * @param array $params  The values for the new record
     *
     * @return object  The new BAO
     * @access public
     * @static
     */
    public static function create(&$params) {
        $customValue =& new CRM_Core_BAO_CustomValue();

        $customValue->copyValues($params);
        
        switch($params['type']) {
            case 'StateProvince':
                $states =& CRM_Core_PseudoConstant::stateProvince();
                $customValue->int_data = 
                    CRM_Utils_Array::key($params['value'], $states);
                $customValue->char_data = $params['value'];
                break;
            case 'Country':
                $countries =& CRM_Core_PseudoConstant::country();
                $customValue->int_data = 
                    CRM_Utils_Array::key($params['value'], $countries);
                $customValue->char_data = $params['value'];
                break;
            case 'String':
                $customValue->char_data = $params['value'];
                break;
            case 'Boolean':
                $customValue->int_data = 
                CRM_Utils_String::strtobool($params['value']);
                break;
            case 'Int':
                $customValue->int_data = $params['value'];
                break;

            case 'Float':
                $customValue->float_data = $params['value'];
                break;

            case 'Money':
                $customValue->decimal_data = number_format( $params['value'], 2, '.', '' );
                break;

            case 'Memo':
                $customValue->memo_data = $params['value'];
                break;
            case 'Date':
                $customValue->date_data = $params['value'];
                break;
        }
        $customValue->save();
        
        return $customValue;
    }

    /**
     * given a 'civicrm' type string, return the mysql data store area
     *
     * @param string $type the civicrm type string
     *
     * @return the mysql data store placeholder
     * @access public
     * @static
     */
    public static function typeToField($type) {
        switch ($type) {
            case 'String':
            case 'StateProvince':
            case 'Country':
                return 'char_data';
            case 'Boolean':
            case 'Int':
                return 'int_data';
            case 'Float':
                return 'float_data';
            case 'Money':
                return 'decimal_data';
            case 'Memo':
                return 'memo_data';
            case 'Date':
                return 'date_data';
            default:
                return null;
        }
    }

    /**
     * return the mysql type of the current value.
     * If boolean type, set the isBool flag too (since int and bool share
     * the same mysql type, we need another differentiator
     *
     * @param boolean $isBool (reference ) set to true if boolean     
     * 
     * @return string the mysql type
     * @access public
     */
    public function getField( &$isBool, $cf = null) {
        if ($cf == null) {
            $cf =& new CRM_Core_BAO_CustomField();
            $cf->id = $this->custom_field_id;

            if (! $cf->find(true)) {
                return null;
            }
        }

        $isBool = $cf->data_type == 'Boolean' ? true : false;

        return $this->typeToField($cf->data_type);
    }

    /**
     * returns the string value of the curren object
     *
     * @param boolean $translateBoolean should a boolean value be translated to yes/no
     *
     * @return string the value
     * @access public
     */
    public function getValue($translateBoolean = false) {
        $field = $this->getField($var1);
        
        if ($translateBoolean && $cf->data_type == 'Boolean') {
            return $this->$field ? 'yes' : 'no';
        }

        if ( $cf->data_type == 'Money' ) {
            return number_format( $this->$field, 3, '.', '' );
        }

        return $this->$field;
    }

    /**
     * Find all the custom values for a given contact.
     *
     * @param int $contactId  the id of the contact
     * @return array $values  Array of CustomValue objects
     * @access public
     * @static
     */
    public static function getContactValues($contactId) {
        $customValue =& new CRM_Core_BAO_CustomValue();

        $customValue->entity_id = $contactId;
        $customValue->entity_table = 'civicrm_contact';

        $customValue->find();
        $values = array();

        while ($customValue->fetch()) {
            $values[] = $customValue;
        }
        return $values;
    }

    /**
     * update the custom calue for a given contact id and field id
     *
     * @param int    $contactId contact id
     * @param int    $cfId      custom field id
     * @param string $value     the value to set the field
     *
     * @return void
     * @access public
     * @static
     */
    public static function updateValue($contactId, $cfId, $value) {
        $customValue =& new CRM_Core_BAO_CustomValue();

        $customValue->custom_field_id = $cfId;
        $customValue->entity_table = 'civicrm_contact';
        $customValue->entity_id = $contactId;
        
        $customValue->find(true);

        $cf =& new CRM_Core_BAO_CustomField();
        $cf->id = $cfId;
        $cf->find(true);

        if ($cf->data_type == 'StateProvince') {
            $states =& CRM_Core_PseudoConstant::stateProvince();
            if (CRM_Utils_Rule::integer($value)) {
                $customValue->int_data = $value;
                $customValue->char_data = 
                    CRM_Utils_Array::value($value, $states);
            } else {
                $customValue->int_data = 
                    CRM_Utils_Array::key($value, $states);
                $customValue->char_data = $value;
            }
        } elseif ($cf->data_type == 'Country') {
            $countries =& CRM_Core_PseudoConstant::country();
            if (CRM_Utils_Rule::integer($value)) {
                $customValue->int_data = $value;
                $customValue->char_data = 
                    CRM_Utils_Array::value($value, $countries);
            } else {
                $customValue->int_data = 
                    CRM_Utils_Array::key($value, $countries);
                $customValue->char_data = $value;
            }
        } else {
            $isBool = false;
            $field = $customValue->getField($isBool, $cf);
            if ($isBool) {
                $value = CRM_Utils_String::strtobool($value);
            }
            $customValue->$field = $value;
        }
        
        $customValue->save();
    }


    /**
     * Get the 'SELECT' query for getting contacts id's 
     * which match all the field_id => value parameters
     *
     * For example given the following parameter
     *       custom_field_1 => value1
     *       custom_field_2 => value2
     *
     * The function returns a select statement which will
     * return contact id's which have a value1 and value2
     * for custom_field_1 and custom_field_2
     *
     * @param array(ref)  $customField
     *
     * @return string $customValueSQL
     *
     * @access public
     * @static
     */
    public static function whereClause( &$params )
    {
        /*
        The query below works fine (using self joins)

SELECT t1.entity_id

FROM civicrm_custom_value t1,
     civicrm_custom_value t2,
     civicrm_custom_value t3
 
WHERE t1.custom_field_id = 1
  AND t2.custom_field_id = 2
  AND t3.custom_field_id = 5
 
  AND t1.int_data = 1
  AND t2.char_data LIKE '%Congress%'
  AND t3.char_data LIKE '%PhD%'
 
  AND t1.entity_id = t2.entity_id
  AND t1.entity_id = t3.entity_id;
        */


        // get number of tables needed
        if ( ! is_array( $params ) && empty( $params ) ) {
            return;
        }

        $select = ' SELECT t1.entity_id ';

        $where = array( );
        $from  = array( );
        $index = 1;
        foreach ( $params as $key => $value ) {
            $clause  = self::getFieldWhereClause( $key, $index, $value );
            if ( $clause ) { 
                $from[]  = "civicrm_custom_value t$index";
                $where[] = "t$index.custom_field_id = $key"; 
                $where[] = $clause;
                $index++;
            }
        }

        // add equality clause for table entities
        for ( $i = 2; $i < $index; $i++) {  
            $where[] = ' t1.entity_id = t' . $i . '.entity_id'; 
            $where[] = ' t1.entity_table = t' . $i . '.entity_table'; 
        }
        
        $from  = " FROM "  . implode( ', '   , $from  );
        $where = " WHERE " . implode( ' AND ', $where );

        return " civicrm_contact.id IN ( $select $from $where ) ";
    }

    static function getFieldWhereClause( $id, $index, $value ) {

        // retrieve the field object
        $cf =& new CRM_Core_DAO_CustomField( ); 
        $cf->id = $id;
        if ( $cf->find( true ) ) {
            switch ( $cf->data_type ) {
            case 'String':
                $sql = ' t' . $index . '.char_data LIKE ';
                if ( $cf->html_type == 'CheckBox' ) {
                    return $sql . '"' . implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys( $value ) ) . '"';
                } else {
                    return $sql . "'%" . $value . "%'";
                } 

            case 'Int':
                return ' t' . $index . '.int_data = ' . $value;
            case 'Boolean':
                $value = ( $value == 'yes' ) ? 1 : 0;
                return ' t' . $index . '.int_data = ' . $value; 

            case 'Float':
                return ' t' . $index . '.float_data = ' . $value;  

            case 'Money':
                return ' t' . $index . '.decimal_data = ' . $value;

            case 'Memo':
                return ' t' . $index . '.memo_data LIKE ' . "'%" . $value . "%'";

            case 'Date':
                return null;

            case 'StateProvince': 
                return ' t' . $index . '.int_data = ' . $value;  

            case 'Country':
                return ' t' . $index . '.int_data = ' . $value;  
            }
        }
        return null;
    }

}

?>
