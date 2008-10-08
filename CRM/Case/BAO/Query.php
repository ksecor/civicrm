<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Case_BAO_Query 
{
    
    static function &getFields( ) 
    {
        $fields = array( );
        require_once 'CRM/Case/DAO/Case.php';
        $fields = array_merge( $fields, CRM_Case_DAO_Case::import( ) );
        
        return $fields;  
    }

    /** 
     * build select for Case 
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {
        $query->_select['case_type_id'     ] = "civicrm_case.case_type_id as case_type";
        $query->_element['case_type_id'    ] = 1;
 
        $query->_select['subject'] = "civicrm_case.subject as subject";
        $query->_element['subject'] = 1;

        $query->_tables['civicrm_case'] = 1;
        $query->_whereTables['civicrm_case'] = 1;
    }

     /** 
     * Given a list of conditions in query generate the required
     * where clause
     * 
     * @return void 
     * @access public 
     */ 
    static function where( &$query ) 
    {
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 5) == 'case_' ) {
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
    
    /** 
     * where clause for a single field
     * 
     * @return void 
     * @access public 
     */ 
    static function whereClauseSingle( &$values, &$query ) 
    {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        switch( $name ) {
            
        case 'case_subject':
            $value = strtolower(addslashes(trim($value)));
            $query->_where[$grouping][] = "civicrm_case.subject $op '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Case Subject %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;

        case 'case_status_id':
            require_once 'CRM/Core/OptionGroup.php' ;
            $caseStatus = CRM_Core_OptionGroup::values('case_status');

            $query->_where[$grouping][] = "civicrm_case.status_id {$op} $value ";

            $value = $caseStatus[$value];
            $query->_qill[$grouping ][] = ts( 'Case Status %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;
            
        case 'case_type_id':
            require_once 'CRM/Core/OptionGroup.php' ;
            $caseType = CRM_Core_OptionGroup::values('case_type');
            $names = array( );
            foreach ( $value as $id => $val ) {
                $names[] = $caseType[$val];
            }
            require_once 'CRM/Case/BAO/Case.php';
            $value = CRM_Case_BAO_Case::VALUE_SEPERATOR . 
                implode( CRM_Case_BAO_Case::VALUE_SEPERATOR . "%' OR civicrm_case.case_type_id LIKE '%" .
                         CRM_Case_BAO_Case::VALUE_SEPERATOR, $value) . 
                CRM_Case_BAO_Case::VALUE_SEPERATOR;
            $query->_where[$grouping][] = "(civicrm_case.case_type_id LIKE '%{$value}%')";

            $value = $caseType[$value];
            $query->_qill[$grouping ][] = ts( 'Case Type %1', array( 1 => $op))  . ' ' . implode( ' ' . ts('or') . ' ', $names );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;

        case 'case_casetag2_id':
            require_once 'CRM/Core/OptionGroup.php' ;
            $caseSubtype = CRM_Core_OptionGroup::values('f1_case_sub_type');
            $names = array( );
            foreach ( $value as $id => $val ) {
                $names[] = $caseSubtype[$val];
            }
            require_once 'CRM/Case/BAO/Case.php';
            $value = CRM_Case_BAO_Case::VALUE_SEPERATOR . 
                implode( CRM_Case_BAO_Case::VALUE_SEPERATOR . "%' OR civicrm_case.casetag2_id LIKE '%" . 
                         CRM_Case_BAO_Case::VALUE_SEPERATOR, $value) . 
                CRM_Case_BAO_Case::VALUE_SEPERATOR;
            $query->_where[$grouping][] = "(civicrm_case.casetag2_id LIKE '%{$value}%')";
            $value = $caseSubtype[$value];
            $query->_qill[$grouping ][] = ts( 'Case SubType %1', array( 1 => $op)) . ' ' . implode( ' ' . ts('or') . ' ', $names );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;
           
        case 'case_casetag3_id':
 
            require_once 'CRM/Core/OptionGroup.php' ;
            $caseViolation = CRM_Core_OptionGroup::values('f1_case_violation');
            $names = array( );
            foreach ( $value as $id => $val ) {
                $names[] = $caseViolation[$val];
            }
            
            require_once 'CRM/Case/BAO/Case.php';
            $value = CRM_Case_BAO_Case::VALUE_SEPERATOR . 
                implode( CRM_Case_BAO_Case::VALUE_SEPERATOR . "%' OR civicrm_case.casetag3_id LIKE '%" . 
                         CRM_Case_BAO_Case::VALUE_SEPERATOR, $value) . 
                CRM_Case_BAO_Case::VALUE_SEPERATOR;
            $query->_where[$grouping][] = "(civicrm_case.casetag3_id LIKE '%{$value}%')";
            
            $value = $caseViolation[$value];
            $query->_qill[$grouping ][] = ts( 'Case Voilation %1', array( 1=> $op)) . ' ' . implode( ' ' . ts('or') . ' ', $names );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;

        case 'case_start_date_low':
        case 'case_start_date_high':
            
             $query->dateQueryBuilder( $values,
                                      'civicrm_case', 'case_start_date', 'start_date', 'Start Date' );
            return;

        case 'case_id':
            $query->_where[$grouping][] = "civicrm_case.id $op $value";
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;
        }
    }

    static function from( $name, $mode, $side ) 
    {
        $from = null;
        switch ( $name ) {
            
        case 'civicrm_case':
            $from = " LEFT JOIN civicrm_case_contact ON civicrm_case_contact.contact_id = contact_a.id
                      LEFT JOIN civicrm_case ON civicrm_case.id = civicrm_case_contact.case_id";
            break;
        }
        return $from;
        
    }
    
    /**
     * getter for the qill object
     *
     * @return string
     * @access public
     */
    function qill( ) {
        return (isset($this->_qill)) ? $this->_qill : "";
    }
    
    static function defaultReturnProperties( $mode ) 
    {
        $properties = array(  
                            'contact_type'              => 1, 
                            'sort_name'                 => 1, 
                            'display_name'              => 1,
                            'case_subject'              => 1,
                            );
        return $properties;
    }
    
    static function tableNames( &$tables ) 
    {
        $tables = array_merge( array( 'civicrm_case' => 1), $tables );
    }
    
    /**
     * add all the elements shared between case search and advanaced search
     *
     * @access public 
     * @return void
     * @static
     */  
    static function buildSearchForm( &$form ) 
    {
        $config =& CRM_Core_Config::singleton( );
        require_once 'CRM/Core/OptionGroup.php';
        $caseType = CRM_Core_OptionGroup::values('case_type');
        $form->addElement('select', 'case_type_id',  ts( 'Case Type' ),  
                          $caseType, array("size"=>"5",  "multiple"));
        
        $caseStatus = CRM_Core_OptionGroup::values('case_status'); 
        $form->add('select', 'case_status_id',  ts( 'Case Status' ),  
                   array( '' => ts( '- select -' ) ) + $caseStatus );
        
        $form->addElement( 'text', 'case_subject', ts( 'Subject' ) );
        if ($config->civiHRD){
            $caseSubType = CRM_Core_OptionGroup::values('f1_case_sub_type');
            $form->add('select', 'case_casetag2_id',  ts( 'Case Sub Type' ),  
                       $caseSubType , false, array("size"=>"5","multiple"));
            
            $caseViolation = CRM_Core_OptionGroup::values('f1_case_violation');
            $form->add('select', 'case_casetag3_id',  ts( 'Violation' ),  
                       $caseViolation , false, array("size"=>"5",  "multiple"));
        }
   
        require_once 'CRM/Contact/BAO/Relationship.php';
        require_once 'CRM/Core/PseudoConstant.php';
        $relTypeInd =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Individual');
        $relTypeOrg =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Organization');
        $relTypeHou =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Household');
        $allRelationshipType =array();
        $allRelationshipType = array_merge(  $relTypeInd , $relTypeOrg);
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);
        $attributes = array('onchange' => "setUrl( );");
        $form->addElement('select', 'relation_type_id', ts('Case Role'),  array('' => ts('- select -')) + $allRelationshipType, $attributes);
        
        // add a dojo facility for searching contacts
        $form->assign( 'dojoIncludes', " dojo.require('dojox.data.QueryReadStore'); dojo.require('dojo.parser'); dojo.require('dijit.form.ComboBox');" );
        $attributes = array( 'dojoType'       => 'dijit.form.ComboBox',
                             'mode'           => 'remote',
                             'store'          => 'contactStore',
                             'pageSize'       => 10, 
                             'id'             => 'case_role'
                             );
              
        $form->addElement('text', 'name'      , ts('Related Contact'), $attributes );

        $form->addElement('date', 'scheduledActivity_start_date_low', ts('From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('scheduledActivity_start_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('date', 'scheduledActivity_start_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('scheduledActivity_start_date_high', ts('Select a valid date.'), 'qfDate'); 

        $activityTypes =
            array( ''   => ' - select activity - ' ) + 
            CRM_Core_PseudoConstant::activityType( );
                
        // we need to remove some activity types
        CRM_Utils_Array::crmArraySplice( $activityTypes, 4, 9);
        $form->add('select', 'scheduledActivity_type_id', ts('Scheduled Activity Type'),
                   $activityTypes,
                   false);

        $form->addElement('date', 'completedActivity_start_date_low', ts('From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('completedActivity_start_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('date', 'completedActivity_start_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('completedActivity_start_date_high', ts('Select a valid date.'), 'qfDate'); 
         
        $form->add('select', 'completedActivity_type_id', ts('Completed Activity Type'),
                   $activityTypes,
                   false);
        


        // add all the custom  searchable fields
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $case = array( 'Case' );
        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail( null, true, $case );
        if ( $groupDetails ) {
            require_once 'CRM/Core/BAO/CustomField.php';
            $form->assign('caseGroupTree', $groupDetails);
            foreach ($groupDetails as $group) {
                foreach ($group['fields'] as $field) {
                    $fieldId = $field['id'];                
                    $elementName = 'custom_' . $fieldId;
                    CRM_Core_BAO_CustomField::addQuickFormElement( $form,
                                                                   $elementName,
                                                                   $fieldId,
                                                                   false, false, true );
                }
            }
        }

        $form->assign( 'validCiviCase', true );
    }

    static function searchAction( &$row, $id ) 
    {
    }

    static function addShowHide( &$showHide ) 
    {
        $showHide->addHide( 'caseForm' );
        $showHide->addShow( 'caseForm_show' );
    }

}


