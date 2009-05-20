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
                         array( 'display_name' => array( 'title'     => ts( 'Participant Name' ),
                                                         'required'  => true,
                                                         'no_repeat' => true ),
                                ),
                         
                         'filters' =>             
                         array('sort_name'    => 
                               array( 'title'      => ts( 'Participant Name' ),
                                      'operator'   => 'like' ), ),
                         ),
                  
                  'civicrm_participant' =>
                  array( 'dao'       => 'CRM_Event_DAO_Participant',
                         'fields'    =>
                         array( 'participant_id' =>  array( 'no_display' => true,
                                                            'required'   => true, ),
                                'event_id'       => null,
                                'status_id'      => array( 'title' => ts('Status') ),
                                'role_id'        => array( 'title' => ts('Role') ),
                                'fee_amount'     => array( 'title' => ts('Fee Amount') ),                                         
                                'register_date'  => array( 'title' => ts('Registration Date') ),
                                ), 
                         'grouping'  => 'event-fields',
                         'filters'   =>             
                         array( 'sid'           =>  array( 'name'    => 'status_id',
                                                           'title'   => ts( 'Participant Status' ),
                                                           'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                                           'options' => CRM_Event_PseudoConstant::participantStatus( ) ), 
                                'rid'           =>  array( 'name'    => 'role_id',
                                                           'title'   => ts( 'Participant Role' ),
                                                           'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                                           'options' => CRM_Event_PseudoConstant::participantRole( ) ),
                                'register_date' =>  array( 'title'   => ' Registration Date',
                                                           'type'    => CRM_Utils_Type::T_DATE),
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
                         array('title'        => 
                               array( 'title'      => ts( 'Event' ),
                                      'operator'   => 'like' ),
                               
                               'eid' =>  array( 'name'    => 'event_type_id',
                                                'title'   => ts( 'Event Type' ),
                                                'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
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
        $this->assign( 'reportTitle', ts('Participant Listing Summary Report' ) );
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

    function statistics( &$rows ) {
        $statistics   = array();
        
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
        $this->_params = $this->controller->exportValues( $this->_name );
        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;
        
        $this->processReportMode( );

        $this->select  ( );

        $this->from    ( );

        $this->where   ( );

        $this->groupBy ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";
        
        $dao   = CRM_Core_DAO::executeQuery( $sql );
        $rows  = $graphRows = array();
        $count = 0;
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row[$key] = $dao->$key;
            }
            $rows[] = $row;
        }
        $this->formatDisplay( $rows );

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        $this->assign( 'statistics', $this->statistics( $rows ) );
        parent::postProcess( );
    }
    
    function alterDisplay( &$rows ) {
        // custom code to alter rows
        
        $entryFound = false;
        
        $eventType  = CRM_Core_OptionGroup::values('event_type');
        
        foreach ( $rows as $rowNum => $row ) {
            // make count columns point to detail report
            // convert display name to links
            if ( array_key_exists('civicrm_participant_event_id', $row) ) {
                if ( $value = $row['civicrm_participant_event_id'] ) {
                    $rows[$rowNum]['civicrm_participant_event_id'] = 
                        CRM_Event_PseudoConstant::event( $value, false );             
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