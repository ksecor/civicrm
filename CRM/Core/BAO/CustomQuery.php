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
 | at http://www.openngo.org/faqs/licensing.html                      | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Social Source Foundation (c) 2005 
 * $Id$ 
 * 
 */ 

class CRM_Core_BAO_CustomQuery {

    const PREFIX = 'custom_value_';

    /**
     * the default set of return properties
     *
     * @var array
     * @static
     */
    protected $_ids;

    public $_select;
    public $_element;
    public $_tables;
    public $_where;
    public $_qill;

    protected $_fields;

    function __construct( $ids ) {
        $this->_ids    =& $ids;

        $this->_select  = array( ); 
        $this->_element = array( ); 
        $this->_tables  = array( ); 
        $this->_where   = array( );
        $this->_qill    = array( );

        $this->_fields  = array( );

        if ( empty( $this->_ids ) ) {
            return;
        }

        // initialize the field array
        $tmpArray = array_keys( $this->_ids );
        $query = 'select * from civicrm_custom_field where is_active = 1 AND id IN ( ' .
            implode( ',', $tmpArray ) . ' ) ';
        $dao =& CRM_Core_DAO::executeQuery( $query ); 
        while ( $dao->fetch( ) ) {
            $this->_fields[$dao->id] = array( 'id'        => $dao->id,
                                              'label'     => $dao->label,
                                              'extends'   => 'civicrm_contact',
                                              'data_type' => $dao->data_type,
                                              'html_type' => $dao->html_type,
                                              'db_field'  => CRM_Core_BAO_CustomValue::typeToField( $dao->data_type ) ); 
        }
    }
    
    function select( ) {
        if ( empty( $this->_fields ) ) {
            return;
        }

        foreach ( $this->_fields as $id => $field ) {
            $name = self::PREFIX . $field['id'];
            $fieldName = 'custom_' . $field['id'];
            $this->_select["{$name}_id"]  = "{$name}.id as {$name}_id";
            $this->_element["{$name}_id"] = 1;
            $this->_select[$fieldName]    = $name . '.' . $field['db_field'] . " as $fieldName";
            $this->_element[$fieldName]   = 1;
            $this->_tables[$name] = "\nLEFT JOIN civicrm_custom_value $name ON $name.custom_field_id = " . $field['id'] .
                " AND $name.entity_table = 'civicrm_contact' AND $name.entity_id = civicrm_contact.id ";
        }

    }

    function where( ) {
        // CRM_Core_Error::debug( 'fld', $this->_fields );
        // CRM_Core_Error::debug( 'ids', $this->_ids );
        foreach ( $this->_ids as $id => $value ) {

            if ( $value === null || ! CRM_Utils_Array::value( $id, $this->_fields ) ) {
                continue;
            }

            $field = $this->_fields[$id];
            switch ( $field['data_type'] ) {

            case 'String':
                $sql = self::PREFIX . $field['id'] . '.char_data LIKE ';
                // if we are coming in from listings, for checkboxes the value is already in the right format and is NOT an array 
                if ( $field['html_type'] == 'CheckBox' && is_array( $value ) ) { 
                    $this->_where[] = $sql . '"' . implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys( $value ) ) . '"';
                } else {
                    $this->_where[] = $sql . "'%" . $value . "%'";
                } 
                $this->_qill[] = $field['label'] . " like - $value";
                continue;
                
            case 'Int':
                $this->_where[] = self::PREFIX . $field['id'] . '.int_data = ' . $value;
                $this->_qill[]  = $field['label'] . " - $value";
                continue;
                
            case 'Boolean':
                $value = (int ) $value;
                // note that to avoid QF's issue with value 0 (and setting the default)
                // we make boolean 2 as the value of NO
                $value = ( $value == 1 ) ? 1 : 0;
                $this->_where[] = self::PREFIX . $field['id'] . '.int_data = ' . $value;
                $value = $value ? ts('Yes') : ts('No');
                $this->_qill[]  = $field['label'] . " - $value";
                continue;

            case 'Float':
                $this->_where[] = self::PREFIX . $field['id'] . '.float_data = ' . $value;  
                $this->_qill[]  = $field['label'] . " - $value";
                continue;                    
                
            case 'Money':
                $this->_where[] = self::PREFIX . $field['id'] . '.decimal_data = ' . $value;
                $this->_qill[]  = $field['label'] . " - $value";
                continue;
                
            case 'Memo':
                $this->_where[] = self::PREFIX . $field['id'] . '.memo_data LIKE ' . "'%" . $value . "%'";
                $this->_qill[]  = $field['label'] . " like - $value";
                continue;
                
            case 'Date':
                $fromValue = CRM_Utils_Array::value( 'from', $value );
                $toValue   = CRM_Utils_Array::value( 'to'  , $value );
                if ( ! $fromValue && ! $toValue ) {
                    $date = CRM_Utils_Date::format( $value );
                    if ( ! $date ) { 
                        continue; 
                    } 
                    
                    $this->_where[] = self::PREFIX . $field['id'] . ".date_data = $date";
                    $date = CRM_Utils_Date::format( $value, '-' ); 
                    $this->_qill[]  = $field['label'] . ' = ' . 
                        CRM_Utils_Date::customFormat( $date ); 
                } else {
                    $fromDate = CRM_Utils_Date::format( $fromValue );
                    $toDate   = CRM_Utils_Date::format( $toValue   );
                    if ( ! $fromDate && ! $toDate ) {
                        continue;
                    }
                    if ( $fromDate ) {
                        $this->_where[] = self::PREFIX . $field['id'] . ".date_data >= $fromDate";
                        $fromDate = CRM_Utils_Date::format( $fromValue, '-' );
                        $this->_qill[]  = $field['label'] . ' >= ' .
                            CRM_Utils_Date::customFormat( $fromDate );
                    }
                    if ( $toDate ) {
                        $this->_where[] = self::PREFIX . $field['id'] . ".date_data <= $toDate";
                        $toDate = CRM_Utils_Date::format( $toValue, '-' );
                        $this->_qill[]  = $field['label'] . ' <= ' .
                            CRM_Utils_Date::customFormat( $toDate );
                    }
                }
                continue;
                
            case 'StateProvince':
                $states =& CRM_Core_PseudoConstant::stateProvince();
                if ( ! is_numeric( $value ) ) {
                    $value  = array_search( $value, $states );
                }
                if ( $value ) {
                    $this->_where[] = self::PREFIX . $field['id'] . '.int_data = ' . $value;
                    $this->_qill[]  = $field['label'] . " - {$states[$value]}";
                }
                continue;
                
            case 'Country':
                $countries =& CRM_Core_PseudoConstant::countries();
                if ( ! is_numeric( $value ) ) {
                    $value  = array_search( $value, $countries );
                }
                if ( $value ) {
                    $this->_where[] = self::PREFIX . $field['id'] . '.int_data = ' . $value;
                    $this->_qill[]  = $field['label'] . " - {$countries[$value]}";
                }
                continue;
            }
        }
        // CRM_Core_Error::debug( 'w', $this->_where );
    }

    function query( ) {
        $this->select( );

        $this->where( );

        return array( implode( ' , '  , $this->_select ),
                      implode( ' '    , $this->_tables ),
                      implode( ' AND ', $this->_where  ) );
    }

}

?>