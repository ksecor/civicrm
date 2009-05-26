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

class CRM_Report_Form_Contribute_Sybunt extends CRM_Report_Form {

    protected $_summary = null;

    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 

                  'civicrm_contribution' =>
                  array(  'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'fields'        =>
                          array(  'contact_id'  => 
                                  array( 'title'      => ts( 'contactId' ),
                                         'no_display' => true,
                                         'required'   => true,
                                         'no_repeat'  => true, ) ,
                                  
                                  'total_amount'  => 
                                  array( 'title'      => ts( 'Total Amount' ),
                                         'no_display' => true,
                                         'required'   => true,
                                         'no_repeat'  => true,),
                                  
                                  'receive_date'  => 
                                  array( 'title'      => ts( 'Year' ),
                                         'no_display' => true,
                                         'required'   => true,
                                         'no_repeat'  => true, ) ,                                  
                                  ),
                          
                          'group_bys'     =>
                          array( 'receive_date'  =>  
                                 array('title'      => ts( 'Receive Date' ),
                                       'required'   => true ), 
                                 
                                 'contact_id'    => 
                                 array( 'title'     => ts( 'Contact ID' ),
                                        'required'  => true ), 
                                 ) , 
                          ) ,                  
                  
                  'civicrm_contact'  =>
                  array( 'dao'       => 'CRM_Contact_DAO_Contact',
                         'fields'    =>
                         array(  'display_name'      => 
                                 array( 'title'      => ts( 'Donor Name' ),
                                        'required'   => true,
                                        'no_repeat ' => true,
                                        'no_display' => true,),                                 
                                 ), 
                         
                         'filters'        =>             
                         array( 'sort_name'   => 
                                array( 'title'      =>  ts( 'Donor Name' ),
                                       'operator'   => 'like', ), ),   
                         ),
                  'civicrm_email'    =>
                  array( 'dao'       => 'CRM_Core_DAO_Email',
                         'fields'    =>
                         array( 'email'   => 
                                array( 'title'      => ts( 'Email' ),
                                       'no_display' => true,
                                       'required'   => true,
                                       'no_repeat'  => true, ) ,
                                ), 
                         ), 
                  
                  'civicrm_contribution_type' =>
                  array( 'dao'                => 'CRM_Contribute_DAO_ContributionType',
                         'filters'            =>             
                         array( 'name'   => 
                                array( 'title'      =>  ts( 'Contribution Type' ),
                                       'operator'   => 'like' ), ),                                       
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
        $this->assign( 'reportTitle', ts('Some Year But Unfortunately not This Year' ) );        
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
                        
                        if( $fieldName == 'total_amount') {
                            
                            $current_year    = date ( 'Y' ) ;
                            
                            $previous_year   = $current_year - 1;
                            
                            $previous_pyear  = $current_year - 2;
                            
                            $previous_ppyear = $current_year - 3;                            
                            
                            $select[ ]       = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}"; 
                            
                            $this->_columnHeaders[ "{$previous_ppyear}" ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$previous_ppyear}" ][ 'title']  = $previous_ppyear;
                            
                            $this->_columnHeaders[ "{$previous_pyear}"  ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$previous_pyear}"  ][ 'title']  = $previous_pyear;
                            
                            $this->_columnHeaders[ "{$previous_year}"   ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$previous_year}"   ][ 'title']  = $previous_year;                            
                            
                            $this->_columnHeaders[ "{$current_year}"    ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$current_year}"    ][ 'title']  = $current_year;
                            
                        } else if ( $fieldName == 'receive_date' ) {                            
                            
                            $select[ ] = "Year({$field[ 'dbAlias' ]} ) as {$tableName}_{$fieldName}"; 
                            
                        } else if ( $fieldName  == 'contact_id' ) { 
                            
                            $select[ ] = "Distinct( {$field['dbAlias']} ) as {$tableName}_{$fieldName}"; 
                            
                        } else { 
                            
                            $select[ ] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders[ "{$tableName}_{$fieldName}" ][ 'type'  ] = $field[ 'type'  ];
                            $this->_columnHeaders[ "{$tableName}_{$fieldName}" ][ 'title' ] = $field[ 'title' ];
                            
                        }                      
                        
                    }
                }
            }
        }
        
        ksort( $this->_columnHeaders );
        
        
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }
    
    static function formRule( &$fields, &$files, $self ) {  
        $errors = $grouping = array( );
        
        //check for searching combination of dispaly columns and
        //grouping criteria
        
        if ( $fields[ 'group_bys' ][ 'receive_date' ] ) {
            
            foreach ( $self->_columns as $tableName => $table ) {
                
                if ( array_key_exists( 'fields', $table ) ) {
                    
                    foreach ( $table['fields'] as $fieldName => $field ) {
                        
                        if ( $fields['fields'][$field['name'] ] && 
                             in_array( $field[ 'name' ], array( 'display_name', 'contribution_source', 'contribution_type' ) ) ) {
                            $grouping[] = $field[ 'title' ];
                            
                        }
                    }
                }
            }
            
            if ( !empty( $grouping ) ) {
                
                $temp = 'and '. implode( ', ', $grouping );
                $errors[ 'fields' ] = ts( "Please Do not use combination of received date %1", array( 1 => $temp ) );    
            }
        }
        
        if ( !$fields['group_bys']['receive_date'] ) {
            if ( CRM_Utils_Date::isDate( $fields['receive_date_from'] ) || CRM_Utils_Date::isDate( $fields['receive_date_to'] ) ) {
                
                $errors['receive_date_relative'] = ts("Do not use filter on Date if group by received date not used ");      
            }
        }
        
        return $errors;
    }
    
    function from( ) {
        
        
        $this->_from = "
  FROM  civicrm_contribution  {$this->_aliases['civicrm_contribution']}
  LEFT  JOIN civicrm_contact {$this->_aliases['civicrm_contact']} 
        ON  {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id  AND 
        YEAR({$this->_aliases['civicrm_contribution']}.receive_date) IN (Year(CURDATE()),Year(CURDATE())-1, Year(CURDATE())-2, Year(CURDATE())-3 )
  LEFT  JOIN civicrm_email  {$this->_aliases['civicrm_email']} 
        ON  {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id
        AND {$this->_aliases['civicrm_email']}.is_primary = 1 " ;
        
    }
    
    function where( ) {
        
        $clauses = array( );
        
        foreach ( $this->_columns as $tableName => $table ) {
            
            if ( array_key_exists( 'filters' , $table) ) {
                
                foreach ( $table['filters'] as $fieldName => $field ) {
                    
                    $clause = null;
                    if ( $field[ 'type' ] & CRM_Utils_Type::T_DATE ) {
                        
                        $relative = CRM_Utils_Array::value(  "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value(  "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value(  "{$fieldName}_to"      , $this->_params );
                        
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
                                                    CRM_Utils_Array::value( "{$fieldName}_min"  , $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_max"  , $this->_params ) );
                        }
                    }
                    
                    if ( ! empty( $clause ) ) {
                        $clauses[ ] = $clause;
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
        
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            if ( $fieldName == 'receive_date' && ( $this->_params['receive_date_relative'] == 0 ) ) {
                                $fromdate = $todate = null;
                                if ( CRM_Utils_Date::isDate( CRM_Utils_Array::value( "receive_date_from", $this->_params ) ) ) {
                                    $revDate  = array_reverse( $this->_params['receive_date_from'] );
                                    $fromdate = ts('From') . " ".CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $revDate, '-' ) );
                                }
                                if ( CRM_Utils_Date::isDate( CRM_Utils_Array::value( "receive_date_to", $this->_params  ) ) ) {
                                    $revDate  = array_reverse( $this->_params['receive_date_to'] );
                                    $todate = ts('To') ." ". CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $revDate, '-' ) );
                                }
                                $combinations[] = $field['title']. $fromdate . $todate ;
                            } else {
                                $combinations[] = $field['title'];
                            }
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
        if ( is_array( $this->_params['group_bys'] ) && 
             !empty  ( $this->_params['group_bys'] ) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                
                if ( array_key_exists('group_bys', $table) ) {

                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            
                            if($fieldName=='receive_date') {
                                
                                $this->_groupBy[ ] = "Year(".$field['dbAlias'].")";
                                $this->_orderBy    = " Order BY Year(".$field['dbAlias'].")";
                                
                            } else {
                                
                                $this->_groupBy[ ] = $field['dbAlias'];
                                
                            }
                            
                        }
                    }
                }
            }
        }  
        
        $this->_groupBy = "Group BY " . implode( ', ', $this->_groupBy ) . $this->_orderBy;  
        
    }
    
    function postProcess( ) 
    {
        $this->_params = $this->controller->exportValues( $this->_name );
        
        if ( empty( $this->_params ) && $this->_force ) {
            
            $this->_params = $this->_formValues;
        }
        
        $this->_formValues = $this->_params ;
        
        $this->processReportMode( );
        
        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );
        $this->limit   ( );
        
        
        $sql          = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}{$this->_limit}";
        
        $current_year = date('Y');
        
        $dao          = CRM_Core_DAO::executeQuery( $sql );
        $rows         = $graphRows = array();
        $count        = 0;
        
        
        while ( $dao->fetch( ) ) {
            $row = array( );          
            $contact_id = $dao->civicrm_contribution_contact_id;            
            $year       = $dao->civicrm_contribution_receive_date;          
            $display[ $contact_id ][ $year ]                            = $dao->civicrm_contribution_total_amount ;            
            $display[ $contact_id ]['civicrm_contact_display_name']     = $dao->civicrm_contact_display_name ;             
            $display[ $contact_id ]['civicrm_email_email']              = $dao->civicrm_email_email ;            
            
            if(isset( $display[ $contact_id ][ $current_year ] ) )  {
                
                unset( $display[ $contact_id ] ) ;                
                
            }
        }
        
        $this->assign( 'columnHeaders', $this->_columnHeaders );
        
        foreach( $display as $key => $value ) {  
            
            $row = array( );            
            
            foreach ( $this->_columnHeaders as $column_key => $column_value ) {
                
                $row[ $column_key ] = $value [ $column_key ];
                
            }
            
            $rows [ ]  = $row ;
        }
        
        $this->formatDisplay( $rows );        
        $this->assign_by_ref( 'rows', $rows );
        
        require_once 'CRM/Utils/PChart.php';
        
        if ( CRM_Utils_Array::value( 'charts', $this->_params ) ) {
            
            foreach ( array ( 'receive_date' , $this->_interval , 'value' ) as $ignore ) {
                
                unset( $graphRows[$ignore][$count-1] );
            }
            
            $graphs = CRM_Utils_PChart::chart( $graphRows, $this->_params['charts'], $this->_interval );
            $this->assign( 'graphFilePath', $graphs['0']['file_name'] );
            
        }
        
        parent::endPostProcess( );
        
    }   
    
}
