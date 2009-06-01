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

class CRM_Report_Form_Contribute_Lybunt extends CRM_Report_Form {
    
    protected $_summary = null;
    
    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $yearsInPast      = 8;
        $yearsInFuture    = 1;
        $date             = CRM_Core_SelectValues::date('custom', $yearsInPast, $yearsInFuture, $dateParts ) ;        
        $count            = $date['maxYear'];
        while ( $date['minYear'] <= $count )  {
            $optionYear[ $date['minYear'] ] = $date['minYear'];
            $date['minYear']++;
        } 

        $this->_columns = 
            array( 'civicrm_contribution' =>
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
                                          'no_repeat'  => true, ) ,
                                   
                                   'receive_date'  => 
                                   array( 'title'      => ts( 'Year' ),
                                          'no_display' => true,
                                          'required'   => true,
                                          'no_repeat'  => true, ) ,
                                   
                                   ),
                           'filters'        =>             
                           array(  'yid'         =>  
                                   array( 'name'    => 'receive_date',
                                          'title'   => ts( 'Select Year' ),
                                          'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_BOOLEAN,
                                          'options' => $optionYear ), 
                                   ),                             
                           
                           'group_bys'     =>
                           array( 'receive_date'  =>  
                                  array('title'      => ts( 'Receive Date' ),
                                        'default'   => true ), 
                                  
                                  'contact_id'    => 
                                  array( 'title'     => ts( 'Contact ID' ),
                                         'default'  => true ), 
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

        $current_year    =  $this->_params['yid_value'] ;
        $previous_year   = $current_year - 1;        
        $previous_pyear  = $current_year - 2;        
        $previous_ppyear = $current_year - 3; 
                
        foreach ( $this->_columns as $tableName => $table ) {
            
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {                        
                        if( $fieldName == 'total_amount') {
                            
                            $select[ ]         = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}"; 
                            $selectLifeTime[ ] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}"; 
                            
                            $this->_columnHeaders[ "{$previous_ppyear}" ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$previous_ppyear}" ][ 'title']  = $previous_ppyear;
                            
                            $this->_columnHeaders[ "{$previous_pyear}"  ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$previous_pyear}"  ][ 'title']  = $previous_pyear;
                            
                            $this->_columnHeaders[ "{$previous_year}"   ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$previous_year}"   ][ 'title']  = $previous_year;
                            
                            $this->_columnHeaders[ "{$current_year}"    ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$current_year}"    ][ 'title']  = $current_year;
                            
                            $this->_columnHeaders[ "civicrm_life_time_total"    ][ 'type' ]  = $field[ 'type' ] ;
                            $this->_columnHeaders[ "civicrm_life_time_total"    ][ 'title']  = 'LifeTime' ;;
                            
                        } else if ( $fieldName == 'receive_date' ) {                            
                            
                            $select[ ] = " Year ( {$field[ 'dbAlias' ]} ) as {$tableName}_{$fieldName} "; 
                            
                        } else if ( $fieldName  == 'contact_id' ) { 
                            
                            $select[ ]          = " Distinct( {$field['dbAlias']} ) as {$tableName }_{$fieldName} "; 
                            $selectLifeTime[ ]  = " Distinct( {$field['dbAlias']} ) as {$tableName }_{$fieldName} "; 
                            
                        }
                        else { 
                            
                            $select[ ] = "{$field['dbAlias']} as {$tableName }_{$fieldName} ";
                            $this->_columnHeaders[ "{$tableName}_{$fieldName}" ][ 'type'  ] = $field[ 'type'  ];
                            $this->_columnHeaders[ "{$tableName}_{$fieldName}" ][ 'title' ] = $field[ 'title' ];
                            
                        }                      
                        
                    }
                }
            }
        }
        
        ksort( $this->_columnHeaders );
        $this->_select          = "SELECT  " . implode( ', ', $select ) . " ";       
        $this->_selectLifeTime  = "SELECT " . implode( ', ', $selectLifeTime ) . " "; 
        
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
                
                $temp               = 'and '. implode( ', ', $grouping );
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

        $IN= "( {$this->_params['yid_value']},{$this->_params['yid_value']} - 1,{$this->_params['yid_value']} - 2 ,{$this->_params['yid_value']} - 3  )"   ;        
        $this->_from = "
        FROM  civicrm_contribution  {$this->_aliases['civicrm_contribution']}
              INNER JOIN civicrm_contact {$this->_aliases['civicrm_contact']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id  AND 
                         YEAR({$this->_aliases['civicrm_contribution']}.receive_date) IN ".$IN.
            " LEFT  JOIN civicrm_email  {$this->_aliases['civicrm_email']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND
                         {$this->_aliases['civicrm_email']}.is_primary = 1 
              LEFT  JOIN civicrm_group_contact  group_contact 
                      ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id  AND group_contact.status='Added'
              LEFT  JOIN civicrm_group  {$this->_aliases['civicrm_group']} 
                      ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id
        " ;
        
        $this->_fromLifeTime =" FROM  civicrm_contribution  {$this->_aliases['civicrm_contribution']} ";
        
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
    
   
    function groupBy( ) {
        
        $this->_groupBy = "";
        if ( is_array( $this->_params['group_bys'] ) && 
             !empty  ( $this->_params['group_bys'] ) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                
                if ( array_key_exists('group_bys', $table) ) {
                    
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            
                            if($fieldName=='receive_date') {
                                
                                $this->_groupBy[] = " Year(".$field['dbAlias'].")";
                                $this->_orderBy   = " Order BY Year(".$field['dbAlias'].") ";
                                
                            } else if ($fieldName=='contact_id') {  
                                
                                $this->_groupBy[ ]         = $field['dbAlias'];
                                $this->_groupByLifeTime[ ] = $field['dbAlias'];
                                
                            } else {
                                
                                $this->_groupBy[] = $field['dbAlias'];
                                
                            }
                            
                        }
                    }
                }
            }
        }  
        
        $this->_groupBy         = "Group BY " . implode( ', ', $this->_groupBy ) . $this->_orderBy;
        $this->_groupByLifeTime = "Group BY " . implode( ', ', $this->_groupByLifeTime ) ;
        
    }
    
    function postProcess( ) {
        
        // get ready with post process params
        $this->beginPostProcess( );

        // build query
        $sql          = $this->buildQuery( true );
        $sqlLifeTime  = "{$this->_selectLifeTime} {$this->_fromLifeTime} {$this->_groupByLifeTime} ";                    
        $current_year = $this->_params['yid_value'] ;  
        $dao          = CRM_Core_DAO::executeQuery( $sql );
        $dao_lifeTime = CRM_Core_DAO::executeQuery( $sqlLifeTime );
        
        while ( $dao_lifeTime->fetch( ) ) {
            
            $contact_id                = $dao_lifeTime->civicrm_contribution_contact_id;
            $life_time [ $contact_id ] = $dao_lifeTime->civicrm_contribution_total_amount;            
        }       
        
        $rows  = $graphRows = array( );
        $count = 0;               
        $this->assign ( 'columnHeaders', $this->_columnHeaders );
       
        while ( $dao->fetch( ) ) {
            $row        = array( );         
            $contact_id = $dao->civicrm_contribution_contact_id;            
            $year       = $dao->civicrm_contribution_receive_date; 
            $display[ $contact_id ][ $year ]                            = $dao->civicrm_contribution_total_amount ;            
            $display[ $contact_id ]['civicrm_contact_display_name']     = $dao->civicrm_contact_display_name ;             
            $display[ $contact_id ]['civicrm_email_email']              = $dao->civicrm_email_email ;   
            $display[ $contact_id ]['civicrm_life_time_total']          = $life_time [ $contact_id ];
                      
                  if(isset( $display[ $contact_id ][ $current_year ] ) )  {
                
                    unset( $display[ $contact_id ] ) ;                
                 }     
            
        }  
     
        if($display) {
            foreach( $display as $key => $value ) {
                
                $row = array( );  
                foreach ( $this->_columnHeaders as $column_key => $column_value ) {
                    $row[ $column_key ] = $value [ $column_key ];
                } 
                
                $rows [ ]  = $row;
            }     
        }
               
        // format result set. 
        $this->formatDisplay( $rows );
        
        $this->assign_by_ref( 'rows', $rows );
        $this->assign( 'statistics',  $this->statistics( $rows ) );
        
        // do print / pdf / instance stuff if needed
        $this->endPostProcess( );
              
    }   
    
}
