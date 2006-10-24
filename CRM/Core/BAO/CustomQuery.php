<?php 

/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.6                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright CiviCRM LLC (c) 2004-2006                                  | 
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
 | http://www.civicrm.org/licensing/                                 | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@civicrm.org> 
 * @copyright CiviCRM LLC (c) 2004-2006 
 * $Id$ 
 * 
 */ 

class CRM_Core_BAO_CustomQuery {

    const PREFIX = 'custom_value_';

    /**
     * the set of custom field ids
     *
     * @var array
     */
    protected $_ids;

    /**
     * the select clause
     *
     * @var array
     */
    public $_select;

    /**
     * the name of the elements that are in the select clause
     * used to extract the values
     *
     * @var array
     */
    public $_element;

    /** 
     * the tables involved in the query
     * 
     * @var array 
     */ 
    public $_tables;
    public $_whereTables;

    /** 
     * the where clause 
     * 
     * @var array 
     */ 
    public $_where;

    /**
     * The english language version of the query
     *  
     * @var array  
     */  
    public $_qill;

    /**
     * The cache to translate the option values into labels
     *   
     * @var array   
     */ 
    public $_options;

    /**
     * The custom fields information
     *    
     * @var array    
     */ 
    protected $_fields;

    static $extendsMap = array(
                               'Contact'      => 'civicrm_contact',
                               'Individual'   => 'civicrm_contact',
                               'Household'    => 'civicrm_contact',
                               'Organization' => 'civicrm_contact',
                               'Contribution' => 'civicrm_contribution',
                               );

    /**
     * class constructor
     *
     * Takes in a set of custom field ids andsets up the data structures to 
     * generate a query
     *
     * @param  array  $ids     the set of custom field ids
     *
     * @access public
     */
    function __construct( $ids ) {
        $this->_ids    =& $ids;

        $this->_select       = array( ); 
        $this->_element      = array( ); 
        $this->_tables       = array( ); 
        $this->_whereTables  = array( ); 
        $this->_where        = array( );
        $this->_qill         = array( );
        $this->_options      = array( );

        $this->_fields       = array( );

        if ( empty( $this->_ids ) ) {
            return;
        }

        // initialize the field array
        $tmpArray = array_keys( $this->_ids );
        $query = 'select * from civicrm_custom_field where is_active = 1 AND id IN ( ' .
            implode( ',', $tmpArray ) . ' ) ';
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $optionIds = array( );
        while ( $dao->fetch( ) ) {
            // get the group dao to figure which class this custom field extends
            $extends =& CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup', $dao->custom_group_id, 'extends' );
            $extendsTable = self::$extendsMap[$extends];
            $this->_fields[$dao->id] = array( 'id'              => $dao->id,
                                              'label'           => $dao->label,
                                              'extends'         => $extendsTable,
                                              'data_type'       => $dao->data_type,
                                              'html_type'       => $dao->html_type,
                                              'is_search_range' => $dao->is_search_range,
                                              'db_field'        => CRM_Core_BAO_CustomValue::typeToField( $dao->data_type ) ); 

            // store it in the options cache to make things easier
            // during option lookup
            $this->_options[$dao->id] = array( );
            $this->_options[$dao->id]['attributes'] = array( 'label'     => $dao->label,
                                                             'data_type' => $dao->data_type, 
                                                             'html_type' => $dao->html_type );
            if ( $dao->html_type == 'CheckBox' || $dao->html_type == 'Radio' || $dao->html_type == 'Select' || $dao->html_type == 'Multi-Select') {
                $optionIds[] = $dao->id;
            }
        }

        // build the cache for custom values with options (label => value)
        if ( ! empty( $optionIds ) ) {
            $query = 'select entity_id, label, value from civicrm_custom_option where entity_id IN ( ' .
                implode( ',', $optionIds ) . ' ) AND entity_table = \'civicrm_custom_field\''; 
            $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            while ( $dao->fetch( ) ) {
                $this->_options[$dao->entity_id][$dao->value] = $dao->label;
            }
        }
    }

    /**
     * generate the select clause and the associated tables
     * for the from clause
     *
     * @param  NULL 
     * @return void
     * @access public
     */   
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
            if ( $field['extends'] == 'civicrm_contact' ) {
                $this->_tables[$name] = "\nLEFT JOIN civicrm_custom_value $name ON $name.custom_field_id = " . $field['id'] .
                    " AND $name.entity_table = 'civicrm_contact' AND $name.entity_id = contact_a.id ";
                if ( $this->_ids[$id] ) {
                    $this->_whereTables[$name] = $this->_tables[$name];
                }
            } else if ( $field['extends'] == 'civicrm_contribution' ) {
                $this->_tables[$name] = "\nLEFT JOIN civicrm_custom_value $name ON $name.custom_field_id = " . $field['id'] .
                    " AND $name.entity_table = 'civicrm_contribution' AND $name.entity_id = civicrm_contribution.id ";
                $this->_tables['civicrm_contribution'] = 1;
                if ( $this->_ids[$id] ) {
                    $this->_whereTables['civicrm_contribution'] = 1;
                }
            }
        }

    }

    /**
     * generate the where clause and also the english language
     * equivalent
     * 
     * @param NULL
     * 
     * @return void
     * 
     * @access public
     */   
    function where( ) {
        //CRM_Core_Error::debug( 'fld', $this->_fields );
        //CRM_Core_Error::debug( 'ids', $this->_ids );

        foreach ( $this->_ids as $id => $values ) {

            // Fixed for Isuue CRM 607
            if ( CRM_Utils_Array::value( $id, $this->_fields ) === null ||
                 ! $values ) {
                continue;
            }

            foreach ( $values as $tuple ) {
                list( $name, $op, $value, $grouping, $wildcard ) = $tuple;
                
                // fix $value here to escape sql injection attacks
                $field = $this->_fields[$id];
                $qillValue = CRM_Core_BAO_CustomField::getDisplayValue( $value, $id, $this->_options );

                if ( ! is_array( $value ) ) {
                    $value = addslashes(trim($value));
                }

                switch ( $field['data_type'] ) {

                case 'String':
                    $sql = 'LOWER(' . self::PREFIX . $field['id'] . '.char_data) ';
                    // if we are coming in from listings, for checkboxes the value is already in the right format and is NOT an array 
                    if ( is_array( $value ) ) {
                        require_once 'CRM/Core/BAO/CustomOption.php';

                        //ignoring $op value for checkbox and multi select
                        $sqlValue = array( );
                        if ($field['html_type'] == 'CheckBox') {
                            foreach ( $value as $k => $v ) { 
                                $sqlValue[] = "( $sql like '%" . CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . $k . CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . "%' ) ";
                            }
                            $this->_where[$grouping][] = implode( ' AND ', $sqlValue );
                            $this->_qill[$grouping][]  = "$field[label] $op $qillValue";
                        } else { // for multi select
                            foreach ( $value as $k => $v ) { 
                                $sqlValue[] = "( $sql like '%" . CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . $v . CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . "%' ) ";
                            }
                            $this->_where[$grouping][] = implode( ' AND ', $sqlValue ); 
                            $this->_qill[$grouping][]  = "$field[label] $op $qillValue";
                        }                    
                    } else {
                        if ( $field['is_search_range'] && is_array( $value ) ) {
                            $this->searchRange( $field['id'], $field['label'], 'char_data', $value, $grouping );
                        } else {
                            $val = CRM_Utils_Type::escape( strtolower(trim($value)), 'String' );
                            $this->_where[$grouping][] = "{$sql} {$op} '{$val}'";
                            $this->_qill[$grouping][]  = "$field[label] $op $qillValue";
                        }
                    } 
                    continue;
                
                case 'Int':
                    if ( $field['is_search_range'] && is_array( $value ) ) {
                        $this->searchRange( $field['id'], $field['label'], 'int_data', $value, $grouping );
                    } else {
                        $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".int_data {$op} " . CRM_Utils_Type::escape( $value, 'Integer' );
                        $this->_qill[$grouping][]  = $field['label'] . " $op $value";
                    }
                    continue;
                
                case 'Boolean':
                    $value = (int ) $value;
                    $value = ( $value == 1 ) ? 1 : 0;
                    $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".int_data {$op} " . CRM_Utils_Type::escape( $value, 'Integer' );
                    $value = $value ? ts('Yes') : ts('No');
                    $this->_qill[$grouping][]  = $field['label'] . " {$op} {$value}";
                    continue;

                case 'Float':
                    if ( $field['is_search_range'] && is_array( $value ) ) {
                        $this->searchRange( $field['id'], $field['label'], 'float_data', $value, $grouping );
                    } else {                
                        $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".float_data {$op} " . CRM_Utils_Type::escape( $value, 'Float' );
                        $this->_qill[$grouping][]  = $field['label'] . " {$op} {$value}";
                    }
                    continue;                    
                
                case 'Money':
                    if ( $field['is_search_range'] && is_array( $value ) ) {
                        $this->searchRange( $field['id'], $field['label'], 'decimal_data', $value, $grouping );
                    } else {                
                        $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".decimal_data {$op} " . CRM_Utils_Type::escape( $value, 'Float' );
                        $this->_qill[$grouping][]  = $field['label'] . " {$op} {$value}";
                    }
                    continue;
                
                case 'Memo':
                    $val = CRM_Utils_Type::escape( strtolower(trim($value)), 'String' );
                    $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".memo_data {$op} '{$val}'";
                    $this->_qill[$grouping][] = "$field[label] $op $value";
                    continue;
                
                case 'Date':
                    $fromValue = CRM_Utils_Array::value( 'from', $value );
                    $toValue   = CRM_Utils_Array::value( 'to'  , $value );
                    if ( ! $fromValue && ! $toValue ) {
                        $date = CRM_Utils_Date::format( $value );
                        if ( ! $date ) { 
                            continue; 
                        } 
                    
                        $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".date_data {$op} {$date}";
                        $date = CRM_Utils_Date::format( $value, '-' ); 
                        $this->_qill[$grouping][]  = $field['label'] . " {$op} " . 
                            CRM_Utils_Date::customFormat( $date ); 
                    } else {
                        $fromDate = CRM_Utils_Date::format( $fromValue );
                        $toDate   = CRM_Utils_Date::format( $toValue   );
                        if ( ! $fromDate && ! $toDate ) {
                            continue;
                        }
                        if ( $fromDate ) {
                            $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".date_data >= $fromDate";
                            $fromDate = CRM_Utils_Date::format( $fromValue, '-' );
                            $this->_qill[$grouping][]  = $field['label'] . ' >= ' .
                                CRM_Utils_Date::customFormat( $fromDate );
                        }
                        if ( $toDate ) {
                            $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".date_data <= $toDate";
                            $toDate = CRM_Utils_Date::format( $toValue, '-' );
                            $this->_qill[$grouping][]  = $field['label'] . ' <= ' .
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
                        $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".int_data {$op} " . CRM_Utils_Type::escape( $value, 'Int' );
                        $this->_qill[$grouping][]  = $field['label'] . " {$op} {$states[$value]}";
                    }
                    continue;
                
                case 'Country':
                    $countries =& CRM_Core_PseudoConstant::country();
                    if ( ! is_numeric( $value ) ) {
                        $value  = array_search( $value, $countries );
                    }
                    if ( $value ) {
                        $this->_where[$grouping][] = self::PREFIX . $field['id'] . ".int_data {$op} " . CRM_Utils_Type::escape( $value, 'Int' );
                        $this->_qill[$grouping][]  = $field['label'] . " {$op} {$countries[$value]}";
                    }
                    continue;
                }
            
            }
            //CRM_Core_Error::debug( 'w', $this->_where );
        }
    }

    /**
     * function that does the actual query generation
     * basically ties all the above functions together
     *
     * @param NULL
     * @return  array   array of strings  
     * @access public
     */   
    function query( ) {
        $this->select( );

        $this->where( );
        
        // CRM_Core_Error::debug( 'cq', $this );
        return array( implode( ' , '  , $this->_select ),
                      implode( ' '    , $this->_tables ),
                      implode( ' AND ', $this->_where  ) );
    }

    function searchRange( &$id, &$label, $type, &$value, &$grouping ) {
        $qill = array( );
        $crmType = CRM_Core_BAO_CustomValue::fieldToType( $type );

        if ( isset( $value['from'] ) ) {
            $val = CRM_Utils_Type::escape( $value['from'], $crmType );

            if ( $type == 'char_data' ) {
                $this->_where[$grouping][] = self::PREFIX . "$id.$type >= '$val'";
            } else {
                $this->_where[$grouping][] = self::PREFIX . "$id.$type >= $val";
            }
            $qill[] = ts( 'greater than or equal to "%1"', array( 1 => $value['from'] ) );
        }

        if ( isset( $value['to'] ) ) {
            $val = CRM_Utils_Type::escape( $value['to'], $crmType );
            if ( $type == 'char_data' ) {
                $this->_where[$grouping][] = self::PREFIX . "$id.$type <= '$val'";
            } else {
                $this->_where[$grouping][] = self::PREFIX . "$id.$type <= $val";
            }
            $qill[] = ts( 'less than or equal to "%1"', array( 1 => $value['to'] ) );
        }

        if ( ! empty( $qill ) ) { 
            $this->_qill[$grouping][] = $label . ' - ' . implode( ' ' . ts('and') . ' ', $qill );
        }
        
    }

}

?>
