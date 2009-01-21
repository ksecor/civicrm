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

require_once 'CRM/Core/Form.php';
require_once "CRM/Core/PseudoConstant.php";
require_once "CRM/Case/PseudoConstant.php";
require_once 'CRM/Case/XMLProcessor/Process.php';
require_once 'CRM/Case/BAO/Case.php';

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
        
        $values['case_type_id'] = explode( CRM_Case_BAO_Case::VALUE_SEPERATOR, 
                                           CRM_Utils_Array::value( 'case_type_id' , $values ) );

        $statuses  = CRM_Case_PseudoConstant::caseStatus( );
        $caseType  = CRM_Case_PseudoConstant::caseTypeName( $this->_caseID );

        $this->_caseDetails = array( 'case_type'    => $caseType['name'],
                                     'case_status'  => $statuses[$values['case_status_id']],
                                     'case_subject' => CRM_Utils_Array::value( 'subject', $values )
                                   );
        $this->_caseType = $caseType['name'];
        $this->assign ( 'caseDetails', $this->_caseDetails );
        
        $newActivityUrl = 
            CRM_Utils_System::url( 'civicrm/case/activity', 
                                   "action=add&reset=1&cid={$this->_contactID}&caseid={$this->_caseID}&atype=", 
                                   false, null, false ); 
        $this->assign ( 'newActivityUrl', $newActivityUrl );

        $reportUrl = 
            CRM_Utils_System::url( 'civicrm/case/report', 
                                   "reset=1&cid={$this->_contactID}&caseid={$this->_caseID}&asn=", 
                                   false, null, false ); 
        $this->assign ( 'reportUrl', $reportUrl );
        
    }

    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $defaults['date_range'] = 1;

        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $xmlProcessor = new CRM_Case_XMLProcessor_Process( );
        $caseRoles    = $xmlProcessor->get( $this->_caseType, 'CaseRoles' );
        $reports      = $xmlProcessor->get( $this->_caseType, 'ActivitySets' );

        $aTypes       = $xmlProcessor->get( $this->_caseType, 'ActivityTypes', true );
        // remove Open Case activity type since we're inside an existing case
        $openCaseID = CRM_Core_OptionGroup::getValue('activity_type', 'Open Case', 'name' );
        unset( $aTypes[$openCaseID] );
        asort( $aTypes );

        
        $this->add('select', 'activity_type_id',  ts( 'New Activity' ), array( '' => ts( '- select activity type -' ) ) + $aTypes );
        $this->add('select', 'report_id',  ts( 'Report' ), array( '' => ts( '- select report -' ) ) + $reports );
        $this->add('select', 'timeline_id',  ts( 'Add Timeline' ), array( '' => ts( '- select activity set -' ) ) + $reports );
        $this->addElement( 'submit', $this->getButtonName('next'), ts('Go'), 
                           array( 'class'   => 'form-submit',
                                  'onclick' => "return checkSelection( this );") ); 
        
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
        
		require_once"CRM/Core/Permission.php";
		if ( CRM_Core_Permission::check( 'administer CiviCRM' ) ) { 
			$this->add( 'checkbox', 'activity_deleted' , ts( 'Deleted Activities' ) );
		}

                                                                                     
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

		// Include client relationships
		$relClient = CRM_Contact_BAO_Relationship::getRelationship($this->_contactID,
									CRM_Contact_BAO_Relationship::CURRENT,
									0, 0, 0, null, null, false);

		// Now remove ones that are also roles so they don't show up twice. Also remove the client itself.
		// We need to do this because otherwise EVERY role will show up twice, since every role is also a client relationship.
		// We conveniently already have the required list of cids in the reporters variable.
		$clientRelationships = array();
		foreach($relClient as $r)
		{
			$cid = $r['cid'];
			if (($cid != $this->_contactID) && (! array_key_exists($cid, $reporters)))
			{
				$clientRelationships[] = $r;
			}
		}
        $this->assign('clientRelationships', $clientRelationships);

		// Now global contact list that appears on all cases.
		$globalGroupName = null;
		$globalGroupId = null;
		$relGlobal = CRM_Case_BAO_Case::getGlobalContacts($globalGroupName, $globalGroupId);
        $this->assign('globalRelationships', $relGlobal);
        $this->assign('globalGroupName', $globalGroupName);
        $this->assign('globalGroupId', $globalGroupId);
        
		// List of relationship types
		$relType = CRM_Core_PseudoConstant::relationshipType();
		$roleTypes = array();
		foreach ($relType as $k => $v)
		{
			$roleTypes[$k] = $v['name_b_a'];
		}
		$this->add('select', 'role_type',  ts( 'Relationship Type' ), array( '' => ts( '- select type -' ) ) + $roleTypes );
	
        $this->addButtons(array(  
                                array ( 'type'      => 'cancel',  
                                        'name'      => ts('Done'),  
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',  
                                        'isDefault' => true   )
                                  )
                          );
    }


    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        $params = $this->controller->exportValues( $this->_name );
                      
        // user context
        $url = CRM_Utils_System::url( 'civicrm/contact/view/case',
                                      "reset=1&action=view&cid={$this->_contactID}&id={$this->_caseID}&show=1" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $url );

        if ( CRM_Utils_Array::value( 'timeline_id', $params ) && 
             CRM_Utils_Array::value( '_qf_CaseView_next', $_POST ) ) {
            $session    =& CRM_Core_Session::singleton();
            $this->_uid = $session->get('userID');
            $xmlProcessor = new CRM_Case_XMLProcessor_Process( );
            $xmlProcessorParams = array( 
                                        'clientID'           => $this->_contactID,
                                        'creatorID'          => $this->_uid,
                                        'standardTimeline'   => 0,
                                        'dueDateTime'        => date ('YmdHis'),
                                        'caseID'             => $this->_caseID,
                                        'caseType'           => $this->_caseType,
                                        'activitySetName'    => $params['timeline_id'] 
                                        );
            $xmlProcessor->run( $this->_caseType, $xmlProcessorParams );
            $reports      = $xmlProcessor->get( $this->_caseType, 'ActivitySets' );
            
            CRM_Core_Session::setStatus( ts('Activities from the %1 activity set have been added to this case.', 
                                            array( 1 => $reports[$params['timeline_id']] ) ) );
        }
    }
}