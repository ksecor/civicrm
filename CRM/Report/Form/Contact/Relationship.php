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
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Contact/BAO/Relationship.php';

class CRM_Report_Form_Contact_Relationship extends CRM_Report_Form {

    protected $_summary = null;
    
    function __construct( ) {
        
        $this->_columns = 
            array('civicrm_contact' =>
                  array( 'dao'       => 'CRM_Contact_DAO_Contact',
                         'fields'    =>
                         array( 'display_name_a' => 
                                array( 'title'    => ts( 'Contact A' ),
                                       'name'     => 'display_name',
                                       'alias'    => 'contact_one',
                                       'required' => true,
                                       ), 
                                'display_name_b' => 
                                array( 'title'    => ts( 'Contact B' ),
                                       'name'     => 'display_name',
                                       'alias'    => 'contact_two',
                                       'required' => true,
                                       ),                                                                 
                                'id'           => 
                                array( 'no_display'=> true,
                                       'alias'     => 'contact_one',
                                       'required'  => true, 
                                       ),
                                ),                         
                         'filters'   =>             
                         array('sort_name'    => 
                               array( 'title' => ts( 'Employee Name' ) 
                                      ),
                               'id'           => 
                               array( 'no_display' => true ), 
                               ),
                         'grouping'  => 'contact-fields',
                         ),
                  
                  'civicrm_relationship' =>
                  array( 'dao'       => 'CRM_Contact_DAO_Relationship',
                         'fields'    =>
                         array( 'start_date' => 
                                array( 'title'      => ts( 'Start Date' ),
                                       'default'    => true
                                       ),
                                ),
                         ),
                  
                  'civicrm_relationship_type' =>
                  array( 'dao'       => 'CRM_Contact_DAO_RelationshipType',
                         'fields'    =>
                         array( 'label_a_b' => 
                                array( 'title'      => ts( 'RelationShip ' ),
                                       'default'    => true
                                       ),
                                'label_b_a' => 
                                array( 'title'      => ts( 'RelationShip ' ),
                                       'default'    => true
                                       ),
                                ),                         
                         'filters'   =>  
                         array( 'contact_type_a' => 
                                array( 'title'        => ts( 'Contact Type  A' ),                                      
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_SelectValues::contactType(),
                                       'type'         => CRM_Utils_Type::T_STRING,
                                       ),
                                'contact_type_b' => 
                                array( 'title'        => ts( 'Contact Type  B' ),                                      
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_SelectValues::contactType(),
                                       'type'         => CRM_Utils_Type::T_STRING,
                                       ),
                                  'label' => 
                                  array( 'title'       => ts( 'Relationship' ),
                                        'operatorType' => CRM_Report_Form::OP_SELECT,
                                        'options'      => array(
                                                                "" => "- any relationship type -") + 
                                         CRM_Contact_BAO_Relationship::getContactRelationshipType( null, 'null', null, null, true),
                                         'type'        => CRM_Utils_Type::T_STRING
                                         ),                                
                                ),                        
                         ),
                  
                  'civicrm_email'   =>
                  array( 'dao'       => 'CRM_Core_DAO_Email',
                         'grouping'  => 'contact-fields',
                         'fields'    =>
                         array( 'email' => 
                                array( 'title'   => ts( 'Email' ), 
                                       'default' => true 
                                       ),
                                ),
                         ),
                  
                  'civicrm_address' =>
                  array( 'dao'       => 'CRM_Core_DAO_Address',
                         'grouping'  => 'contact-fields',
                         'fields'    =>
                         array( 'street_address'    => null,),
                         
                         'filters'   =>             
                         array( 'country_id' => 
                                array( 'title'        => ts( 'Country' ), 
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_PseudoConstant::country(null,false),
                                       ), 
                                'state_province_id' =>  
                                array( 'title'        => ts( 'State/Province' ), 
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_PseudoConstant::stateProvince( ),
                                       ),
                                ),
                         ),
                  
                  'civicrm_group' => 
                  array( 'dao'    => 'CRM_Contact_DAO_Group',
                         'alias'  => 'cgroup',
                         'filters'=>             
                         array( 'gid' => 
                                array( 'name'         => 'id',
                                       'title'        => ts( 'Group' ),
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_PseudoConstant::staticGroup( ) 
                                       ),
                                ), 
                         ),
                  );
        parent::__construct( );
    }
    
    function preProcess( ) {
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
        
        $relationship = explode("_",$this->_params['name_a_b_value']);

        $alias_contact_one = 'contact_one';
        $alias_contact_two = 'contact_two';

        $relationship_contact_one = "contact_id_a" ;
        $relationship_contact_two = "contact_id_b" ;
        $this->relationship_label = "label_a_b";

        if( $relationship['1'] == 'b' ) {            
            $relationship_contact_one = "contact_id_b" ;
            $relationship_contact_two = "contact_id_a" ;
            $this->relationship_label = "label_b_a";
        }        
        $this->_from = "
     FROM civicrm_contact   {$alias_contact_one }
 
     LEFT JOIN civicrm_relationship {$this->_aliases['civicrm_relationship']}
          ON ( {$this->_aliases['civicrm_relationship']}.{$relationship_contact_one}  ={$alias_contact_one }.id )
    
     LEFT JOIN civicrm_contact {$alias_contact_two }
          ON ( {$this->_aliases['civicrm_relationship']}.{$relationship_contact_two}  ={$alias_contact_two }.id )
        
     LEFT JOIN civicrm_relationship_type {$this->_aliases['civicrm_relationship_type']}
          ON ( {$this->_aliases['civicrm_relationship']}.relationship_type_id={$this->_aliases['civicrm_relationship_type']}.id  )

     LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
          ON (  {$this->_aliases['civicrm_address']}.contact_id =  {$alias_contact_one }.id
             AND {$this->_aliases['civicrm_address']}.is_primary = 1 )
 
     LEFT JOIN  civicrm_email {$this->_aliases['civicrm_email']} 
          ON ( {$alias_contact_one }.id = {$this->_aliases['civicrm_email']}.contact_id 
             AND {$this->_aliases['civicrm_email']}.is_primary = 1 )

     LEFT  JOIN civicrm_group_contact group_contact 
          ON  {$alias_contact_one }.id = group_contact.contact_id AND group_contact.status='Added'

     LEFT  JOIN civicrm_group {$this->_aliases['civicrm_group']} 
          ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id " ;

      
    }

    function where( ) {
        
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) { 
                foreach ( $table['filters'] as $fieldName => $field ) { 
                    $clause = null;
                    if ( $field['operatorType'] & CRM_Report_Form::OP_DATE ) { 
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );
                        
                        $clause = $this->dateClause( $field['name'], $relative, $from, $to );
                    } else {
                        $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                        
                        if ( $fieldName == 'label') {
                            $field['dbAlias']   = "relationship_type.". $this->relationship_label ;
                            $field['name'   ]   = $this->relationship_label ;
                            $fieldName          = $this->relationship_label;
                            
                            $this->_params[ "{$fieldName}_value" ] = $field['options'][$this->_params['label_value']]; 
                            foreach( $field['options'] as $optionKey => $optionValue ) {
                                $option_value [ $optionValue ] =$optionValue;
                            }
                            $field['options'] = $option_value;
                        }
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
    
    function postProcess( ) {
        
        $this->beginPostProcess( );
        $sql = $this->buildQuery( );
        require_once 'CRM/Utils/PChart.php';
        $dao   = CRM_Core_DAO::executeQuery( $sql );
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
        $this->endPostProcess( $rows );
        
    }
    
    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $checkList = array();
        $entryFound = false;
        
        foreach ( $rows as $rowNum => $row ) {
            
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
