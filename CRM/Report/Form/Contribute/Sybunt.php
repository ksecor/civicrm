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

       protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    

    function __construct( ) {
        $yearsInPast      = 8;
        $yearsInFuture    = 2;
        $dateParts        = implode( CRM_Core_DAO::VALUE_SEPARATOR, array( 'Y') );
        $date             = CRM_Core_SelectValues::date('custom', $yearsInPast, $yearsInFuture, $dateParts ) ;        
        $count            = $date['maxYear'] ;
        while ( $date['minYear'] <= $count )  {
            $optionYear[ $date['minYear'] ] = $date['minYear'];
            $date['minYear']++;
        }
        
        $this->_columns = 
            array(
                  'civicrm_contact'  =>
                  array( 'dao'       => 'CRM_Contact_DAO_Contact',
                         'grouping'  => 'contact-field',
                         'fields'    =>
                         array(  'display_name'      => 
                                 array( 'title'      => ts( 'Donor Name' ),
                                        'required'   => true ), ), 
                         
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
                                       'default'    => true,
                                       ) ,
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
                          array( 'contact_id'  => 
                                 array( 'title'      => ts( 'contactId' ),
                                        'no_display' => true,
                                        'required'   => true,
                                        'no_repeat'  => true, ) ,
                                 
                                 'total_amount'  => 
                                 array( 'title'      => ts( 'Total Amount' ),
                                        'no_display' => true,
                                        'required'   => true,
                                        'no_repeat'  => true, ),
                                 
                                 'receive_date'  => 
                                 array( 'title'      => ts( 'Year' ),
                                        'no_display' => true,
                                        'required'   => true,
                                        'no_repeat'  => true,), ),

                          'filters'       =>             
                          array(  'yid'          =>  
                                  array( 'name'    => 'receive_date',
                                         'title'   => ts( 'This Year' ),
                                         'operatorType' => CRM_Report_Form::OP_SELECT,
                                         'options' => $optionYear,
                                         'default' => date('Y'),
                                         'clause'  => "contribution.contact_id NOT IN
(SELECT distinct cont.id FROM civicrm_contact cont, civicrm_contribution contri
 WHERE  cont.id = contri.contact_id AND YEAR (contri.receive_date) >= \$value)" ),       
                                  ), ),   
                          
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
        $previous_pyear  = $current_year - 2;        
        $previous_ppyear = $current_year - 3; 
        $upTo_year       = $current_year - 4; 
       
        foreach ( $this->_columns as $tableName => $table ) {
            
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        if( $fieldName == 'total_amount') {
                            $select[ ]         = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}"; 
                            
                            $this->_columnHeaders[ "civicrm_upto_{$upTo_year}"][ 'type' ]  = $field[ 'type' ] ;
                            $this->_columnHeaders[ "civicrm_upto_{$upTo_year}"][ 'title']  = "Up To $upTo_year";

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
                            $select[ ] = "Year({$field[ 'dbAlias' ]} ) as {$tableName}_{$fieldName}"; 
                        } else { 
                            $select[ ] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders[ "{$tableName}_{$fieldName}" ][ 'type'  ] = CRM_Utils_Array::value( 'type', $field );
                            $this->_columnHeaders[ "{$tableName}_{$fieldName}" ][ 'title' ] = $field[ 'title' ];
                        }
                        if ( CRM_Utils_Array::value( 'no_display', $field ) ) {
                            $this->_columnHeaders["{$tableName}_{$fieldName}"][ 'no_display' ] = true;
                        }
                    }
                }
            }
        }
        
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }
    
    function from( $year = null, $yearColumn = false ) {        
        $yearClause = $yearColumn ? " AND YEAR({$this->_aliases['civicrm_contribution']}.receive_date) IN ( {$this->_params['yid_value']} - 1, {$this->_params['yid_value']} - 2, {$this->_params['yid_value']} - 3 )" : '';

        $this->assign( 'chartSupported', true );
        if ( $year ) {
            $yearClause .= " AND YEAR({$this->_aliases['civicrm_contribution']}.receive_date) < $year";
        }
        
        $this->_from = " 
        FROM       civicrm_contribution  {$this->_aliases['civicrm_contribution']}
                   INNER JOIN civicrm_contact {$this->_aliases['civicrm_contact']} 
                           ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id $yearClause
                   LEFT  JOIN civicrm_email  {$this->_aliases['civicrm_email']} 
                           ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id  
                          AND {$this->_aliases['civicrm_email']}.is_primary = 1
                   LEFT  JOIN civicrm_phone  {$this->_aliases['civicrm_phone']} 
                           ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND
                              {$this->_aliases['civicrm_phone']}.is_primary = 1  
                   LEFT  JOIN civicrm_group_contact  group_contact 
                           ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id  AND group_contact.status='Added'

                   LEFT  JOIN civicrm_group  {$this->_aliases['civicrm_group']} 
                           ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id
        " ;
    }
    
    function where( $min = 0, $max = 0 ) {
        $this->_where = "";
        $clauses = array( );

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists( 'filters' , $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    if ( CRM_Utils_Array::value( 'type', $field ) & CRM_Utils_Type::T_DATE ) {
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
        
        if ( ($min > 0 || $max > 0) && !empty($this->_limit) ) {
            $clauses[] = "contribution.contact_id BETWEEN $min AND $max";
        }

        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ( 1 ) ";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $clauses );
        }
    }
    
    function groupBy( $receiveDate = false ) {
        $this->assign( 'chartSupported', true );
        $this->_groupBy = $receiveDate ? "Group BY Year(contribution.receive_date), contribution.contact_id" : 
            "Group BY contribution.contact_id";  
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
                                                     'title' => 'Total LifeTime',
                                                     'type'  => CRM_Utils_Type::T_MONEY );
        }
        return $statistics;
    }
    
    function postProcess( ) 
    {
        // get ready with post process params
        $this->beginPostProcess( );
        $this->select ( );
        $this->from   ( );
        $this->where  ( );
        $this->groupBy( false );
        $this->limit( );

        $sqlLifeTime = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_limit}";

        //use from and where clauses of LifeTime for Statistics
        $this->lifeTime_from  = $this->_from;
        $this->lifeTime_where = $this->_where;

        $daoLifeTime = CRM_Core_DAO::executeQuery( $sqlLifeTime );
        $this->setPager( );
        $min = $max = 0;
        $chartRow = array( 'civicrm_life_time_total' => 0);
        while ( $daoLifeTime->fetch( ) ) {
            $contact_id = $daoLifeTime->civicrm_contribution_contact_id;
            $display[ $contact_id ]['civicrm_contribution_contact_id'] = $contact_id;
            $display[ $contact_id ][''] = 
            $display[ $contact_id ]['civicrm_life_time_total'] = 
                $daoLifeTime->civicrm_contribution_total_amount;
            $display[ $contact_id ]['civicrm_contact_display_name'] = $daoLifeTime->civicrm_contact_display_name;
            if ( isset( $daoLifeTime->civicrm_email_email ) ) {
                $display[ $contact_id ]['civicrm_email_email']          = $daoLifeTime->civicrm_email_email ;
            }
            if ( isset( $daoLifeTime->civicrm_phone_phone ) ) {
                $display[ $contact_id ]['civicrm_phone_phone']          = $daoLifeTime->civicrm_phone_phone ; 
            }
            $min = ($contact_id < $min) ? $contact_id : ($max > 0) ? $min : $contact_id;
            $max = ($contact_id > $max) ? $contact_id : $max;
            
            $chartRow['civicrm_life_time_total']  += $daoLifeTime->civicrm_contribution_total_amount;

        }

        $daoLifeTime->free( );

        $this->from   ( null, true );
        $this->where  ( $min, $max );
        $this->groupBy( true );
        $sqlYear = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";

        $this->from   ( $this->_params['yid_value'] - 3, false );
        $this->groupBy( false );
        $sqlUpTo = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";

        $daoYear = CRM_Core_DAO::executeQuery( $sqlYear ); 
        $daoUpTo = CRM_Core_DAO::executeQuery( $sqlUpTo );

        $upto = $this->_params['yid_value'] - 4;
        
        while ( $daoUpTo->fetch( ) ) {
            $contact_id = $daoUpTo->civicrm_contribution_contact_id;
            $display[ $contact_id ]["civicrm_upto_{$upto}"] =
                $daoUpTo->civicrm_contribution_total_amount;            
            $display[ $contact_id ]['civicrm_contact_display_name'] = $daoUpTo->civicrm_contact_display_name;
            if ( isset( $daoUpTo->civicrm_email_email ) ) {
                $display[ $contact_id ]['civicrm_email_email']          = $daoUpTo->civicrm_email_email ;
            }
            if ( isset( $daoUpTo->civicrm_phone_phone ) ) {
                $display[ $contact_id ]['civicrm_phone_phone']          = $daoUpTo->civicrm_phone_phone ;
            }
            $chartRow[ "civicrm_upto_{$upto}" ]      = $chartRow[ "civicrm_upto_{$upto}" ] + $daoUpTo->civicrm_contribution_total_amount;
  
        } 
        $daoUpTo->free( );

        while ( $daoYear->fetch( ) ) { 
            $contact_id = $daoYear->civicrm_contribution_contact_id;            
            $display[ $contact_id ][ $daoYear->civicrm_contribution_receive_date ] = 
                $daoYear->civicrm_contribution_total_amount ;
            $display  [ $contact_id ][ 'civicrm_contact_display_name' ]       = $daoYear->civicrm_contact_display_name;
            if (isset($daoYear->civicrm_email_email ) ) {
                $display  [ $contact_id ][ 'civicrm_email_email' ]                = $daoYear->civicrm_email_email ;
            }
            if ( isset( $daoYear->civicrm_phone_phone ) ) {
                $display[ $contact_id ]['civicrm_phone_phone']                    = $daoYear->civicrm_phone_phone ;
            } 
  
            $chartRow [ $daoYear->civicrm_contribution_receive_date ]         =  
    CRM_Utils_Array::value( $daoYear->civicrm_contribution_receive_date, $chartRow )   +     $daoYear->civicrm_contribution_total_amount ;
            
        }
        $daoYear->free( );
        $rows = array( );
        if( ! empty($display) ) {
            foreach( $display as $key => $value ) {              
                $row = array( );                        
                foreach ( $this->_columnHeaders as $column_key => $column_value ) {
                    if ( CRM_Utils_Array::value( $column_key, $value ) ) {
                        $row[ $column_key ] = $value [ $column_key ];
                    }
                }
                $rows[] = $row;
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
        $graphRows           = array();
        $count               = 0;
        $current_year        = $this->_params['yid_value'];
        $previous_year       =  $current_year - 1 ;
        $previous_two_year   =  $current_year - 2 ;
        $previous_three_year =  $current_year - 3 ;
        $upto                =  $current_year - 4 ;
  
        $interval[$previous_year]                = $previous_year ;
        $interval[$previous_two_year]            = $previous_two_year ;
        $interval[$previous_three_year]          = $previous_three_year ;
        $interval["upto_{$upto}"]   = "upto_{$upto}";
   
        foreach ( $rows as $key => $row ) {
            $display["upto_{$upto}"] =  
                         $display["upto_{$upto}"] + $row[ "civicrm_upto_{$upto}" ];
            $display[ $previous_year ]              =  $display[ $previous_year ] + $row [ $previous_year ];
            $display[ $previous_two_year ]          =  $display[ $previous_two_year ] + $row [ $previous_two_year ];
            $display[ $previous_three_year ]        =  $display[ $previous_three_year ] + $row [ $previous_three_year ];           
        }
        
        $graphRows['value'] = $display;
        $chartInfo          = array( 'legend' => 'Sybunt Report',
                                     'xname'  => 'Amount',
                                     'yname'  => 'Year'
                                     );
        if($this->_params['charts']) {
            $graphs = CRM_Utils_PChart::reportChart( $graphRows, $this->_params['charts'] , $interval , $chartInfo );
            $this->assign( 'graphFilePath', $graphs['0']['file_name'] );
            $this->_graphPath =  $graphs['0']['file_name'];
        }        
    }
    
    function alterDisplay( &$rows ) {
        
        foreach ( $rows as $rowNum => $row ) {
            //Convert Display name into link
            if ( array_key_exists('civicrm_contact_display_name', $row) &&
                 array_key_exists('civicrm_contribution_contact_id', $row) ) {
                $url = CRM_Report_Utils_Report::getNextUrl( 'contribute/detail', 
                                                            'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contribution_contact_id'],
                                                            $this->_absoluteUrl, $this->_id );
                $rows[$rowNum]['civicrm_contact_display_name_link' ] = $url;
                $rows[$rowNum]['civicrm_contact_display_name_hover'] =  
                    ts("View Contribution Details for this Contact.");
            }
        }
    }
}
