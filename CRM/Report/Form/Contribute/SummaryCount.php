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

class CRM_Report_Form_Contribute_SummaryCount extends CRM_Report_Form {

    protected $_summary = null;

    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contact'  =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'fields'    =>
                          array( 'id'           => 
                                 array( 'no_display' => true,
                                        'required'  => true, ), 
                                 'display_name' => 
                                 array( 'title'      => ts( 'Contact Name' ),
                                        'required'   => true,
                                        'no_repeat'  => true ),), 
                          ),
                   
                   'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'fields'        =>
                          array( 'total_amount'        => 
                                 array( 'title'        => ts( 'Amount Statistics' ),
                                        'default'      => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              'avg'    => ts( 'Average' ), ), ), ),
                          'filters'               =>             
                          array( 'receive_date'   => 
                                 array( 'default' => 'this.month'),
                                 'total_count'   => 
                                 array( 'title'   => ts( 'Total Count' ),
                                        'type'    => CRM_Utils_Type::T_INT ),
                                 ),
                          ),
                   
                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Group' ),
                                        'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                        'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
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
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        
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
                            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                        }
                    }
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    static function formRule( &$fields, &$files, $self ) {  
        $errors = array( );
        return $errors;
    }

    function from( ) {
        $this->_from = "
FROM       civicrm_contact            {$this->_aliases['civicrm_contact']}
INNER JOIN civicrm_contribution       {$this->_aliases['civicrm_contribution']} 
       ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id
LEFT  JOIN civicrm_group_contact      group_contact 
       ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id  AND group_contact.status='Added'
LEFT  JOIN civicrm_group              {$this->_aliases['civicrm_group']} 
       ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id
";
    }

    function where( ) {
        $clauses = array( );
        $this->_tempClause = '';
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    if ( $field['type'] & CRM_Utils_Type::T_DATE ) {
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );
                        
                        if ( $relative || $from || $to ) {
                            $clause = $this->dateClause( $field['name'], $relative, $from, $to );
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
                        if ( $fieldName == 'total_count' ) {
                            //replace string because the
                            //contribution.total_count column doesn't exist
                            //and we have to show the result on the
                            //basis of total count, the replaced string
                            //is present in temparary table
                            $this->_tempClause = str_replace("contribution.total_count", "civicrm_contribution_total_amount_count_temp", 
                                                             $clause);
                        } else{
                            $clauses[] = $clause;
                        }
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

    function statistics( &$rows ) {
        $statistics = array();

        $statistics[] = array( 'title' => ts('Row(s) Listed'),
                               'value' => count($rows) );
        return $statistics;
    }

    function groupBy( ) {
        $this->_rollup = '';    
        if ( !empty($this->_statFields) && 
             CRM_Utils_Array::value( 'include_grand_total', $this->_params['options'] ) ) {             
            $this->_rollup = " WITH ROLLUP";
        }
        
        $this->_groupBy = "GROUP BY contact.id {$this->_rollup}";
    }

    function tempTable ( ) {

        $temSql = " SELECT contact.id as civicrm_contact_id,
                    COUNT(contribution.total_amount) as civicrm_contribution_total_amount_count_temp
                    {$this->_from} {$this->_where} {$this->_groupBy}";

        //define table name
        $randomNum = md5( uniqid( ) );
        $this->_tableName = "civicrm_temp_report_{$randomNum}"; 

        //Create the Temporary Table
        $sql =  " CREATE  TEMPORARY TABLE report_{$this->_tableName} ( id int PRIMARY KEY AUTO_INCREMENT,
                                                             civicrm_contact_id int,
                                                             civicrm_contribution_total_amount_count_temp int
                                                            ) ENGINE=HEAP ";

        CRM_Core_DAO::executeQuery( $sql,CRM_Core_DAO::$_nullArray );

        //insert the data based on the search criteria excluding the Total counts Filter
        $distanceQuery = 
            "INSERT INTO report_{$this->_tableName} ( civicrm_contact_id,
                                                      civicrm_contribution_total_amount_count_temp
                                                     )
             {$temSql}" ;

         CRM_Core_DAO::executeQuery( $distanceQuery, CRM_Core_DAO::$_nullArray );

        //select the contact on the Totals Counts Filter criteria
        $sql = 
            " {$this->_select}
              FROM civicrm_contact contact 
                   INNER JOIN report_{$this->_tableName} temptable ON ( contact.id = temptable.civicrm_contact_id )
                   LEFT  JOIN civicrm_contribution contribution ON( contribution.contact_id= temptable.civicrm_contact_id )
              $this->_where AND {$this->_tempClause} {$this->_groupBy}";

        return $sql;
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );
        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;
       
        $this->processReportMode( );

        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";
        //To show contact only in speicfied range
        //create temp table for all record and then filter the record
        //based on the specified counting range
        if ( $this->_tempClause ) {
            $sql = $this->tempTable( $temSql );
        }
        $dao   = CRM_Core_DAO::executeQuery( $sql );
        
        $rows  = $graphRows = array();
        $count = 0;
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row[$key] = $dao->$key;
            }

            require_once 'CRM/Utils/PChart.php';
            if ( CRM_Utils_Array::value('charts', $this->_params ) && 
                 $row['civicrm_contribution_receive_date_subtotal'] ) {
                $graphRows['receive_date'][]   = $row['civicrm_contribution_receive_date_start'];
                $graphRows[$this->_interval][] = $row['civicrm_contribution_receive_date_interval'];
                $graphRows['value'][]          = $row['civicrm_contribution_total_amount_sum'];
                $count++;
            }
            
            $rows[] = $row;
        }
        $this->formatDisplay( $rows );

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        $this->assign( 'statistics', $this->statistics( $rows ) );
        
        require_once 'CRM/Utils/PChart.php';
        if ( CRM_Utils_Array::value('charts', $this->_params ) ) {
            foreach ( array ( 'receive_date', $this->_interval, 'value' ) as $ignore ) {
                unset( $graphRows[$ignore][$count-1] );
            }
            $graphs = CRM_Utils_PChart::chart( $graphRows, $this->_params['charts'], $this->_interval );
            $this->assign( 'graphFilePath', $graphs['0']['file_name'] );

        }
        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
 
        $entryFound = false;

        foreach ( $rows as $rowNum => $row ) {
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/report/contribute/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'] );
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
