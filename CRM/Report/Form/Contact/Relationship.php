<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Contact/BAO/Relationship.php';

class CRM_Report_Form_Contact_Relationship extends CRM_Report_Form {
    
    protected $_summary       = null;
    protected $_emailField_a  = false;
    protected $_emailField_b  = false;
    
    function __construct( ) {
        
        $contact_type = CRM_Core_SelectValues::contactType();
        unset($contact_type[""]);
        
        $this->_columns = 
            array(
                  'civicrm_contact' =>
                  array( 'dao'       => 'CRM_Contact_DAO_Contact',
                         'fields'    =>
                         array( 'display_name_a' => 
                                 array( 'title'     => ts( 'Contact A' ),
                                        'name'      => 'display_name',
                                        'required'  => true,
                                        ),
                                'id' => 
                                array( 'no_display' => true,
                                       'required'   => true,
                                       ), ),
                         'filters'    =>
                         array( 'sort_name'    =>
                                array( 'title'     => ts('Contact A'),
                                       'operator'  => 'like',
                                       'type'      => CRM_Report_Form::OP_STRING ), ),
                         'grouping'   => 'conact_a_fields',
                         ),
                  
                  'civicrm_contact_b' =>
                  array( 'dao'       => 'CRM_Contact_DAO_Contact',
                         'alias'     => 'contact_b',
                         'fields'    =>
                         array( 'display_name_b' => 
                                 array( 'title'     => ts( 'Contact B' ),
                                        'name'      => 'display_name',
                                        'required'  => true,
                                        ),
                                'id' => 
                                array( 'no_display' => true,
                                        'required'   => true,
                                       ), ),
                         'filters'    =>
                         array( 'sort_name'=>
                                array( 'title'     => ts('Contact B'),
                                       'operator'  => 'like',
                                       'type'      => CRM_Report_Form::OP_STRING ), ),
                         'grouping'   => 'conact_b_fields',
                         ),
                  
                  'civicrm_email' => 
                  array( 'dao'    => 'CRM_Core_DAO_Email',
                         'fields' =>
                         array( 'email_a' => 
                                array( 'title' => ts('Email of Contact A'),
                                       'name'  => 'email' ), ),
                         'grouping'   => 'conact_a_fields',
                         ),
                  
                  'civicrm_email_b' => 
                  array( 'dao'    => 'CRM_Core_DAO_Email',
                         'alias'  => 'email_b',
                         'fields' =>
                         array( 'email_b' => 
                                array( 'title'  => ts('Email of Contact B'),
                                       'name'   => 'email' ), ),
                         'grouping'   => 'conact_b_fields',
                         ),

                  'civicrm_relationship_type' =>
                  array( 'dao'       => 'CRM_Contact_DAO_RelationshipType',
                         'fields'    =>
                         array( 'label_a_b' => 
                                array( 'title'   => ts( 'Relationship A-B ' ),
                                       'default' => true,),
                                
                                'label_b_a' => 
                                array( 'title' => ts( 'Relationship B-A ' ),
                                       'default' => true, ),
                                ),                         
                         'filters'   =>  
                         array( 'contact_type_a' => 
                                array( 'title'        => ts( 'Contact Type  A' ),
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => $contact_type,
                                       'type'         => CRM_Utils_Type::T_STRING,
                                       ),
                                'contact_type_b' => 
                                array( 'title'        => ts( 'Contact Type  B' ), 
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => $contact_type,
                                       'type'         => CRM_Utils_Type::T_STRING,
                                       ), ),    
                         'grouping'  => 'relation-fields',
                         ),
                  
                  'civicrm_relationship' =>
                  array( 'dao'       => 'CRM_Contact_DAO_Relationship',
                         'fields'    =>
                         array( 'start_date' => 
                                array( 'title'     => ts( 'Relationship Start Date' ),
                                       ),
                                'end_date'   =>
                                array( 'title'     => ts( 'Relationship End Date' ),
                                       ),
                                ),
                         'filters'   =>
                         array('is_active'=> 
                               array( 'title'        => ts( 'Relationship Status' ),
                                      'operatorType' => CRM_Report_Form::OP_SELECT,
                                      'options'      => 
                                      array( ''  => '- Any -',
                                             1   => 'Active',
                                             0   => 'Inactive',
                                             ), 
                                      'type'     => CRM_Utils_Type::T_INT ),
                               'relationship_type_id' =>
                               array( 'title'        => ts( 'Relationship' ),
                                      'operatorType' => CRM_Report_Form::OP_SELECT,
                                      'options'      => 
                                      array( ''     => '- any relationship type -') +
                                      CRM_Contact_BAO_Relationship::getContactRelationshipType( null, 'null', null, null, true),
                                      'type'        => CRM_Utils_Type::T_INT
                                      ),

                               ),
                         
                         'grouping'  => 'relation-fields',
                         ),
                  
                  'civicrm_address'  =>
                  array( 'dao'       => 'CRM_Core_DAO_Address',
                         'filters'   =>             
                         array( 'country_id' => 
                                array( 'title'        => ts( 'Country' ), 
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_PseudoConstant::country(),
                                       ), 
                                'state_province_id' =>  
                                array( 'title'        => ts( 'State/Province' ), 
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_PseudoConstant::stateProvince( ),
                                       ),
                                ),
                         'grouping'  => 'contact-fields',
                         ),

                  
                  'civicrm_group' => 
                  array( 'dao'    => 'CRM_Contact_DAO_Group',
                         'alias'  => 'cgroup',
                         'filters'=>             
                         array( 'gid' => 
                                array( 'name'         => 'group_id',
                                       'title'        => ts( 'Group' ),
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'group'        => true,
                                       'options'      => CRM_Core_PseudoConstant::group( ) 
                                       ),
                                ), 
                         ),
                  );
        parent::__construct( );
    }
        

    function preProcess( ) {
        parent::preProcess( );
    }
  
    function select( ) {        
        $select = $this->_columnHeaders = array( );        
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {  
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                                                 
                        if ( $fieldName == 'email_a' ) {
                            $this->_emailField_a = true;
                        }
                        if( $fieldName == 'email_b' ) {
                            $this->_emailField_b = true;
                        }
                        $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value( 'type', $field );
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = CRM_Utils_Array::value( 'title', $field );
                    }
                }
            }
        }
        
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }
    
    function from( ) {        
       $this->_from = "
        FROM civicrm_relationship {$this->_aliases['civicrm_relationship']}
 
             INNER JOIN civicrm_contact {$this->_aliases['civicrm_contact']}
                        ON ( {$this->_aliases['civicrm_relationship']}.contact_id_a = 
                             {$this->_aliases['civicrm_contact']}.id )

             INNER JOIN civicrm_contact {$this->_aliases['civicrm_contact_b']}
                        ON ( {$this->_aliases['civicrm_relationship']}.contact_id_b = 
                             {$this->_aliases['civicrm_contact_b']}.id )

             LEFT  JOIN civicrm_address {$this->_aliases['civicrm_address']} 
                         ON (( {$this->_aliases['civicrm_address']}.contact_id =
                               {$this->_aliases['civicrm_contact']}.id  OR
                               {$this->_aliases['civicrm_address']}.contact_id =
                               {$this->_aliases['civicrm_contact_b']}.id ) AND 
                             {$this->_aliases['civicrm_address']}.is_primary = 1 )

             LEFT  JOIN civicrm_relationship_type {$this->_aliases['civicrm_relationship_type']}
                        ON ( {$this->_aliases['civicrm_relationship']}.relationship_type_id  = 
                             {$this->_aliases['civicrm_relationship_type']}.id  ) ";
        
        // include Email Field 
       if ( $this->_emailField_a ) {
           $this->_from .= " 
             LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']}
                       ON ( {$this->_aliases['civicrm_contact']}.id =
                            {$this->_aliases['civicrm_email']}.contact_id AND 
                            {$this->_aliases['civicrm_email']}.is_primary = 1 )";
       }
       if ( $this->_emailField_b ) {
            $this->_from .= " 
             LEFT JOIN civicrm_email {$this->_aliases['civicrm_email_b']} 
                       ON ( {$this->_aliases['civicrm_contact_b']}.id =
                            {$this->_aliases['civicrm_email_b']}.contact_id AND 
                            {$this->_aliases['civicrm_email_b']}.is_primary = 1 )";
       }
    }
    
    function statistics( &$rows ) {
        $statistics = parent::statistics( $rows );
        
        $isStatusFilter = false;
        $relStatus      = null;
        if ( CRM_Utils_Array::value('is_active_value', $this->_params ) == '1' ) {
            $relStatus = 'Is equal to Active';
        } elseif ( CRM_Utils_Array::value('is_active_value', $this->_params ) == '0' ) {
            $relStatus = 'Is equal to Inactive';
        } 
        if ( CRM_Utils_Array::value( 'filters', $statistics ) ) {
            foreach( $statistics['filters'] as $id => $value ) {
                //for displaying relationship type filter
                if( $value['title'] == 'Relationship' ) {
                    $relTypes = CRM_Core_PseudoConstant::relationshipType();
                    $statistics['filters'][$id]['value'] = 
                        'Is equal to '.$relTypes[$this->_params['relationship_type_id_value']]['label_'.$this->relationType] ;  
                }

                //for displaying relationship status
                if ( $value['title'] == 'Relationship Status' ) {
                    $isStatusFilter  = true;
                    $statistics['filters'][$id]['value'] = $relStatus;
                }
            }
        }
        //for displaying relationship status
        if ( !$isStatusFilter && $relStatus ) {
            $statistics['filters'][] = array ( 'title' => 'Relationship Status',
                                               'value' => $relStatus ) ;
        }
        return $statistics;
    }

    function groupBy( ) {
        $this->_groupBy = " ";
        $groupBy        = array();
        if ( $this->relationType == 'a_b' ) {
            $groupBy[] = " {$this->_aliases['civicrm_contact']}.id";
        } elseif ( $this->relationType == 'b_a' ) {
            $groupBy[] = " {$this->_aliases['civicrm_contact_b']}.id";
        }
        
        if( !empty($groupBy) ){
            $this->_groupBy = " GROUP BY  " . implode( ', ', $groupBy )." ,  {$this->_aliases['civicrm_relationship']}.id ";
        } else {
            $this->_groupBy = " GROUP BY {$this->_aliases['civicrm_relationship']}.id ";
        }
        
    }
    function postProcess( ) {
        $this->beginPostProcess( );

        $this->relationType = null;
        if ( CRM_Utils_Array::value( 'relationship_type_id_value', $this->_params ) ) {
               
            $this->relationType = substr($this->_params['relationship_type_id_value'], 2 );
            $this->_params['relationship_type_id_value'] = intval( substr($this->_params['relationship_type_id_value'],0,1) );
        }
        
        $sql = $this->buildQuery( );
        $this->buildRows ( $sql, $rows );

        $this->formatDisplay( $rows );
        $this->doTemplateAssignment( $rows );
        $this->endPostProcess( $rows );
    }
    
    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $entryFound = false;  
        
        foreach ( $rows as $rowNum => $row ) {
            
            // handle country
            if ( array_key_exists('civicrm_address_country_id', $row) ) {
                if ( $value = $row['civicrm_address_country_id'] ) {
                    $rows[$rowNum]['civicrm_address_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value, false );
                }
                $entryFound = true;
            }
            
            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = 
                        CRM_Core_PseudoConstant::stateProvince( $value, false );
                }
                $entryFound = true;
            }

            if ( array_key_exists('civicrm_contact_display_name_a', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Report_Utils_Report::getNextUrl( 'contact/detail', 
                                                            'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'],
                                              $this->_absoluteUrl, $this->_id );
                $rows[$rowNum]['civicrm_contact_display_name_a_link' ] = $url;
                $rows[$rowNum]['civicrm_contact_display_name_a_hover'] = ts("View Contact details for this contact.");
                $entryFound = true;
            }

            if ( array_key_exists('civicrm_contact_b_display_name_b', $row) && 
                 array_key_exists('civicrm_contact_b_id', $row) ) {
                $url = CRM_Report_Utils_Report::getNextUrl( 'contact/detail', 
                                                            'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_b_id'],
                                              $this->_absoluteUrl, $this->_id );
                $rows[$rowNum]['civicrm_contact_b_display_name_b_link' ] = $url;
                $rows[$rowNum]['civicrm_contact_b_display_name_b_hover'] = ts("View Contact details for this contact.");
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
