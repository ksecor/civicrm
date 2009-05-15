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

    protected $_defaults = array( );

    /**
     * Set of statistic fields
     *
     * @var array
     */
    protected $_statFields = array();

    /**
     * Set of statistics data
     *
     * @var array
     */
    protected $_statistics = array();

    /**
     * List of fields not to be repeated during display
     *
     * @var array
     */
    protected $_noRepeats  = array();

    /**
     * List of fields not to be displayed
     *
     * @var array
     */
    protected $_noDisplay  = array();

    /**
     * An attribute for checkbox/radio form field layout
     *
     * @var array
     */
    protected $_fourColumnAttribute = array('</td><td width="25%">', '</td><td width="25%">', 
                                            '</td><td width="25%">', '</tr><tr><td>');

    protected $_force = 1;

    protected $_params         = null;
    protected $_formValues     = null;
    protected $_instanceValues = null;

    protected $_instanceForm   = false;

    protected $_instanceButtonName = null;
    protected $_printButtonName    = null;
    protected $_pdfButtonName      = null;

    protected $_rollup         = null;

    /**
     * To what frequency group-by a date column
     *
     * @var array
     */
    protected $_groupByDateFreq = array( 'MONTH'    => 'Month',
                                         ''         => '-select-',
                                         'YEARWEEK' => 'Week',
                                         'QUARTER'  => 'Quarter',
                                         'YEAR'     => 'Year'  );
    
    /**
     * 
     */
    function __construct( ) {
        parent::__construct( );
    }

    function preProcessCommon( ) {
        $this->_force = CRM_Utils_Request::retrieve( 'force',
                                                     'Boolean',
                                                     CRM_Core_DAO::$_nullObject );

        $this->_id    = CRM_Utils_Request::retrieve( 'id', 'Integer', $this );

        if ( $this->_id ) {
            $params = array( 'id' => $this->_id );
            $this->_instanceValues = array( );
            CRM_Core_DAO::commonRetrieve( 'CRM_Report_DAO_Instance',
                                          $params,
                                          $this->_instanceValues );
            if ( empty($this->_instanceValues) ) {
                CRM_Core_Error::fatal("report criteria could not be loaded.");
            }
            $this->_formValues = unserialize( $this->_instanceValues['form_values'] );

            // lets always do a force if a valid id is found in the url.
            $this->_force      = 1;
        }


        // lets display the 
        $this->_instanceForm       = $this->_force || $this->_id || ( ! empty( $_POST ) );

        $this->_instanceButtonName = $this->getButtonName( 'submit', 'save'  );
        $this->_printButtonName    = $this->getButtonName( 'submit', 'print' );
        $this->_pdfButtonName      = $this->getButtonName( 'submit', 'pdf'   );
    }

    function preProcess( ) {
        self::preProcessCommon( );

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

            $doNotCopy   = array('required');

            $fieldGroups = array('fields', 'filters', 'group_bys', 'order_bys');
            foreach ( $fieldGroups as $fieldGrp ) {
                if ( is_array( $table[$fieldGrp] ) ) {
                    foreach ( $table[$fieldGrp] as $fieldName => $field ) {
                        if ( array_key_exists($fieldName, $expFields) ) {
                            foreach ( $doNotCopy as $dnc ) {
                                // unset the values we don't want to be copied.
                                unset($expFields[$fieldName][$dnc]);
                            }
                            if ( empty($field) ) {
                                $this->_columns[$tableName][$fieldGrp][$fieldName] = $expFields[$fieldName];
                            } else {
                                foreach ( $expFields[$fieldName] as $property => $val ) {
                                    if ( ! array_key_exists($property, $field) ) {
                                        $this->_columns[$tableName][$fieldGrp][$fieldName][$property] = $val;
                                    }
                                }
                            }

                            // fill other vars
                            if ( CRM_Utils_Array::value( 'no_repeat', $field ) ) {
                                $this->_noRepeats[] = "{$tableName}_{$fieldName}";
                            }
                            if ( CRM_Utils_Array::value( 'no_display', $field ) ) {
                                $this->_noDisplay[] = "{$tableName}_{$fieldName}";
                            }
                        }

                        $alias = isset($field['alias']) ? $field['alias'] : 
                            ( isset($this->_columns[$tableName]['alias']) ? 
                              $this->_columns[$tableName]['alias'] : $tableName );
                        $this->_columns[$tableName][$fieldGrp][$fieldName]['alias'] = $alias;

                        if ( !isset($this->_columns[$tableName][$fieldGrp][$fieldName]['name']) ) {
                            $this->_columns[$tableName][$fieldGrp][$fieldName]['name'] = $fieldName;
                        }

                        $this->_columns[$tableName][$fieldGrp][$fieldName]['dbAlias'] = 
                            $alias . '.' . $this->_columns[$tableName][$fieldGrp][$fieldName]['name'];
                    }
                }
            }

            // copy filters to a separate handy variable
            if ( array_key_exists('filters', $table) ) {
                $this->_filters[$tableName] = $this->_columns[$tableName]['filters'];
            }

            if ( array_key_exists('group_bys', $table) ) {
                $groupBys[$tableName] = $this->_columns[$tableName]['group_bys'];
            }
        }

        if ( $this->_force ) {
            $this->setDefaultValues( false );
        }

        require_once 'CRM/Report/Utils/Get.php';
        CRM_Report_Utils_Get::processFilter( $this->_filters,
                                             $this->_defaults );
        CRM_Report_Utils_Get::processGroupBy( $groupBys,
                                              $this->_defaults );

        if ( $this->_force ) {
            $this->_formValues = $this->_defaults;
            $this->postProcess( );
        }

    }

    function setDefaultValues( $freeze = true ) {
        $freezeGroup = array();

        // FIXME: generalizing form field naming conventions would reduce 
        // lots of lines below.
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( !array_key_exists('no_display', $field) ) {
                        if ( isset($field['required']) ) {
                            // set default
                            $this->_defaults['fields'][$fieldName] = 1;
                            
                            if ( $freeze ) {
                                // find element object, so that we could use quickform's freeze method 
                                // for required elements
                                $obj = $this->getElementFromGroup("fields",
                                                                  $fieldName);
                                if ( $obj ) {
                                    $freezeGroup[] = $obj;
                                }
                            }
                        } else if ( isset($field['default']) ) {
                            $this->_defaults['fields'][$fieldName] = $field['default'];
                        }
                    }
                }
            }

            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( isset($field['default']) ) {
                        if ( CRM_Utils_Array::value('frequency', $field) ) {
                            $this->_defaults['group_bys_freq'][$fieldName] = 'MONTH';
                        }
                        $this->_defaults['group_bys'][$fieldName] = $field['default'];
                    }
                }
            }
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    if ( isset($field['default']) ) {
                        if ( $field['type'] & CRM_Utils_Type::T_DATE ) {
                            $this->_defaults["{$fieldName}_relative"] = $field['default'];
                        } else {
                            $this->_defaults[$fieldName] = $field['default'];
                        }
                    }
                }
            }

            foreach ( $this->_options as $fieldName => $field ) {
                if ( isset($field['default']) ) {
                    $this->_defaults['options'][$fieldName] = $field['default'];
                }
            }
        }

        // lets finish freezing task here itself
        if ( !empty($freezeGroup) ) {
            foreach ( $freezeGroup as $elem ) {
                $elem->freeze();
            }
        }

        if ( $this->_formValues ) {
            $this->_defaults = array_merge( $this->_defaults, $this->_formValues );
        }

        if ( $this->_instanceValues ) {
            $this->_defaults = array_merge( $this->_defaults, $this->_instanceValues );
        }

        if ( $this->_instanceForm ) {
            require_once 'CRM/Report/Form/Instance.php';
            CRM_Report_Form_Instance::setDefaultValues( $this, $this->_defaults );
        }
        
        return $this->_defaults;
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
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( !array_key_exists('no_display', $field) ) {
                        if ( isset($table['grouping']) ) { 
                            $colGroups[$table['grouping']][$field['title']] = $fieldName;
                        } else {
                            $colGroups[$tableName][$field['title']] = $fieldName;
                        }
                        $options[$field['title']] = $fieldName;
                    }
                } 
            }
        }
        
        $this->addCheckBox( "fields", ts('Select Columns'), $options, null, 
                            null, null, null, $this->_fourColumnAttribute );
        $this->assign( 'colGroups', $colGroups );
    }

    function addFilters( ) {
        require_once 'CRM/Utils/Date.php';
        require_once 'CRM/Core/Form/Date.php';
        $options = array();
        $count = 1;
        foreach ( $this->_filters as $table => $attributes ) {
            foreach ( $attributes as $fieldName => $field ) {
                // get ready with option value pair
                $operations = self::getOperationPair( $field['type'] );
                
                switch ( $field['type'] ) {
                case CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM :
                    // assume a multi-select field
                    $this->addElement('select', "{$fieldName}_op", ts( 'Operator:' ), $operations);
                    $select = $this->addElement('select', "{$fieldName}_value", null, $field['options']);
                    $select->setMultiple( true );
                    break;

                case CRM_Utils_Type::T_DATE :
                case CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME :
                    // build datetime fields
                    CRM_Core_Form_Date::buildDateRange( $this, $fieldName, $count );
                    $count++;
                    break;

                case CRM_Utils_Type::T_INT:
                case CRM_Utils_Type::T_MONEY:
                    // and a min value input box
                    $this->add( 'text', "{$fieldName}_min", ts('Min') );
                    // and a max value input box
                    $this->add( 'text', "{$fieldName}_max", ts('Max') );
                default:
                    // default type is string
                    $this->addElement('select', "{$fieldName}_op", ts( 'Operator:' ), $operations,
                                      array('onchange' =>"return showHideMaxMinVal( '$fieldName', this.value );"));
                    // we need text box for value input
                    $this->add( 'text', "{$fieldName}_value", ts('Value') );
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
            
            $options = array();
            foreach ( $this->_options as $fieldName => $field ) {
                $options[$field['title']] = $fieldName;
            }
            $this->addCheckBox( "options", $field['title'], 
                                $options, null, 
                                null, null, null, $this->_fourColumnAttribute );
        }
    }

    function addChartOptions( ) {
        if ( !empty( $this->_charts ) ) {
            $this->addElement( 'select', "charts", ts( 'Chart' ), $this->_charts );
            $this->assign( 'charts', $this->_charts );
        }
    }
    
    function addGroupBys( ) {
        $options = $freqElements = array( );

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( !empty($field) ) {
                        $options[$field['title']] = $fieldName;
                        if ( $field['frequency'] ) {
                            $freqElements[$field['title']] = $fieldName;
                        }
                    }
                }
            }
        }
        $this->addCheckBox( "group_bys", ts('Group by columns'), $options, null, 
                            null, null, null, $this->_fourColumnAttribute );
        $this->assign( 'groupByElements', $options );

        foreach ( $freqElements as $name ) {
            $this->addElement( 'select', "group_bys_freq[$name]", 
                               ts( 'Frequency' ), $this->_groupByDateFreq );
        }
    }

    function buildInstanceAndButtons( ) {
        if ( $this->_instanceForm ) {
            require_once 'CRM/Report/Form/Instance.php';
            CRM_Report_Form_Instance::buildForm( $this );
            
            $this->addElement('submit', $this->_instanceButtonName, ts( 'Save Report' ) );
            $this->addElement('submit', $this->_printButtonName, ts( 'Print Report' ) );
            $this->addElement('submit', $this->_pdfButtonName, ts( 'Print PDF' ) );

            $this->assign( 'instanceForm', true );
        }
        $this->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Generate Report'),
                                         'isDefault' => true   ),
                                 )
                           );
    }

    function buildQuickForm( ) {
        $this->addColumns( );

        $this->addFilters( );
      
        $this->addOptions( );

        $this->addChartOptions( );

        $this->addGroupBys( );

        $this->buildInstanceAndButtons( );

        //add form rule for report
        if ( is_callable( array( $this, 'formRule' ) ) ) {
            $this->addFormRule( array( get_class($this), 'formRule' ), $this );
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
        case CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM :
            return array( 'in'  => 'Is one of' );
            break;
        default:
            // type is string
            return array('has'  => 'Contains', 
                         'sw'   => 'Starts with', 
                         'ew'   => 'Ends with',
                         'nhas' => 'Does not contain', 
                         'eq'   => 'Is equal to', 
                         'neq'  => 'Is not equal to', 
                         );
        }
    }

    static function getSQLOperator( $operator = "like" ) {
        switch ( $operator ) {
        case 'eq':
            return '=';
        case 'lt':
            return '<'; 
        case 'lte':
            return '<='; 
        case 'gt':
            return '>'; 
        case 'gte':
            return '>='; 
        case 'ne' :
        case 'neq':
            return '!=';
        case 'nhas':
            return 'NOT LIKE';
        case 'in':
            return 'IN';
        default:
            // type is string
            return 'LIKE';
        }
    }

    static function whereClause( &$field, $op,
                                 $value, $min, $max ) {

        $type   = CRM_Utils_Type::typeToString( $field['type'] );
        $clause = null;

        switch ( $op ) {
        case 'bw':
        case 'nbw':
            if ( ( $min !== null && strlen( $min ) > 0 ) ||
                 ( $max !== null && strlen( $max ) > 0 ) ) {
                $min = CRM_Utils_Type::escape( $min, $type );
                $max = CRM_Utils_Type::escape( $max, $type );
                $clauses = array( );
                if ( $min ) {
                    if ( $op == 'bw' ) {
                        $clauses[] = "( {$field['dbAlias']} >= $min )";
                    } else {
                        $clauses[] = "( {$field['dbAlias']} < $min )";
                    }
                }
                if ( $max ) {
                    if ( $op == 'bw' ) {
                        $clauses[] = "( {$field['dbAlias']} <= $max )";
                    } else {
                        $clauses[] = "( {$field['dbAlias']} > $max )";
                    }
                }

                if ( ! empty( $clauses ) ) {
                    if ( $op == 'bw' ) {
                        $clause = implode( ' AND ', $clauses );
                    } else {
                        $clause = implode( ' OR ', $clauses );
                    }
                }
            }
            break;

        case 'has':
        case 'nhas': 
            if ( $value !== null && strlen( $value ) > 0 ) {
                $value  = CRM_Utils_Type::escape( $value, $type );
                if ( strpos( $value, '%' ) === false ) {
                    $value = "'%{$value}%'";
                } else {
                    $value = "'{$value}'";
                }
                $sqlOP  = self::getSQLOperator( $op );
                $clause = "( {$field['dbAlias']} $sqlOP $value )";
            }
            break;
                
        case 'in':
            if ( $value !== null && count( $value ) > 0 ) {
                $sqlOP  = self::getSQLOperator( $op );
                $clause = "( {$field['dbAlias']} $sqlOP (" . implode( ', ', $value ) . ") )";
            }
            break;

        case 'sw':
        case 'ew':
            if ( $value !== null && strlen( $value ) > 0 ) {
                $value  = CRM_Utils_Type::escape( $value, $type );
                if ( strpos( $value, '%' ) === false ) {
                    if ( $op == 'sw' ) {
                        $value = "'{$value}%'";
                    } else {
                        $value = "'%{$value}'";
                    }
                } else {
                    $value = "'{$value}'";
                }
                $sqlOP  = self::getSQLOperator( $op );
                $clause = "( {$field['dbAlias']} $sqlOP $value )";
            }
            break;
                
        default:
            if ( $value !== null && strlen( $value ) > 0 ) {
                $value  = CRM_Utils_Type::escape( $value, $type );
                $sqlOP  = self::getSQLOperator( $op );
                if ( $field['type'] == CRM_Utils_Type::T_STRING ) {
                    $value = "'{$value}'";
                }
                $clause = "( {$field['dbAlias']} $sqlOP $value )";
            }
            break;
        }
        
        return $clause;
    }

    static function dateClause( $fieldName,
                                $relative, $from, $to ) {
        $clauses         = array( );
        list($from, $to) = self::getFromTo($relative, $from, $to);

        if ( $from ) {
            $clauses[] = "( {$fieldName} >= $from )";
        }

        if ( $to ) {
            $clauses[] = "( {$fieldName} <= $to )";
        }

        if ( ! empty( $clauses ) ) {
            return implode( ' AND ', $clauses );
        }

        return null;
    }

    static function dateDisplay( $relative, $from, $to ) {
        list($from, $to) = self::getFromTo($relative, $from, $to);

        if ( $from ) {
            $clauses[] = CRM_Utils_Date::customFormat($from, null, array('m', 'M'));
        } else {
            $clauses[] = 'Past';
        }

        if ( $to ) {
            $clauses[] = CRM_Utils_Date::customFormat($to, null, array('m', 'M'));
        } else {
            $clauses[] = 'Today';
        }

        if ( ! empty( $clauses ) ) {
            return implode( ' - ', $clauses );
        }

        return null;
    }

    static function getFromTo( $relative, $from, $to ) {
        require_once 'CRM/Utils/Date.php';
        if ( $relative ) {
            list( $term, $unit ) = explode( '.', $relative );
            $dateRange = CRM_Utils_Date::relativeToAbsolute( $term, $unit );
            $from = $dateRange['from'];
            $to   = $dateRange['to'];
        }

        if ( CRM_Utils_Date::isDate( $from ) ) {
            $revDate = array_reverse( $from );
            $from    = CRM_Utils_Date::format( $revDate );
        } else {
            $from    = null;
        }

        if ( CRM_Utils_Date::isDate( $to ) ) {
            $revDate = array_reverse( $to );
            $to      = CRM_Utils_Date::format( $revDate );
        } else {
            $to      = null;
        }

        return array($from, $to);
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
    }

    function removeDuplicates( &$rows ) {
        if ( empty($this->_noRepeats) ) {
            return;
        }
        $checkList = array();

        foreach ( $rows as $key => $list ) {
            foreach ( $list as $colName => $colVal ) {
                if ( is_array($checkList[$colName]) && 
                     in_array($colVal, $checkList[$colName]) ) {
                    $rows[$key][$colName] = "";
                }
                if ( in_array($colName, $this->_noRepeats) ) {
                    $checkList[$colName][] = $colVal;
                }
            }
        }
    }

    function fixSubTotalDisplay( &$row, $fields, $subtotal = true ) {
        foreach ( $row as $colName => $colVal ) {
            if ( in_array($colName, $fields) ) {
                $row[$colName] = 
                    "<strong>{$row[$colName]}</strong>";
            } else if ( isset($this->_columnHeaders[$colName]) ) {
                if ( $subtotal ) {
                    $row[$colName] = "Sub Total";
                    $subtotal = false;
                } else {
                    unset($row[$colName]);
                }
            }
        }
    }

    function grandTotal( &$rows ) {
        if ( !$this->_rollup ) {
            return false;
        }
        $lastRow = array_pop($rows);

        $grandFlag = false;
        foreach ($this->_columnHeaders as $fld => $val) {
            if ( !in_array($fld, $this->_statFields) ) {
                if ( !$grandFlag ) {
                    $lastRow[$fld] = "Grand Total";
                    $grandFlag = true;
                } else{
                    $lastRow[$fld] = "";
                }
            }
        }

        $this->assign( 'grandStat', $lastRow );
        return true;
    }

    function formatDisplay( &$rows ) {
        // unset columns not to be displayed.
        foreach ( $this->_columnHeaders as $key => $value ) {
            if ( is_array($value) && isset($value['no_display']) ) {
                unset($this->_columnHeaders[$key]);
            }
        }

        // unset columns not to be displayed.
        if ( !empty($rows) ) {
            foreach ( $this->_noDisplay as $noDisplayField ) {
                foreach ( $rows as $rowNum => $row ) {
                    unset($this->_columnHeaders[$noDisplayField]);
                }
            }
        }

        // process grand-total row
        $this->grandTotal( $rows );

        // this takes care of formatting rows for display purpose.
        $this->alterDisplay( $rows );
    }

    function processReportMode( ) {
        $buttonName = $this->controller->getButtonName( );

        $output = CRM_Utils_Request::retrieve( 'output',
                                               'String', CRM_Core_DAO::$_nullObject );

        $this->assign( 'printOnly', false );
        if ( $this->_printButtonName == $buttonName || $output == 'print' ) {
            $this->assign( 'printOnly', true );
            $this->_reportMode = 'print';
        } else if ( $this->_pdfButtonName   == $buttonName || $output == 'pdf' ) {
            $this->assign( 'printOnly', true );
            $this->_reportMode = 'pdf';
        } else {
            $this->_reportMode = 'html';
        }

    }

    function postProcess( ) {
        if ( $this->_reportMode == 'print' || $this->_reportMode == 'pdf' ) {
            $templateFile = parent::getTemplateFileName( );

            $content =
                $this->_formValues['report_header'] .
                CRM_Core_Form::$_template->fetch( $templateFile ) .
                $this->_formValues['report_footer'] ;
            
            if ( $this->_reportMode == 'print' ) {
                echo $content;
            } else {
                require_once 'CRM/Utils/PDF/Utils.php';
                CRM_Utils_PDF_Utils::html2pdf( $content, "CiviReport.pdf" );
            }
            exit( );
        } else if ( $this->_instanceButtonName == $this->controller->getButtonName( ) ) {
            require_once 'CRM/Report/Form/Instance.php';
            CRM_Report_Form_Instance::postProcess( $this );
        }
    }

    function limit( ) {
        // lets do the pager if in html mode
        $this->_limit = null;
        if ( $this->_reportMode == 'html' ) {
            require_once 'CRM/Utils/Pager.php';
            $sql    = "SELECT count(*) {$this->_from} {$this->_where}";
            $count  = CRM_Core_DAO::singleValueQuery( $sql );
            $params = array( 'total'    => CRM_Core_DAO::singleValueQuery( $sql ),
                             'rowCount' => 50,
                             'status'   => ts( 'Contributions %%StatusMessage%%' ) );
            $pager = new CRM_Utils_Pager( $params );
            $this->assign_by_ref( 'pager', $pager );
            
            list( $offset, $rowCount ) = $pager->getOffsetAndRowCount( );
            if ( $offset >= 0 && $rowCount >= 0 ) {
                $this->_limit = " LIMIT $offset, $rowCount ";
            }
        }
    }

}
