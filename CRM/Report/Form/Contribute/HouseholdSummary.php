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

class CRM_Report_Form_Contribute_HouseholdSummary extends CRM_Report_Form {

    protected $_summary = null;

    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contact_household'  =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'fields'    =>
                          array( 'household_name'    => 
                                 array( 'title'      => ts( 'Household Name' ),
                                        'required'   => true, ),
                                 'id'           => 
                                 array( 'no_display' => true,
                                        'required'   => true, ), ), 
                          'filters' =>   
                          array( 'household_name'=> 
                                 array ('title'      => ts( 'Household Name' )),
                                 ),
                          'grouping' => 'required-field',
                          ),
                   
                   'civicrm_relationship' =>
                   array( 'dao'    => 'CRM_Contact_DAO_Relationship',
                          'fields' =>
                          array(
                                'relationship_type_id' =>
                                array( 'title' => ts('RelationShip Type'),
                                       'required'  => true),
                                ),
                          'grouping' => 'required-field',
                          ),
                  
                   'civicrm_contact'  =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'fields'    =>
                          array( 'display_name' => 
                                 array( 'title' => ts( 'Contributor Name' ),
                                        'required'   => true,
                                        'no_repeat'  => true ),
                                 'id'           => 
                                 array( 'no_display' => true,
                                        'required'   => true, ), ),
                          'filters'  =>             
                          array('sort_name'          => 
                                array( 'title'       => ts( 'Contact Name' ),
                                       'operator'    => 'like' ),
                                'id' => 
                                array( 'title'       => ts( 'Contact ID' ) ), ),
                          'grouping'=> 'contact-fields',
                          ),
                   
                   'civicrm_address' =>
                   array( 'dao' => 'CRM_Core_DAO_Address',
                          'fields' =>
                          array( 'street_address'    => null,
                                 'city'              => null,
                                 'postal_code'       => null,
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ), ),  
                                 ),
                          'grouping' => 'contact-fields',
                          ),
                   
                   'civicrm_email' => 
                   array( 'dao' => 'CRM_Core_DAO_Email',
                          'fields' =>
                          array( 'email' => null),
                          'grouping' => 'contact-fields',
                          ),
                   
                   'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'fields'        =>
                          array( 'total_amount'  => array( 'title' => ts( 'Amount' ),
                                                           'required'      => true,
                                                           'statistics'   => 
                                                           array('sum'    => ts( 'Total Amount' ), 
                                                                 ),
                                                           ),
                                 'trxn_id'       => null,
                                 'receive_date'  => array( 'default' => true ),
                                 ),
                          'filters'     =>             
                          array( 'receive_date' => 
                                 array( 'type'    => CRM_Utils_Type::T_DATE),
                                 'total_amount' => 
                                 array( 'title'   => ts( 'Amount Between' ), ), ),
                          'grouping' => 'contri-fields',
                          ),

                   'civicrm_group'  => 
                   array( 'dao'     => 'CRM_Contact_DAO_Group',
                          'alias'   => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Group' ),
                                        'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                        'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
                   );

        $this->_options = array( 'include_grand_total' => array( 'title'  => ts( 'Include Grand Totals' ),
                                                                 'type'   => 'checkbox',
                                                                 'default'=> true ),
                                 );
        parent::__construct( );
    }
    
    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Household Contribution Summary Report' ) );
        parent::preProcess( );
    }
    
    // Temp table to store contact_ids of household and individuals seperately
    // This is because relationships may keep on changing and table cam handle new household 
    // relationship types created
    function tempTable ( ) {
        
        //define table name randomly 
        //to avoid multiple copies of the same table
        $randomNum = md5( uniqid( ) );
        $this->_tableName = "civicrm_temp_report_{$randomNum}"; 
 
        $sql = "
        CREATE  TEMPORARY TABLE report_{$this->_tableName} ( id int PRIMARY KEY AUTO_INCREMENT,
                                                             relationship_id int, 
                                                             household_contact_id int, 
                                                             relationship_contact_id int,
                                                             relationship_type VARCHAR(255),
                                                             contribution_id int ) ENGINE=HEAP ";
        
        CRM_Core_DAO::executeQuery( $sql,CRM_Core_DAO::$_nullArray );
        
        $query_a = " 
        SELECT relationship.id  as rel_id,
               relationship.contact_id_b,
               relationship.contact_id_a,
               relation_types.name_b_a,
               contribution.id as contribution_id
 
        FROM   civicrm_relationship relationship, 
               civicrm_contact      contact,
               civicrm_contribution contribution,
               civicrm_relationship_type relation_types 

        WHERE  contribution.contact_id = relationship.contact_id_a AND
               contact.id              = relationship.contact_id_b AND
               contact.contact_type    = 'Household' AND
               relationship.is_active  = 1 AND
               relation_types.id       = relationship.relationship_type_id

        ORDER BY relationship.contact_id_b,
                 relationship.contact_id_a, 
                 contribution.id ";
        
        $dao_result = CRM_Core_DAO::executeQuery( $query_a, CRM_Core_DAO::$_nullArray );
        $count = 0;
        while ($dao_result->fetch() ) {
            $count++;
            $query = "
            INSERT INTO  report_{$this->_tableName} 
            VALUES ( $count, $dao_result->rel_id, $dao_result->contact_id_b, 
                     $dao_result->contact_id_a, '$dao_result->name_b_a', 
                     $dao_result->contribution_id ) ";
            CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        }
        
        $query_b = " 
	    SELECT relationship.id as rel_id,
               relationship.contact_id_a,
  		       relationship.contact_id_b, 
               relation_types.name_a_b,
               contribution.id as contribution_id  

		FROM   civicrm_relationship relationship, 
               civicrm_contact contact,
               civicrm_contribution contribution,
               civicrm_relationship_type relation_types

        WHERE  contribution.contact_id = relationship.contact_id_b AND
               contact.id              = relationship.contact_id_a AND
               contact.contact_type    = 'Household' AND
               relationship.is_active  = 1  AND
               relation_types.id       = relationship.relationship_type_id

        ORDER BY relationship.contact_id_a,
                 relationship.contact_id_b,
                 contribution.id";

        $dao_resultb= CRM_Core_DAO::executeQuery( $query_b, CRM_Core_DAO::$_nullArray );
        while( $dao_resultb->fetch() ) {
            $count++;
            $query_insert = "
            INSERT INTO report_{$this->_tableName} 
            VALUES ($count, $dao_resultb->rel_id, $dao_resultb->contact_id_a, 
                    $dao_resultb->contact_id_b, '$dao_resultb->name_a_b',
                    $dao_resultb->contribution_id)";
            CRM_Core_DAO::executeQuery( $query_insert, CRM_Core_DAO::$_nullArray );
        }
    }
    
    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( );
        $select[] = "report.relationship_type";
        
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        if ( $tableName == 'civicrm_address' ) {
                            $this->_addressField = true;
                        } else if ( $tableName == 'civicrm_email' ) {
                            $this->_emailField = true;
                        }
                        
                        if ( CRM_Utils_Array::value('statistics', $field) ) {
                            foreach ( $field['statistics'] as $stat => $label ) {
                                $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}_{$stat}";
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = 
                                    $label;
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type']  = 
                                    $field['type'];
                                $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                            }   
                        } else { 
                            $select[] = "{$table['alias']}.{$fieldName} as {$tableName}_{$fieldName}";
                            
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];             
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type']  = $field['type'];
                        }
                    }
                }
            }
        }
        $this->_select = "SELECT " . implode( ', ', $select ) . " "; 
    }
    
    function from( ) {
        $this->_from = null;
        
        $this->_from = "
        FROM  report_{$this->_tableName} report  
              LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact_household']} 
                        ON ({$this->_aliases['civicrm_contact_household']}.id=report.household_contact_id)
              LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact']}
                        ON ({$this->_aliases['civicrm_contact']}.id=report.relationship_contact_id )
              LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}  
                        ON ({$this->_aliases['civicrm_contribution']}.id = report.contribution_id )
              LEFT JOIN civicrm_relationship {$this->_aliases['civicrm_relationship']} 
                        ON ({$this->_aliases['civicrm_relationship']}.id=report.relationship_id)
              LEFT JOIN civicrm_group_contact group_contact 
                        ON report.relationship_contact_id = group_contact.contact_id  AND 
                           group_contact.status = 'Added'
              LEFT JOIN civicrm_group {$this->_aliases['civicrm_group']} 
                        ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id ";

        if ( $this->_addressField ) {
            $this->_from .= "
            LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND {$this->_aliases['civicrm_address']}.is_primary = 1\n";
        }       
        
        if ( $this->_emailField ) {
            $this->_from .= "
            LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND {$this->_aliases['civicrm_email']}.is_primary = 1\n";
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
    
    function statistics( &$rows ) {
        $statistics = array();
        $statistics[] = array( 'title' => ts('Row(s) Listed'),
                               'value' => count($rows) );
        return $statistics; 
    }
    
    function groupBy( ) {
        $this->_groupBy = " GROUP BY report.household_contact_id, report.relationship_contact_id, 
                                     contribution.id, report.relationship_type";
    }
    
    function postProcess( ) {
        $this->tempTable ( );
        
        $this->beginPostProcess( );
        $sql   = $this->buildQuery( false );
        $dao   = CRM_Core_DAO::executeQuery( $sql );
        $rows  = $graphRows = array();
        $count = 0;
        while ( $dao->fetch( ) ) {
            $row = array( );
            $this->_columnHeaders['relationship_type']=null;
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row[$key] = $dao->$key;
            }
            
            require_once 'CRM/Utils/PChart.php';
            if ( CRM_Utils_Array::value('charts', $this->_params ) && 
                 $row['civicrm_contribution_receive_date_subtotal'] ) {
                $graphRows['receive_date'][]   = $row['civicrm_contribution_receive_date_start'];
                $graphRows[$this->_interval][] = $row['civicrm_contribution_receive_date_interval'];
                $graphRows['value'][]          = $row['civicrm_contribution_total_amount_sum'];
                $count++;
            }
            $rows[] = $row;
        }
        $this->formatDisplay( $rows );
        unset( $this->_columnHeaders['relationship_type'] );

        // assign variables to templates
        $this->doTemplateAssignment( $rows );
        
        require_once 'CRM/Utils/PChart.php';
        if ( CRM_Utils_Array::value('charts', $this->_params ) ) {
            foreach ( array ( 'receive_date', $this->_interval, 'value' ) as $ignore ) {
                unset( $graphRows[$ignore][$count-1] );
            }
            $graphs = CRM_Utils_PChart::chart( $graphRows, $this->_params['charts'], $this->_interval );
            $this->assign( 'graphFilePath', $graphs['0']['file_name'] );
        }
        $this->endPostProcess( );
    }
    
    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $checkList      = $subTotalKeys = array();
        $entryFound     = false;
        $noOfKeysFixed  = 0;
        $household_flag = 0;
        $flag_contact   = 0;
        foreach ( $rows as $rowNum => $row ) {
            if ( array_key_exists('relationship_type', $row ) ) {
                if ( $value = $row['relationship_type'] ) {
                    $rows[$rowNum]['civicrm_relationship_relationship_type_id'] = $value;
                    unset($rows[$rowNum]['relatipnship_type']);
                }
            }
            
            if ( array_key_exists('civicrm_contact_household_household_name', $row) ) {
                if ( $value = $row['civicrm_contact_household_household_name'] ) {
                    if( $rowNum == 0 ) {
                        $prev_household =  $value;
                    } else {
                        if( $prev_household == $value) {
                            $household_flag = 1;
                            $prev_household = $value;
                        } else { 
                            $household_flag = 0;
                            $prev_household = $value;
                        }
                    }
                    
                    if( $household_flag ) {
                        $rows[$rowNum]['civicrm_contact_household_household_name'] = "";          
                    } else {
                        $url = CRM_Utils_System::url( 'civicrm/contact/view', 
                                                      'reset=1&cid=' . $rows[$rowNum]['civicrm_contact_household_id'] );
                        
                        $rows[$rowNum]['civicrm_contact_household_household_name'] ="<a href='$url'>" .$value. '</a>';
                    }
                    $entryFound = true;
                }
            }
            
            if ( array_key_exists('civicrm_contact_id', $row) ) {
                if ( $value = $row['civicrm_contact_id'] ) {
                    if( $rowNum == 0 ) {
                        $prev_contact = $value;
                    } else {
                        if( $prev_contact == $value ) {
                            $flag_contact = 1;
                            $prev_contact = $value;
                        } else { 
                            $flag_contact = 0;
                            $prev_contact = $value; 
                        }
                    }
                    
                    if( $flag_contact == 1 && $household_flag == 1 ) {
                       $rows[$rowNum]['civicrm_contact_display_name'] = "";   
                       $rows[$rowNum]['civicrm_relationship_relationship_type_id'] = "";        
                    }
                }
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
                } else {
                    $rows[$rowNum]['civicrm_address_country_id'] = 'Not Specified';
                }
                $entryFound = true;
            }
            
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 $rows[$rowNum]['civicrm_contact_display_name'] && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/report/contribute/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'] );
                $rows[$rowNum]['civicrm_contact_display_name'] = "<a href='$url'>" . 
                    $rows[$rowNum]["civicrm_contact_display_name"] . '</a>';
                $entryFound = true;
            }
            
            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
            $lastKey = $rowNum;
        }
        
        // show grand total only when more than one grouping
        if ( $noOfKeysFixed > 1 ) {
            $this->fixSubTotalDisplay($rows[$rowNum], array('civicrm_contribution_total_amount_sum'));
        } else if ( $noOfKeysFixed == 1 ) {
            unset($rows[$rowNum]);
        }
    }
}
