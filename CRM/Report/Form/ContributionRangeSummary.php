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

class CRM_Report_Form_ContributionRangeSummary extends CRM_Report_Form {

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
                          array( 'total_amount1'        => 
                                 array( 'title'        => ts( 'Amount Statistics1' ),
                                        'default'      => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              'avg'    => ts( 'Average' ), ), 
                                        'dbAlias'      => 'contribution1.total_amount' ),
                                 'total_amount2'        => 
                                 array( 'title'        => ts( 'Amount Statistics2' ),
                                        'default'      => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              'avg'    => ts( 'Average' ), ), 
                                        'dbAlias'      => 'contribution2.total_amount' ),
                                 ),
                          'filters'               =>             
                          array( 
                                 'receive_date1'  => 
                                 array( 'title'   => ts( 'Date Range1' ),
                                        'default' => 'this.month',
                                        'type'    => 12,
                                        'dbAlias' => 'contribution1.receive_date' ),
                                 'receive_date2'  => 
                                 array( 'title'   => ts( 'Date Range2' ),
                                        'default' => 'this.month',
                                        'type'    => 12,
                                        'dbAlias' => 'contribution2.receive_date' ),
                                 'total_amount'   => 
                                 array( 'title'   => ts( 'Total  Amount Between' ), ), ), ),
                   'civicrm_address' =>
                   array( 'dao' => 'CRM_Core_DAO_Address',
                          'group_bys'           =>
                          array( 'country_id'   => 
                                 array( 'default'    => true,
                                        'title'      => ts( 'Country' ) ), ), ),
                   );

        $this->_options = array( 'include_grand_total' => array( 'title'  => ts( 'Include Grand Totals' ),
                                                                 'type'   => 'checkbox',
                                                                 'default'=> true ),
                                 );
        parent::__construct( );
    }

    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Contribution Range Summary Report' ) );
        
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
                     CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                    
                    // only include statistics columns if set
                    if ( CRM_Utils_Array::value('statistics', $field) ) {
                        foreach ( $field['statistics'] as $stat => $label ) {
                            //for ( $i = 1 ; $i <= 2 ; $i++ ) {
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
                                //}
                        }   

                    } else {
                        $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                    }
                }
            }

            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                        switch ( $this->_params['group_bys_freq'][$fieldName] ) {
                        case 'YEARWEEK' :
                            $select[] = "DATE_SUB({$field['dbAlias']}, 
INTERVAL WEEKDAY({$field['dbAlias']}) DAY) AS {$tableName}_{$fieldName}_start";
                            $select[] = "WEEKOFYEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Week';
                            break;
                            
                        case 'YEAR' :
                            $select[] = "MAKEDATE(YEAR({$field['dbAlias']}), 1)  
AS {$tableName}_{$fieldName}_start";
                            $select[] = "YEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Year';
                            break;
                            
                        case 'MONTH':
                            $select[] = "DATE_SUB({$field['dbAlias']}, 
INTERVAL (DAYOFMONTH({$field['dbAlias']})-1) DAY) as {$tableName}_{$fieldName}_start";
                            $select[] = "MONTHNAME({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Month';
                            break;
                            
                        case 'QUARTER':
                            $select[] = "STR_TO_DATE(CONCAT( 3 * QUARTER( {$field['dbAlias']} ) -2 , '/', '1', '/', YEAR( {$field['dbAlias']} ) ), '%m/%d/%Y') AS {$tableName}_{$fieldName}_start";
                            $select[] = "QUARTER({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Quarter';
                            break;
                            
                        }
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys_freq'] ) ) {
                            $this->_interval = $field['title'];
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
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( ) {
        $join1 = $this->where(true, array('receive_date1'));
        $join2 = $this->where(true, array('receive_date2'));

        $this->_from = "
FROM       civicrm_contact contact
LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}1 ON (contact.id = {$this->_aliases['civicrm_contribution']}1.contact_id AND {$join1})
LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}2 ON (contact.id = {$this->_aliases['civicrm_contribution']}2.contact_id AND {$join2})
LEFT JOIN  civicrm_address {$this->_aliases['civicrm_address']} ON contact.id={$this->_aliases['civicrm_address']}.contact_id
";
    }

    function where( $joinClause = false, $joinFields = array() ) {
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    if ($joinClause && (!in_array($fieldName,$joinFields))) {continue;}
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

        if ($joinClause) {
            if ( empty( $clauses ) ) {
                return " ( 1 ) ";
            } else {
                return implode( ' AND ', $clauses );
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

        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            $combinations[] = $field['title'];
                        }
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
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
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
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;

        $this->processReportMode( );

        $r1_relative = CRM_Utils_Array::value( "receive_date1_relative", $this->_params );
        $r1_from     = CRM_Utils_Array::value( "receive_date1_from"    , $this->_params );
        $r1_to       = CRM_Utils_Array::value( "receive_date1_to"      , $this->_params );

        $c1_clause = $this->dateClause( 'c1.receive_date', $r1_relative, $r1_from, $r1_to );

        $r2_relative = CRM_Utils_Array::value( "receive_date2_relative", $this->_params );
        $r2_from     = CRM_Utils_Array::value( "receive_date2_from"    , $this->_params );
        $r2_to       = CRM_Utils_Array::value( "receive_date2_to"      , $this->_params );

        $c2_clause = $this->dateClause( 'c2.receive_date', $r2_relative, $r2_from, $r2_to );

        $sql = "
SELECT    c.id, c.display_name,
          sum(c1.total_amount) as c1_amount,
          count(c1.total_amount) as c1_count
FROM      civicrm_contact c
INNER JOIN civicrm_contribution c1 on c.id = c1.contact_id
LEFT JOIN civicrm_address ad ON ad.contact_id=c1.contact_id
WHERE   $c1_clause
GROUP BY ad.country_id
";

        //CRM_Core_Error::debug( '$sql', $sql );
        $dao = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );

        while ( $dao->fetch( ) ) {
            $rows[$dao->id] = array( 'cid'         => $dao->id,
                                     'display_name' => $dao->display_name,
                                     'c1_amount'    => $dao->c1_amount   ,
                                     'c2_amount'    => null
                                     );
        }
        $this->_columnHeaders = array( 'cid' =>     array('title' => 'cid'),
                                       'display_name'=> array('title' => 'display'),
                                       'c1_amount'=> array('title' => 'c1 amount'),
                                       'c2_amount'=> array('title' => 'c2 amount'),
                                       );


        $sql = "
SELECT    c.id, c.display_name,
          sum(c2.total_amount) as c2_amount,
          count(c2.total_amount) as c2_count
FROM      civicrm_contact c
INNER JOIN civicrm_contribution c2 on c.id = c2.contact_id
LEFT JOIN civicrm_address ad ON c.id=ad.contact_id 
WHERE     $c2_clause
GROUP BY ad.country_id
";

        $dao = CRM_Core_DAO::executeQuery( $sql );
        while ( $dao->fetch( ) ){
            if ( isset( $rows[$dao->id] ) ) {
                $rows[$dao->id]['c2_amount'] = $dao->c2_amount;
            } else {
                $rows[$dao->id] = array( 'cid'         => $dao->id,
                                         'display_name' => $dao->display_name,
                                         'c1_amount'    => null,
                                         'c2_amount'    => $dao->c2_amount
                                         );
            }
        }


        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        $this->assign( 'statistics', $this->statistics( $rows ) );
        
        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
 
        $entryFound = false;
        foreach ( $rows as $rowNum => $row ) {
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view', 
                                              'reset=1&cid=' . $row['civicrm_contact_id'] );
                $rows[$rowNum]['civicrm_contact_display_name'] = "<a href='$url'>" . 
                    $row["civicrm_contact_display_name"] . '</a>';
                $entryFound = true;
            }

            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
        }
    }
}
