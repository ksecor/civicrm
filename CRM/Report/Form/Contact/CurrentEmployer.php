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

class CRM_Report_Form_Contact_CurrentEmployer extends CRM_Report_Form {

    protected $_summary = null;
    
    function __construct( ) {

      $this->_columns = 
            array(
		  'civicrm_employer'=>
		  array( 'dao'       =>'CRM_Contact_DAO_Contact',
			 'fields'    =>
			 array( 'organization_name'=>
				array( 'title' => ts( 'Employer Name' ),
				       'required'  => true,
				       'no_repeat' => true ),
				'id'           => 
				array( 'no_display'=> true,
				       'required'  => true, ),),
			 'filters'   =>
			 array(   'organization_name'       => 
				  array( 'title'      => ts( 'Employer Name' ),
					 'type'       => CRM_Utils_Type::T_STRING ),),
			 ),
		  
		  'civicrm_contact' =>
		  array( 'dao'       => 'CRM_Contact_DAO_Contact',
			 'fields'    =>
			 array( 'display_name' => 
				array( 'title' => ts( 'Employee Name' ),
				       'required'  => true,),
				
				'job_title'    => 
				array( 'title' => ts( 'Job Title'),),
				
				'gender_id'    =>
				array( 'title' => ts( 'Gender'),),
				
				'id'           => 
				array( 'no_display'=> true,
				       'required'  => true, ),),
			 
			 'filters'   =>             
			 array('sort_name'    => 
			       array( 'title'      => ts( 'Employee Name' )  ),
			       
			       'id'           => 
			       array( 'title'     => ts( 'Employee Contact ID' ) ), ),
			 'grouping'  => 'contact-fields',
			 ),
		  
		  'civicrm_relationship' =>
		  array( 'dao'       => 'CRM_Contact_DAO_Relationship',
			 'fields'    =>
			 array( 'start_date' => 
				array( 'title'      => ts( 'Employee Since' ),
				       'default'    =>true ),),
			 
			 'filters'   =>  
			 array( 'start_date' => 
				array( 'title'      => ts( 'Employee Since' ),					       'type'       => CRM_Utils_Type::T_DATE),),
			 ),
		  
		  'civicrm_email'   =>
		  array( 'dao'       => 'CRM_Core_DAO_Email',
			 'grouping'  => 'contact-fields',
			 'fields'    =>
			 array( 'email' => 
				array( 'title'     => ts( 'Email' ), ),),
			 
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
				array( 'title'   => ts( 'Country' ), ),	),
			 
			 'filters'   =>             
			 array( 'country_id' => 
				array( 'title'   => ts( 'Country ID' ), 
				       'type'    => CRM_Utils_Type::T_INT ), 
				'state_province_id' =>  
				array( 'title'   => ts( 'State/Province ID' ), 
				       'type'    => CRM_Utils_Type::T_INT ), ), 
			 ),
		  
		  'civicrm_group' => 
		  array( 'dao'    => 'CRM_Contact_DAO_Group',
			 'alias'  => 'cgroup',
			 'filters'=>             
			 array( 'gid' => 
				array( 'name'    => 'id',
				       'title'   => ts( 'Group' ),
				       'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
				       'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), 
			 ),
		  
		  );
      parent::__construct( );
    }
    
    function preProcess( ) {
        $this->assign( 'reportTitle', ts( 'Current Employer Report' ));
        parent::preProcess( );
    }
    
    function setDefaultValues( ) {
        return parent::setDefaultValues( );
    }

    function select( ) {

        $select = $this->_columnHeaders = array( );

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

    function from( ) {
        $this->_from = "
FROM civicrm_contact {$this->_aliases['civicrm_contact']}

     LEFT JOIN civicrm_contact {$this->_aliases['civicrm_employer']}
          ON {$this->_aliases['civicrm_employer']}.id={$this->_aliases['civicrm_contact']}.employer_id
 
     LEFT JOIN civicrm_relationship {$this->_aliases['civicrm_relationship']}
          ON ( {$this->_aliases['civicrm_relationship']}.contact_id_a={$this->_aliases['civicrm_contact']}.id 
              AND {$this->_aliases['civicrm_relationship']}.contact_id_b={$this->_aliases['civicrm_contact']}.employer_id 
              AND {$this->_aliases['civicrm_relationship']}.relationship_type_id=4) 
     LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
          ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id 
             AND {$this->_aliases['civicrm_address']}.is_primary = 1 )
 
     LEFT JOIN  civicrm_email {$this->_aliases['civicrm_email']} 
          ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id 
             AND {$this->_aliases['civicrm_email']}.is_primary = 1)

     LEFT  JOIN civicrm_group_contact group_contact 
          ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id              AND group_contact.status='Added'

     LEFT  JOIN civicrm_group {$this->_aliases['civicrm_group']} 
          ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id ";
 
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
	  $this->_where = "WHERE {$this->_aliases['civicrm_contact']}.employer_id!='null' ";
        } else {
	  $this->_where = "WHERE ({$this->_aliases['civicrm_contact']}.employer_id!='null') AND " . implode( ' AND ', $clauses );
        }
    }
    
    function statistics( &$rows ) {
      
      $statistics[] = array( 'title' => ts('Row(s) Listed'),
			     'value' => count($rows) );
      return $statistics;
    }
    
    function groupBy( ) {

      $this->_groupBy = "GROUP BY {$this->_aliases['civicrm_employer']}.id,{$this->_aliases['civicrm_contact']}.id";
      
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
      $checkList = array();
      $entryFound = false;
      
      foreach ( $rows as $rowNum => $row ) {
	
	// convert employer name to links
	if ( array_key_exists('civicrm_employer_organization_name', $row) && 
	     array_key_exists('civicrm_employer_id', $row) ) {
	  
	  $url = CRM_Utils_System::url( 'civicrm/contact/view', 
					'reset=1&cid=' . $rows[$rowNum]['civicrm_employer_id'] );
	  $rows[$rowNum]['civicrm_employer_organization_name'] ="<a href='$url'>" . $row["civicrm_employer_organization_name"] . '</a>';;
	  
	  $entryFound = true;
	}

	if ( !empty($this->_noRepeats) ) {
	  // not repeat contact display names if it matches with the one 
	  // in previous row
	  
	  foreach ( $row as $colName => $colVal ) {
	    if ( is_array($checkList[$colName]) && 
		 in_array($colVal, $checkList[$colName]) ) {
	      $rows[$rowNum][$colName] = "";
	    }
	    if ( in_array($colName, $this->_noRepeats) ) {
	      $checkList[$colName][] = $colVal;
	    }
	  }
	}
		
	//handle gender
	if ( array_key_exists('civicrm_contact_gender_id', $row) ) {
	  if ( $value = $row['civicrm_contact_gender_id'] ) {
	    $gender=CRM_Core_PseudoConstant::gender();
	    $rows[$rowNum]['civicrm_contact_gender_id'] =$gender[$value];
	  }
	  $entryFound = true;
	}
	
	// convert employee name to links
	if ( array_key_exists('civicrm_contact_display_name', $row) && 
	     array_key_exists('civicrm_contact_id', $row) ) {
	  $url = CRM_Utils_System::url( 'civicrm/report/contact/detail', 
					'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'] );
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
}
