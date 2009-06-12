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
require_once 'CRM/Member/PseudoConstant.php';
require_once "CRM/Member/BAO/MembershipType.php";
require_once "CRM/Member/BAO/Membership.php";

class CRM_Report_Form_Member_Summary extends CRM_Report_Form {
    
    protected $_summary = null;
    
    protected $_charts  = array( ''         => 'Tabular',
                                 'barGraph' => 'Bar Graph',
                                 'pieGraph' => 'Pie Graph'
                                 );
    
    function __construct( ) {
        // UI for selecting columns to appear in the report list
        // array conatining the columns, group_bys and filters build and provided to Form
        
        $this->_columns = 
            array( 
                  'civicrm_membership' =>
                  array( 'dao'         => 'CRM_Member_DAO_MembershipType',
                         'grouping'    => 'member-fields',
                         'fields'      =>
                         array( 'membership_type_id' =>
                                array( 'title'    => 'Membership Type',
                                       'required' => true,),
                                ),
                         
                         'filters'     =>             
                         array( 'join_date' =>
                                array('title'        =>  'Memberships Join Date', 
                                      'operatorType' =>   CRM_Report_Form::OP_DATE ),
                                'membership_type_id'  =>
                                array('title'         => ts('Membership Type'),
                                      'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                      'options'       => CRM_Member_PseudoConstant::membershipType(),
                                      ),
                                ),  
                         'group_bys'        =>
                         array( 'join_date' => 
                                array('title'      => ts('Join Date'),
                                      'default'    => true,
                                      'frequency'  => true,
                                      'chart'      => true,
                                      'type'       => 12 ),
                                'membership_type_id' => 
                                array( 'title'     => 'Membership Type',
                                       'default'   => true,
                                      'chart'      => true, )
                                ),
                         ),
                  
                  'civicrm_contribution' =>
                  array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                         'fields'        =>
                         array( 'total_amount'        => 
                                array( 'title'        => ts( 'Amount Statistics' ),
                                       'required'     => true,
                                       'statistics'   => 
                                       array('sum'    => ts( 'Total Payment Made' ), 
                                             'count'  => ts( 'Contribution Count' ), 
                                             'avg'    => ts( 'Average' ), ), 
                                       ),
                                ),
                         'grouping'   => 'member-fields',
                         ),
                   );
        parent::__construct( );
    }
    
    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( ); 
        $select[] = " COUNT( DISTINCT membership.id ) as civicrm_membership_member_count";
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                        
                        switch ( $this->_params['group_bys_freq'][$fieldName] ) {
                        case 'YEARWEEK' :
                            $select[] = "DATE_SUB({$field['dbAlias']}, INTERVAL WEEKDAY({$field['dbAlias']}) DAY) AS {$tableName}_{$fieldName}_start";
                            
                            $select[] = "YEARWEEK({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                            $select[] = "WEEKOFYEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Week';
                            break;
                            
                        case 'YEAR' :
                            $select[] = "MAKEDATE(YEAR({$field['dbAlias']}), 1)  AS {$tableName}_{$fieldName}_start";
                            $select[] = "YEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                            $select[] = "YEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Year';
                            break;
                            
                        case 'MONTH':
                            $select[] = "DATE_SUB({$field['dbAlias']}, INTERVAL (DAYOFMONTH({$field['dbAlias']})-1) DAY) as {$tableName}_{$fieldName}_start";
                            $select[] = "MONTH({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                            $select[] = "MONTHNAME({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Month';
                            break;
                            
                        case 'QUARTER':
                            $select[] = "STR_TO_DATE(CONCAT( 3 * QUARTER( {$field['dbAlias']} ) -2 , '/', '1', '/', YEAR( {$field['dbAlias']} ) ), '%m/%d/%Y') AS {$tableName}_{$fieldName}_start";
                            $select[] = "QUARTER({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
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
                            
                            // just to make sure these values are transfered to rows.
                            // since we need that for calculation purpose, 
                            // e.g making subtotals look nicer or graphs
                            $this->_columnHeaders["{$tableName}_{$fieldName}_interval"] = array('no_display' => true);
                            $this->_columnHeaders["{$tableName}_{$fieldName}_subtotal"] = array('no_display' => true);
                        }
                    }
                }
            }// end of select
            
            if ( array_key_exists('fields', $table) ){
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
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] = CRM_Utils_Type::T_INT;
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
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['operatorType'] = $field['operatorType'];
                        }
                    }
                }
            }
            $this->_columnHeaders["civicrm_membership_member_count"] = array('title' => ts('Member Count'),
                                                                             'type'  => null); 
        }
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
        
    }
    
    function from( ) {
        $this->_from = "
        FROM  civicrm_membership membership
              LEFT JOIN civicrm_membership_status 
                        ON ( membership.status_id = civicrm_membership_status.id  )
              LEFT JOIN civicrm_membership_payment payment
                        ON ( membership.id = payment.membership_id )
              INNER JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']} 
                         ON payment.contribution_id = {$this->_aliases['civicrm_contribution']}.id";
        
    }// end of from
    
    function where( ) {
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    
                    if ( $field['operatorType'] & CRM_Utils_Type::T_DATE ) {
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
                        $clauses[$fieldName] = $clause;
                    }
                }
            }
        }
        
        if ( array_key_exists('membership_type_id', $clauses ) ||
             array_key_exists('join_date', $clauses) ) {
            $this->_where = "WHERE  " . implode( ' AND ', $clauses );
        } else { 
            $this->_where = "WHERE membership.is_test = 0 AND
                            civicrm_membership_status.is_current_member = 1";
        }
    }
    
    function groupBy( ) {
        $this->_groupBy = "";
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            if ( CRM_Utils_Array::value( 'chart', $field ) ) {
                                $this->assign( 'displayChart', true );
                            }
                            if ( CRM_Utils_Array::value('frequency', $table['group_bys'][$fieldName]) && 
                                 CRM_Utils_Array::value($fieldName, $this->_params['group_bys_freq']) ) {
                                
                                $append = "YEAR({$field['dbAlias']}),";
                                if ( in_array(strtolower($this->_params['group_bys_freq'][$fieldName]), 
                                              array('year')) ) {
                                    $append = '';
                                }
                                $this->_groupBy[] = "$append {$this->_params['group_bys_freq'][$fieldName]}({$field['dbAlias']})";
                                $append = true;
                            } else {
                                $this->_groupBy[] = $field['dbAlias'];
                            }
                        }
                    }
                }
            }
            
            if ( !empty($this->_statFields) && 
                 CRM_Utils_Array::value( 'include_grand_total', $this->_params['options'] ) && 
                 (( $append && count($this->_groupBy) <= 1 ) || (!$append)) ) {
                $this->_rollup = " WITH ROLLUP";
            }
            $this->_groupBy = "GROUP BY " . implode( ', ', $this->_groupBy ) . " {$this->_rollup} ";
        } else {
            $this->_groupBy = "GROUP BY membership.join_date";
        }
    }
    
    function postProcess( ) {
        parent::postProcess( );
      
    }

    function buildChart( &$rows ) {
        $graphRows            = array();
        $count                = 0;
        $membershipTypeValues = CRM_Member_PseudoConstant::membershipType( );
        $isMembershipType     = CRM_Utils_Array::value( 'membership_type_id', $this->_params['group_bys'] );
        $isJoiningDate        = CRM_Utils_Array::value( 'join_date', $this->_params['group_bys'] );
        if ( CRM_Utils_Array::value('charts', $this->_params ) ) {
            foreach ( $rows as $key => $row ) {                                              
                if ( $isMembershipType ) { 
                    $join_date            = $row['civicrm_membership_join_date_start'];
                    $displayInterval      = $row['civicrm_membership_join_date_interval'];
                    list( $year, $month ) = explode( '-', $join_date );
                    if ( $row ['civicrm_membership_join_date_subtotal'] ) {
                        
                        switch ($this->_interval ) {
                        case 'Month' :                           
                            $displayRange = $displayInterval.' '. $year ;                            
                            break;
                            
                        case 'Quarter' :                           
                            $displayRange = 'Quarter '. $displayInterval.' of '. $year ;                            
                            break;
                            
                        case 'Week' :                           
                            $displayRange = 'Week '. $displayInterval.' of '. $year ;                            
                            break;
                            
                        case 'Year' :                           
                            $displayRange = $year;                                                          
                            break;
                        }                        
                        $membershipType = $displayRange ."-". $membershipTypeValues[ $row['civicrm_membership_membership_type_id'] ]; 
                        
                    } else {
                        
                        $membershipType = $membershipTypeValues[ $row['civicrm_membership_membership_type_id'] ];
                    }
                    
                    $interval[ $membershipType ] = $membershipType;
                    $display [ $membershipType ] = $row [ 'civicrm_contribution_total_amount_sum' ]; 
                    
                } else  {
                    $graphRows['receive_date'] [ ]   = $row['civicrm_membership_join_date_start'];
                    $graphRows[$this->_interval] [ ] = $row['civicrm_membership_join_date_interval'];
                    $graphRows['value'] [ ]          = $row['civicrm_contribution_total_amount_sum'];
                    $count++ ;                    
                } 
            }
            
            if( $isMembershipType ) {  
                
                $graphRows['value'] = $display;
                $chartInfo          = array( 'legend' => 'MemberShip Summary',
                                             'xname'  => 'Amount',
                                             'yname'  => 'Year' );                
                $graphs             = CRM_Utils_PChart::reportChart( $graphRows, $this->_params['charts'] , $interval , $chartInfo );
                
            } else {                
                $graphs             = CRM_Utils_PChart::chart( $graphRows, $this->_params['charts'], $this->_interval );
            }
            
            $this->assign( 'graphFilePath', $graphs['0']['file_name'] );
            $this->_graphPath =  $graphs['0']['file_name'];
            
        }
    }
    
    function alterDisplay( &$rows ){
        // custom code to alter rows
        $hoverText  = ts("Lists Summary of Memberships.");
        $entryFound = false;
        foreach ( $rows as $rowNum => $row ) {
            // make count columns point to detail report
            if ( CRM_Utils_Array::value('join_date', $this->_params['group_bys']) && 
                 CRM_Utils_Array::value('civicrm_membership_join_date_start', $row) &&
                 $row['civicrm_membership_join_date_start'] && 
                 $row['civicrm_membership_join_date_subtotal'] ) {
                $dateStart = CRM_Utils_Date::customFormat($row['civicrm_membership_join_date_start'], 
                                                          '%Y%m%d');
                $dateEnd   = CRM_Utils_Date::unformat($dateStart, '');
                
                switch(strtolower($this->_params['group_bys_freq']['join_date'])) {
                case 'month': 
                    $dateEnd   = date("Ymd", mktime(0, 0, 0, $dateEnd['M']+1, 
                                                    $dateEnd['d']-1, $dateEnd['Y']));
                    break;
                case 'year': 
                    $dateEnd   = date("Ymd", mktime(0, 0, 0, $dateEnd['M'], 
                                                    $dateEnd['d']-1, $dateEnd['Y']+1));
                    break;
                case 'yearweek': 
                    $dateEnd   = date("Ymd", mktime(0, 0, 0, $dateEnd['M'], 
                                                    $dateEnd['d']+6, $dateEnd['Y']));
                    break;
                case 'quarter': 
                    $dateEnd   = date("Ymd", mktime(0, 0, 0, $dateEnd['M']+3, 
                                                    $dateEnd['d']-1, $dateEnd['Y']));
                    break;
                }
                
                $url =
                    CRM_Utils_System::url( 'civicrm/report/member/detail',
                                           "reset=1&force=1&join_date_from={$dateStart}&join_date_to={$dateEnd}"
                                           );
                $row['civicrm_membership_join_date_start'] =  CRM_Utils_Date::format($row['civicrm_membership_join_date_start']);
                $rows[$rowNum]['civicrm_membership_join_date_start_link'] = $url;
                $entryFound = true;
            }
            
            // handle Membership Types
            if ( array_key_exists('civicrm_membership_membership_type_id', $row) ) {
                if ( $value = $row['civicrm_membership_membership_type_id'] ) {
                    $rows[$rowNum]['civicrm_membership_membership_type_id'] = 
                        CRM_Member_PseudoConstant::membershipType( $value, false );
                }
                $entryFound = true;
            }       
            
            // make subtotals look nicer
            if ( array_key_exists('civicrm_membership_join_date_subtotal', $row) && 
                 !$row['civicrm_membership_join_date_subtotal'] ) {
                $this->fixSubTotalDisplay( $rows[$rowNum], $this->_statFields );
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
?>