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

class CRM_Report_Form_Contribute_OrganizationSummary extends CRM_Report_Form {

    protected $_addressField = false;
    
    protected $_emailField   = false;
    
    protected $_summary      = null;
    
    function __construct( ) {
        
        $this->_columns = 
            array(
                  'civicrm_contact_organization' =>
                  array( 'dao'           =>  'CRM_Contact_DAO_Contact',
                         'fields'        =>
                         array( 
                               'organization_name'=> 
                               array ('title'    => ts( 'Organization Name' ),
                                      'required' =>true ),
                               'id'=>
                               array( 'no_display' => true,
                                      'required'   => true, )
                               ),
                         'filters' =>   
                         array(
                               'organization_name' => 
                               array ('title'      => ts( 'Organization Name' )),
                               )
                         ),
                  
                  'civicrm_relationship' =>
                  array( 'dao'           => 'CRM_Contact_DAO_Relationship',
                         'fields'        =>
                         array( 
                               'relationship_type_id' => 
                               array ('title'    => ts( 'Relationship Type' ),
                                      'required' => true ),
                               ),
                         ),
                  
                  'civicrm_contact'      =>
                  array( 'dao'     => 'CRM_Contact_DAO_Contact',
                         'fields'  =>
                         array( 'display_name' => 
                                array( 'title'     => ts( 'Contact Name' ),
                                       'required'  => true,
                                       ),
                                'id'           => 
                                array( 'no_display' => true,
                                       'required'   => true, ), ),
                         'filters' =>             
                         array('sort_name'    => 
                               array( 'title'      => ts( 'Contact Name' ),
                                      'operator'   => 'like' ),
                               'id'    => 
                               array( 'title'      => ts( 'Contact ID' ) ), ),
                         'grouping'=> 'contact-fields',
                         ),
                  
                  'civicrm_contribution' =>
                  array( 'dao'     => 'CRM_Contribute_DAO_Contribution',
                         'fields'  =>
                         array( 'total_amount'  => array( 'title'      => ts( 'Amount' ),
                                                          'required'   => true,
                                                          'statistics' => 
                                                          array('sum'  => ts( 'Amount' )), ),
                                'trxn_id'       => null,
                                'receive_date'  => array( 'default' => true ),
                                'receipt_date'  => null,
                                ),
                         'filters' =>             
                         array( 'receive_date' => 
                                array( 'type'       => CRM_Utils_Type::T_DATE ),
                                'total_amount' => 
                                array( 'title'      => ts( 'Amount Between' ) ),
                                ),
                         'grouping'=> 'contri-fields',
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
	      
                  'civicrm_group' => 
                  array( 'dao'     => 'CRM_Contact_DAO_Group',
                         'alias'   => 'cgroup',
                         'filters' =>             
                         array( 'gid' => 
                                array( 'name'    => 'id',
                                       'title'   => ts( 'Group' ),
                                       'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                       'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
                  );
        
        $this->_options = array( 'include_statistics' => 
                                 array( 'title'   => ts( 'Include Organization Contribution Statistics' ),
                                        'type'    => 'checkbox',
                                        'default' => true ),
                                 );
        
        parent::__construct( );
    }
    
    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Organization Contribution Summery Report' ) );
        parent::preProcess( );
    }
    
    // Create temperaory table which store organization relatioship
    // data with contribution
    function tempTable ( ) {
        $sql =  " 
             CREATE  TEMPORARY TABLE report_org_summay ( id int PRIMARY KEY AUTO_INCREMENT, rel_id int, org_contact_id int, rel_contact_id int, rel_type VARCHAR(255), contribution_id int ) ENGINE=HEAP ";
        
        CRM_Core_DAO::executeQuery( $sql,CRM_Core_DAO::$_nullArray );
        
        //get contacts and relationship when contact_b is organization
        $query_a=" 
        SELECT relationship.id as rel_id ,relationship.contact_id_b,relationship.contact_id_a, 
               relation_types.name_b_a, contribution.id as contribution_id 
        FROM   civicrm_relationship relationship, civicrm_contact contact,
               civicrm_contribution contribution, civicrm_relationship_type relation_types 
        WHERE  contribution.contact_id=relationship.contact_id_a 
               AND contact.id           = relationship.contact_id_b
               AND contact.contact_type = 'Organization' AND relationship.is_active=1
               AND relation_types.id    = relationship.relationship_type_id
       ORDER BY relationship.contact_id_b, relationship.contact_id_a, contribution.id ";
        
        $result_a = CRM_Core_DAO::executeQuery( $query_a, CRM_Core_DAO::$_nullArray );
        $count    = 0;
        while ( $result_a->fetch() ) {
            $count++;
            $distanceQuery = "
            INSERT INTO report_org_summay values( $count,
                                                  $result_a->rel_id,$result_a->contact_id_b,$result_a->contact_id_a,
                                                  '$result_a->name_b_a',$result_a->contribution_id)";
            
            CRM_Core_DAO::executeQuery( $distanceQuery, CRM_Core_DAO::$_nullArray );
        }
        
        //get contacts and relationship when contact_a is organization
        $query_b=" 
        SELECT relationship.id as rel_id ,relationship.contact_id_a,
               relationship.contact_id_b, relation_types.name_a_b,contribution.id as contribution_id
        FROM  civicrm_relationship relationship, civicrm_contact contact,
              civicrm_contribution contribution, civicrm_relationship_type relation_types 
        WHERE contribution.contact_id=relationship.contact_id_b 
            AND contact.id             = relationship.contact_id_a
            AND contact.contact_type   = 'Organization' 
            AND relationship.is_active = 1 
            AND relation_types.id      = relationship.relationship_type_id
        ORDER BY relationship.contact_id_a, relationship.contact_id_b, contribution.id ";
        
        $result_b = CRM_Core_DAO::executeQuery( $query_b, CRM_Core_DAO::$_nullArray );
        while ( $result_b->fetch() ) {
            $count++;
            $query_insert="
            INSERT INTO report_org_summay values( $count,
                                                  $result_b->rel_id, $result_b->contact_id_a, $result_b->contact_id_b,
                                                  '$result_b->name_a_b', $result_b->contribution_id ) ";
            CRM_Core_DAO::executeQuery( $query_insert, CRM_Core_DAO::$_nullArray );	
        }
    }

    function select( ) {
        $this->_columnHeaders = $select = array( );   
        $select[] ="report.rel_type";
        
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
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type']  = $field['type'];
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
        FROM  report_org_summay report  
            LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact_organization']} ON 
                      ({$this->_aliases['civicrm_contact_organization']}.id = report.org_contact_id)
            LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact']} ON 
                      ({$this->_aliases['civicrm_contact']}.id = report.rel_contact_id )
            LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']} ON
                      ({$this->_aliases['civicrm_contribution']}.id = report.contribution_id )
            LEFT JOIN civicrm_relationship {$this->_aliases['civicrm_relationship']} ON
                      ({$this->_aliases['civicrm_relationship']}.id = report.rel_id)  ";
        if ( $this->_addressField ) {
            $this->_from .= " 
            LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} ON 
                      {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND 
                      {$this->_aliases['civicrm_address']}.is_primary = 1\n ";
        }
       
        if ( $this->_emailField ) {
            $this->_from .= "
            LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']} ON 
                      {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND 
                      {$this->_aliases['civicrm_email']}.is_primary = 1\n ";
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
                        
                        $clause = $this->dateClause( $field['name'], $relative, $from, $to );
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
            $this->_where = "WHERE ( 1 )";
        } else {
            $this->_where = "WHERE "  . implode( ' AND ', $clauses );
        }
    }
    
    
    function groupBy( ) {
        $this->_groupBy = " GROUP BY report.org_contact_id ,report.rel_contact_id,contribution.id,report.rel_type";
    }

    function statistics( &$rows ) {
        $statistics   = array();
        
        $statistics[] = array( 'title' => ts('Row(s) Listed'),
                               'value' => count($rows) );
	 return $statistics;
    }

    function postProcess( ) {
        $this->tempTable ( );
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;
        
        $this->processReportMode( );
        
        $this->select ( );
        $this->from   ( );
        $this->where  ( );
        $this->groupBy( );
        $this->limit  ( );
        
        $sql  = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_limit}";
        $dao  = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );

        while ( $dao->fetch( ) ) {
            $row = array( );
            //assign null value to rel_type : refere it in alterDispaly function
            $this->_columnHeaders['rel_type'] = null;
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row[$key] = $dao->$key;
            }
            $rows[] = $row;
        }

        $this->formatDisplay( $rows );
        unset($this->_columnHeaders['rel_type']);

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        
        if ( CRM_Utils_Array::value( 'include_statistics', $this->_params['options'] ) ) {
            $this->assign( 'statistics', $this->statistics($rows ) );
        }
        
        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $entryFound = false;
      	$flag_org   = $flag_contact = 0;

        
        foreach ( $rows as $rowNum => $row ) {
	  
            //replace retionship id by relationship name 
            if ( array_key_exists('rel_type', $row ) ) {
                if ( $value = $row['rel_type'] ) {
                    $rows[$rowNum]['civicrm_relationship_relationship_type_id'] = $value;
                    unset($rows[$rowNum]['rel_type']);
                    $entryFound = true;
                }
            }
	  
            //remove duplicate Organization names
            if ( array_key_exists('civicrm_contact_organization_organization_name', $row) ) {
                if ( $value = $row['civicrm_contact_organization_organization_name'] ) {
                    if( $rowNum == 0 ) {
                        $privious_org = $value;
                    } else {
                        if(  $privious_org == $value) {
                            $flag_org     = 1;
                            $privious_org = $value;
                        } else { $flag_org=0;$privious_org=$value; }
                    }
                    
                    if(  $flag_org == 1 ) {
                        $rows[$rowNum]['civicrm_contact_organization_organization_name'] = "";          
                    } else {
                        $url = CRM_Utils_System::url( 'civicrm/contact/view', 
                                                      'reset=1&cid=' . $rows[$rowNum]['civicrm_contact_organization_id'] );
                        
                        $rows[$rowNum]['civicrm_contact_organization_organization_name'] ="<a href='$url'>" .$value. '</a>';
                    }
                    $entryFound = true;
                }
            }
            
            //remove duplicate Contact names and relationship type
            if ( array_key_exists('civicrm_contact_id', $row) ) {
                if ( $value = $row['civicrm_contact_id'] ) {
                    if ( $rowNum == 0 ) {
                        $privious_contact= $value;
                    } else {
                        if( $privious_contact == $value ) {
                            $flag_contact     = 1;
                            $privious_contact = $value;
                        } else { 
                            $flag_contact     = 0;
                            $privious_contact = $value;
                        }
                    }
                    
                    if( $flag_contact == 1 && $flag_org == 1 ) {
                        $rows[$rowNum]['civicrm_contact_display_name']              = "";   
                        $rows[$rowNum]['civicrm_relationship_relationship_type_id'] = "";        
                    }
                    
                    $entryFound = true;
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
                }
                $entryFound = true;
            }
            
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 $rows[$rowNum]['civicrm_contact_display_name'] && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/report/contribute/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'] );
                $rows[$rowNum]['civicrm_contact_display_name'] = "<a href='$url'>" . $rows[$rowNum]["civicrm_contact_display_name"] . '</a>'; 
                
                $entryFound = true;
            }
            
            // skip looking further in rows, if first row itself doesn't 
            if ( !$entryFound ) {
                break;
            }
            $lastKey = $rowNum;
        }
    }
}