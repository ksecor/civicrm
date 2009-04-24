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
     * The set of optional columns in the report
     *
     * @var array
     */
    protected $_options = array( );

    /**
     * The set of optional columns in the report
     *
     * @var array
     */
    protected $_fourColumnAttribute = array('</td><td width="25%">', '</td><td width="25%">', 
                                            '</td><td width="25%">', '</tr><tr><td>');

    protected $_force = 1;

    protected $_instanceForm = false;

    /**
     * 
     */
    function __construct( ) {
        parent::__construct( );
    }

    function preProcess( ) {
        
        $this->_force = CRM_Utils_Request::retrieve( 'force',
                                                     'Boolean',
                                                     CRM_Core_DAO::$_nullObject );

        // lets display the 
        $this->_instanceForm = $this->_force || ( ! empty( $_POST ) );

        foreach ( $this->_columns as $tableName => $table ) {
            // set alias
            if ( ! isset( $table['alias'] ) ) {
                $this->_columns[$tableName]['alias'] = substr( $tableName, 8 );
            }
            $this->_aliases[$tableName] = $this->_columns[$tableName]['alias'];

            // higher preference to bao object
            if ( array_key_exists('bao', $table) ) {
                require_once str_replace( '_', DIRECTORY_SEPARATOR, $table['bao'] . '.php' );
                eval( "\$expFields = {$table['bao']}::exportableFields( );");
            } else {
                require_once str_replace( '_', DIRECTORY_SEPARATOR, $table['dao'] . '.php' );
                eval( "\$expFields = {$table['dao']}::export( );");
            }

            // prepare columns
            foreach ( $table['fields'] as $fieldName => $field ) {
                if ( array_key_exists($fieldName, $expFields) ) {
                    if ( empty($field) ) {
                        $this->_columns[$tableName]['fields'][$fieldName] = $expFields[$fieldName];
                    } else {
                        foreach ( $expFields[$fieldName] as $property => $val ) {
                            if ( ! array_key_exists($property, $field) ) {
                                $this->_columns[$tableName]['fields'][$fieldName][$property] = $val;
                            }
                        }
                    }
                    $this->_columns[$tableName]['fields'][$fieldName]['dbAlias'] = 
                        $this->_columns[$tableName]['alias'] . '.' . $expFields[$fieldName]['name'];
                }
            }

            // prepare filters
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    if ( array_key_exists($fieldName, $expFields) ) {
                        if ( empty($field) ) {
                            $this->_columns[$tableName]['filters'][$fieldName] = $expFields[$fieldName];
                        } else {
                            foreach ( $expFields[$fieldName] as $property => $val ) {
                                if ( ! array_key_exists($property, $field) ) {
                                    $this->_columns[$tableName]['filters'][$fieldName][$property] = $val;
                                }
                            }
                        }
                        $this->_columns[$tableName]['filters'][$fieldName]['dbAlias'] = 
                            $this->_columns[$tableName]['alias'] . '.' . $expFields[$fieldName]['name'];
                    }
                }
            }
            
            // prepare group_bys
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( array_key_exists($fieldName, $expFields) ) {
                        if ( empty($field) ) {
                            $this->_columns[$tableName]['group_bys'][$fieldName] = $expFields[$fieldName];
                        } else {
                            foreach ( $expFields[$fieldName] as $property => $val ) {
                                if ( ! array_key_exists($property, $field) ) {
                                    $this->_columns[$tableName]['group_bys'][$fieldName][$property] = $val;
                                }
                            }
                        }
                        $this->_columns[$tableName]['group_bys'][$fieldName]['dbAlias'] = 
                            $this->_columns[$tableName]['alias'] . '.' . $expFields[$fieldName]['name'];
                    }
                }
            }

            // copy filters to a separate handy variable
            if ( array_key_exists('filters', $table) ) {
                $this->_filters[$tableName] = $this->_columns[$tableName]['filters'];
            }
        }
        
        if ( $this->_force ) {
            $this->postProcess( );
        }

    }

    function setDefaultValues( ) {
        $defaults = array();

        foreach ( $this->_columns as $tableName => $table ) {
            foreach ( $table['fields'] as $fieldName => $field ) {
                if ( isset($field['required']) ) {
                    if ( isset($table['grouping']) ) { 
                        $group = $table['grouping'];
                    } else {
                        $group = $tableName;
                    }
                    // set default
                    $defaults['select_columns'][$group][$fieldName] = 1;

                    // find element object, so that we could use quickform's freeze method 
                    // for required elements
                    $obj = $this->getElementFromGroup("select_columns[$group]", 
                                                      $fieldName);
                    if ( $obj ) {
                        $freezeGroup[] = $obj;
                    }
                }
            }
        }

        // lets finish freezing task here itself
        foreach ( $freezeGroup as $elem ) {
            $elem->freeze();
        }

        if ( $this->_instanceForm ) {
            CRM_Report_Form_Instance::setDefaultValues( $this, $defaults );
        }
        
        return $defaults;
    }

    function getElementFromGroup( $group, $grpFieldName ) {
        $eleObj = $this->getElement( $group );
        foreach ( $eleObj->_elements as $index => $obj ) {
            if ( $grpFieldName == $obj->_attributes['name']) {
                return $obj;
            }
        }
        return false;
    }

    function addColumns( ) {
        $options = array();
        
        foreach ( $this->_columns as $tableName => $table ) {
            foreach ( $table['fields'] as $fieldName => $field ) {
                if ( isset($table['grouping']) ) { 
                    $options[$table['grouping']][$field['title']] = $fieldName;
                } else {
                    $options[$tableName][$field['title']] = $fieldName;
                }
            } 
        }
        
        $colGroups = array( );
        foreach ( $options as $grp => $grpOptions ) {
            $this->addCheckBox( "select_columns[$grp]", ts('Select Columns'), $grpOptions, null, 
                                null, null, null, $this->_fourColumnAttribute );
            $colGroups[] = $grp;
        }
        $this->assign( 'colGroups', $colGroups );
    }

    function addFilters( ) {
        require_once 'CRM/Utils/Date.php';
        require_once 'CRM/Core/Form/Date.php';
        $options = array();

        foreach ( $this->_filters as $table => $attributes ) {
            foreach ( $attributes as $fieldName => $field ) {
                // get ready with option value pair
                $operations = self::getOperationPair( $field['type'] );
                
                switch ( $field['type'] ) {
                case CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME :
                case CRM_Utils_Type::T_DATE :
                    // build datetime fields
                    CRM_Core_Form_Date::buildDateRange( $this, $fieldName );
                    break;

                default:
                    // default type is string
                    $this->addElement('select', "{$fieldName}_op", ts( 'Operator:' ), $operations,
                                      array('onchange' =>"return showHideMaxMinVal( '$fieldName', this.value );"));
                    
                    // we need text box for value input
                    $this->add( 'text', "{$fieldName}_value", ts('Value') );
                    
                    // and a min value input box
                    $this->add( 'text', "{$fieldName}_min", ts('Min') );
                    
                    // and a max value input box
                    $this->add( 'text', "{$fieldName}_max", ts('Max') );
                    break;
                }
            }
        }
        $this->assign( 'filters', $this->_filters );
    }

    function addOptions( ) {
        if ( !empty( $this->_options ) ) {
            // FIXME: For now lets build all elements as checkboxes. 
            // Once we clear with the format we can build elements based on type

            foreach ( $this->_options as $fieldName => $field ) {
                $options = array( $field['title'] => $fieldName );
            }
            $this->addCheckBox( "options", $field['title'], $options, null, 
                                null, null, null, $this->_fourColumnAttribute );
            $this->assign( 'options', $this->_options );
        }
    }

    function addGroupBys( ) {
        $options = $freqOptions = array( );

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( !empty($field) ) {
                        $options[$field['title']] = $fieldName;
                    }
                }
            }
            if ( array_key_exists('group_bys_freq', $table) ) {
                $freqOptions = $table['group_bys_freq'];
            }
        }

        $this->addCheckBox( "group_bys", ts('Group by columns'), $options, null, 
                            null, null, null, $this->_fourColumnAttribute );
        
        if ( ! empty($freqOptions) ) {
            $this->addElement( 'select', "group_bys_freq", 
                               ts( 'Frequency' ), $freqOptions );
        }
    }

    function buildQuickForm( ) {
        $this->addColumns( );

        $this->addFilters( );
      
        $this->addOptions( );

        $this->addGroupBys( );

        if ( $this->_instanceForm ) {
            require_once 'CRM/Report/Form/Instance.php';
            CRM_Report_Form_Instance::buildForm( $this );

            $this->_instanceButtonName = $this->getButtonName( 'submit', 'save' );

            $this->addButtons( array(
                                     array ( 'type'      => 'submit',
                                             'name'      => ts('Generate Report'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'submit',
                                             'name'      => ts('Save Report'),
                                             'subName'   => 'save' ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );

            $this->assign( 'instanceForm', true );
        } else {
            $this->addButtons( array(
                                     array ( 'type'      => 'submit',
                                             'name'      => ts('Generate Report'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
        }
    }

    static function getOperationPair( $type = "string" ) {
        // FIXME: At some point we should move these key-val pairs 
        // to option_group and option_value table.

        switch ( $type ) {
        case CRM_Utils_Type::T_INT :
        case CRM_Utils_Type::T_MONEY :
            return array( 'lte' => 'Is less than or equal to', 
                          'gte' => 'Is greater than or equal to',
                          'bw'  => 'Is between',
                          'eq'  => 'Is equal to', 
                          'lt'  => 'Is less than', 
                          'gt'  => 'Is greater than',
                          'neq' => 'Is not equal to', 
                          'nbw' => 'Is not between',
                          );
            break;

        default:
            // type is string
            return array('has'  => 'Contains', 
                         'sw'   => 'Starts with', 
                         'ew'   => 'Ends with',
                         'nhas' => 'Does not contain', 
                         'like' => 'Is equal to', 
                         'neq'  => 'Is not equal to', 
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
                $min = CRM_Utils_Type::escape( $min,
                                               CRM_Utils_Type::typeToString( $field['type'] ) );
                $max = CRM_Utils_Type::escape( $max,
                                               CRM_Utils_Type::typeToString( $field['type'] ) );
                $clause = "( ( {$field['name']} >= $min ) AND ( {$field['name']} <= $max ) )";
            }
            break;

        default:
            if ( $value !== null &&
                 strlen( $value ) > 0 ) {
                $value  = CRM_Utils_Type::escape( $value,
                                                  CRM_Utils_Type::typeToString( $field['type'] ) );
                $sqlOP  = self::getSQLOperator( $op );
                if ( $field['type'] == CRM_Utils_Type::T_STRING ) {
                    if ( $sqlOP == 'LIKE' &&
                         strpos( $value, '%' ) === false ) {
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

        require_once 'CRM/Utils/Date.php';
        if ( $relative ) {
            list( $term, $unit ) = explode( '.', $relative );
            $dateRange = CRM_Utils_Date::relativeToAbsolute( $term, $unit );
            $from = $dateRange['from'];
            $to   = $dateRange['to'];
        }

        $clauses = array( );
        if ( CRM_Utils_Date::isDate( $from ) ) {
            $revDate = array_reverse( $from );
            $date    = CRM_Utils_Date::format( $revDate );
            if ( $date ) {
                $clauses[] = "( {$field['name']} >= $date )";
            }
        }

        if ( CRM_Utils_Date::isDate( $to ) ) {
            $revDate = array_reverse( $to );
            $date    = CRM_Utils_Date::format( $revDate );
            $clauses[] = "( {$field['name']} <= $date )";
        }

        if ( ! empty( $clauses ) ) {
            return implode( ' AND ', $clauses );
        }

        return null;
    }

    function postProcess( ) {
        if ( $this->_instanceForm &&
             CRM_Utils_Array::value( 'title', $this->_params ) ) {
            CRM_Report_Form_Instance::postProcess( );
        }
    }

}