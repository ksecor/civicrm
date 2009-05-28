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

class CRM_Report_Form_Pledge_Summary extends CRM_Report_Form {

    protected $_summary = null;
    
    function __construct( ) {
        $this->_columns = 
            array(
                  'civicrm_contact'  =>
                  array( 'dao'       => 'CRM_Contact_DAO_Contact',
                         'fields'    =>
                         array( 'display_name' => 
                                array( 'title'     => ts( 'Contact Name' ),
                                       'required'  => true,
                                       'no_repeat' => true ),
                                ),
                         'filters'   =>             
                         array('sort_name'    => 
                               array( 'title' => ts( 'Contact Name' )  ),
                               
                               'id'           => 
                               array( 'title' => ts( 'Contact ID' ) ), ),
                         'grouping'  => 'contact-fields',
                         ),
			    
                  'civicrm_pledge'  =>
                  array('dao'       => 'CRM_Contact_DAO_Contact',
                        'fields'    =>
                        array('id'         =>
                              array( 'no_display'=> true,
                                     'required'  => true, ),
                              
                              'contact_id' =>
                              array( 'no_display'=> true,
                                     'required'  => true, ),
                              
                              'amount'     =>
                              array( 'title'     => ts('Pledge Amount'),
                                     'required'  => true,
                                     'type'      => 1024              ),
                              
                              'frequency_unit'=>
                              array( 'title'=> ts('Frequency Unit'),),
                              
                              'installments'=>
                              array( 'title'=> ts('Installments'),),
                              
                              'start_date'  =>
                              array( 'title'=> ts('Start Date'),
                                     'type' => 12             ),
                              
                              'create_date' =>
                              array( 'title'=> ts('Create Date'),
                                     'type' => 12              ),
                                          

                              'end_date'    =>   
                              array( 'title'=> ts('End Date'),
                                     'type' => 12           ),
                              
                              'status_id'   =>
                              array( 'title'   => ts('Pledge Status'),
                                     'required'=>true               ),
                              
                              ),
                        'filters'   => 
                        array(
                              'create_date' => array( 'title'   => 'Event Create Date',
                                                      'type'    => CRM_Utils_Type::T_DATE ),
                              'sid'         => array( 'name'    => 'status_id',
                                                      'title'   => ts( 'Status Type' ),
                                                      'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                                      'options' => CRM_Core_OptionGroup::values('contribution_status') ),
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
        
        parent::__construct( );
    }
    
    function preProcess( ) {
        
        $this->assign( 'reportTitle', ts('Pledge Summary Report' ) );
        parent::preProcess( );            
    }
    
    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        
                        $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                    }
                }
            }
        }
        
        $this->_select = "SELECT " . implode( ', ', $select ) . " , payment.id as payment_id, payment.scheduled_amount, payment.scheduled_date, payment.status_id ";
    }
    
    function from( ) {
        $this->_from = "
            FROM civicrm_pledge {$this->_aliases['civicrm_pledge']}
                 LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact']} 
                      ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_pledge']}.contact_id )

                 LEFT JOIN  civicrm_pledge_payment payment 
                      ON ( payment.pledge_id ={$this->_aliases['civicrm_pledge']}.id )
 
                 LEFT  JOIN civicrm_group_contact group_contact 
                      ON {$this->_aliases['civicrm_pledge']}.contact_id = group_contact.contact_id  AND group_contact.status='Added'

                 LEFT  JOIN civicrm_group {$this->_aliases['civicrm_group']} 
                      ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id ";

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
                        $clauses[] = $clause;
                    }
                }
            }
        }    
        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ({$this->_aliases['civicrm_pledge']}.is_test=0 ) ";
        } else {
            $this->_where = "WHERE  ({$this->_aliases['civicrm_pledge']}.is_test=0 )  AND " . implode( ' AND ', $clauses );
        }
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
        
        $this->_groupBy = "GROUP BY {$this->_aliases['civicrm_pledge']}.contact_id ,{$this->_aliases['civicrm_pledge']}.id, payment.id ";
        
    }
    
    function buildRows( $sql, &$rows ) {
        
        $tableHeader = array( 'scheduled_date'  => array ( 'type'  => 12 , 
                                                           'title' => 'Next Payment Date'),
                              'scheduled_amount'=> array ( 'type'  => 1024 , 
                                                           'title' => 'Next Payment Amount'),  
                              'total_paid'      => array ( 'type'  => 1024 , 
                                                           'title' => 'Total Paid'),
                              'balance_due'     => array ( 'type'  => 1024 , 
                                                           'title' => 'Balance Due') , 
                              'status_id'       => null,
                              );
        
        $pledge_data = $payment = array();
        
        $dao         = CRM_Core_DAO::executeQuery( $sql );
        
        $check_id    = 0;
        while ( $dao->fetch( )  ) {
            
            $row = array();
            foreach ( $this->_columnHeaders as $key => $value ){
                
                $row[$key] = $dao->$key;
                $pledge_data[$dao->civicrm_pledge_id][$dao->payment_id][$key] = $dao->$key;	  
            }
            
            foreach ( $tableHeader as $key => $value ) {
                
                $pledge_data[$dao->civicrm_pledge_id][$dao->payment_id][$key] = $dao->$key;
            }
            if( !($check_id == $dao->civicrm_pledge_id) ) {
                
                $check_id        = $dao->civicrm_pledge_id;
                $rows[$check_id] = $row;
            } 
            
        }
        
        foreach ( $tableHeader as $k => $val ) {
            $this->_columnHeaders[$k] = $val;
        } 
        
        foreach ( $pledge_data as $pledge_id => $data ) {
            $count = $due = $paid = 0;	
            foreach ( $data as $payment_id => $payment_info ) {
                
                if ( $payment_info['status_id'] == 2 ) {
                    $due = $due + $payment_info['scheduled_amount'];
                    if ( $count == 0 ){
                        $first_date = $payment_info['scheduled_date'];
                        $payment[$pledge_id]['scheduled_date'  ] = $first_date;
                        $payment[$pledge_id]['scheduled_amount'] = $payment_info['scheduled_amount'];
                    } else {
                        if ( $first_date > $payment_info['scheduled_date'] ) {
                            $first_date = $payment_info['scheduled_date'];
                            $payment[$pledge_id]['scheduled_date'  ] = $first_date; 
                            $payment[$pledge_id]['scheduled_amount'] = $payment_info['scheduled_amount'];
                        }
                    }
                    $count++;
                } else if ( $payment_info['status_id'] == 1 ) {
                    $paid = $paid + $payment_info['scheduled_amount'];
                }
                
                $payment[$pledge_id]['total_paid' ] = $paid;
                $payment[$pledge_id]['balance_due'] = $due;
            }
        }
        
        foreach ( $payment as $pledge_id => $pay_data){
            foreach ($pay_data as $k => $val) {
                $rows[$pledge_id][$k] = $val;
            }
        }
        unset($this->_columnHeaders['status_id']);
        unset($this->_columnHeaders['civicrm_pledge_contact_id']);
    }
    
    function postProcess( ) {
        
        $this->beginPostProcess( );
        $sql  = $this->buildQuery( false );
        
        $rows = $graphRows = array();
        $this->buildRows ( $sql, $rows );
        
        $this->formatDisplay( $rows );
        $this->doTemplateAssignment( $rows );
        $this->endPostProcess( );	
    }
    
    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $entryFound = false;
        $checkList  =  array();  
        foreach ( $rows as $rowNum => $row ) {
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 array_key_exists('civicrm_pledge_contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/report/contact/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_pledge_contact_id'] );
                $rows[$rowNum]['civicrm_contact_display_name'] = "<a href='$url'>" . 
                    $row['civicrm_contact_display_name'] . '</a>';
                $entryFound = true;
            }
            
            //handle status id
            if ( array_key_exists( 'civicrm_pledge_status_id', $row ) ) {
                if ( $value = $row['civicrm_pledge_status_id'] ) {
                    $rows[$rowNum]['civicrm_pledge_status_id'] = 
                        CRM_Core_OptionGroup::getLabel( 'contribution_status', $value );
                }
                $entryFound = true;
            } 
            
            if ( !empty($this->_noRepeats) ) {
                // not repeat contact display names if it matches with the one 
                // in previous row
                
                $repeatFound = false;
                foreach ( $row as $colName => $colVal ) {
                    if ( is_array($checkList[$colName]) && 
                         in_array($colVal, $checkList[$colName]) ) {
                        $rows[$rowNum][$colName] = "";
                        $repeatFound = true; 
                    }
                    if ( in_array($colName, $this->_noRepeats) ) {
                        $checkList[$colName][] = $colVal;
                    }
                }
            }
            
            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
        }
    }
}
