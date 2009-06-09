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
        $yearsInPast      = 5;
        $yearsInFuture    = 3;
        $date             = CRM_Core_SelectValues::date('custom', $yearsInPast, $yearsInFuture, $dateParts ) ;        
        $count            = $date['maxYear'];
        while ( $date['minYear'] <= $count )  {
            $optionYear[ $date['minYear'] ] = $date['minYear'];
            $date['minYear']++;
        } 
        
        $this->_columns = 
            array( 
                  'civicrm_membership' =>
                  array( 'dao'        => 'CRM_Member_DAO_MembershipType',
                         'grouping'   => 'member-fields',
                         'fields'     =>
                         array( 'membership_type_year' =>
                                array( 'title'    => 'Membership Type',
                                       'required' => true,),
                                
                                ),
                         'filters'        =>             
                         array(  'join_date'         =>  
                                 array( 'name'    => 'join_date',
                                        'title'   => ts( 'Select Year' ),
                                        'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_BOOLEAN,
                                        'options' => array('-- select --')+$optionYear,
                                        ), 
                                 'membership_type_id' =>
                                 array('title'    => ts('Membership Type'),
                                       'type'     => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                       'options'  => CRM_Member_PseudoConstant::membershipType(),
                                       ),
                                 ),  
                         'group_bys'           =>
                         array( 'join_date' => 
                                array('title'      => ts('Join Date'),
                                      'frequency'  => true,
                                      'type'       => 12),
                                 ),
                         ),
                   );
        parent::__construct( );
    }
    
    function memberSummary($params){
        $membershipSummary = array();
        $preMonth     = CRM_Utils_Date::customFormat(date( "Y-m-d", mktime(0, 0, 0, date("m")-1,01,$params['join_date_value'])) , '%Y%m%d');
        $preMonthEnd  = CRM_Utils_Date::customFormat(date( "Y-m-t", mktime(0, 0, 0, date("m")-1,01,$params['join_date_value'])) , '%Y%m%d');
        $preMonthYear =  mktime(0, 0, 0, substr($preMonth, 4, 2), 1, substr($preMonth, 0, 4));
            
        $today = getdate();
        $date  = CRM_Utils_Date::getToday();
        $isCurrentMonth = 0;
        $ym  = sprintf("%04d%02d",     $params['join_date_value'], $today['mon']);
        $ymd = sprintf("%04d%02d%02d", $params['join_date_value'], $today['mon'], $today['mday']);
        $monthStartTs = mktime(0, 0, 0, $today['mon'], 1, $params['join_date_value']);
        
        $current = null;          
        $isCurrentMonth = 1;
        $isPreviousMonth = 1;
        
        $monthStart = $ym . '01';
        $yearStart = substr($ym, 0, 4) . '0101';
        
        $membershipTypes = CRM_Member_BAO_MembershipType::getMembershipTypes(false);
        $membership = new CRM_Member_BAO_Membership;//added
                
        foreach ( $membershipTypes as $key => $value ) {
            $membershipSummary[$key]['premonth'] = array(
                                                         'count'=>self::buildMemberData($key ,
                                                                                        $preMonth,
                                                                                        $preMonthEnd ),
                                                         'name' => $value
                                                         );
            
            $membershipSummary[$key]['month']    = array(
                                                         'count'=>self::buildMemberData($key ,
                                                                                        $monthStart,
                                                                                        $ymd),
                                                         'name' => $value
                                                         );
            
            $membershipSummary[$key]['year']     = array(
                                                         'count'=>self::buildMemberData($key ,
                                                                                        $yearStart, 
                                                                                        $ymd),
                                                         'name' => $value
                                                         );
            
            $membershipSummary[$key]['current']  = array(
                                                         'count'=>self::buildMemberDataCount($key, 
                                                                                             $current),
                                                         'name' => $value
                                                         );
            
            $membershipSummary[$key]['expired']  = array(
                                                         'count'=>self::buildMemberCountExpired($key, 
                                                                                                $exp),
                                                         'name' => $value
                                                         );
            
            $membershipSummary[$key]['total']    = array( 'count' => self::buildMemberDataCount($key, 
                                                                                                $ymd),
                                                          );
            
        }
        require_once "CRM/Member/BAO/MembershipStatus.php";
        $status = CRM_Member_BAO_MembershipStatus::getMembershipStatusCurrent();
        $status = implode(',' , $status );
        
        $totalCount = array();
        $totalCountPreMonth = $totalCountMonth = $totalCountYear = $totalCountCurrent = $totalCountTotal = 0;
        foreach( $membershipSummary as $key => $value ) {
            $totalCountPreMonth   = $totalCountPreMonth   +  $value['premonth']['count'];
            $totalCountMonth      = $totalCountMonth      +  $value['month']['count'];
            $totalCountYear       = $totalCountYear       +  $value['year']['count'];
            $totalCountCurrent    = $totalCountCurrent    +  $value['current']['count'];
            $totalCountExpired    = $totalCountExpired    +  $value['expired']['count'];
            $totalCountTotal      = $totalCountTotal      +  $value['total']['count'];
        }
        $totalCount['premonth'] = array("count" => $totalCountPreMonth ); 
        $totalCount['month']    = array("count" => $totalCountMonth );
        $totalCount['year']     = array("count" => $totalCountYear );
        $totalCount['current']  = array("count" => $totalCountCurrent  );
        $totalCount['expired']  = array("count" => $totalCountExpired  );
        $totalCount['total']    = array("count" => $totalCountTotal );
        if (! $isCurrentMonth ) {
            $totalCount['total'] = array( "count" => $totalCountTotal);
        }
        
        $membershipSummary['totalCount'] = $totalCount;
        
        $rows   = array();
        $pmonth =  date('F',$monthStartTs-1);
        $mnth   =  date('F', $monthStartTs);
        $year   =  date('Y', $monthStartTs);
        
        foreach( $membershipSummary as $key => $value ) {
            $rows[$key]['membership_type'] =  $value['premonth']['name'];               
            
            if ( array_key_exists('premonth', $value) ) {
                $rows[$key]['premonth'] = $value['premonth']['count'];
            }
            if ( array_key_exists('month', $value) ) {
                $rows[$key]['month'] = $value['month']['count'];
            }
            if ( array_key_exists('year', $value) ) {
                $rows[$key]['year'] = $value['year']['count'];
            }
            if ( array_key_exists('current', $value) ) {
                $rows[$key]['current'] = $value['current']['count'];
            }
            if ( array_key_exists('expired', $value) ) {
                $rows[$key]['expired'] = $value['expired']['count'];
            }
            if ( $key == 'totalCount' ) {
                $rows['totalCount']['membership_type'] =  'Total (of All Types)';
            }
        } 
        
        $this->_columnHeaders['membership_type'] =  array ( 'title' => 'Membership Type',
                                                            'type'  => null);
        $this->_columnHeaders['premonth']        =  array ( 'title' => $pmonth.'- New/Renew (Last Month)',
                                                            'type'  => null);
        $this->_columnHeaders['month']           =  array ( 'title' => $mnth.'- New/Renew (MTS)',
                                                            'type'  => null);
        $this->_columnHeaders['year']            =  array ( 'title' => $year.'- New/Renew (YTD)',
                                                            'type'  => null);
        $this->_columnHeaders['current']         =  array ( 'title' => 'Current Active Memberships',
                                                            'type'  => null);
        $this->_columnHeaders['expired']         =  array ( 'title' => 'Expired Memberships',
                                                            'type'  => null);
        $this->formatDisplay( $rows );
        $this->assign_by_ref( 'rows', $totalCount);
        $this->assign_by_ref( 'rows', $rows );
        // assign variables to templates
        $this->doTemplateAssignment( $rows );
    }
    
    function buildMemberData( $membershipTypeId, $startDate, $endDate, $isTest = 0) {
        $query = "
        SELECT count(civicrm_membership.id) as member_count
        FROM   civicrm_membership 
               LEFT JOIN civicrm_membership_status 
                         ON ( civicrm_membership.status_id = civicrm_membership_status.id )
        WHERE  membership_type_id = %1    AND 
               start_date >= '$startDate' AND 
               start_date <= '$endDate'   AND 
               civicrm_membership_status.is_current_member = 1 AND
               is_test = %2";
        $params = array(1 => array($membershipTypeId, 'Integer'),
                        2 => array($isTest, 'Boolean') );
        $memberCount = CRM_Core_DAO::singleValueQuery( $query, $params );
        return (int)$memberCount;
    }
    
    function buildMemberDataCount( $membershipTypeId, $date = null, $isTest = 0 ) {
        if ( !is_null($date) && ! preg_match('/^\d{8}$/', $date) ) {
            CRM_Core_Error::fatal(ts('Invalid date "%1" (must have form yyyymmdd).', array(1 => $date)));
        }
        
        $params = array(1 => array($membershipTypeId, 'Integer'),
                        2 => array($isTest, 'Boolean') );
        $query = "
        SELECT  count(civicrm_membership.id ) as member_count
        FROM    civicrm_membership 
                LEFT JOIN civicrm_membership_status 
                          ON ( civicrm_membership.status_id = civicrm_membership_status.id  )
        WHERE  civicrm_membership.membership_type_id = %1 AND 
               civicrm_membership.is_test = %2";
        if ( ! $date ) {
            $query .= " AND civicrm_membership_status.is_current_member = 1";
        } else {
            $date   = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
            $query .= " AND civicrm_membership.start_date <= '$date' 
                        AND civicrm_membership_status.is_current_member = 1";
        }
        $memberCount = CRM_Core_DAO::singleValueQuery( $query, $params );
        return (int)$memberCount;
    }  
    
    
    function buildMemberCountExpired( $membershipTypeId, $date = null, $isTest = 0 ) {
        if ( !is_null($date) && ! preg_match('/^\d{8}$/', $date) ) {
            CRM_Core_Error::fatal(ts('Invalid date "%1" (must have form yyyymmdd).', array(1 => $date)));
        }
        
        $params = array(1 => array($membershipTypeId, 'Integer'),
                        2 => array($isTest, 'Boolean') );
        $query = "
        SELECT  count(civicrm_membership.id ) as member_count
        FROM    civicrm_membership 
                LEFT JOIN civicrm_membership_status 
                          ON ( civicrm_membership.status_id = civicrm_membership_status.id  )
        WHERE  civicrm_membership.membership_type_id = %1 AND 
               civicrm_membership.is_test = %2";
        if ( ! $date ) {
            $query .= " AND civicrm_membership_status.id = 4";
        }
        $memberCount = CRM_Core_DAO::singleValueQuery( $query, $params );
        return (int)$memberCount;
    }  
    
    function preProcess( ) {
        parent::preProcess( );
        
    }
    
    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( ); 
        $select[] = "count(membership.id ) as civicrm_membership_member_count";
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
        }
        $this->_columnHeaders["civicrm_membership_member_count"] = array('title' => ts('Member Count'),
                                                                         'type'  => null);
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }
    
    static function formRule( &$fields, &$files, $self ) {  
        $errors = $grouping = array( );
        
        //check for searching combination of dispaly columns and
        //grouping criteria
        if ( ($fields['group_bys']['join_date']   && !$fields['membership_type_id_value'] ) || 
             ($fields['membership_type_id_value'] && !$fields['group_bys']['join_date'] ) ) {
            $errors['membership_type_id_value'] = ts("Please use combination of Membership Type Filter along with Join Date Grouping only ");      
            
        } 
        
        if ( $fields['join_date_value'] != 0 && $fields['group_bys']['join_date'] ) {
            $errors['join_date_value'] = ts("Please do not use combination of Year Filter along with Join Date Grouping");
        }
       
        return $errors;
    }
    
    function from( ) {
        $this->_from = "
        FROM  civicrm_membership membership
              LEFT JOIN civicrm_membership_status 
                        ON ( membership.status_id = civicrm_membership_status.id  )";
    }// end of from
    
    function where( ) {
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                    if ( $op ) {
                        $clause = 
                            $this->whereClause( $field,
                                                $op,
                                                CRM_Utils_Array::value( "{$fieldName}_value", $this->_params ),
                                                CRM_Utils_Array::value( "{$fieldName}_min", $this->_params ),
                                                CRM_Utils_Array::value( "{$fieldName}_max", $this->_params ) );
                    }
                    if ( ! empty( $clause ) ) {
                        $clauses[$fieldName] = $clause;
                    }
                }
            }
        }
        
        if ( array_key_exists('gid', $clauses ) &&
             !array_key_exists('end_date', $clauses) ) {
            $this->_where = "WHERE  membership.is_test = 0 AND
                  civicrm_membership_status.is_current_member = 1 AND" . implode( ' AND ', $clauses );
        } else {
            $this->_where = "WHERE membership.is_test = 0 AND
                  civicrm_membership_status.is_current_member = 1 AND" . implode( ' AND ', $clauses );
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
        $this->beginPostProcess();
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            $this->beginPostProcess();
            
            $sql = $this->buildQuery( true );
            $dao   = CRM_Core_DAO::executeQuery( $sql );
            $rows  = $graphRows = array();
            while ( $dao->fetch( ) ) { 
                $row = array( );
                foreach ( $this->_columnHeaders as $key => $value ) {
                    $row[$key] = $dao->$key;
                }
                $rows[] = $row;
            }
            
            $this->formatDisplay( $rows );
            
            // assign variables to templates
            $this->doTemplateAssignment( $rows );
        }
        
        // To Display Reports based on Year filter only
        if ( $this->_params['join_date_value'] != 0 ){ 
            $this->memberSummary($this->_params);
        }
        $this->endPostProcess( );
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