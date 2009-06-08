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
require_once 'CRM/Contribute/PseudoConstant.php';

class CRM_Report_Form_Contribute_SoftCredit extends CRM_Report_Form {
    
    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contact'   =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'fields'    =>
                          array( 'display_name_creditor'      => 
                                 array( 'title'      => ts( 'Soft Creditor\'s Name' ),
                                        'name'       => 'display_name',
                                        'alias'      => 'creditorname',
                                        'required'   => true,
                                        'no_repeat'  => true,
                                        ), 
                                 'id_creditor'       =>
                                 array( 'title'      => ts('Creditor Id'),
                                        'name'       => 'id',
                                        'alias'      => 'creditorname',
                                        'no_display' => true,
                                        'required'   => true,
                                        ),
                                 'display_name_constituent'   => 
                                 array( 'title'      => ts( 'Constituent Name' ),
                                        'name'       => 'display_name',
                                        'alias'      => 'constituentname',
                                        'required'   => true,
                                        ),
                                 'id_constituent'    =>
                                 array( 'title'      => ts('Const Id'),
                                        'name'       => 'id',
                                        'alias'      => 'constituentname',
                                        'no_display' => true,
                                        'required'   => true,
                                        ),
                                 ), 
                          'grouping'  => 'contact-fields',
                          ),
                   
                   'civicrm_email' => 
                   array( 'dao'    => 'CRM_Core_DAO_Email',
                          'fields' =>
                          array( 'email_creditor'    => 
                                 array( 'title'      => ts('Creditors\'s Email'), 
                                        'name'       => 'email',
                                        'alias'      => 'emailcredit',
                                        'default'    => true,
                                        'no_repeat'  => true,
                                        ),
                                 'email_constituent' => 
                                 array( 'title'      => ts('Constituent\'s Email'), 
                                        'name'       => 'email',
                                        'alias'      => 'emailconst',
                                        ),
                                 ),
                          'grouping'=> 'contact-fields',
                          ),
                   
                   'civicrm_phone' => 
                   array( 'dao'    => 'CRM_Core_DAO_Phone',
                          'fields' =>
                          array( 'phone_creditor'    => 
                                 array( 'title'      => ts('Creditors\'s Phone'), 
                                        'name'       => 'phone',
                                        'alias'      => 'pcredit',
                                        'default'    => true,
                                        ),
                                 'phone_constituent' => 
                                 array( 'title'      => ts('Constituent\'s Phone'), 
                                        'name'       => 'phone',
                                        'alias'      => 'pconst',
                                        'no_repeat'  => true,
                                        ),
                                 ),
                          'grouping'=> 'contact-fields',
                          ),
                   
                   'civicrm_contribution_type' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_ContributionType',
                          'fields'        =>
                          array( 'contribution_type' => null, ), 
                          'filters' =>             
                          array( 'id' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Contribution Type' ),
                                        'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                        'options' => CRM_Contribute_PseudoConstant::contributionType( ) ),),
                          'grouping'  => 'softcredit-fields',
                          ),
                   
                   'civicrm_contribution' =>
                   array( 'dao'          => 'CRM_Contribute_DAO_Contribution',
                          'fields'       =>
                          array( 'contribution_source' => null, 
                                 'total_amount'        => 
                                 array( 'title'         => ts( 'Amount Statistics' ),
                                       'default'       => true,
                                        'statistics'    => 
                                        array('sum'     => ts( 'Total Amount' ), 
                                              'count'   => ts( 'Count' ), 
                                              'avg'     => ts( 'Average' ), ), ), ),
                          'grouping'  => 'softcredit-fields',
                          'filters'   =>             
                          array( 'receive_date'   => 
                                 array( 'type'    => CRM_Utils_Type::T_DATE),
                                 'total_amount'   => 
                                 array( 'title'   => ts( 'Total  Amount Between' ), ), ),
                          ),
                   
                   'civicrm_contribution_soft' =>
                   array( 'dao'    => 'CRM_Contribute_DAO_ContributionSoft',
                          'fields' =>
                          array( 'contribution_id'    => 
                                 array( 'title'       => ts('Contribution ID'),
                                        'no_display'  => true,
                                        'default'     => true, ),
                                 'id' => 
                                 array( 'default'     => true,
                                        'no_display'  => true),),
                          'grouping'  => 'softcredit-fields',
                          ),
                   
                   'civicrm_group'     => 
                   array( 'dao'        => 'CRM_Contact_DAO_Group',
                          'alias'      => 'cgroup',
                          'filters'    =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Creditor\'s Group' ),
                                        'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                        'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
                   );
        
        $this->_options = array( 'include_grand_total' => array( 'title'   => ts( 'Include Grand Totals' ),
                                                                 'type'    => 'checkbox',
                                                                 'default' => true ),
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
                        
                        // include email column if set
                        if ( $tableName == 'civicrm_email' ) { 
                            $this->_emailField = true;
                            $this->_emailFieldCredit = true;
                        }else if ( $tableName == 'civicrm_email_creditor' ) {
                            $this->_emailFieldCredit = true;
                        }
                        
                        // include phone columns if set
                        if( $tableName == 'civicrm_phone' ) {
                            $this->_phoneField       = true; 
                            $this->_phoneFieldCredit = true;
                        } else if ( $tableName == 'civicrm_phone_creditor' ){
                            $this->_phoneFieldCredit = true;
                        }
                        
                        // only include statistics columns if set
                        if ( CRM_Utils_Array::value('statistics', $field) ) {
                            foreach ( $field['statistics'] as $stat => $label ) {
                                switch (strtolower($stat)) {
                                case 'sum':
                                    $select[] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] = $field['type'];
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                case 'count':
                                    $select[] = "COUNT({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type']  = CRM_Utils_Type::T_INT;
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                case 'avg':
                                    $select[] = "ROUND(AVG({$field['dbAlias']}),2) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] =  $field['type'];
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                }
                            }   
                            
                        } else {
                            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                        }
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
        $alias_constituent = 'constituentname';
        $alias_creditor    = 'creditorname';
        $this->_from = "
        FROM  civicrm_contribution {$this->_aliases['civicrm_contribution']}
              INNER JOIN civicrm_contribution_soft {$this->_aliases['civicrm_contribution_soft']} 
                         ON {$this->_aliases['civicrm_contribution_soft']}.contribution_id = 
                            {$this->_aliases['civicrm_contribution']}.id
              INNER JOIN civicrm_contact {$alias_constituent} 
                         ON {$this->_aliases['civicrm_contribution']}.contact_id = 
                            {$alias_constituent}.id
              LEFT  JOIN civicrm_contribution_type  {$this->_aliases['civicrm_contribution_type']} 
                         ON {$this->_aliases['civicrm_contribution']}.contribution_type_id = 
                            {$this->_aliases['civicrm_contribution_type']}.id
              LEFT  JOIN civicrm_contact {$alias_creditor}
                         ON {$this->_aliases['civicrm_contribution_soft']}.contact_id = 
                            {$alias_creditor}.id
              LEFT  JOIN civicrm_group_contact group_contact 
                         ON {$alias_creditor}.id = 
                             group_contact.contact_id  AND 
                             group_contact.status='Added'
              LEFT  JOIN civicrm_group {$this->_aliases['civicrm_group']} 
                         ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id ";
        
        // include Constituent email field if email column is to be included
        if ( $this->_emailField ) { 
            $alias_constituent = 'constituentname';
            $alias_creditor    = 'creditorname';
            $alias = 'emailconst';
            $this->_from .= "
            LEFT JOIN civicrm_email {$alias} 
                      ON {$alias_constituent}.id = 
                         {$alias}.contact_id   AND 
                         {$alias}.is_primary = 1\n";     
        }
        
        // include  Creditors email field if email column is to be included
        if ( $this->_emailFieldCredit ) { 
            $alias_constituent = 'constituentname';
            $alias_creditor    = 'creditorname';
            $alias = 'emailcredit';
            $this->_from .= "
            LEFT JOIN civicrm_email {$alias} 
                      ON {$alias_creditor}.id = 
                         {$alias}.contact_id  AND 
                         {$alias}.is_primary = 1\n";     
        }
        
        // include  Constituents phone field if email column is to be included
        if ( $this->_phoneField ) { 
            $alias_constituent = 'constituentname';
            $alias_creditor    = 'creditorname';
            $alias = 'pconst';
            $this->_from .= "
            LEFT JOIN civicrm_phone {$alias} 
                      ON {$alias_constituent}.id = 
                         {$alias}.contact_id  AND
                         {$alias}.is_primary = 1\n";     
        }
        
        // include  Creditors phone field if email column is to be included
        if ( $this->_phoneFieldCredit ) {
            $alias_constituent = 'constituentname';
            $alias_creditor    = 'creditorname';
            $alias             = 'pcredit';
            $this->_from .= "
            LEFT JOIN civicrm_phone pcredit
                      ON {$alias_creditor}.id = 
                         {$alias}.contact_id  AND 
                         {$alias}.is_primary = 1\n";     
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
        $alias_constituent = 'constituentname';
        $alias_creditor    = 'creditorname';
        $this->_groupBy    = "GROUP BY contribution_soft.contact_id,
                                       {$alias_constituent}.id, 
                                       {$alias_creditor}.display_name";
    }
    
    function postProcess( ) {
        $this->beginPostProcess( );
        $sql = $this->buildQuery( );
        
        require_once 'CRM/Utils/PChart.php';
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
        
        // to hide the contact ID field from getting displayed
        unset( $this->_columnHeaders['civicrm_contact_id_constituent'] );
        unset( $this->_columnHeaders['civicrm_contact_id_creditor'] );
        
        // assign variables to templates
        $this->doTemplateAssignment( $rows );
        $this->endPostProcess( );
    }
    
    function alterDisplay( &$rows ) {
        // custom code to alter rows
        
        $entryFound = false;
        foreach ( $rows as $rowNum => $row ) {
            
            // Handling Creditor's display_name no Repeat
            if ( array_key_exists('civicrm_contact_display_name_creditor', $row) ) {
                if ( $value = $row['civicrm_contact_display_name_creditor'] ) {
                    if( $rowNum == 0 ) {
                        $prev_dispname =  $value;
                    } else {
                        if( $prev_dispname == $value) {
                            $dispname_flag = 1;
                            $prev_dispname = $value;
                        } else { 
                            $dispname_flag = 0;
                            $prev_dispname = $value;
                        }
                    }
                    
                    if( $dispname_flag ) {
                        unset($rows[$rowNum]['civicrm_contact_display_name_creditor']);          
                    } else {
                        $rows[$rowNum]['civicrm_contact_display_name_creditor'] = $value;
                    }
                    $entryFound = true;
                }
            }
            
            // Handling Creditor's Phone No Repeat
            if ( array_key_exists('civicrm_phone_phone_creditor', $row) ) {
                //$value = 0;
                if ( $value = $row['civicrm_phone_phone_creditor'] ) {
                    if( $rowNum == 0 ) {
                        $prev_phone=  $value;
                    } else {
                        if( $prev_phone == $value) {
                            $phone_flag = 1;
                            $prev_phone = $value;
                        } else { 
                            $phone_flag = 0;
                            $prev_phone = $value;
                        }
                    }
                    
                    if( $phone_flag ) {
                        unset($rows[$rowNum]['civicrm_phone_phone_creditor']);          
                    } else {
                        $rows[$rowNum]['civicrm_phone_phone_creditor'] = $value;
                    }
                    $entryFound = true;
                }
            }
            
            // Handling Creditor's Email No Repeat
            if ( array_key_exists('civicrm_email_email_creditor', $row) ) {
                if ( $value = $row['civicrm_email_email_creditor'] ) {
                    if( $rowNum == 0 ) {
                        $prev_email=  $value;
                    } else {
                        if( $prev_email == $value) {
                            $email_flag = 1;
                            $prev_email = $value;
                        } else { 
                            $email_flag = 0;
                            $prev_email = $value;
                        }
                    }
                    
                    if( $email_flag ) {
                        unset($rows[$rowNum]['civicrm_email_email_creditor']);          
                    } else {
                        $rows[$rowNum]['civicrm_email_email_creditor'] = $value;
                    }
                    $entryFound = true;
                }
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
            
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name_constituent', $row) && 
                 array_key_exists('civicrm_contact_id_constituent', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/report/contribute/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id_constituent'] );
                $rows[$rowNum]['civicrm_contact_display_name_constituent'] = "<a href='$url'>" . 
                    $row["civicrm_contact_display_name_constituent"] . '</a>';
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