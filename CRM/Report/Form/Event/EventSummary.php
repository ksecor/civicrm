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
require_once 'CRM/Event/PseudoConstant.php';
require_once 'CRM/Core/OptionGroup.php';

class CRM_Report_Form_Event_EventSummary extends CRM_Report_Form {
    
    protected $_summary = null;
    
    function __construct( ) {
        
        $this->_columns = 
            array( 
                  'civicrm_event' =>
                  array( 'dao'    => 'CRM_Event_DAO_Event',
                         'fields' =>
                         array(
                               'id'               => array( 'no_display'=> true,
                                                            'required'  => true ),
                               'title'            => array( 'title'     => ts( 'Event Title' ),
                                                            'required'  => true ), 
                               'event_type_id'    => array( 'title'     => ts( 'Event Type' ),
                                                            'required'  => true ),
                               'fee_label'        => array( 'title'     => ts( 'Fee Label' ) ),
                               'start_date'       => array( 'title'     => ts( 'Event Start Date' ),),
                               'end_date'         => array( 'title'     => ts( 'Event End Date' ) ),
                               'max_participants' => array( 'title'     => ts( 'Capacity' ) )
                               ),
                         
                         'filters' =>             
                         array( 			 			   
                               'eid'        => array( 'name'    => 'title',
                                                      'title'   => ts( 'Event Title' ),
                                                      'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                                      'options' => CRM_Event_PseudoConstant::event() ), 
                               
                               'tid'        => array( 'name'    => 'event_type_id',
                                                      'title'   => ts( 'Event Type' ),
                                                      'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                                      'options' => CRM_Core_OptionGroup::values('event_type') ),
                               'start_date' => array( 'title'   => 'Event Start Date',
                                                      'type'    => CRM_Utils_Type::T_DATE ),
                              
                               'end_date'   => array( 'title'   => 'Event End Date',
                                                      'type'    => CRM_Utils_Type::T_DATE ), ), 	 
                         ),
                  );
     
        parent::__construct( );
    }
    
    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Event Summary Report' ) );
        parent::preProcess( );
    }
    
    function setDefaultValues( ) { 
        return parent::setDefaultValues( );
    }
    
    function select( ) {
        $select = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                    }
                }
            }
        }
        
        $this->_select = "SELECT " . implode( ', ', $select ) ;
    }
    
    function from( ) {
        $this->_from = " FROM civicrm_event {$this->_aliases['civicrm_event']} ";
    }
    
    function where( ) {
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) { 
                foreach ( $table['filters'] as $fieldName => $field ) {
                    
                    if ( $fieldName != 'eid') {
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
                    } 
                    if ( $fieldName =='eid') {
                        $clause=" {$this->_aliases['civicrm_event']}.id IN (" .implode( ',' , $this->_params['eid_value']) . " )";
                    }
                    
                    if ( ! empty( $clause ) ) {
                        $clauses[] = $clause;
                    }
                }
            }
        }
        
        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ";
        } else {
            $this->_where = "WHERE  " . implode( ' AND ', $clauses);
        }
    }
    
    function groupBy( ) {
        
        $this->_groupBy = " GROUP BY {$this->_aliases['civicrm_event']}.id";
    }
   
    function statistics( &$rows ) {
        $statistics[] = array( 'title' => ts('Row(s) Listed'),
                               'value' => count($rows) );
        return $statistics;
    }    
    
    //get participants information for events
    function participantInfo( ) {
        
        $statusType1 = CRM_Event_PseudoConstant::participantStatus( null, "filter = 1" ); 
        $statusType2 = CRM_Event_PseudoConstant::participantStatus( null, "filter = 0" ); 
        
        $sql = "
          SELECT civicrm_participant.event_id as event_id, 
                 civicrm_participant.status_id   AS statusId, 
                 COUNT( civicrm_participant.id ) AS participant, 
                 SUM( civicrm_participant.fee_amount ) AS amount

            FROM civicrm_participant 

           WHERE civicrm_participant.event_id IN (". implode( ',' , $this->_params['eid_value']) ." ) 
                 AND civicrm_participant.is_test  = 0 

        GROUP BY civicrm_participant.event_id, 
                 civicrm_participant.status_id";
        
        $info = CRM_Core_DAO::executeQuery( $sql );
      	$participant_data = array();
        
        while( $info->fetch() ) {
            $participant_data[$info->event_id][$info->statusId]['participant']= $info->participant;
            $participant_data[$info->event_id][$info->statusId]['amount']= $info->amount;          
        }
	
        $amt = $particiType1 = $particiType2 = 0; 
        
        foreach ( $participant_data as $event_id => $event_data ){
            foreach ( $event_data as $status_id => $data) {
                
                //total income of event 
                $amt = $amt + $data['amount'];
                if ( array_key_exists($status_id , $statusType1 ) ) {
                    
                    //number of Registered/Attended participants    
                    $particiType1 = $particiType1 + $data['participant'];
                } else if ( array_key_exists( $info->statusId , $statusType2 ) ) {
                    
                    //number of No-show/Cancelled/Pending participants 
                    $particiType2 = $particiType2 + $data['participant'];
                }
                
            }
            
            $participant_info[$event_id]['totalAmount'] = $amt;
            $participant_info[$event_id]['statusType1'] = $particiType1;
            $participant_info[$event_id]['statusType2'] = $particiType2;
            $amt  = $particiType1 = $particiType2 = 0;
        }
        
        return $participant_info;   
    }

    //build header for table
    function buildColumnHeaders() {
        
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type' ] = $field['type'];
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                    }
                }
            }
        }
        
        $statusType1 = CRM_Event_PseudoConstant::participantStatus( null, "filter = 1" ); 
        $statusType2 = CRM_Event_PseudoConstant::participantStatus( null, "filter = 0" ); 
        
        //make column header for participant status  Registered/Attended  
        $type1_header = implode( '/' , $statusType1 );
        
        //make column header for participant status No-show/Cancelled/Pending 
        $type2_header = implode( '/' , $statusType2 );
        
        $this->_columnHeaders['statusType1'] = array ( 'title' => $type1_header );		 
        $this->_columnHeaders['statusType2'] = array ( 'title' => $type2_header ); 
        $this->_columnHeaders['totalAmount'] = array ( 'type'  => 1024 , 
                                                       'title' => 'Total Income');  
        
    }
   
    function postProcess( ) {
      
        $this->beginPostProcess( );
        
        $this->buildColumnHeaders();
        
        //set default as all events
        if( empty($this->_params['eid_value']) ){
	  
            $default       = array();
            $defaultEvents = CRM_Event_PseudoConstant::event();
            foreach ( $defaultEvents as $event => $title ) {
                $default[] = $event;
            }
            $this->_params['eid_value'] = $default;
        }
        
        $sql = $this->buildQuery( false );
        
        $dao = CRM_Core_DAO::executeQuery( $sql );
        
        while ( $dao->fetch( ) ) {	
            $row = array();
            foreach ( $this->_columnHeaders as $key => $value ) {
                if ( ( $key == 'civicrm_event_start_date') || ($key == 'civicrm_event_end_date') ) {
                    //get event start date and end date in custom datetime format
                    $row[$key] = CRM_Utils_Date::customFormat($dao->$key);
                } else {	   
                    $row[$key] = $dao->$key;
                }
            } 
            $rows[] = $row;	  
        }
 	
        if( !empty($rows)) {
            $participant_info= $this->participantInfo();
            foreach ( $rows as $key => $value ) {
                foreach ($participant_info[$value['civicrm_event_id']] as $k => $v ) {
                    $rows[$key][$k]=$v;
                }	      
            }
            
        }
        
        $this->formatDisplay( $rows );
        unset($this->_columnHeaders['civicrm_event_id']);
        
        $this->doTemplateAssignment( $rows );
        $this->endPostProcess( );
        
    }
    
    function alterDisplay( &$rows ) {
      
        if ( is_array( $rows ) ) {
            $eventType  = CRM_Core_OptionGroup::values('event_type');
            foreach ( $rows as $rowNum => $row ) {
                if ( array_key_exists('civicrm_event_event_type_id', $row) ) {
                    if ( $value = $row['civicrm_event_event_type_id'] ) {
                        $rows[$rowNum]['civicrm_event_event_type_id'] = $eventType[$value];
                    }	  
                }
            }
        }
    }
}