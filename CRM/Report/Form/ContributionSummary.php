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

require_once 'CRM/Report/Form.php';

class CRM_Report_Form_ContributionSummary extends CRM_Report_Form {

    protected $_summary = null;

    function __construct( ) {
        $this->_columns = array( 
                                 'civicrm_contribution' =>
                                 array( 'dao'     => 'CRM_Contribute_DAO_Contribution',
                                        'bao'     => 'CRM_Contribute_BAO_Contribution',
                                        'fields'  =>
                                        array( 'total_amount'  => 
                                               array( 'title'     => ts( 'Amount Statistics' ),
                                                      'default'   => true,
                                                      'statistics'=> array('sum'  => ts( 'Total Amount' ), 
                                                                           'count'=> ts( 'Count' ), 
                                                                           'avg'  => ts( 'Average' )) ),
                                               ),
                                        'filters'  =>             
                                        array( 'receive_date' => 
                                               array( 'default'    => 'this.month' ),
                                               'total_amount' => 
                                               array( 'title'      => ts( 'Total  Amount Between' ) ),
                                               ),
                                        'group_bys'=>             
                                        array( 'receive_date' => 
                                               array( 'default'    => true,
                                                      'frequency'  => true ),
                                               'contribution_contact_id' => 
                                               array( 'title'      => ts( 'Contacts' ) ),
                                               'contribution_source'     => null,
                                               //'contribution_type'       => null,
                                               //'contribution_page_id'    => null,
                                               ),
                                        ),
                                 );

        $this->_options = array( 'include_grand_total' => array( 'title'  => ts( 'Include Grand Totals' ),
                                                                 'type'   => 'checkbox',
                                                                 'default'=> true ),
                                 );
        
        parent::__construct( );
    }

    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Contribution Summary Report' ) );
        
        parent::preProcess( );
    }
    
    function setDefaultValues( ) {
        return parent::setDefaultValues( );
    }

    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            foreach ( $table['fields'] as $fieldName => $field ) {
                if ( CRM_Utils_Array::value( 'required', $field ) ||
                     CRM_Utils_Array::value( $fieldName, $this->_params['select_columns'][$table['grouping']] ) ||
                     CRM_Utils_Array::value( $fieldName, $this->_params['select_columns'][$tableName] ) ) {
                    
                    // only include statistics columns if set
                    if ( CRM_Utils_Array::value('statistics', $field) ) {
                        foreach ( $field['statistics'] as $stat => $label ) {
                            switch (strtolower($stat)) {
                            case 'sum':
                                $select[] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] = $field['type'];
                                $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                break;
                            case 'count':
                                $select[] = "COUNT({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                break;
                            case 'avg':
                                $select[] = "ROUND(AVG({$field['dbAlias']}),2) as {$tableName}_{$fieldName}_{$stat}";
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] =  $field['type'];
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                break;
                            }
                        }   

                    } else {
                        $select[] = "{$table['alias']}.{$fieldName} as {$tableName}_{$fieldName}";
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                    }
                }
            }

            foreach ( $table['group_bys'] as $fieldName => $field ) {
                if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                    switch ( $this->_params['group_bys_freq'][$fieldName] ) {
                    case 'YEARWEEK' :
                        $select[] = "DATE_SUB({$field['dbAlias']}, 
INTERVAL WEEKDAY({$field['dbAlias']}) DAY) AS {$tableName}_{$fieldName}_start";
                        $field['title'] = 'Week';
                        break;

                    case 'YEAR' :
                        $select[] = "MAKEDATE(YEAR({$field['dbAlias']}), 1)  
AS {$tableName}_{$fieldName}_start";
                        $field['title'] = 'Year';
                        break;

                    case 'MONTH':
                        $select[] = "DATE_SUB({$field['dbAlias']}, 
INTERVAL (DAYOFMONTH({$field['dbAlias']})-1) DAY) as {$tableName}_{$fieldName}_start";
                        $field['title'] = 'Month';
                        break;

                    case 'QUARTER':
                        $select[] = "STR_TO_DATE(CONCAT( 3 * QUARTER( {$field['dbAlias']} ) -2 , '/', '1', '/', YEAR( {$field['dbAlias']} ) ), '%m/%d/%Y') AS {$tableName}_{$fieldName}_start";
                        $field['title'] = 'Quarter';
                        break;

                    }
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys_freq'] ) ) {
                        $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['title'] = 
                            $field['title'] . ' Beginning';
                        $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['type']  = 
                            $field['type'];
                        $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['group_by'] = 
                            $this->_params['group_bys_freq'][$fieldName];
                    }
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( ) {
        $this->_from = "
FROM       civicrm_contribution {$this->_aliases['civicrm_contribution']}
";
    }

    function where( ) {
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    if ( $field['type'] & CRM_Utils_Type::T_DATE ) {
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );
                        
                        if ( $relative || $from || $to ) {
                            $clause = $this->dateClause( $field, $relative, $from, $to );
                        }
                    } else {
                        $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                        if ( $op ) {
                            $clause = 
                                $this->whereClause( $field,
                                                    $op,
                                                    CRM_Utils_Array::value( "{$fieldName}_value", $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_min", $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_max", $this->_params ) );
                        }
                    }
                    
                    if ( ! empty( $clause ) ) {
                        $clauses[] = $clause;
                    }
                }
            }
        }

        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ( 1 ) ";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $clauses );
        }
    }

    function grandTotal( &$rows ) {
        if ( !empty($this->_statFields) && 
             CRM_Utils_Array::value( 'include_grand_total', $this->_params['options'] ) ) {
            $grandStat = array();
            $grandStat[] = array_pop($rows);
            
            foreach ($grandStat[0] as $fld => $val) {
                if ( !in_array($fld, $this->_statFields) ) {
                    $grandStat[0][$fld] = "";
                }
            }
            return $grandStat;
        }
        return false;
    }

    function statistics( &$rows ) {
        $statistics = array();

        $statistics[] = array( 'title' => ts('Row(s) Listed'),
                               'value' => count($rows) );

        if ( ! empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                        $combinations[] = $field['title'];
                    }
                }
            }
            $statistics[] = array( 'title' => ts('Grouping(s)'),
                                   'value' => implode( ' & ', $combinations ) );
        }
        

        return $statistics;
    }

    function groupBy( ) {
        $this->_groupBy = "";
        if ( ! empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                        if ( CRM_Utils_Array::value('frequency', $table['group_bys'][$fieldName]) && 
                             CRM_Utils_Array::value($fieldName, $this->_params['group_bys_freq']) ) {
                            $this->_groupBy[] = 
                                $this->_params['group_bys_freq'][$fieldName] . "({$field['dbAlias']})";
                        } else {
                            $this->_groupBy[] = $field['dbAlias'];
                        }
                    }
                }
            }
            $rollUP = "";
            if ( !empty($this->_statFields) && 
                 CRM_Utils_Array::value( 'include_grand_total', $this->_params['options'] ) ) {
                $rollUP = "WITH ROLLUP";
            }
            $this->_groupBy = "GROUP BY " . implode( ', ', $this->_groupBy ) . " $rollUP ";
        }
    }

    function postProcess( ) {
        if ( $this->_force ) {
            $this->_params = $this->_formValues;
        } else {
            $this->_params = $this->controller->exportValues( $this->_name );
        }
        $this->_formValues = $this->_params ;

        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";

        $dao  = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row[$key] = $dao->$key;

            }
            $rows[] = $row;
        }
        $this->assign( 'grandStat', $this->grandTotal( $rows ) );

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );

        $this->assign( 'statistics', $this->statistics( $rows ) );

        parent::postProcess( );
    }

}
