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
       
    }

    /** 
     * build select for Case 
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {
        $query->_select['casetag1_id'] = "civicrm_case.casetag1_id as case_type";
        $query->_element['casetag1_id'] = 1;
        $query->_tables['civicrm_case'] = 1;
        $query->_whereTables['civicrm_case'] = 1;
        
        $query->_select['subject'] = "civicrm_case.subject as subject";
        $query->_element['subject'] = 1;
        $query->_tables['civicrm_case'] = 1;
        $query->_whereTables['civicrm_case'] = 1;
    }

 
    static function where( &$query ) 
    {
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 5) == 'case_' ) {
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
    
  
    static function whereClauseSingle( &$values, &$query ) 
    {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        
        switch( $name ) {
            
        case 'case_subject':
            
            $value = strtolower(addslashes(trim($value)));
            
            $query->_where[$grouping][] = "civicrm_case.subject $op '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Case %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_case'] = $query->_whereTables['civicrm_case'] = 
                " LEFT JOIN civicrm_case ON ( contact_a.id = civicrm_case.contact_id ) ";;
            
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
        if ( !CRM_Utils_Array::value( 'civicrm_case', $tables ) ) {
            $tables = array_merge( array( 'civicrm_case' => 1), $tables );
        }
    }
    
    
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
