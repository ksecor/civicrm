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

require_once "CRM/Core/Form.php";

/**
 * This class generates form components for OpenCase Activity
 * 
 */
class CRM_Case_Form_Activity_ChangeCase
{
    
    static function buildQuickForm( &$form ) 
    {
        $form->assign( 'dojoIncludes', " dojo.require('dojox.data.QueryReadStore'); dojo.require('dojo.parser');" );
        $form->addElement( 'hidden', 'hidden_changeCase', 1 );
        $caseAttributes = array( 'dojoType'       => 'civicrm.FilteringSelect',
                                 'mode'           => 'remote',
                                 'store'          => 'caseStore');
          
        $caseUrl = CRM_Utils_System::url( "civicrm/ajax/caseSubject",
                                          "c={$form->_currentlyViewedContactId}",
                                          false, null, false );
        $form->assign('caseUrl',$caseUrl );
        
        $form->add( 'text','case_id',ts('Case'), $caseAttributes );
        
        /*if ( $subject->getValue( ) ) {
            $caseSbj=CRM_Core_DAO::getFieldValue('CRM_Case_DAO_Case',$subject->getValue( ), 'subject' );
            $this->assign( 'subject_value',  $caseSbj );
        }
        */
        require_once 'CRM/Core/OptionGroup.php';        
        $caseType = CRM_Core_OptionGroup::values('case_type');
        $form->add('select', 'case_type_id',  ts( 'Case Type' ),  
                   $caseType , true, array("size"=>"5",  "multiple"));
        
        $form->addElement('checkbox','is_resetTimeline', ts('Reset Case Timeline?'), null, array('onclick' =>"return showHideByValue('is_resetTimeline','','TimelineDate','table-row','radio',false);",'checked' => 'true') );

        $form->addElement('date', 'timeline_date', ts('Restart Timeline On'), CRM_Core_SelectValues::date('activityDate')); 
        $form->addRule('timeline_date', ts('Select a valid date.'), 'qfDate');

        $form->addElement('checkbox','is_resetCaseroles', ts('Reset Case Roles?'), null, array('checked' => 'true'));
        
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( &$form, &$params ) 
    {
    }
}
