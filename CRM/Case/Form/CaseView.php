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

require_once 'CRM/Core/Form.php';

/**
 * This class generates view mode for CiviCase
 * 
 */
class CRM_Case_Form_CaseView extends CRM_Core_Form
{
    /**  
     * Function to set variables up before form is built  
     *                                                            
     * @return void  
     * @access public  
     */
    public function preProcess( ) 
    {
        $this->_contactID = $this->get('cid');
        $this->_caseID    = $this->get('id');

        //retrieve details about case
        $params = array( 'id' => $this->_caseID );

        $returnProperties = array( 'case_type_id', 'subject', 'status_id' );
        CRM_Core_DAO::commonRetrieve('CRM_Case_BAO_Case', $params, $values, $returnProperties );
        
        $values['case_type_id'] = explode( CRM_Case_BAO_Case::VALUE_SEPERATOR, CRM_Utils_Array::value( 'case_type_id' , $values ) );

        require_once "CRM/Case/PseudoConstant.php";
        $statuses  = CRM_Case_PseudoConstant::caseStatus( );
        $caseTypes = CRM_Case_PseudoConstant::caseType( );

        $caseType = null;
        foreach( $values['case_type_id'] as $value ) {
            if ( $value ) {
                if ( $caseType ) {
                    $caseType .= ", ";
                }
                $caseType .= $statuses[$value];
            }
        }

        $caseDetails = array( 'case_type'    => $caseType,
                              'case_status'  => $statuses[$values['case_status_id']],
                              'case_subject' => $values['subject']
                              );
        
        $this->assign ( 'caseDetails', $caseDetails );

    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $activities = array( 1 => "Presenting Problem", "Prescribing Privileges", "Medication and Drug Use" );
        $this->add('select', 'activity_id',  ts( 'New Activity' ), $activities );

        $reports = array( 1 => "15-day Review", "Disability Application");
        $this->add('select', 'report_id',  ts( 'Report' ), $reports );

        require_once "CRM/Case/PseudoConstant.php";
        $parentCategories = CRM_Case_PseudoConstant::category( );
        $childCategories  = CRM_Case_PseudoConstant::category( false );
        $childParentIds   = CRM_Case_PseudoConstant::category( false, 'parent_id' );

        $sel =& $this->addElement('hierselect', "category", ts('Category') );

        $sel1 = $parentCategories;
        $sel2 = array( );
        foreach( $childParentIds as $childId => $parentId ) {
            $sel2[$parentId][$childId] = $childCategories[$childId];
        }

        $sel->setOptions( array( $sel1, $sel2 ) );

        require_once "CRM/Core/PseudoConstant.php";
        $activityStatus = CRM_Core_PseudoConstant::activityStatus( );
        $this->add('select', 'status_id',  ts( 'Status' ), $activityStatus );

        // Date selects for date 
        $this->add('date', 'activity_date_low', ts('Activity Dates - From'), CRM_Core_SelectValues::date('relative')); 
        $this->addRule('activity_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $this->add('date', 'activity_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $this->addRule('activity_date_high', ts('Select a valid date.'), 'qfDate'); 

        $choice   = array( );
        $choice[] =& $this->createElement( 'radio', null, '11', ts( 'Due' ), '1' );
        $choice[] =& $this->createElement( 'radio', null, '11', ts( 'Completed' ) , '0' );
        
        $group =& $this->addGroup( $choice, 'date_range' );

        //get case related relationships (Case Role)
        $caseRelationships = CRM_Contact_BAO_Relationship::getRelationship( $this->_contactID,
                                                                            CRM_Contact_BAO_Relationship::CURRENT  ,
                                                                            0, 0, 0,
                                                                            null, null, false, $this->_caseID );
        $this->assign('caseRelationships', $caseRelationships);

        //build reporter select
        $reporters = array( );
        foreach( $caseRelationships as $key => $value ) {
            $reporters[$value['cid']] = $value['name'];
        }

        $this->add('select', 'reporter_id',  ts( 'Reporter/Role' ), $reporters );

        $this->addButtons(array(  
                                array ( 'type'      => 'cancel',  
                                        'name'      => ts('Done'),  
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',  
                                        'isDefault' => true   )
                                  )
                          );
    }
}


