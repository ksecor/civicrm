<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
        $query->_select['casetag1_id'] = "civicrm_case.casetag1_id as Case type";
        $query->_element['casetag1_id'] = 1;
        $query->_tables['civicrm_case'] = 1;
        $query->_whereTables['civicrm_case'] = 1;
 
        $query->_select['casetag2_id'] = "civicrm_case.casetag2_id as case subtype";
        $query->_element['casetag2_id'] = 1;
        $query->_tables['civicrm_case'] = 1;
        $query->_whereTables['civicrm_case'] = 1;

        $query->_select['casetag3_id'] = "civicrm_case.casetag3_id as case violation";
        $query->_element['casetag3_id'] = 1;
        $query->_tables['civicrm_case'] = 1;
        $query->_whereTables['civicrm_case'] = 1;
        
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

        case 'case_casetag1_id':
            
            $value = strtolower(addslashes(trim($value)));
            
            $query->_where[$grouping][] = "civicrm_case.casetag1_id $op '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Case Type %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;

        case 'case_casetag2_id':
            
            $value = strtolower(addslashes(trim($value)));
            
            $query->_where[$grouping][] = "civicrm_case.casetag2_id $op '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Case SubType %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;

        case 'case_casetag3_id':
            
            $value = strtolower(addslashes(trim($value)));
            
            $query->_where[$grouping][] = "civicrm_case.casetag3_id $op '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Case Voilation %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;
        
        case 'case_status_id':
            
            $value = strtolower(addslashes(trim($value)));
            
            $query->_where[$grouping][] = "civicrm_case.status_id $op '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Case Status %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 1;
            return;

        case 'case_start_date_low':
        case 'case_start_date_high':
            
             $query->dateQueryBuilder( $values,
                                      'civicrm_case', 'case_start_date', 'start_date', 'Start Date' );
            return;

        }
    }

    static function from( $name, $mode, $side ) 
    {
        $from = null;
        switch ( $name ) {
            
        case 'civicrm_case':
            $from = " LEFT JOIN civicrm_case ON civicrm_case.contact_id = contact_a.id ";
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
        $properties = null;
        
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
        $domainID = CRM_Core_Config::domainID( );
        require_once 'CRM/Core/OptionGroup.php';
        $caseType = CRM_Core_OptionGroup::values('f1_case_type');
        $form->addElement('select', 'case_casetag1_id',  ts( 'Case Type' ),  
                          array( '' => ts( '-select-' ) ) + $caseType );
        
        $caseSubType = CRM_Core_OptionGroup::values('f1_case_sub_type');
        $form->addElement('select', 'case_casetag2_id',  ts( 'Case Sub Type' ),  
                          array( '' => ts( '-select-' ) ) + $caseSubType);
        
        $caseViolation = CRM_Core_OptionGroup::values('f1_case_violation');
        $form->addElement('select', 'case_casetag3_id',  ts( 'Violation' ),  
                          array( '' => ts( '-select-' ) ) + $caseViolation);

        $caseStatus  = array( 1 => 'Resolved', 2 => 'Ongoing' ); 
        $form->add('select', 'case_status_id',  ts( 'Case Status' ),  
                   array( '' => ts( '-select-' ) ) + $caseStatus );
        $form->addElement( 'text', 'case_subject', ts( 'Subject' ) );
    
        $form->addElement('date', 'case_start_date_low', ts('Start Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('case_start_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('date', 'case_start_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('case_start_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->assign( 'validCase', true );
    }

    static function addShowHide( &$showHide ) 
    {
        $showHide->addHide( 'caseForm' );
        $showHide->addShow( 'caseForm_show' );
    }

}

?>
