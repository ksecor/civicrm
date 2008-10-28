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
        
        $this->assign( 'caseID', $this->_caseID );
        $this->assign( 'contactID', $this->_contactID );

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
                $caseType .= $caseTypes[$value];
            }
        }

        $this->_caseDetails = array( 'case_type'    => $caseType,
                                     'case_status'  => $statuses[$values['case_status_id']],
                                     'case_subject' => $values['subject']
                              );
        
        $this->assign ( 'caseDetails', $this->_caseDetails );

        $newActivityUrl = 
            CRM_Utils_System::url( 'civicrm/case/activity', 
                                   "action=add&reset=1&cid={$this->_contactID}&id={$this->_caseID}&selectedChild=activity&atype=", 
                                   false, null, false ); 
        $this->assign ( 'newActivityUrl', $newActivityUrl );

        $reportUrl = 
            CRM_Utils_System::url( 'civicrm/case/report', 
                                   "reset=1&cid={$this->_contactID}&id={$this->_caseID}&asn=", 
                                   false, null, false ); 
        $this->assign ( 'reportUrl', $reportUrl );

        $show = CRM_Utils_Request::retrieve( 'show', 'Integer', CRM_Core_DAO::$_nullObject );
        if ( $show ) {
            $this->assign ( 'show', $show );
        }
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        require_once 'CRM/Case/XMLProcessor/Process.php';
        $xmlProcessor = new CRM_Case_XMLProcessor_Process( );
        $caseRoles    = $xmlProcessor->get( $this->_caseDetails['case_type'], 'CaseRoles' );

        $reports = $xmlProcessor->get( $this->_caseDetails['case_type'], 'ActivitySets' );
        $this->add('select', 'report_id',  ts( 'Report' ), array( '' => ts( '- select report -' ) ) + $reports );

        require_once "CRM/Case/PseudoConstant.php";
        require_once 'CRM/Core/Component.php';
        $condition = "(component_id = " . CRM_Core_Component::getComponentID( 'CiviCase' ) . ")";
        $parentCategories = CRM_Case_PseudoConstant::category( true, 'label',
                                                               $condition );
        $childCategories  = CRM_Case_PseudoConstant::category( false, 'label',
                                                               $condition );
        $childParentIds   = CRM_Case_PseudoConstant::category( false, 'parent_id',
                                                               $condition );

        $sel =& $this->addElement('hierselect', "category", ts('Category'), array( 'id' => 'category' ) );

        $sel1 = array( "0" => ts(' - any category - ') ) + $parentCategories;
        $sel2 = array( );
        
        foreach( $childParentIds as $childId => $parentId ) {
            if ( empty( $sel2[$parentId] ) ) {
                $sel2[$parentId][0] = ts(' - any activity type - ');
            }
            
            $sel2[$parentId][$childId] = $childCategories[$childId];
        }
        
        $sel2 = array( "0" => array( "0" => ts(' - any activity type - ') ) ) + $sel2;
        
        $sel->setOptions( array( $sel1, $sel2 ) );

        require_once "CRM/Core/PseudoConstant.php";
        $activityStatus = CRM_Core_PseudoConstant::activityStatus( );
        $this->add('select', 'status_id',  ts( 'Status' ), array( "" => ts(' - any status - ') ) + $activityStatus );

        // Date selects for date 
        $this->add('date', 'activity_date_low', ts('Activity Dates - From'), CRM_Core_SelectValues::date('relative')); 
        $this->addRule('activity_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $this->add('date', 'activity_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $this->addRule('activity_date_high', ts('Select a valid date.'), 'qfDate'); 

        $choices   = array( 1 => ts( 'Due' ),
                            2 => ts( 'Actual' )
                            );

        $this->addRadio('date_range', null, $choices );
        
        //get case related relationships (Case Role)
        $caseRelationships = CRM_Case_BAO_Case::getCaseRoles( $this->_contactID, $this->_caseID );
        $this->assign('caseRelationships', $caseRelationships);

        //build reporter select
        $reporters = array( "" => ts(' - any reporter - ') );
        foreach( $caseRelationships as $key => $value ) {
            $reporters[$value['cid']] = $value['name'] . " ( {$value['relation']} )";

            //calculate roles that don't have relationships
            if ( $key = array_search( $value['relation'], $caseRoles ) ) {
                unset( $caseRoles[$key] ) ;
            }
        }

        $this->assign( 'caseRoles', $caseRoles );
        
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


