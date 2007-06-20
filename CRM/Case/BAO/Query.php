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
