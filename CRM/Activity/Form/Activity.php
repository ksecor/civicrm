<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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


require_once 'CRM/Activity/Form.php';


/**
 * This class generates form components for Activity
 * 
 */
class CRM_Activity_Form_Activity extends CRM_Activity_Form
{

    /**
     * variable to store activity type id
     *
     */
    protected $_activityType = 5; //this is for other activity

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
       
        $this->_context = CRM_Utils_Request::retrieve( 'context', 'String',$this );
        $this->assign( 'context', $this->_context );
        
        parent::preProcess();
        
    }

    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }
        
        $this->add('text', 'description', ts('Description'),
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_OptionValue', 'description' ), false);

        $this->add('text', 'subject', ts('Subject') , CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'subject' ), true );

        $this->add('date', 'activity_date_time', ts('Date and Time'), CRM_Core_SelectValues::date('datetime'), true);
        $this->addRule('activity_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->add('select','duration_hours',ts('Duration'),CRM_Core_SelectValues::getHours());
        $this->add('select','duration_minutes', null,CRM_Core_SelectValues::getMinutes());

        $this->add('text', 'location', ts('Location'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'location' ) );
        
        $this->add('textarea', 'details', ts('Details'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'details' ) );
        
        $this->add('select','status_id',ts('Status'), CRM_Core_PseudoConstant::activityStatus( ), true );
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if ( $this->_action & CRM_Core_Action::VIEW ) { 
            return;
        }
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            CRM_Activity_BAO_Activity::removeActivity( $this->_activityId, 'Activity');
            CRM_Core_Session::setStatus( ts("Selected Meeting is deleted sucessfully."));
            return;
        }
        
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        $ids = array( );
        
        // store the date with proper format
        $params['activity_date_time']= CRM_Utils_Date::format( $params['activity_date_time'] );

        // get ids for associated contacts
        $params['source_contact_id'] = CRM_Contact_BAO_Contact::getIdByDisplayName($params['source_contact']);
        $params['target_contact_id'] = CRM_Contact_BAO_Contact::getIdByDisplayName($params['target_contact']);
        $params['assignee_contact_id'] = CRM_Contact_BAO_Contact::getIdByDisplayName($params['assignee_contact']);

        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $params['id'] = $this->_activityId;

//            require_once 'CRM/Case/DAO/CaseActivity.php';
//            $caseActivity = new CRM_Case_DAO_CaseActivity();
//            $caseActivity->activity_entity_table = 'civicrm_activity';
//            $caseActivity->activity_entity_id = $ids['id'];
//            $caseActivity->find(true);
//            $ids['cid'] = $caseActivity->id;
//            require_once 'CRM/Activity/DAO/ActivityAssignment.php';
        }

        require_once "CRM/Activity/BAO/Activity.php";
        CRM_Activity_BAO_Activity::create( $params );

        // set status message
        CRM_Core_Session::setStatus( ts('Activity "%1"  has been saved.', array( 1 => $params['subject'] ) ) );

//        $activity = CRM_Activity_BAO_Activity::createActivity($params, $ids,$params["activity_type_id"] );

//        require_once 'CRM/Case/BAO/Case.php';
//        $caseParams['activity_entity_table'] = 'civicrm_activity';
//        $caseParams['activity_entity_id']    = $activity->id;
//        $caseParams['subject']               = $params['case_subject'];
//        CRM_Activity_BAO_Activity::createActivityAssignment( &$caseParams,$ids );
//        CRM_Case_BAO_Case::createCaseActivity( &$caseParams,$ids );        
    }
}

?>
