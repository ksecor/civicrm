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
    
      
    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $yearsInPast      = 8;
        $yearsInFuture    = 2;
        $date             = CRM_Core_SelectValues::date('custom', $yearsInPast, $yearsInFuture, $dateParts ) ;        
        $count            = $date['maxYear'];
        while ( $date['minYear'] <= $count )  {
            $optionYear[ $date['minYear'] ] = $date['minYear'];
            $date['minYear']++;
        } 

        $this->_columns = 
            array( 'civicrm_contact'  =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'grouping'  => 'contact-field',
                          'fields'    =>
                          array(  'display_name'      => 
                                  array( 'title'      => ts( 'Donor Name' ),
                                         'default'   => true,
                                         'required'   => true
                                         ),                                 
                                  ), 
                          
                          'filters'        =>             
                          array( 'sort_name'   => 
                                 array( 'title'      =>  ts( 'Donor Name' ),
                                        'operator'   => 'like', ), ),   
                          ),
                   
                   'civicrm_email'    =>
                   array( 'dao'       => 'CRM_Core_DAO_Email',
                          'grouping'  => 'contact-field',
                          'fields'    =>
                          array( 'email'   => 
                                 array( 'title'      => ts( 'Email' ),
                                        'default' => true,
                                        ),
                                 ), 
                          ),                  
                   'civicrm_phone'    =>
                   array( 'dao'       => 'CRM_Core_DAO_Phone',
                          'grouping'  => 'contact-field',
                          'fields'    =>
                          array( 'phone'   => 
                                 array( 'title'      => ts( 'Phone No' ),
                                        'default' => true,
                                        ),
                                 ), 
                          ),  
                   
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
                                          'no_repeat'  => true,
                                         
                                          ),
                                   
                                   'receive_date'  => 
                                   array( 'title'      => ts( 'Year' ),
                                          'no_display' => true,
                                          'required'   => true,
                                          'no_repeat'  => true,
                                         
                                          ),
                                   
                                   ),
                           'filters'        =>             
                           array(  'yid'         =>  
                                   array( 'name'    => 'receive_date',
                                          'title'   => ts( 'This Year' ),
                                          'operatorType' => CRM_Report_Form::OP_SELECT,
                                          // 'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_BOOLEAN,
                                          'options' => $optionYear,
                                          'default' => date('Y') ,
                                          'clause'  => "contribution.contact_id NOT IN
(SELECT distinct cont.id FROM civicrm_contact cont, civicrm_contribution contri
 WHERE  cont.id = contri.contact_id AND YEAR (contri.receive_date) >= \$value)"
                                          ), 
                                   ),                            
                           ) , 
                 
                   
                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Group' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
                   );        
        
        parent::__construct( );
        
    }
    
    function preProcess( ) {
        parent::preProcess( );
        
    }

    function select( ) {
        
        $select = array( );
        $this->_columnHeaders = array( );
        $current_year    =  $this->_params['yid_value'] ;
        $previous_year   = $current_year - 1;        
       
                
        foreach ( $this->_columns as $tableName => $table ) {
            
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {

                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {                        
                        if( $fieldName == 'total_amount') {                            
                            $select[ ]         = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}"; 
                            $selectLifeTime[ ] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}";  
                                                     
                            $this->_columnHeaders[ "{$previous_year}"   ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$previous_year}"   ][ 'title']  = $previous_year;
                            
                            $this->_columnHeaders[ "{$current_year}"    ][ 'type' ]  = $field[ 'type' ];
                            $this->_columnHeaders[ "{$current_year}"    ][ 'title']  = $current_year;
                            
                            $this->_columnHeaders[ "civicrm_life_time_total"    ][ 'type' ]  = $field[ 'type' ] ;
                            $this->_columnHeaders[ "civicrm_life_time_total"    ][ 'title']  = 'LifeTime' ;;
                            
                        } else if ( $fieldName == 'receive_date' ) {                                                        
                            $select[ ] = " Year ( {$field[ 'dbAlias' ]} ) as {$tableName}_{$fieldName} ";                             
                        } else if ( $fieldName  == 'contact_id' ) { 
                            $select[ ] = "{$field['dbAlias']} as {$tableName}_{$fieldName}"; 
                            $selectLifeTime[ ]  = "{$field['dbAlias']} as {$tableName }_{$fieldName} "; 
                        } else {                             
                            $select[ ]          = "{$field['dbAlias']} as {$tableName }_{$fieldName} ";
                            $this->_columnHeaders[ "{$tableName}_{$fieldName}" ][ 'type'  ] = $field[ 'type'  ];
                            $this->_columnHeaders[ "{$tableName}_{$fieldName}" ][ 'title' ] = $field[ 'title' ];
                            
                        }                      
                        
                    }
                }
            }
        }
        
       // ksort( $this->_columnHeaders );
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
    
    function from( $year = null, $yearColumn = false ) {
         if ( ! $year ) {
          $year = $this->_params['yid_value'];
                }

        $yearClause = $yearColumn ? "AND YEAR({$this->_aliases['civicrm_contribution']}.receive_date) IN ( {$this->_params['yid_value']} - 1 )" : '';

        $yearClause .= " AND YEAR({$this->_aliases['civicrm_contribution']}.receive_date) < $year";



          
        $this->_from = "
        FROM  civicrm_contribution  {$this->_aliases['civicrm_contribution']}
              INNER JOIN civicrm_contact {$this->_aliases['civicrm_contact']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id $yearClause 

              LEFT  JOIN civicrm_email  {$this->_aliases['civicrm_email']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND
                         {$this->_aliases['civicrm_email']}.is_primary = 1 
              LEFT  JOIN civicrm_phone  {$this->_aliases['civicrm_phone']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND
                         {$this->_aliases['civicrm_phone']}.is_primary = 1 

              LEFT  JOIN civicrm_group_contact  group_contact 
                      ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id  AND group_contact.status='Added'

              LEFT  JOIN civicrm_group  {$this->_aliases['civicrm_group']} 
                      ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id " ;
                
    }
    
    function where( $IN = NULL ) {
        $this->_where = "";  
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
        
        if ( $IN != NULL ) {
            $clauses[] = "contribution.contact_id IN  $IN ";
        }
        
        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ( 1 ) ";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $clauses );
        }
    }
    
    
    function groupBy( $receiveDate = false ) {
        $this->_groupBy = $receiveDate ? "Group BY Year(contribution.receive_date), contribution.contact_id" : 
            "Group BY contribution.contact_id";  
        $this->assign( 'displayChart', true );
    }

    function statistics( &$rows ) {
        $statistics = parent::statistics( $rows );
        
        $select = "
        SELECT 
               SUM(contribution.total_amount ) as amount 
        ";
        $sql = "{$select} {$this->lifeTime_from } {$this->lifeTime_where}";
        $dao = CRM_Core_DAO::executeQuery( $sql );
        if ( $dao->fetch( ) ) {
            $statistics['counts']['amount'] = array( 'value' => $dao->amount,
                                                     'title' => 'Total LifeTime' );
        }
        return $statistics;
    }
    
    function postProcess( ) {
        
        // get ready with post process params
        $this->beginPostProcess( );        
        $this->select ( );
        $this->from   ( null, true );
        $this->where  ( ); 
        $this->groupBy( true );   
        $this->limit( );  
        
        
        $sql   = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}{$this->_limit}";
        $dao   = CRM_Core_DAO::executeQuery( $sql );       
        $rows  = array( );
        $count = 0;     
        $this->setPager( );
        $this->assign ( 'columnHeaders', $this->_columnHeaders );
        $IN = " ( ";
        while ( $dao->fetch( ) ) {

            $row        = array( );         
            $contact_id = $dao->civicrm_contribution_contact_id;            
            $year       = $dao->civicrm_contribution_receive_date;      
            $display[ $contact_id ][ $year ]                            =  $dao->civicrm_contribution_total_amount ;            
            $display[ $contact_id ]['civicrm_contact_display_name']     =  $dao->civicrm_contact_display_name ;             
            $display[ $contact_id ]['civicrm_email_email']              =  $dao->civicrm_email_email ; 
            $display[ $contact_id ]['civicrm_phone_phone']              =  $dao->civicrm_phone_phone ;                       
            $IN.= "{$contact_id},";            
        }
 
        $IN = substr( $IN, 0 ,-1 ) . " ) ";
        $dao->free( );        
   
        //Build LifeTime Query
        $this->from   ( );
        $this->where  ( $IN );
        $this->groupBy( false );       
        
        $sqlLifeTime  = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} ";             
        //use from and where clauses of LifeTime for Statistics
        $this->lifeTime_from  = $this->_from;
        $this->lifeTime_where = $this->_where;
        
        $current_year = $this->_params['yid_value'] ; 
        $dao_lifeTime = CRM_Core_DAO::executeQuery( $sqlLifeTime );       
            
        while ( $dao_lifeTime->fetch( ) ) {
            
            $contact_id                                                 = $dao_lifeTime->civicrm_contribution_contact_id;         
            $display[ $contact_id ]['civicrm_life_time_total']          = $dao_lifeTime->civicrm_contribution_total_amount;
        }
   
        $dao_lifeTime->free( );   
             
        if( ! empty($display) ) {
            foreach( $display as $key => $value ) {                
                $row = array( );  
                foreach ( $this->_columnHeaders as $column_key => $column_value ) {
                    $row[ $column_key ] = $value [ $column_key ];                   
                } 
                
                $rows [ ]  = $row;
            }     
        }
        // format result set. 
        $this->formatDisplay( $rows, false );
        
        // assign variables to templates
        $this->doTemplateAssignment( $rows );
        
        // do print / pdf / instance stuff if needed
        $this->endPostProcess( );   
        
        
    }   

    function buildChart( &$rows ) {
        
        $graphRows                = array();
        $count                    = 0;
        
        $current_year             = $this->_params['yid_value'];
        $previous_year            =  $current_year - 1 ;
        $interval[$previous_year] = $previous_year ;
        $interval['life_time']    = 'life_time' ; 
        
        foreach ( $rows as $key => $row ) {
            $display['life_time']                   =  $display[ 'life_time' ] + $row[ 'civicrm_life_time_total' ];           
            $display[ $previous_year ]              =  $display[ $previous_year ] + $row [ $previous_year ];                    
        }
        
        $graphRows['value'] = $display;
        $chartInfo          = array( 'legend' => 'Lybunt Report',
                                     'xname'  => 'Amount',
                                     'yname'  => 'Year'
                                     );
        if($this->_params['charts']) {
            $graphs = CRM_Utils_PChart::reportChart( $graphRows, $this->_params['charts'] , $interval , $chartInfo );
            $this->assign( 'graphFilePath', $graphs['0']['file_name'] );
            $this->_graphPath =  $graphs['0']['file_name'];
        }        
    }  
}
