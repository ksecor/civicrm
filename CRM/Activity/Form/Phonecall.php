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
 * This class generates form components for Call
 * 
 */
class CRM_Activity_Form_Phonecall extends CRM_Activity_Form
{

    /**
     * variable to store activity type name
     *
     */
    public $_activityType = 2;

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
        $contactPhone[''] = ts('Select Phone Number');
        if ( is_array(CRM_Core_BAO_Phone::getphoneNumber($this->_contactId))) {
            $contactPhone = CRM_Core_BAO_Phone::getphoneNumber($this->_contactId);
        }
        
        $this->add('text', 'subject', ts('Subject'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Phonecall', 'subject' ),true);
        $this->add('date', 'scheduled_date_time', ts('Date and Time'),CRM_Core_SelectValues::date('datetime'),true);
        //$this->addRule( 'scheduled_date_time', ts('Please enter a valid date and time for this call.'), 'qfDate' );
        
        $this->add('select','phone_id',ts('Phone Number'), $contactPhone );
        $this->add('text', 'phone_number'  , ' ' . ts('OR New Phone') , CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Phonecall', 'phone_number' ));
        $this->add('select', 'duration_hours', '', CRM_Core_SelectValues::getHours());
        $this->add('select', 'duration_minutes', '', CRM_Core_SelectValues::getMinutes());
        
        $status =& $this->add('select','status',ts('Status'),CRM_Core_SelectValues::activityStatus(true));
                
        $this->add('textarea', 'details'       , ts('Details')       ,CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Phonecall', 'details' ));
        
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
            CRM_Activity_BAO_Activity::del( $this->_id, 'Phonecall');
            CRM_Core_Session::setStatus( ts("Selected Phone Call is deleted sucessfully."));
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
            $caseActivity->activity_entity_table = 'civicrm_phonecall';
            $caseActivity->activity_entity_id = $ids['id'];
            $caseActivity->find(true);
            $ids['cid'] = $caseActivity->id;
            require_once 'CRM/Activity/DAO/ActivityAssignment.php';
            $ActivityAssignment = new CRM_Activity_DAO_ActivityAssignment();
            $ActivityAssignment->activity_entity_table = 'civicrm_phonecall';
            $ActivityAssignment->activity_entity_id = $ids['id'];
            $ActivityAssignment->find(true);
            $ids['aid'] = $ActivityAssignment->id;
        }
      
        require_once "CRM/Activity/BAO/Activity.php";
        $params['activity_tag3_id']   = CRM_Activity_BAO_Activity::VALUE_SEPERATOR.implode(CRM_Activity_BAO_Activity::VALUE_SEPERATOR, $params['activity_tag3_id'] ).CRM_Activity_BAO_Activity::VALUE_SEPERATOR;

        $caseParams['to_contact'] = CRM_Case_BAO_Case::retrieveCid($params['to_contact']);

        $activity = CRM_Activity_BAO_Activity::createActivity($params, $ids, $this->_activityType);
      
        $caseParams['activity_entity_table'] = 'civicrm_phonecall';
        $caseParams['activity_entity_id']    = $activity->id;
        $caseParams['subject']               = $params['case_subject'];
        CRM_Activity_BAO_Activity::createActivityAssignment( &$caseParams,$ids );
        CRM_Case_BAO_Case::createCaseActivity( &$caseParams,$ids );

    }
}

?>
