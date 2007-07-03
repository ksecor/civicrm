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

require_once 'CRM/Activity/Form.php';

/**
 * This class generates form components for Meeting
 * 
 */
class CRM_Activity_Form_Meeting extends CRM_Activity_Form
{

    /**
     * variable to store activity type name
     *
     */
    public $_activityType = 1;

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }

        $this->applyFilter('__ALL__', 'trim');
       
        $this->add('text', 'subject', ts('Subject') , CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Meeting', 'subject' ),true );
        
        $this->add('date', 'scheduled_date_time', ts('Date and Time'), CRM_Core_SelectValues::date('datetime'),true);
        $this->addRule('scheduled_date_time', ts('Select a valid date.'), 'qfDate');
              
        $this->add('select','duration_hours',ts('Duration'),CRM_Core_SelectValues::getHours());
        $this->add('select','duration_minutes', null,CRM_Core_SelectValues::getMinutes());

        $this->add('text', 'location', ts('Location'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Meeting', 'location' ) );
        
        $this->add('textarea', 'details', ts('Details'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Meeting', 'details' ) );
        
        $status =& $this->add('select','status',ts('Status'), CRM_Core_SelectValues::activityStatus());
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if ($this->_action & CRM_Core_Action::VIEW ) { 
            return;
        }

        if ($this->_action & CRM_Core_Action::DELETE ) { 
            CRM_Activity_BAO_Activity::del( $this->_id, $this->_activityType);
            CRM_Core_Session::setStatus( ts("Selected Meeting is deleted sucessfully."));
            return;
        }

        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        
        require_once 'CRM/Case/BAO/Case.php';
        $this->_sourceCID = CRM_Case_BAO_Case::retrieveCid($params['from_contact']);
        $this->_targetCID = CRM_Case_BAO_Case::retrieveCid($params['regarding_contact']);

        $ids = array();
        
        $dateTime = $params['scheduled_date_time'];

        $dateTime = CRM_Utils_Date::format($dateTime);

        // store the date with proper format
        $params['scheduled_date_time']= $dateTime;

        // store the contact id and current drupal user id
        $params['source_contact_id'  ] = $this->_sourceCID;
        $params['target_entity_id'   ] = $this->_targetCID;
        $params['target_entity_table'] = 'civicrm_contact';
        
        //set parent id if exists for follow up activities
        if ($this->_pid) {
            $params['parent_id'] = $this->_pid;            
        }
        
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['id'] = $this->_id;
            require_once 'CRM/Case/DAO/CaseActivity.php';
            $caseActivity = new CRM_Case_DAO_CaseActivity();
            $caseActivity->activity_entity_table = 'civicrm_meeting';
            $caseActivity->activity_entity_id = $ids['id'];
            $caseActivity->find(true);
            $ids['cid'] = $caseActivity->id;
            require_once 'CRM/Activity/DAO/ActivityAssignment.php';
            $ActivityAssignment = new CRM_Activity_DAO_ActivityAssignment();
            $ActivityAssignment->activity_entity_table = 'civicrm_meeting';
            $ActivityAssignment->activity_entity_id = $ids['id'];
            $ActivityAssignment->find(true);
            $ids['aid'] = $ActivityAssignment->id;
        }

        $ids['source_contact_id'] = $this->_sourceCID;
        $ids['target_entity_id' ] = $this->_targetCID;
        
        require_once "CRM/Activity/BAO/Activity.php";
        $activity = CRM_Activity_BAO_Activity::createActivity($params, $ids, $this->_activityType);
  
        $caseParams['to_contact'] = CRM_Case_BAO_Case::retrieveCid($params['to_contact']);
        $caseParams['activity_entity_table'] = 'civicrm_meeting';
        $caseParams['activity_entity_id']    = $activity->id;
        $caseParams['subject']               = $params['case_subject'];
        CRM_Activity_BAO_Activity::createActivityAssignment( &$caseParams,$ids );
        CRM_Case_BAO_Case::createCaseActivity( &$caseParams,$ids );
    }

}

?>
