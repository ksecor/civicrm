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

class CRM_Report_Form_Contact_Detail extends CRM_Report_Form {

    protected $_summary = null;

    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contact' =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'fields'    =>
                          array( 'display_name' => 
                                 array( 'title' => ts( 'Contact Name' ),
                                        'required'  => true,
                                        'no_repeat' => true ),
                                 'id'           => 
                                 array( 'no_display'=> true,
                                        'required'  => true, ), ),
                          'filters'   =>             
                          array( 'id'           => 
                                 array( 'title'      => ts( 'Contact ID' ),
                                        'default'    => 'eq' ), ),
                          'grouping'  => 'contact-fields',
                          ),
                   
                   'civicrm_address' =>
                   array( 'dao'       => 'CRM_Core_DAO_Address',
                          'grouping'  => 'contact-fields',
                          'fields'    =>
                          array( 'street_address'    => null,
                                 'city'              => null,
                                 'postal_code'       => null,
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ),  
                                        'default' => true ), 
                                 ),
                          ),

                   'civicrm_email'   =>
                   array( 'dao'       => 'CRM_Core_DAO_Email',
                          'fields'    =>
                          array( 'email' => 
                                 array( 'title'      => ts( 'Email' ),
                                        'no_repeat'  => true 
                                        ),
                                 ), 
                          ),

                   'civicrm_contribution'   =>
                   array( 'dao'       => 'CRM_Contribute_DAO_Contribution',
                          'fields'    =>
                          array( 'contribution_id'        => 
                                 array( 'title'      => ts( 'Contribution' ),
                                        'no_repeat'  => true,
                                        'default'    => true 
                                        ),
                                 'total_amount'           => array( 'default' => true),
                                 'contribution_type_id'   => array( 'title' => ts('Contribution Type'),
                                                                    'default'    => true ),
                                 'contribution_status_id' => array( 'default' => true),
                                 'contribution_source'    => null,
                                 ), 
                          ),
                   'civicrm_membership'   =>
                   array( 'dao'       => 'CRM_Member_DAO_Membership',
                          'fields'    =>
                          array( 'membership_id'      => 
                                 array( 'title'      => ts( 'Membership' ),
                                        'no_repeat'  => true,
                                        'default'    => true 
                                        ),
                                 'membership_type_id' => array( 'default' => true ),
                                 'start_date'         => array( 'title'   => ts('Start Date'),
                                                                'default' => true ),
                                 'end_date'           => array( 'title'   => ts('End Date'),
                                                                'default' => true ),
                                 'status_id'          => null,
                                 ), 
                          ),
                   'civicrm_participant'   =>
                   array( 'dao'       => 'CRM_Event_DAO_Participant',
                          'fields'    =>
                          array( 'participant_id' => 
                                 array( 'title'      => ts( 'Participant' ),
                                        'no_repeat'  => true,
                                        'default'    => true 
                                        ),
                                 'event_id'       => array( 'default' => true),
                                 'status_id'      => array( 'title'   => ts('Status'),
                                                            'default' => true ),
                                 'role_id'        => array( 'title'   => ts('Role'),
                                                            'default' => true ),
                                 'register_date'  => array( 'title'   => ts('Registe Date'),
                                                            'default' => true ),
                                 'fee_level'      => array( 'title'   => ts('Fee Level'),
                                                            'default' => true ),
                                 'fee_amount'     => array( 'title'   => ts('Fee Amount'),
                                                            'default' => true ),
                                 ), 
                          ),
                               
                   );
        parent::__construct( );
    }
    
    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Contact Detail Report' ) );
        parent::preProcess( );


    }
    
    function select( ) {
        $select               = array( );
        $this->_columnHeaders = array( );
        $this->_component     = array( 'contribution', 'membership', 'participant' );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        //isolate the select clause compoenent wise
                        if ( in_array( $table['alias'], $this->_component ) ) {
                            $select[$table['alias']][] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeadersComponent[$table['alias']]["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                            $this->_columnHeadersComponent[$table['alias']]["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                        } else {
                            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                        }
                    }
                }
            }
        }
        foreach( $this->_component as $val ) {
            if ( CRM_Utils_Array::value( $val, $select ) ) {
                $this->_selectComponent[$val] = "SELECT " . implode( ', ', $select[$val] ) . " ";
                unset($select[$val]);
            }
        }
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    static function formRule( &$fields, &$files, $self ) {  
        $errors = array( );
        if ( !CRM_Utils_Array::value('id_value', $fields ) ) {
            $errors['id_value'] = ts('Contact ID is requied field');
        }
        return $errors;
    }

    function from( ) {
        $this->_from = "
FROM civicrm_contact {$this->_aliases['civicrm_contact']}
LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
          ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND 
              {$this->_aliases['civicrm_address']}.is_primary = 1 )
LEFT JOIN  civicrm_email {$this->_aliases['civicrm_email']} 
          ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND
              {$this->_aliases['civicrm_email']}.is_primary = 1) ";

        foreach( $this->_component as $val ) {
            if ( CRM_Utils_Array::value( 'contribution', $this->_selectComponent ) ) {
                $this->_formComponent['contribution'] = 
                    " FROM 
                            civicrm_contact  {$this->_aliases['civicrm_contact']}
                            INNER JOIN civicrm_contribution       {$this->_aliases['civicrm_contribution']} 
                       ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id ";
            } 
            if ( CRM_Utils_Array::value( 'membership', $this->_selectComponent ) ) {
                $this->_formComponent['membership'] = 
                    " FROM 
                            civicrm_contact  {$this->_aliases['civicrm_contact']}
                            INNER JOIN civicrm_membership       {$this->_aliases['civicrm_membership']} 
                       ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_membership']}.contact_id ";
            }
            if ( CRM_Utils_Array::value( 'participant', $this->_selectComponent ) ) {
                $this->_formComponent['participant'] = 
                    " FROM 
                            civicrm_contact  {$this->_aliases['civicrm_contact']}
                            INNER JOIN civicrm_participant       {$this->_aliases['civicrm_participant']} 
                       ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_participant']}.contact_id ";
            }
        }
    }

    function where( ) {
        $clauses = array( );

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                    if ( $op ) {
                        //temporary fix default op = eq    
                        $clause = 
                            $this->whereClause( $field,
                                                'eq',
                                                CRM_Utils_Array::value( "{$fieldName}_value", $this->_params ),
                                                CRM_Utils_Array::value( "{$fieldName}_min", $this->_params ),
                                                CRM_Utils_Array::value( "{$fieldName}_max", $this->_params ) );
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
    function clauseComponent( ) {
        $contribution = $membership =  $participant = null;
        $final = $rows = array();
        foreach( $this->_component as $val ) {
            if ( CRM_Utils_Array::value( $val, $this->_selectComponent ) ) {
                $sql  = "{$this->_selectComponent[$val]} {$this->_formComponent[$val]} $this->_where";
                $dao  = CRM_Core_DAO::executeQuery( $sql );
                while ( $dao->fetch( ) ) {
                    $row = array( );
                    foreach ( $this->_columnHeadersComponent[$val] as $key => $value ) {
                        $row[$key] = $dao->$key;
                    }
                    $rows[$val][] = $row;
                }
            }
        }
        return $rows;
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
            //temparary fix
            $this->_groupBy = "GROUP BY civicrm_contact_id";// . implode( ', ', $this->_groupBy );
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
        $componentRows = $this->clauseComponent( );
        $this->altercomponentDisplay( $componentRows);
        $this->assign_by_ref( 'columnHeadersComponent', $this->_columnHeadersComponent );
        $this->assign_by_ref( 'componentRows', $componentRows );

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

        foreach ( $rows as $rowNum => $row ) {
            // make count columns point to detail report
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view', 
                                              "reset=1&cid={$row['civicrm_contact_id']}" );

                $rows[$rowNum]['civicrm_contact_display_name'] = "<a href='$url'>" . 
                    $row["civicrm_contact_display_name"] . '</a>';
                $entryFound = true;
            }

            // handle country
            if ( array_key_exists('civicrm_address_country_id', $row) ) {
                if ( $value = $row['civicrm_address_country_id'] ) {
                    $rows[$rowNum]['civicrm_address_country_id'] = CRM_Core_PseudoConstant::country( $value, false );
                }
                $entryFound = true;
            }
            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince( $value, false );
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
    function altercomponentDisplay( &$componentRows ) {
        // custom code to alter rows
 
        $entryFound = false;
        foreach( $componentRows as $component => $rows) { 
        
            foreach ( $rows as $rowNum => $row ) {
                // handle contribution
                if ( $component == 'contribution' ) {
                    require_once 'CRM/Contribute/PseudoConstant.php';
                    if ( $val = CRM_Utils_Array::value('civicrm_contribution_contribution_type_id', $row ) ) {
                        $componentRows[$component][$rowNum]['civicrm_contribution_contribution_type_id'] = 
                            CRM_Contribute_PseudoConstant::contributionType( $val, false );
                    }

                    if ( $val = CRM_Utils_Array::value('civicrm_contribution_contribution_status_id', $row ) ) {
                        $componentRows[$component][$rowNum]['civicrm_contribution_contribution_status_id'] = 
                            CRM_Contribute_PseudoConstant::contributionStatus( $val, false );
                    }
                    $entryFound = true;
                }

                if ( $component == 'membership' ) {
                    require_once 'CRM/Member/PseudoConstant.php';
                    if ( $val = CRM_Utils_Array::value('civicrm_membership_membership_type_id', $row ) ) {
                        $componentRows[$component][$rowNum]['civicrm_membership_membership_type_id'] = 
                            CRM_Member_PseudoConstant::membershipType( $val, false );
                    }

                    if ( $val = CRM_Utils_Array::value('civicrm_membership_status_id', $row ) ) {
                        $componentRows[$component][$rowNum]['civicrm_membership_status_id'] = 
                            CRM_Member_PseudoConstant::membershipStatus( $val, false );
                    }
                    $entryFound = true;
                }

                if ( $component == 'participant' ) {
                    require_once 'CRM/Event/PseudoConstant.php';
                    if ( $val = CRM_Utils_Array::value('civicrm_participant_event_id', $row ) ) {
                        $componentRows[$component][$rowNum]['civicrm_participant_event_id'] = 
                            CRM_Event_PseudoConstant::event( $val, false );
                    }

                    if ( $val = CRM_Utils_Array::value('civicrm_participant_status_id', $row ) ) {
                        $componentRows[$component][$rowNum]['civicrm_participant_status_id'] = 
                            CRM_Event_PseudoConstant::participantStatus( $val, false );
                    }
                    if ( $val = CRM_Utils_Array::value('civicrm_participant_role_id', $row ) ) {
                        $componentRows[$component][$rowNum]['civicrm_participant_role_id'] = 
                            CRM_Event_PseudoConstant::participantRole( $val, false );
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
}