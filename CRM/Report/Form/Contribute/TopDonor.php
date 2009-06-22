<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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
require_once 'CRM/Contribute/PseudoConstant.php';
class CRM_Report_Form_Contribute_TopDonor extends CRM_Report_Form {

    protected $_summary = null;

    protected $_charts  = array( ''         => 'Tabular',
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
                                        'required'     => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              'avg'    => ts( 'Average' ), ), ), ),
                          'filters'               =>             
                          array( 'receive_date'   => 
                                 array( 'default' => 'this.year',
                                        'operatorType' =>   CRM_Report_Form::OP_DATE ),
                                 'total_range'   => 
                                 array( 'title'   => ts( 'Show no. of Top Donors' ),
                                        'type'    => CRM_Utils_Type::T_INT ),
                                 'contribution_type_id'=>
                                 array( 'name'    => 'contribution_type_id',
                                        'title'   => ts( 'Contribution Type' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options' => CRM_Contribute_PseudoConstant::contributionType( ) ),
                                 ),
                          ),
                   
                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Group' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options' => CRM_Core_PseudoConstant::staticGroup( ) 
                                        ), ), ),
                   );
        
        $this->_options = array( 'include_grand_total' => array( 'title'  => ts( 'Include Grand Totals' ),
                                                                 'type'   => 'checkbox',
                                                                 'default'=> true ),
                                 );
        parent::__construct( );
    }
    
    function preProcess( ) {
        parent::preProcess( );
    }
    
    /* function setDefaultValues( ) {
        return parent::setDefaultValues( );
        }*/

    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        $this->_columnHeaders["civicrm_donor_rank"]['title'] = 'Rank';
                        $this->_columnHeaders["civicrm_donor_rank"]['type']  = 1;
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
        FROM civicrm_contact {$this->_aliases['civicrm_contact']}

        	 INNER JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']} 
		             ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id

          	 LEFT  JOIN civicrm_group_contact   group_contact 
       			     ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id  AND group_contact.status='Added'

		     LEFT  JOIN civicrm_group  {$this->_aliases['civicrm_group']} 
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

                        $op    = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                        $value = CRM_Utils_Array::value( "total_range_value", $this->_params );

                        $this->_limit = '';
                        if ( $value ) {
                            $value++;
                            $this->_limit = " LIMIT 0, {$value} ";
                        } else if( $op ) {
                            
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

    function grandTotal( &$rows ) {

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


    function postProcess( ) {

        $this->beginPostProcess( );
        
        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );

        $sql = "
        SELECT * FROM ( {$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} ) as abc 
            ORDER BY abc.civicrm_contribution_total_amount_sum DESC $this->_limit
        ";
        
        $dao   = CRM_Core_DAO::executeQuery( $sql );
        
        $rows  = $graphRows = array();
        $count = 0;
        $rank  = 0;
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row['civicrm_donor_rank'] = $rank;
                $row[$key] = $dao->$key;
                
            }
            $rank++;
            $rows[] = $row;
        }
        if ( $this->_rollup ) {
            $rows[] = $rows[0];
            unset($rows[0]);
        }
        $this->formatDisplay( $rows );

        $this->doTemplateAssignment( $rows );
   
        require_once 'CRM/Utils/PChart.php';
        if ( CRM_Utils_Array::value('charts', $this->_params ) ) {
            foreach ( array ( 'receive_date', $this->_interval, 'value' ) as $ignore ) {
                unset( $graphRows[$ignore][$count-1] );
            }
            $graphs = CRM_Utils_PChart::chart( $graphRows, $this->_params['charts'], $this->_interval );
            $this->assign( 'graphFilePath', $graphs['0']['file_name'] );

        }
        $this->endPostProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
 
        $entryFound = false;

        foreach ( $rows as $rowNum => $row ) {
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url =CRM_Report_Utils_Report::getNextUrl( 'contribute/detail', 
                                                           'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'],
                                                           $this->_absoluteUrl, $this->_id  );
                $rows[$rowNum]['civicrm_contact_display_name_link'] = $url;
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
