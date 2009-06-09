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

class CRM_Report_Form_Event_ParticipantListing extends CRM_Report_Form {

    protected $_summary = null;

    
    function __construct( ) {
        $this->_columns = 
            array( 
                  'civicrm_contact' =>
                  array( 'dao'     => 'CRM_Contact_DAO_Contact',
                         'fields'  =>
                         array( 'display_name' => 
                                array( 'title'     => ts( 'Participant Name' ),
                                       'required'  => true,
                                       'no_repeat' => true ),
                                ),
                         
                         'filters' =>             
                         array('sort_name'     => 
                               array( 'title'      => ts( 'Participant Name' ),
                                      'operator'   => 'like' ), ),
                         ),

                  'civicrm_email'   =>
                  array( 'dao'     => 'CRM_Core_DAO_Email',
                         'fields'  =>
                         array( 'email' => 
                                array( 'title'     => ts( 'Email' ),
                                       'no_repeat' => true 
                                       ),
                                ), 
                         ),
                  
                  'civicrm_participant' =>
                  array( 'dao'     => 'CRM_Event_DAO_Participant',
                         'fields'  =>
                         array( 'participant_id'   => array( 'no_display' => true,
                                                             'required'   => true, ),
                                'event_id'         => array( 'default' => true ),
                                'status_id'        => array( 'title'   => ts('Status'),
                                                             'default' => true ),
                                'role_id'          => array( 'title'   => ts('Role'),
                                                             'default' => true ),
                                'fee_amount'       => array( 'title'   => ts('Fee Amount'),
                                                             'type'    => CRM_Utils_Type::T_MONEY ),
                                'register_date'    => array( 'title'   => ts('Registration Date') ),
                                ), 
                         'grouping' => 'event-fields',
                         'filters'  =>             
                         array( 'event_id'         =>  array( 'name'    => 'event_id',
                                                             'title'   => ts( 'Event' ),
                                                              'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                                              'options' => CRM_Event_PseudoConstant::event( ) ), 
                                
                                'sid'              =>  array( 'name'    => 'status_id',
                                                              'title'   => ts( 'Participant Status' ),
                                                              'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                                              'options' => CRM_Event_PseudoConstant::participantStatus( ) ), 
                                'rid'              =>  array( 'name'    => 'role_id',
                                                              'title'   => ts( 'Participant Role' ),
                                                              'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                                              'options' => CRM_Event_PseudoConstant::participantRole( ) ),
                                'register_date'    =>  array( 'title'   => ' Registration Date',
                                                              'operatorType' => CRM_Report_Form::OP_DATE ),
                                ),
                         
                         'group_bys' => 
                         array( 'event_id' => 
                                array( 'title' => ts( 'Event' ), ), ),            
                         ),
                  
                  'civicrm_event' =>
                  array( 'dao'        => 'CRM_Event_DAO_Event',
                         'fields'     =>
                         array( 
                               'event_type_id' => array( 'title' => ts('Event Type') ), 
                               'fee_label'     => array( 'title' => ts('Fee Label') ),
                               ),
                         'grouping'  => 'event-fields', 
                         'filters'   =>             
                         array(                      
                               'eid' =>  array( 'name'    => 'event_type_id',
                                                'title'   => ts( 'Event Type' ),
                                                'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                                'options' => CRM_Core_OptionGroup::values('event_type') ), 
                               ),
                         'group_bys' => 
                         array( 'event_type_id'      => 
                                array( 'title'      => ts( 'Event Type ' ), ), ),
                         ),                
                  
                  'civicrm_address'     =>
                  array( 'dao'          => 'CRM_Core_DAO_Address',
                         'fields'       =>
                         array( 'street_address' => null,                                
                                ),
                         'grouping'     => 'event-fields',
                         ),

                  'civicrm_email' => 
                  array( 'dao'     => 'CRM_Core_DAO_Email',                         
                         'filters' =>
                         array( 'email' => 
                                array( 'title'    => ts( 'Participant E-mail' ),
                                       'operator' => 'like' ) ),
                         ),
                  );
        
        
        parent::__construct( );
    }
    
    function preProcess( ) {
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

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }
    
    static function formRule( &$fields, &$files, $self ) {  
        $errors = $grouping = array( );
        return $errors;
    }
    
    function from( ) {
        $this->_from = "
        FROM civicrm_participant {$this->_aliases['civicrm_participant']}
             LEFT JOIN civicrm_event {$this->_aliases['civicrm_event']} 
                    ON ({$this->_aliases['civicrm_event']}.id = {$this->_aliases['civicrm_participant']}.event_id )
             LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact']} 
                    ON ({$this->_aliases['civicrm_participant']}.contact_id  = {$this->_aliases['civicrm_contact']}.id  )
             LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']}
                    ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND 
                       {$this->_aliases['civicrm_address']}.is_primary = 1 
             LEFT JOIN  civicrm_email {$this->_aliases['civicrm_email']} 
                    ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND
                       {$this->_aliases['civicrm_email']}.is_primary = 1) ";
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
            $this->_where = "WHERE ( 1 ) ";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $clauses );
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
                            $this->_groupBy[] = $field['dbAlias'];
                        }
                    }
                }
            }
            $this->_groupBy = "ORDER BY " . implode( ', ', $this->_groupBy ) ;
        } else {
            $this->_groupBy = "GROUP BY contact.id";
        }
    }

    function postProcess( ) {

        // get ready with post process params
        $this->beginPostProcess( );

        // build query
        $sql = $this->buildQuery( true );

        // build array of result based on column headers. This method also allows 
        // modifying column headers before using it to build result set i.e $rows.
        $this->buildRows ( $sql, $rows );

        // format result set. 
        $this->formatDisplay( $rows );

        // assign variables to templates
        $this->doTemplateAssignment( $rows );

        // do print / pdf / instance stuff if needed
        $this->endPostProcess( );

      
    }
    
    function alterDisplay( &$rows ) {
        // custom code to alter rows
        
        $entryFound = false;
        $hoverEvent = ts("View Event Income Details for this Event");
        $eventType  = CRM_Core_OptionGroup::values('event_type');
        
        foreach ( $rows as $rowNum => $row ) {
            // make count columns point to detail report
            // convert display name to links
            if ( array_key_exists('civicrm_participant_event_id', $row) ) {
                if ( $value = $row['civicrm_participant_event_id'] ) {
                    $eventTitle= 
                        CRM_Event_PseudoConstant::event( $value, false );  
                    $url = CRM_Utils_System::url( 'civicrm/report/event/eventIncome', 
                                                  'reset=1&force=1&id_value='.$value,
                                                  $this->_absoluteUrl );
                    $rows[$rowNum]['civicrm_participant_event_id'] ="<a title='{$hoverEvent}' href='{$url}'>".$eventTitle."</a>";


            
                }
                $entryFound = true;
            }
            
            // handle event type id
            if ( array_key_exists('civicrm_event_event_type_id', $row) ) {
                if ( $value = $row['civicrm_event_event_type_id'] ) {
                    $rows[$rowNum]['civicrm_event_event_type_id'] = $eventType[$value];
                }
                $entryFound = true;
            }
            
            // handle participant status id
            if ( array_key_exists('civicrm_participant_status_id', $row) ) {
                if ( $value = $row['civicrm_participant_status_id'] ) {
                    $rows[$rowNum]['civicrm_participant_status_id'] = 
                        CRM_Event_PseudoConstant::participantStatus( $value, false );
                }
                $entryFound = true;
            }
            
            // handle participant role id
            if ( array_key_exists('civicrm_participant_role_id', $row) ) {
                if ( $value = $row['civicrm_participant_role_id'] ) {
                    $rows[$rowNum]['civicrm_participant_role_id'] = 
                        CRM_Event_PseudoConstant::participantRole( $value, false );
                }
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