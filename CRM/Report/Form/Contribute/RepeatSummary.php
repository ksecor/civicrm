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

class CRM_Report_Form_Contribute_RepeatSummary extends CRM_Report_Form {

    protected $_summary = null;

    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'fields'        =>
                          array( 'total_amount'        => 
                                 array( 'title'        => ts( 'Amount Statistics' ),
                                        'default'      => true,
                                        'required'     => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              //'avg'    => ts( 'Average' ), 
                                              ), ), ),
                          'filters'       =>             
                          array( 
                                'receive_date1'  => 
                                array( 'title'   => ts( 'Date Range One' ),
                                       'default' => 'previous.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'name'    => 'receive_date' ),
                                'receive_date2'  => 
                                array( 'title'   => ts( 'Date Range Two' ),
                                       'default' => 'this.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'name'    => 'receive_date' ), ),
                          'group_bys'           =>
                          array( 'receive_date' => 
                                 array( 'default'    => true,
                                        'frequency'  => true ), ), ),

                   'civicrm_address' =>
                   array( 'dao' => 'CRM_Core_DAO_Address',
                          'alias'  => 'addr',
                          'fields' =>
                          array( 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ) ),
                                 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ) ), ),
                          'group_bys' =>
                          array( 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ) ), ),
                          ),
                   );

        parent::__construct( );
    }

    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Contribution Repeat Summary Report' ) );
        
        parent::preProcess( );
    }
    
    function setDefaultValues( ) {
        return parent::setDefaultValues( );
    }

    function select( $alias = 'c1' ) {
        $select = $uni = array( );

/*         if ( $alias == 'c1' ) { */
/*             $ele = "receive_date1"; */
/*         } else if ( $alias == 'c2' ) { */
/*             $ele = "receive_date2"; */
/*         } */
/*         $relative = CRM_Utils_Array::value( "{$ele}_relative", $this->_params ); */
/*         $from     = CRM_Utils_Array::value( "{$ele}_from"    , $this->_params ); */
/*         $to       = CRM_Utils_Array::value( "{$ele}_to"      , $this->_params ); */
/*         $clause   = $this->dateDisplay( $relative, $from, $to ); */
/*         $this->_columnHeaders["{$alias}_amount"] =  */
/*             array('title' => "Amount ($clause)", */
/*                   'type'  => CRM_Utils_Type::T_MONEY); */

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        
                        // do alias over-riding.
                        if ( $field['alias'] == 'contribution' ) {
                            $field['alias'] = $alias;
                        }

                        // only include statistics columns if set
                        if ( CRM_Utils_Array::value('statistics', $field) ) {
                            foreach ( $field['statistics'] as $stat => $label ) {
                                switch (strtolower($stat)) {
                                case 'sum':
                                    $select[] = "SUM({$field['alias']}.{$field['name']}) as {$field['alias']}_{$field['name']}_{$stat}";
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['title']= 
                                        $label;
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['type'] = 
                                        $field['type'];
                                    $this->_statFields[] = "{$field['alias']}_{$field['name']}_{$stat}";
                                    break;
                                case 'count':
                                    $select[] = "COUNT({$field['alias']}.{$field['name']}) as {$field['alias']}_{$field['name']}_{$stat}";
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['title']= 
                                        $label;
                                    $this->_statFields[] = "{$field['alias']}_{$field['name']}_{$stat}";
                                    break;
                                case 'avg':
                                    $select[] = "ROUND(AVG({$field['alias']}.{$field['name']}),2) as {$field['alias']}_{$field['name']}_{$stat}";
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['type'] =  
                                        $field['type'];
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['title']= 
                                        $label;
                                    $this->_statFields[] = "{$field['alias']}_{$field['name']}_{$stat}";
                                    break;
                                }
                            }   
                        } else {
                            $select[] = "{$field['alias']}.{$field['name']} as {$field['alias']}_{$field['name']}";
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['type'] = $field['type'];
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['title'] = $field['title'];
                        }
                    }
                }

                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {

                        // do alias over-riding.
                        if ( $field['alias'] == 'contribution' ) {
                            $field['alias'] = $alias;
                        }

                        switch ( $this->_params['group_bys_freq'][$fieldName] ) {
                        case 'YEARWEEK' :
                            $select[] = "DATE_SUB({$field['alias']}.{$field['name']}, 
INTERVAL WEEKDAY({$field['alias']}.{$field['name']}) DAY) AS start";
                            $uni[] = "YEAR({$field['alias']}.{$field['name']}), YEARWEEK({$field['alias']}.{$field['name']})";
                            $field['title'] = 'Week';
                            break;
                            
                        case 'YEAR' :
                            $select[] = "MAKEDATE(YEAR({$field['alias']}.{$field['name']}), 1)  
AS start";
                            $uni[] = "YEAR({$field['alias']}.{$field['name']})";
                            $field['title'] = 'Year';
                            break;
                            
                        case 'MONTH':
                            $select[] = "DATE_SUB({$field['alias']}.{$field['name']}, 
INTERVAL (DAYOFMONTH({$field['alias']}.{$field['name']})-1) DAY) as start";
                            $uni[] = "YEAR({$field['alias']}.{$field['name']}), MONTH({$field['alias']}.{$field['name']})";
                            $field['title'] = 'Month';
                            break;
                            
                        case 'QUARTER':
                            $select[] = "STR_TO_DATE(CONCAT( 3 * QUARTER( {$field['alias']}.{$field['name']} ) -2 , '/', '1', '/', YEAR( {$field['alias']}.{$field['name']} ) ), '%m/%d/%Y') AS start";
                            $uni[] = "YEAR({$field['alias']}.{$field['name']}), QUARTER({$field['alias']}.{$field['name']})";
                            $field['title'] = 'Quarter';
                            break;
                            
                        }
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys_freq'] ) ) {
                            $this->_columnHeaders["start"]['title'] = $field['title'] . ' Beginning';
                            $this->_columnHeaders["start"]['type']  = $field['type'];
                            $this->_columnHeaders["start"]['group_by'] = 
                                $this->_params['group_bys_freq'][$fieldName];

                        } else {
                            $uni[]  = "{$field['alias']}.{$field['name']}";
                        }
                    }
                }
            }
        }

        if ( count($uni) >=1 ) {
            $select[] = "CONCAT({$append}" . implode( ', ', $uni ) . ") AS uni";
            $this->_columnHeaders["uni"] = array('no_display' => true);
        }
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function groupBy( $alias = 'c1' ) {
        $this->_groupBy = "";
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {

                            // do alias over-riding.
                            if ( $field['alias'] == 'contribution' ) {
                                $field['alias'] = $alias;
                            }

                            if ( CRM_Utils_Array::value('frequency', $table['group_bys'][$fieldName]) && 
                                 CRM_Utils_Array::value($fieldName, $this->_params['group_bys_freq']) ) {
                                
                                $append = "YEAR({$field['alias']}.{$field['name']}),";
                                if ( in_array(strtolower($this->_params['group_bys_freq'][$fieldName]), 
                                              array('year')) ) {
                                    $append = '';
                                }
                                $this->_groupBy[] = "$append {$this->_params['group_bys_freq'][$fieldName]}({$field['alias']}.{$field['name']})";
                                $append = true;
                            } else {
                                $this->_groupBy[] = "{$field['alias']}.{$field['name']}";
                            }
                        }
                    }
                }
            }
            
            $rollUP = "";
            if ( !empty($this->_statFields) && 
                 CRM_Utils_Array::value( 'include_grand_total', $this->_params['options'] ) && 
                 ( $append && count($this->_groupBy) <= 1 ) ) {
                $rollUP = " WITH ROLLUP";
            }
            $this->_groupBy = "GROUP BY " . implode( ', ', $this->_groupBy ) . " $rollUP ";
        }
    }

    function from( $alias = 'c1' ) {
        $this->_from = "
FROM civicrm_contribution $alias 
LEFT JOIN civicrm_address addr ON addr.contact_id = {$alias}.contact_id";
    }

    function where( $alias = 'c1' ) {
        if ( $alias == 'c1' ) {
            $r1_relative = CRM_Utils_Array::value( "receive_date1_relative", $this->_params );
            $r1_from     = CRM_Utils_Array::value( "receive_date1_from"    , $this->_params );
            $r1_to       = CRM_Utils_Array::value( "receive_date1_to"      , $this->_params );
            
            $clause = $this->dateClause( "{$alias}.receive_date", $r1_relative, $r1_from, $r1_to );
        } else if ( $alias == 'c2' ) {
            $r2_relative = CRM_Utils_Array::value( "receive_date2_relative", $this->_params );
            $r2_from     = CRM_Utils_Array::value( "receive_date2_from"    , $this->_params );
            $r2_to       = CRM_Utils_Array::value( "receive_date2_to"      , $this->_params );
            
            $clause = $this->dateClause( "{$alias}.receive_date", $r2_relative, $r2_from, $r2_to );
        }

        $this->_where = "WHERE {$clause}";
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params;

        $this->processReportMode( );

        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";

        $dao = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );

        while ( $dao->fetch( ) ) {
            foreach ( $this->_columnHeaders as $key => $value ) {
                $rows[$dao->uni][$key] = $dao->$key;
            }
        }

        $this->select  ( 'c2' );
        $this->from    ( 'c2' );
        $this->where   ( 'c2' );
        $this->groupBy ( 'c2' );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";
        $dao = CRM_Core_DAO::executeQuery( $sql );

        while ( $dao->fetch( ) ) {
            foreach ( $this->_columnHeaders as $key => $value ) {
                if ( substr( $key, 0, 3 ) != 'c1_' ) {
                    $rows[$dao->uni][$key] = $dao->$key;
                }
            }
        }

        //re-ordering so that 'start' is at the end
        foreach ( array('start', 'addr_country_id') as $orderFld ) {
            if ( array_key_exists($orderFld, $this->_columnHeaders) ) {
                $temp = $this->_columnHeaders[$orderFld];
                unset($this->_columnHeaders[$orderFld]);
                $this->_columnHeaders[$orderFld] = $temp;
            }
        }

        $this->formatDisplay( $rows );
        
        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        
        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        
        foreach ( $rows as $rowNum => $row ) {
            // handle country
            if ( array_key_exists('addr_country_id', $row) ) {
                if ( $value = $row['addr_country_id'] ) {
                    $rows[$rowNum]['addr_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value, false );
                }
                $entryFound = true;
            }
        }
    }
}
