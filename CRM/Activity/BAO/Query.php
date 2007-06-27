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

class CRM_Activity_BAO_Query 
{
    
       
    /**
     * add all the elements shared between case activity search  and advanaced search
     *
     * @access public 
     * @return void
     * @static
     */  
    static function buildSearchForm( &$form ) 
    {
       
        require_once 'CRM/Core/OptionGroup.php';
        $caseActivityType = CRM_Core_OptionGroup::values('case_activity_type');
        $form->add('select', 'activity_activitytag1_id',  ts( 'Activity Type' ),  
                   array( '' => ts( '-select-' ) ) + $caseActivityType );
        
        $comunicationMedium = CRM_Core_OptionGroup::values('communication_medium'); 
        $form->add('select', 'activity_activitytag2_id',  ts( 'Activity Medium' ),  
                   array( '' => ts( '-select-' ) ) + $comunicationMedium );
        
        $caseViolation = CRM_Core_OptionGroup::values('f1_case_violation');
        $form->addElement('select', 'activity_activitytag3_id',  ts( 'Violation Type'  ),  
                          array( '' => ts( '-select-' ) ) + $caseViolation);

        $form->addElement( 'text', 'activity_subject', ts( 'Subject' ) );
        $form->addElement( 'text', 'activity_details', ts( 'Content' ) );
    
        $form->addElement('date', 'activity_start_date_low', ts('Start Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('activity_start_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('date', 'activity_start_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('activity_start_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->assign( 'validCaseActivity', true );
    }

    static function addShowHide( &$showHide ) 
    {
        $showHide->addHide( 'caseActivityForm' );
        $showHide->addShow( 'caseActivityForm_show' );
    }

}

?>
