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
require_once 'CRM/Contribute/PseudoConstant.php';

class CRM_Report_Form_Pledge_Pbnp extends CRM_Report_Form {
    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contact'  =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'fields'    =>
                          array( 'display_name'      => 
                                 array( 'title'      => ts( 'Constituent Name' ),
                                        'required'   => true ),
                                 'id' =>
                                 array( 'no_display' => true,
                                        'required'   => true, ),
                                 ), 
                          'grouping' => 'contact-fields',
                          ),
                   
                   'civicrm_pledge' =>
                   array( 'dao'     => 'CRM_Pledge_DAO_Pledge',
                          'fields'  =>
                          array( 'create_date' => 
                                 array( 'title'    => ts( 'Pledged Date' ),
                                        'required' => true,
                                        ),
                                 'contribution_type_id' =>
                                 array( 'title'    => ts('Contribution Type'),
                                        'requrie'  => true,
                                        ),
                                'amount'    =>
                                 array( 'title'    => ts('Amount'),
                                        'required' => true,
                                        ),
                                 'status_id' =>
                                 array( 'title'    => ts('Status'),
                                        ),
                                 ),
                          'filters'  => 
                          array( 'create_date' =>
                                 array('title'    =>  'Pledged Date', 
                                       'operatorType' => CRM_Report_Form::OP_DATE ),
                                 ),
                          'grouping' => 'pledge-fields',
                          ),
                   
                   'civicrm_pledge_payment'  =>
                   array( 'dao'       => 'CRM_Pledge_DAO_Payment',
                          'fields'    =>
                          array( 'scheduled_date' =>
                                 array( 'title'    => ts( 'Due Date' ),
                                        'required' => true,),
                                 ), 
                          'grouping'  => 'pledge-fields',
                          ),
                   
                   'civicrm_contribution_type' =>
                   array( 'dao'       => 'CRM_Contribute_DAO_ContributionType',
                          'filters' =>
                          array( 'contribution_type' => null, ),
                          'grouping'  => 'pledge-fields',
                          ),
                   
                   'civicrm_address' =>
                   array( 'dao'      => 'CRM_Core_DAO_Address',
                          'fields'   =>
                          array( 'street_address'    => null,
                                 'city'              => null,
                                 'postal_code'       => null,
                                 'state_province_id' => 
                                 array( 'title'      => ts( 'State/Province' ), ),
                                 'country_id'        => 
                                 array( 'title'      => ts( 'Country' ),  
                                        'default'    => true ), 
                                 ),
                          'grouping'=> 'contact-fields',
                          ),
                   
                   'civicrm_email' => 
                   array( 'dao'    => 'CRM_Core_DAO_Email',
                          'fields' =>
                          array( 'email' => null),
                          'grouping'=> 'contact-fields',
                          ),
                   
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
        $this->assign( 'reportTitle', ts('Membership Summary Report' ) );
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
                        // to include optional columns address and email, only if checked
                        if ( $tableName == 'civicrm_address' ) {
                            $this->_addressField = true;
                            $this->_emailField = true; 
                        } else if ( $tableName == 'civicrm_email' ) { 
                            $this->_emailField = true;  
                        }
                        $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                    }
                }
            }
        }
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }
    
    function from( ) {
        $this->_from = null;
        
        $this->_from = "
        FROM civicrm_contact {$this->_aliases['civicrm_contact']}
             INNER JOIN civicrm_pledge  {$this->_aliases['civicrm_pledge']} 
                        ON ({$this->_aliases['civicrm_pledge']}.contact_id =
                            {$this->_aliases['civicrm_contact']}.id)  AND 
                            {$this->_aliases['civicrm_pledge']}.status_id = 2
             LEFT  JOIN civicrm_pledge_payment {$this->_aliases['civicrm_pledge_payment']}
                        ON ({$this->_aliases['civicrm_pledge']}.id =
                            {$this->_aliases['civicrm_pledge_payment']}.pledge_id)
             LEFT  JOIN civicrm_contribution_type {$this->_aliases['civicrm_contribution_type']} 
                        ON ({$this->_aliases['civicrm_pledge']}.contribution_type_id = 
                            {$this->_aliases['civicrm_contribution_type']}.id)
             LEFT  JOIN civicrm_group_contact group_contact 
                        ON ({$this->_aliases['civicrm_pledge']}.contact_id = 
                            group_contact.contact_id)  AND 
                            group_contact.status='Added'
             LEFT  JOIN civicrm_group {$this->_aliases['civicrm_group']} 
                        ON (group_contact.group_id = {$this->_aliases['civicrm_group']}.id) ";
        
        // include address field if address column is to be included
        if ( $this->_addressField ) {  
            $this->_from .= "LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
                                       ON ({$this->_aliases['civicrm_contact']}.id = 
                                           {$this->_aliases['civicrm_address']}.contact_id) AND
                                           {$this->_aliases['civicrm_address']}.is_primary = 1\n";
        }
        
        // include email field if email column is to be included
        if ( $this->_emailField ) { 
            $this->_from .= "LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']} 
                                       ON ({$this->_aliases['civicrm_contact']}.id = 
                                           {$this->_aliases['civicrm_email']}.contact_id) AND 
                                           {$this->_aliases['civicrm_email']}.is_primary = 1\n";     
        }
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
            
            if ( !empty($this->_statFields) && 
                 (( $append && count($this->_groupBy) <= 1 ) || (!$append)) ) {
                $this->_rollup = " WITH ROLLUP";
            }
            $this->_groupBy = "GROUP BY " . implode( ', ', $this->_groupBy ) . " {$this->_rollup} ";
        } else {
            $this->_groupBy = "GROUP BY pledge.contact_id, pledge.id";
        }
    }
    
    function postProcess( ) {
        $this->beginPostProcess( );
        $sql = $this->buildQuery( false );
        
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
        
        // assign variables to templates
        $this->doTemplateAssignment( $rows );
        
        $this->endPostProcess( );
    }
    
    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $entryFound = false;
        $checkList  =  array();   
        
        foreach ( $rows as $rowNum => $row ) {
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
            
            //handle the Contribution Type Ids
            if ( array_key_exists('civicrm_pledge_contribution_type_id', $row) ) {
                if ( $value = $row['civicrm_pledge_contribution_type_id'] ) {
                    $rows[$rowNum]['civicrm_pledge_contribution_type_id'] = 
                        CRM_Contribute_PseudoConstant::contributionType( $value, false );
                }
                $entryFound = true;
            }  
            
            //handle the Status Ids
            if ( array_key_exists( 'civicrm_pledge_status_id', $row ) ) {
                if ( $value = $row['civicrm_pledge_status_id'] ) {
                    $rows[$rowNum]['civicrm_pledge_status_id'] = 
                        CRM_Core_OptionGroup::getLabel( 'contribution_status', $value );
                }
                $entryFound = true;
            } 
            
            //handle the Pleged Date
            if ( array_key_exists( 'civicrm_pledge_create_date', $row ) ) {
                if ( $value = $row['civicrm_pledge_create_date'] ) {
                    $datePledged = CRM_Utils_Date::customFormat( $value,'%Y%m%d' );
                    $rows[$rowNum]['civicrm_pledge_create_date'] = 
                        CRM_Utils_Date::customFormat( $datePledged );
                }
                $entryFound = true;
            } 
            
            //handle the Scheduled Date
            if ( array_key_exists( 'civicrm_pledge_payment_scheduled_date', $row ) ) {
                if ( $value = $row['civicrm_pledge_payment_scheduled_date'] ) {
                    $dateScheduled = CRM_Utils_Date::customFormat( $value,'%Y%m%d' );
                    $rows[$rowNum]['civicrm_pledge_payment_scheduled_date'] =
                        CRM_Utils_Date::customFormat( $dateScheduled );
                }
                $entryFound = true;
            } 
            
            // handle state province
            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = 
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $value, false );
                }
                $entryFound = true;
            }
            
            // handle country
            if ( array_key_exists('civicrm_address_country_id', $row) ) {
                if ( $value = $row['civicrm_address_country_id'] ) {
                    $rows[$rowNum]['civicrm_address_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value, false );
                }
                $entryFound = true;
            }
            
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Report_Utils_Report::getNextUrl( 'contact/detail', 
                                                            'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'],
                                                            $this->_absoluteUrl, $this->_id );
                $rows[$rowNum]['civicrm_contact_display_name_link' ] = $url;
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
