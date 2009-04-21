<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

class CRM_Report_Form extends CRM_Core_Form {

    /**
     * The id of the report instance
     *
     * @var integer
     */
    protected $_id;

    /**
     * The id of the report template
     *
     * @var integer;
     */
    protected $_templateID;

    /**
     * The report title
     *
     * @var string
     */
    protected $_title;

    /**
     * The set of all columns in the report. An associative array
     * with column name as the key and attribues as the value
     *
     * @var array
     */
    protected $_columns;

    /**
     * The set of filters in the report
     *
     * @var array
     */
    protected $_filters = array( );

    /**
     * 
     */
    function __construct( ) {
        parent::__construct( );
    }

    function preProcess( ) {
        foreach ( $this->_columns as $tableName => $table ) {
            // add alias
            if ( ! isset( $table['alias'] ) ) {
                $this->_columns[$tableName]['alias'] = substr( $tableName, 8 );
            }

            $this->_aliases[$tableName] = $this->_columns[$tableName]['alias'];

            // get export fields
            require_once str_replace( '_', DIRECTORY_SEPARATOR, $table['dao'] .'.php' );
            eval( "\$impFields = {$table['dao']}::export( );");

            foreach ( $impFields as $fieldName => $field ) {
                // prepare columns
                if ( array_key_exists($fieldName, $this->_columns[$tableName]['fields']) ) {
                    if ( empty($this->_columns[$tableName]['fields'][$fieldName]) ) {
                        $this->_columns[$tableName]['fields'][$fieldName] = $impFields[$fieldName];
                    } else {
                        foreach ( $field as $property => $val ) {
                            if ( ! array_key_exists($property, $this->_columns[$tableName]['fields'][$fieldName]) ) {
                                $this->_columns[$tableName]['fields'][$fieldName][$property]  = $val;
                            }
                        }
                    }
                }

                // prepare filters
                if ( is_array($this->_columns[$tableName]['filters']) && 
                     array_key_exists($fieldName, $this->_columns[$tableName]['filters']) ) {
                    if ( empty($this->_columns[$tableName]['filters'][$fieldName]) ) {
                        $this->_columns[$tableName]['filters'][$fieldName] = $impFields[$fieldName];
                    } else {
                        foreach ( $field as $property => $val ) {
                            if ( ! array_key_exists($property, $this->_columns[$tableName]['filters'][$fieldName]) ) {
                                $this->_columns[$tableName]['filters'][$fieldName][$property] = $val;
                            }
                        }
                    }
                    $this->_columns[$tableName]['filters'][$fieldName]['name'] = "{$this->_aliases[$tableName]}.{$fieldName}";
                }
            }
            
            // copy filters to a separate handy variable
            if ( array_key_exists('filters', $table) ) {
                $this->_filters[$tableName] = $this->_columns[$tableName]['filters'];
            }
        }
    }


    function addColumns( ) {
        $options = array();
        
        foreach ( $this->_columns as $tableName => $table ) {
            require_once str_replace( '_', DIRECTORY_SEPARATOR, $table['dao'] .'.php' );
            eval( "\$impFields = {$table['dao']}::import( );");
            foreach ( $table['fields'] as $fieldName => $field ) {
                $options[$field['title']] = $fieldName;
            } 
        }
        $this->addCheckBox( 'select_columns', ts('Select Columns'), $options, null, 
                            null, null, null, array('</td><td>', '</td><td>', 
                                                    '</td><td>', '</tr><tr><td>') );
    }

    function addFilters( ) {
        $options = $filterFields = array();

        foreach ( $this->_filters as $table => $fieldNames ) {
            foreach ( $fieldNames as $fieldName => $properties ) {
                $filterFields[$properties['title']] = $fieldName;
                
                // get ready with option value pair
                $operations = self::getOperationPair( $fltrProperties['type'] );
                
                // build form elements based on types
                switch ( $fltrProperties['type'] ) {
                case 'integer':
                default:
                    // default type is string
                    $this->addElement('select', "{$fieldName}_op", ts( 'Operator:' ), $operations, 
                                      array('onchange' =>"return showHideMaxMinVal( '$fieldName', this.value );"));
                    
                    break;
                }
                
                // we need text box for value input
                $this->add( 'text', "{$fieldName}_value", ts('Value') );
                
                // and a min value input box
                $this->add( 'text', "{$fieldName}_min", ts('Min') );
                
                // and a max value input box
                $this->add( 'text', "{$fieldName}_max", ts('Max') );
            }
        }

        $this->assign( 'filterFields', $filterFields );
    }

    function buildQuickForm( ) {
        $this->addColumns( );

        $this->addFilters( );
      
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Next'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    static function getOperationPair( $type = "string" ) {
        // FIXME: At some point we should move these key-val pairs 
        // to option_group and option_value table.

        switch ( $type ) {
        case 'money':
        case 'integer':
            return array( 'lt'  => 'Is less than', 
                          'lte' => 'Is less than or equal to', 
                          'eq'  => 'Is equal to', 
                          'neq' => 'Is not equal to', 
                          'gt'  => 'Is greater than',
                          'gte' => 'Is greater than or equal to',
                          'bw'  => 'Is between',
                          'nbw' => 'Is not between',
                          );
            break;

        case 'date':
        default:
            // type is string
            return array( 'like' => 'Is equal to', 
                          'neq'  => 'Is not equal to', 
                          'has'  => 'Contains', 
                          'sw'   => 'Starts with', 
                          'ew'   => 'Ends with',
                          'nhas' => 'Does not contain', 
                          );
        }
    }

    static function getSQLOperator( $operator = "like" ) {
        switch ( $operator ) {
        case 'eq':
            return "=";
        case 'lt':
            return "<"; 
        case 'lte':
            return "<="; 
        case 'gt':
            return ">"; 
        case 'gte':
            return ">="; 
        case 'ne':
            return "!=";
        default:
            // type is string
            return "LIKE";
        }
    }

    static function whereClause( &$field, $op,
                                 $value, $min, $max ) {
        $clause = null;
        switch ( $op ) {
        case 'bw':
            if ( $min !== null &&
                 strlen( $min ) > 0 &&
                 $max !== null &&
                 strlen( $max ) > 0 ) {
                $min = CRM_Utils_Type::escape( $min, $field['type'] );
                $max = CRM_Utils_Type::escape( $max, $field['type'] );
                $clause = "( ( {$field['name']} >= $min ) AND ( {$field['name']} <= $max ) )";
            }
            break;

        default:
            if ( $value !== null &&
                 strlen( $value ) > 0 ) {
                $value  = CRM_Utils_Type::escape( $value, $field['type'] );
                $sqlOP  = self::getSQLOperator( $op );
                if ( $field['type'] == 'String' ) {
                    if ( $sqlOP == 'LIKE' &&
                         strpos( '%', $value ) === false ) {
                        $value = "'%{$value}%'";
                    } else {
                        $value = "'{$value}'";
                    }
                }

                $clause = "( {$field['name']} $sqlOP $value )";
            }
            break;
        }
        
        return $clause;
    }

    static function dateClause( &$field,
                                $relative, $from, $to ) {

        if ( $relative ) {
            require_once 'CRM/Utils/Date.php';
            list( $term, $unit ) = explode( '.', $relative );
            $dateRange = CRM_Utils_Date::relativeToAbsolute( $term, $unit );
            $from = $dateRange['from'];
            $to   = $dateRange['to'];
        }

        $clauses = array( );
        if ( ! empty( $from ) ) {
            $revDate = array_reverse( $from );
            $date    = CRM_Utils_Date::format( $revDate );
            if ( $date ) {
                $clauses[] = "( {$field['name']} >= $date )";
            }
        }

        if ( ! empty( $to ) ) {
            $revDate = array_reverse( $to );
            $date    = CRM_Utils_Date::format( $revDate );
            $clauses[] = "( {$field['name']} >= $to )";
        }

        if ( ! empty( $clauses ) ) {
            return implode( ' AND ', $clauses );
        }

        return null;
    }

}
